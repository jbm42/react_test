<?php

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

function ensureDirectory(string $path): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

function cacheAsset(string $baseUrl, ?string $assetPath, string $localBase): ?string
{
    if ($assetPath === null) {
        return null;
    }

    $assetPath = '/' . ltrim($assetPath, '/');
    $fullLocalPath = rtrim($localBase, '/') . $assetPath;

    if (!file_exists($fullLocalPath)) {
        $content = fetchRemote($baseUrl . $assetPath);
        if ($content === null) {
            return null;
        }
        ensureDirectory($fullLocalPath);
        if (file_put_contents($fullLocalPath, $content) === false) {
            error_log("Failed to persist asset to {$fullLocalPath}");
            return null;
        }
    }

    return $assetPath;
}

$nodeInternal = rtrim(getenv('NODE_INTERNAL_URL') ?: 'http://node:5173', '/');
$assetPublicBase = '/build';
$assetLocalBase = __DIR__ . $assetPublicBase;

$ssr = fetchSsrPayload($nodeInternal . '/ssr');
$ssrHtml = $ssr['html'] ?? '';
$ssrCss = isset($ssr['css']) && is_array($ssr['css']) ? $ssr['css'] : [];
$ssrEntry = $ssr['entry'] ?? null;
$cachedCss = [];
foreach ($ssrCss as $href) {
    $cached = cacheAsset($nodeInternal, $href, $assetLocalBase);
    if ($cached !== null) {
        $cachedCss[] = $cached;
    }
}
$cachedEntry = cacheAsset($nodeInternal, $ssrEntry, $assetLocalBase);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PHP + Reactive (server-side fetch)</title>
<?php foreach ($cachedCss as $href): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($assetPublicBase . $href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<?php endforeach; ?>
</head>
<body>
  <h1>PHP + Reactive Test</h1>
  <p>Server time: <?= date("H:i:s") ?></p>
  <div id="reactive"><?= $ssrHtml ?></div>

<?php if ($cachedEntry !== null): ?>
  <script type="module" src="<?= htmlspecialchars($assetPublicBase . $cachedEntry, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"></script>
<?php else: ?>
  <script>
    console.error('SSR entry not available. Check Node server logs.');
  </script>
<?php endif; ?>
</body>
</html>
