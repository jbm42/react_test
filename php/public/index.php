<?php
$bundlePath = __DIR__ . '/build/inject.js';
$bundleExists = file_exists($bundlePath);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PHP + Reactive (server-side fetch)</title>
</head>
<body>
  <h1>PHP + Reactive Test</h1>
  <p>Server time: <?= date("H:i:s") ?></p>
  <div id="reactive"></div>

<?php if ($bundleExists): ?>
  <script type="module" src="/build/inject.js"></script>
<?php else: ?>
  <script>
    console.error('Build artifact missing at <?= addslashes($bundlePath) ?>');
  </script>
<?php endif; ?>
</body>
</html>
