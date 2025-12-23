<?php
declare(strict_types=1);

const REACTIVE_BUILD_DIR = __DIR__ . '/build';
const REACTIVE_PUBLIC_BASE = '/build';
const REACTIVE_RENDERER_DEFAULT = 'vue';

/**
 * @return array<string, array{label: string, base_url: string}>
 */
function reactiveRendererCatalog(): array
{
    static $catalog = null;

    if ($catalog !== null) {
        return $catalog;
    }

    $catalog = [
        'vue' => [
            'label' => 'Vue 3',
            'base_url' => reactiveResolveRendererBaseUrl('NODE_VUE_INTERNAL_URL', 'http://vue:5173'),
        ],
        'svelte' => [
            'label' => 'Svelte',
            'base_url' => reactiveResolveRendererBaseUrl('NODE_SVELTE_INTERNAL_URL', 'http://svelte:5174'),
        ],
        'next' => [
            'label' => 'Next.js (React)',
            'base_url' => reactiveResolveRendererBaseUrl('NODE_NEXT_INTERNAL_URL', 'http://next:5175'),
        ],
        'react' => [
            'label' => 'React',
            'base_url' => reactiveResolveRendererBaseUrl('NODE_REACT_INTERNAL_URL', 'http://react:5176'),
        ],
    ];

    return $catalog;
}

function reactiveResolveRendererBaseUrl(string $envName, string $fallback): string
{
    $value = trim((string) getenv($envName));
    if ($value === '') {
        return rtrim($fallback, '/');
    }

    return rtrim($value, '/');
}

function reactiveRendererDefault(): string
{
    $catalog = reactiveRendererCatalog();
    if (array_key_exists(REACTIVE_RENDERER_DEFAULT, $catalog)) {
        return REACTIVE_RENDERER_DEFAULT;
    }

    $first = array_key_first($catalog);
    return $first !== null ? $first : REACTIVE_RENDERER_DEFAULT;
}

function reactiveNormalizeRenderer(?string $candidate): string
{
    $catalog = reactiveRendererCatalog();
    if ($candidate !== null) {
        $normalized = strtolower(trim($candidate));
        if ($normalized !== '' && array_key_exists($normalized, $catalog)) {
            return $normalized;
        }
    }

    return reactiveRendererDefault();
}

/**
 * @return array{label: string, base_url: string}
 */
function reactiveRendererConfig(string $renderer): array
{
    $catalog = reactiveRendererCatalog();
    if (array_key_exists($renderer, $catalog)) {
        return $catalog[$renderer];
    }

    $fallback = reactiveRendererDefault();
    return $catalog[$fallback];
}

/**
 * @return array<string, array{label: string, base_url: string}>
 */
function reactiveRendererOptions(): array
{
    return reactiveRendererCatalog();
}

function reactiveRendererLabel(string $renderer): string
{
    $config = reactiveRendererConfig($renderer);
    return $config['label'];
}

function reactiveComponent(string $componentName, array $context = [], ?string $renderer = null): string
{
    $rendererSelection = $renderer ?? ($_GET['renderer'] ?? null);
    $rendererKey = reactiveNormalizeRenderer($rendererSelection);
    $rendererConfig = reactiveRendererConfig($rendererKey);
    $rendererBaseUrl = rtrim($rendererConfig['base_url'], '/');
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

    $localBase = REACTIVE_BUILD_DIR . '/' . $rendererKey;
    $cssAssets = [];
    if (isset($payload['css']) && is_array($payload['css'])) {
        foreach ($payload['css'] as $href) {
            $normalized = reactiveNormalizeAssetPath($href);
            if ($normalized === null) {
                continue;
            }
            $cached = reactiveCacheAsset($rendererBaseUrl, $normalized, $localBase);
            if ($cached !== null) {
                $cssAssets[] = REACTIVE_PUBLIC_BASE . '/' . $rendererKey . $cached;
            }
        }
    }

    $entryAssets = [];
    foreach (reactiveNormalizeEntries($payload['entry'] ?? null) as $assetPath) {
        $cached = reactiveCacheAsset($rendererBaseUrl, $assetPath, $localBase);
        if ($cached !== null) {
            $entryAssets[] = REACTIVE_PUBLIC_BASE . '/' . $rendererKey . $cached;
        }
    }

    $cssJson = json_encode($cssAssets, JSON_UNESCAPED_SLASHES);
    if ($cssJson === false) {
        $cssJson = '[]';
    }

    $entryJson = json_encode($entryAssets, JSON_UNESCAPED_SLASHES);
    if ($entryJson === false) {
        $entryJson = '[]';
    }

    $contextJson = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($contextJson === false) {
        $contextJson = '{}';
    }

    $pageIdentifier = $payload['page'] ?? $componentName;
    $rendererJsLiteral = json_encode($rendererKey, JSON_UNESCAPED_SLASHES);
    if ($rendererJsLiteral === false) {
        $rendererJsLiteral = json_encode('unknown', JSON_UNESCAPED_SLASHES);
    }
    $html = $ssrEnabled ? ($payload['html'] ?? '') : '';

    $attrs = [
        'data-page' => $pageIdentifier,
        'data-entry' => $entryJson,
        'data-css' => $cssJson,
        'data-context' => $contextJson,
        'data-ssr' => $ssrEnabled ? '1' : '0',
        'data-renderer' => $rendererKey,
    ];

    $attrString = reactiveFormatAttributes($attrs);

    $entryJsLiteral = $entryJson;
    $pageJsLiteral = json_encode($pageIdentifier, JSON_UNESCAPED_SLASHES);

    $linkBlocks = reactiveBuildStylesheetLinks($cssAssets);
    $useShadowRoot = $rendererKey !== 'next';
    $resetStyle = '<style data-reactive-reset="true">:host { all: initial !important; display: block; }</style>';
    if ($useShadowRoot) {
        $hostAttributes = 'data-reactive-host';
        $rootContent = '<template shadowroot="open">' .
            $resetStyle .
            $linkBlocks .
            '<div ' . $hostAttributes . '>' . $html . '</div></template>';
        $cssJsLiteral = '[]';
    } else {
        $hostAttributes = 'data-reactive-host class="reactive-next-host" style="all: initial; display: block; width: 100%;"';
        $nextReset = '<style data-reactive-next-reset="true">'
            . '.reactive-next-host, .reactive-next-host * {'
            . 'all: revert;'
            . '}'
            . '.reactive-next-host {'
            . 'font: inherit;'
            . 'color: inherit;'
            . 'background: transparent;'
            . 'width: 100%;'
            . '}'
            . '.reactive-next-host > [data-reactive-next-root] {'
            . 'width: 100%;'
            . '}'
            . '.reactive-next-host p,'
            . '.reactive-next-host li,'
            . '.reactive-next-host h1,'
            . '.reactive-next-host h2,'
            . '.reactive-next-host h3 {'
            . 'line-height: inherit;'
            . '}'
            . '</style>';
        $rootContent = '<div ' . $hostAttributes . '>' . $nextReset . $linkBlocks . '<div data-reactive-next-root>' . $html . '</div></div>';
        $cssJsLiteral = $cssJson;
    }

    $bootstrap = <<<HTML
<div class="reactive-shell">
  <reactive-root {$attrString}>
    {$rootContent}
  </reactive-root>
  <script>
(function() {
  const entry = {$entryJsLiteral};
  const page = {$pageJsLiteral};
  const css = {$cssJsLiteral};
  const entries = Array.isArray(entry) ? entry : (entry ? [entry] : []);
  const renderer = {$rendererJsLiteral};
  const cssEntries = Array.isArray(css) ? css : (css ? [css] : []);
  const isModuleScript = renderer !== 'next';
  if (!entries || entries.length === 0) {
    console.error('reactive: entry script missing for component', page);
    return;
  }
  entries.forEach(function(href) {
    if (typeof href !== 'string' || href.length === 0) {
      return;
    }
    const existing = document.querySelector('script[data-reactive-entry="' + href + '"]');
    if (existing) {
      return;
    }
    const script = document.createElement('script');
    if (isModuleScript) {
    script.type = 'module';
    } else {
      script.type = 'text/javascript';
    }
    script.async = false;
    if (!isModuleScript) {
      script.crossOrigin = 'anonymous';
    }
    script.src = href;
    script.dataset.reactiveEntry = href;
    document.head.appendChild(script);
  });
  if (renderer === 'next') {
    cssEntries.forEach(function(href) {
      if (typeof href !== 'string' || href.length === 0) {
        return;
      }
      const existing = document.querySelector('link[data-reactive-css="' + href + '"]');
      if (existing) {
        return;
      }
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = href;
      link.dataset.reactiveCss = href;
      document.head.appendChild(link);
    });
  }
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

/**
 * @param mixed $entry
 * @return array<int, string>
 */
function reactiveNormalizeEntries($entry): array
{
    if (is_array($entry)) {
        $normalized = [];
        foreach ($entry as $value) {
            $path = reactiveNormalizeAssetPath($value);
            if ($path !== null) {
                $normalized[$path] = $path;
            }
        }
        return array_values($normalized);
    }

    $single = reactiveNormalizeAssetPath($entry);
    return $single !== null ? [$single] : [];
}

/**
 * @param mixed $path
 */
function reactiveNormalizeAssetPath($path): ?string
{
    if (!is_string($path)) {
        return null;
    }

    $trimmed = trim($path);
    if ($trimmed === '') {
        return null;
    }

    return '/' . ltrim($trimmed, '/');
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

