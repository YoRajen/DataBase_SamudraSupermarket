<?php
$pageTitle = "Transaksi Baru - Struk UI";
require_once __DIR__ . "/inc/layout_top.php";
?>
<section class="card">
  <div class="card__header">
    <h1>Buat Transaksi Baru</h1>
    <p class="muted">Pilih kasir, tambahkan item, masukkan tunai.</p>
  </div>

  <div class="grid2">
    <div>
      <div class="form">
        <div class="field">
          <label>Kasir</label>
          <select id="KasirID"></select>
        </div>

        <div class="field">
          <label>Potongan</label>
          <input type="number" id="Potongan" min="0" step="1" value="0" />
        </div>

        <div class="field">
          <label>Tunai</label>
          <input type="number" id="Tunai" min="0" step="1" value="0" />
        </div>

        <hr class="sep" />

        <h3>Tambah Item</h3>
        <div class="row">
          <div class="field" style="flex: 1">
            <label>Produk</label>
            <select id="ProductID"></select>
          </div>
          <div class="field" style="width: 120px">
            <label>Qty</label>
            <input type="number" id="Qty" min="1" step="1" value="1" />
          </div>
          <div class="field" style="width: 140px; align-self: flex-end">
            <button class="btn" id="btnAdd">Tambah</button>
          </div>
        </div>
      </div>

      <div class="alert muted small">
        <b>Catatan:</b> Tunai tidak boleh kurang dari harga yang harus dibayar! Kembalian tidak boleh <0
      </div>
    </div>

    <div>
      <h3>Keranjang</h3>
      <div class="table-wrap">
        <table class="table" id="tblCart">
          <thead>
            <tr>
              <th>Produk</th>
              <th class="right">Harga</th>
              <th class="right">Qty</th>
              <th class="right">Subtotal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="5" class="muted">Belum ada item.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="right"><b>Total</b></td>
              <td class="right"><b id="sumTotal">0</b></td>
              <td></td>
            </tr>
            <tr>
              <td colspan="3" class="right">Potongan</td>
              <td class="right" id="sumPotongan">0</td>
              <td></td>
            </tr>
            <tr>
              <td colspan="3" class="right"><b>Bayar</b></td>
              <td class="right"><b id="sumBayar">0</b></td>
              <td></td>
            </tr>
            <tr>
              <td colspan="3" class="right">Tunai</td>
              <td class="right" id="sumTunai">0</td>
              <td></td>
            </tr>
            <tr>
              <td colspan="3" class="right"><b>Kembali</b></td>
              <td class="right"><b id="sumKembali">0</b></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="row" style="margin-top: 12px">
        <button class="btn primary" id="btnSaveTransaksi">Simpan Transaksi</button>
        <a class="btn" href="index.php">Batal</a>
      </div>

      <p class="muted small" id="saveResult"></p>
    </div>
  </div>
</section>

<script src="assets/transaksi-baru.js"></script>
<?php require_once __DIR__ . "/inc/layout_bottom.php"; ?>
