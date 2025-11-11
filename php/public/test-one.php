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
}

$pageName = 'test-one';
$title = 'Test Page One — Counter Demo';
$altStylesheet = isset($_GET['theme']) ? $_GET['theme'] === '1' : false;
$ssrEnabled = !isset($_GET['ssr']) || $_GET['ssr'] !== '0';
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
      <a href="/test-one.php">Test One</a>
      <a href="/test-two.php">Test Two</a>
    </nav>
  </header>

  <main>
    <section>
      <h1><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
      <p>This host template exercises the counter-driven Vue component.</p>
      <p>Server time: <?= date("H:i:s") ?></p>
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
      <p class="ssr-status">SSR is currently <strong><?= $ssrEnabled ? 'enabled' : 'disabled' ?></strong>.</p>
    </section>

    <?= reactiveComponent($pageName) ?>
  </main>
</body>
</html>

