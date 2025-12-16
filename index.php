<?php
$pageTitle = "Dashboard - Struk UI";
require_once __DIR__ . "/inc/layout_top.php";
?>
<section class="card">
  <div class="card__header">
    <h1>Daftar Transaksi</h1>
    <p class="muted">Cari transaksi dan buka halaman struk.</p>
  </div>

  <div class="toolbar">
    <div class="field">
      <label for="q">Cari TransID</label>
      <input id="q" type="text" placeholder="contoh: 004-..." />
    </div>
    <div class="field">
      <label for="dateFrom">Dari</label>
      <input id="dateFrom" type="date" />
    </div>
    <div class="field">
      <label for="dateTo">Sampai</label>
      <input id="dateTo" type="date" />
    </div>

    <div class="toolbar__actions">
      <button class="btn" id="btnRefresh">Refresh</button>
    </div>
  </div>

  <div class="table-wrap">
    <table class="table" id="tblTransaksi">
      <thead>
        <tr>
          <th>TransID</th>
          <th>Tanggal</th>
          <th>Kasir</th>
          <th class="right">Total</th>
          <th class="right">Potongan</th>
          <th class="right">Tunai</th>
          <th class="right">Kembali</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="8" class="muted">Memuat...</td></tr>
      </tbody>
    </table>
  </div>
</section>

<script src="assets/dashboard.js"></script>
<?php require_once __DIR__ . "/inc/layout_bottom.php"; ?>
