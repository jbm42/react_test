<?php
declare(strict_types=1);

const REACTIVE_BUILD_DIR = __DIR__ . '/build';
const REACTIVE_PUBLIC_BASE = '/build';

function reactiveComponent(string $componentName, array $context = []): string
{
    $rendererBaseUrl = rtrim(getenv('NODE_INTERNAL_URL') ?: 'http://node:5173', '/');
    $query = ['page' => $componentName];

    if (!empty($context)) {
        $encoded = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($encoded !== false) {
            $query['context'] = $encoded;
        }
    }

    $payload = reactiveFetchPayload($rendererBaseUrl . '/ssr?' . http_build_query($query));

    if ($payload === null) {
        return '<!-- reactive: failed to load component -->';
    }

    $cssAssets = [];
    if (isset($payload['css']) && is_array($payload['css'])) {
        foreach ($payload['css'] as $href) {
            $cached = reactiveCacheAsset($rendererBaseUrl, $href, REACTIVE_BUILD_DIR);
            if ($cached !== null) {
                $cssAssets[] = REACTIVE_PUBLIC_BASE . $cached;
            }
        }
    }

    $entryPath = reactiveCacheAsset($rendererBaseUrl, $payload['entry'] ?? null, REACTIVE_BUILD_DIR);
    $entry = $entryPath ? REACTIVE_PUBLIC_BASE . $entryPath : null;

    $containerId = 'reactive';
    $attrs = [
        'id' => $containerId,
        'data-page' => $payload['page'] ?? $componentName,
        'data-entry' => $entry ?? '',
        'data-css' => json_encode($cssAssets, JSON_UNESCAPED_SLASHES),
        'data-context' => json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    ];

    $attrString = reactiveFormatAttributes($attrs);
    $html = $payload['html'] ?? '';

    $bootstrap = <<<HTML
<div {$attrString}>{$html}</div>
<script>
(function() {
  const script = document.currentScript;
  const container = script.previousElementSibling;
  if (!container) return;
  let css = [];
  try {
    css = JSON.parse(container.dataset.css || '[]');
  } catch (err) {
    console.warn('reactive: failed to parse css dataset', err);
  }
  css.forEach(function(href) {
    if (!href) return;
    if (document.querySelector('link[data-reactive-css="' + href + '"]')) return;
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    link.dataset.reactiveCss = href;
    document.head.appendChild(link);
  });
  const entry = container.dataset.entry;
  if (entry && !document.querySelector('script[data-reactive-entry="' + entry + '"]')) {
    const injected = document.createElement('script');
    injected.type = 'module';
    injected.src = entry;
    injected.dataset.reactiveEntry = entry;
    document.head.appendChild(injected);
  }
})();
</script>
HTML;

    return $bootstrap;
}

function reactiveFormatAttributes(array $attrs): string
{
    $parts = [];
    foreach ($attrs as $key => $value) {
        if ($value === '' || $value === null) {
            continue;
        }
        $parts[] = sprintf(
            '%s="%s"',
            htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        );
    }

    return implode(' ', $parts);
}

/**
 * @return array<string, mixed>|null
 */
function reactiveFetchPayload(string $url): ?array
{
    $data = reactiveFetchRemote($url);
    if ($data === null) {
        return null;
    }

    $decoded = json_decode($data, true);
    if (!is_array($decoded)) {
        error_log("Reactive SSR payload was not valid JSON: {$data}");
        return null;
    }

    return $decoded;
}

function reactiveCacheAsset(string $baseUrl, ?string $assetPath, string $localBase): ?string
{
    if ($assetPath === null) {
        return null;
    }

    $normalized = '/' . ltrim($assetPath, '/');
    $fullLocalPath = rtrim($localBase, '/') . $normalized;

    if (!file_exists($fullLocalPath)) {
        $content = reactiveFetchRemote($baseUrl . $normalized);
        if ($content === null) {
            return null;
        }
        reactiveEnsureDirectory($fullLocalPath);
        if (file_put_contents($fullLocalPath, $content) === false) {
            error_log("Failed to persist reactive asset to {$fullLocalPath}");
            return null;
        }
    }

    return $normalized;
}

function reactiveEnsureDirectory(string $path): void
{
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

function reactiveFetchRemote(string $url): ?string
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
        error_log("Reactive fetch failed for {$url} ({$code}): {$err}");
        return null;
    }

    return $data;
}

