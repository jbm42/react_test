<?php
require_once __DIR__ . '/ssr_bootstrap.php';

$pageName = 'test-one';
$title = 'Test Page One — Counter Demo';
$ssr = getSsrPayload($pageName);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <style>
    body {
      font-family: Avenir, Helvetica, Arial, sans-serif;
      margin: 0;
      background-color: #f6f8fa;
      color: #24292f;
    }

    header {
      background-color: #fff;
      border-bottom: 1px solid #d0d7de;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header nav {
      display: flex;
      gap: 1rem;
    }

    header a {
      color: #0969da;
      text-decoration: none;
      font-weight: 600;
    }

    main {
      max-width: 960px;
      margin: 2rem auto;
      padding: 0 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    #reactive {
      background-color: #fff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
      padding: 2rem;
    }
  </style>
<?php foreach ($ssr['css'] as $href): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<?php endforeach; ?>
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
    </section>

    <div id="reactive" data-page="<?= htmlspecialchars($ssr['page'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= $ssr['html'] ?></div>
  </main>

<?php if ($ssr['entry'] !== null): ?>
  <script type="module" src="<?= htmlspecialchars($ssr['entry'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"></script>
<?php else: ?>
  <script>
    console.error('SSR entry not available. Check Node server logs.');
  </script>
<?php endif; ?>
</body>
</html>

