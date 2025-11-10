<?php
declare(strict_types=1);

const SSR_BUILD_DIR = __DIR__ . '/build';
const SSR_PUBLIC_BASE = '/build';

function fetchRemote(string $url): ?string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_TIMEOUT => 5,
    ]);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($data === false || $code >= 400) {
        error_log("Fetch failed for {$url} ({$code}): {$err}");
        return null;
    }

    return $data;
}

function ensureDirectory(string $path): void
{
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

function cacheAsset(string $baseUrl, ?string $assetPath, string $localBase): ?string
{
    if ($assetPath === null) {
        return null;
    }

    $normalized = '/' . ltrim($assetPath, '/');
    $fullLocalPath = rtrim($localBase, '/') . $normalized;

    if (!file_exists($fullLocalPath)) {
        $content = fetchRemote($baseUrl . $normalized);
        if ($content === null) {
            return null;
        }
        ensureDirectory($fullLocalPath);
        if (file_put_contents($fullLocalPath, $content) === false) {
            error_log("Failed to persist asset to {$fullLocalPath}");
            return null;
        }
    }

    return $normalized;
}

function fetchSsrPayload(string $url): ?array
{
    $data = fetchRemote($url);
    if ($data === null) {
        return null;
    }

    $decoded = json_decode($data, true);
    if (!is_array($decoded)) {
        error_log("SSR payload was not valid JSON: {$data}");
        return null;
    }

    return $decoded;
}

function getSsrPayload(string $pageName): array
{
    $nodeInternal = rtrim(getenv('NODE_INTERNAL_URL') ?: 'http://node:5173', '/');
    $query = http_build_query(['page' => $pageName]);
    $ssr = fetchSsrPayload($nodeInternal . '/ssr?' . $query);

    if ($ssr === null) {
        return [
            'html' => '',
            'css' => [],
            'entry' => null,
            'page' => $pageName,
        ];
    }

    $cssAssets = [];
    if (isset($ssr['css']) && is_array($ssr['css'])) {
        foreach ($ssr['css'] as $href) {
            $cached = cacheAsset($nodeInternal, $href, SSR_BUILD_DIR);
            if ($cached !== null) {
                $cssAssets[] = SSR_PUBLIC_BASE . $cached;
            }
        }
    }

    $entryPath = cacheAsset($nodeInternal, $ssr['entry'] ?? null, SSR_BUILD_DIR);
    $entry = $entryPath ? SSR_PUBLIC_BASE . $entryPath : null;

    return [
        'html' => $ssr['html'] ?? '',
        'css' => $cssAssets,
        'entry' => $entry,
        'page' => $ssr['page'] ?? $pageName,
    ];
}

