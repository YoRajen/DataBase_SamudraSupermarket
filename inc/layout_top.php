<?php
// inc/layout_top.php
if (!isset($pageTitle)) $pageTitle = "Samudra Supermarket";
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="assets/styles.css" />

  <script src="assets/app.js"></script>
</head>
<body>
  <header class="topbar">
    <div class="container topbar__inner">
      <div class="brand">
        <div class="brand__logo">🧾</div>
        <div class="brand__text">
          <div class="brand__title" style="font-size: 25px;">Samudra Supermarket</div>
        </div>
      </div>

      <nav class="nav">
        <a class="nav__link" href="index.php">Dashboard</a>
        <a class="nav__link" href="transaksi_baru.php">Transaksi Baru</a>
        <a class="nav__link" href="produk.php">Produk</a>
      </nav>
    </div>
  </header>

  <main class="container">
