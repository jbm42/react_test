<?php
declare(strict_types=1);

const REACTIVE_BUILD_DIR = __DIR__ . '/build';
const REACTIVE_PUBLIC_BASE = '/build';

function reactiveComponent(string $componentName, array $context = []): string
{
    $rendererBaseUrl = rtrim(getenv('NODE_INTERNAL_URL') ?: 'http://node:5173', '/');
    $query = ['page' => $componentName];
    $ssrParam = $_GET['ssr'] ?? null;
    $ssrEnabled = $ssrParam === null ? true : ($ssrParam !== '0');

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

    $cssJson = json_encode($cssAssets, JSON_UNESCAPED_SLASHES);
    if ($cssJson === false) {
        $cssJson = '[]';
    }

    $contextJson = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($contextJson === false) {
        $contextJson = '{}';
    }

    $pageIdentifier = $payload['page'] ?? $componentName;
    $html = $ssrEnabled ? ($payload['html'] ?? '') : '';

    $attrs = [
        'data-page' => $pageIdentifier,
        'data-entry' => $entry ?? '',
        'data-css' => $cssJson,
        'data-context' => $contextJson,
        'data-ssr' => $ssrEnabled ? '1' : '0',
    ];

    $attrString = reactiveFormatAttributes($attrs);

    $entryJsLiteral = $entry !== null ? json_encode($entry, JSON_UNESCAPED_SLASHES) : 'null';
    $pageJsLiteral = json_encode($pageIdentifier, JSON_UNESCAPED_SLASHES);

    $linkBlocks = reactiveBuildStylesheetLinks($cssAssets);
    $resetStyle = '<style data-reactive-reset="true">:host { all: initial !important; display: block; }</style>';

    $bootstrap = <<<HTML
<div class="reactive-shell">
  <reactive-root {$attrString}>
    <template shadowroot="open">{$resetStyle}{$linkBlocks}<div data-reactive-host>{$html}</div></template>
  </reactive-root>
  <script>
(function() {
  const entry = {$entryJsLiteral};
  const page = {$pageJsLiteral};
  if (!entry) {
    console.error('reactive: entry script missing for component', page);
    return;
  }
  const existing = document.querySelector('script[data-reactive-entry="' + entry + '"]');
  if (existing) {
    return;
  }
  const script = document.createElement('script');
  script.type = 'module';
  script.src = entry;
  script.dataset.reactiveEntry = entry;
  document.head.appendChild(script);
})();
</script>
</div>
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

/**
 * @param array<int, string> $assets
 */
function reactiveBuildStylesheetLinks(array $assets): string
{
    if (empty($assets)) {
        return '';
    }

    $links = array_map(static function (string $href): string {
        return '<link rel="stylesheet" data-reactive-stylesheet="true" href="' .
            htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') .
            '">';
    }, $assets);

    return implode('', $links);
}

