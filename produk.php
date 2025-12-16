<?php
$pageTitle = "Produk - Struk UI";
require_once __DIR__ . "/inc/layout_top.php";
?>
<section class="card">
  <div class="card__header">
    <h1>Master Produk</h1>
    <p class="muted">Tambah / ubah / hapus produk.</p>
  </div>

  <div class="grid2">
    <div>
      <form class="form" id="formProduk">
        <input type="hidden" id="ProductID" value="" />
        <div class="field">
          <label>Nama Produk</label>
          <input type="text" id="ProductName" placeholder="Nama produk..." required />
        </div>
        <div class="field">
          <label>Harga Satuan</label>
          <input type="number" id="HargaSatuan" min="0" step="1" placeholder="0" required />
        </div>

        <div class="row" style="margin-top: 12px;">
          <button class="btn primary" type="submit" id="btnSave">Simpan</button>
          <button class="btn" type="button" id="btnReset">Reset</button>
        </div>
        <p class="muted small">Klik baris produk untuk edit.</p>
      </form>
    </div>

    <div>
      <div class="table-wrap">
        <table class="table" id="tblProduk">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th class="right">Harga</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="4" class="muted">Memuat...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script src="assets/produk.js"></script>
<?php require_once __DIR__ . "/inc/layout_bottom.php"; ?>
