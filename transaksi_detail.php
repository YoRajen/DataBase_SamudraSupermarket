<?php
$pageTitle = "Detail Transaksi - Struk UI";
require_once __DIR__ . "/inc/layout_top.php";
$transid = $_GET['transid'] ?? '';
?>
<section class="card">
  <div class="card__header">
    <h1>Struk</h1>
    <p class="muted">TransID: <span id="transidLabel"><?= htmlspecialchars($transid) ?></span></p>
  </div>

  <div class="row" style="justify-content: flex-end; margin-bottom: 10px">
    <button class="btn" id="btnPrint">Print</button>
    <a class="btn" href="index.php">Kembali</a>
  </div>

  <div class="receipt" id="receipt">
    <div class="receipt__title">TOKO CONTOH</div>
    <div class="receipt__meta" id="meta"></div>

    <div class="receipt__line"></div>

    <table class="receipt__table" id="items">
      <thead>
        <tr>
          <th>Item</th>
          <th class="right">Qty</th>
          <th class="right">Subtotal</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <div class="receipt__line"></div>

    <div class="receipt__summary" id="summary"></div>
    <div class="receipt__line"></div>

    <div class="receipt__footer">Terima kasih 😊</div>
  </div>

  <p class="muted small">PPN 11% akan dihitung menggunakan function DB <code>HitungPPN</code> bila tersedia.</p>
</section>

<script>
  window.__TRANSID__ = <?= json_encode($transid) ?>;
</script>
<script src="assets/transaksi-detail.js"></script>

<?php require_once __DIR__ . "/inc/layout_bottom.php"; ?>
