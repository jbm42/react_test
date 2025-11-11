<?php
require_once __DIR__ . '/reactive.php';

function redirectWithQuery(array $params): void
{
    $current = $_GET;
    foreach ($params as $key => $value) {
        if ($value === null) {
            unset($current[$key]);
        } else {
            $current[$key] = $value;
        }
    }

    $query = http_build_query($current);
    $target = strtok($_SERVER['REQUEST_URI'], '?');
    if ($query !== '') {
        $target .= '?' . $query;
    }
    header('Location: ' . $target);
    exit;
}

function rendererAwareUrl(string $path, array $overrides = [], array $omit = []): string
{
    $params = $_GET;

    foreach ($omit as $key) {
        unset($params[$key]);
    }

    foreach ($overrides as $key => $value) {
        if ($value === null) {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }

    $query = http_build_query($params);

    return $query === '' ? $path : $path . '?' . $query;
}

$availableRenderers = reactiveRendererOptions();
$currentRenderer = reactiveNormalizeRenderer($_GET['renderer'] ?? null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_theme'])) {
        $current = isset($_GET['theme']) ? $_GET['theme'] : '0';
        $next = $current === '1' ? '0' : '1';
        redirectWithQuery(['theme' => $next]);
    }

    if (isset($_POST['toggle_ssr'])) {
        $current = $_GET['ssr'] ?? '1';
        $next = $current === '0' ? '1' : '0';
        redirectWithQuery(['ssr' => $next]);
    }

    if (isset($_POST['set_renderer'])) {
        $selected = reactiveNormalizeRenderer($_POST['renderer'] ?? null);
        redirectWithQuery(['renderer' => $selected]);
    }
}

$pageName = 'test-one';
$title = 'SSR Demo — Test One';
$altStylesheet = isset($_GET['theme']) ? $_GET['theme'] === '1' : false;
$ssrEnabled = !isset($_GET['ssr']) || $_GET['ssr'] !== '0';
$currentRendererLabel = reactiveRendererLabel($currentRenderer);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <?php
    $stylesheetHref = $altStylesheet ? '/style2.css' : '/style.css';
  ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($stylesheetHref, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" id="php-theme">
</head>
<body>
  <header>
    <div>
      <strong>SSR Demo</strong>
    </div>
    <nav>
      <a href="<?= htmlspecialchars(rendererAwareUrl('/test-one.php', ['renderer' => $currentRenderer]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Test One</a>
      <a href="<?= htmlspecialchars(rendererAwareUrl('/test-two.php', ['renderer' => $currentRenderer]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Test Two</a>
    </nav>
  </header>

  <main>
    <section>
      <h1><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
      <p>Server time: <?= date("H:i:s") ?></p>
      <p>This host page renders the <code><?= htmlspecialchars($pageName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></code> <?= htmlspecialchars($currentRendererLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> component.</p>
      <form action="" method="post" style="margin-top:1.5rem;">
        <button type="submit" name="toggle_theme" value="1" class="toggle-theme">
          Switch to <?= $altStylesheet ? 'Classic' : 'Radical' ?> Theme
        </button>
      </form>
      <form action="" method="post" style="margin-top:1rem;">
        <button type="submit" name="toggle_ssr" value="1" class="toggle-ssr">
          <?= $ssrEnabled ? 'Disable SSR (Client Render Only)' : 'Enable SSR Rendering' ?>
        </button>
      </form>
      <form action="" method="post" style="margin-top:1rem;">
        <label for="renderer-select"><strong>Renderer</strong></label>
        <div style="display:flex; gap:0.75rem; align-items:center; margin-top:0.5rem;">
          <select id="renderer-select" name="renderer">
            <?php foreach ($availableRenderers as $key => $info): ?>
              <option value="<?= htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" <?= $key === $currentRenderer ? 'selected' : '' ?>>
                <?= htmlspecialchars($info['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="set_renderer" value="1" class="switch-renderer">
            Switch
          </button>
        </div>
      </form>
      <p class="ssr-status">SSR is currently <strong><?= $ssrEnabled ? 'enabled' : 'disabled' ?></strong>.</p>
    </section>

    <?= reactiveComponent($pageName, [], $currentRenderer) ?>
  </main>
</body>
</html>
