// assets/transaksi-baru.js
(function(){
  const selKasir = qs('#KasirID');
  const selProduct = qs('#ProductID');
  const inpQty = qs('#Qty');
  const inpPotongan = qs('#Potongan');
  const inpTunai = qs('#Tunai');

  const btnAdd = qs('#btnAdd');
  const btnSave = qs('#btnSaveTransaksi');

  const cartBody = qs('#tblCart tbody');
  const sumTotal = qs('#sumTotal');
  const sumPotongan = qs('#sumPotongan');
  const sumBayar = qs('#sumBayar');
  const sumTunai = qs('#sumTunai');
  const sumKembali = qs('#sumKembali');
  const saveResult = qs('#saveResult');

  let products = [];
  let cart = []; // {ProductID, ProductName, HargaSatuan, Qty}

  function calc(){
    const total = cart.reduce((acc,it)=> acc + (it.HargaSatuan * it.Qty), 0);
    const pot = Number(inpPotongan.value || 0);
    const bayar = Math.max(total - pot, 0);
    const tunai = Number(inpTunai.value || 0);
    const kembali = tunai - bayar;

    sumTotal.textContent = rupiah(total);
    sumPotongan.textContent = rupiah(pot);
    sumBayar.textContent = rupiah(bayar);
    sumTunai.textContent = rupiah(tunai);
    sumKembali.textContent = rupiah(kembali);

    return { total, pot, bayar, tunai, kembali };
  }

  function render(){
    if(cart.length === 0){
      cartBody.innerHTML = `<tr><td colspan="5" class="muted">Belum ada item.</td></tr>`;
      calc();
      return;
    }

    cartBody.innerHTML = cart.map((it, idx)=>`
      <tr>
        <td>${it.ProductName}</td>
        <td class="right">${rupiah(it.HargaSatuan)}</td>
        <td class="right">
          <div class="qtyctrl" aria-label="Ubah jumlah">
            <button class="btn sm" data-dec="${idx}" title="Kurangi">−</button>
            <span class="qtynum" title="Qty">${it.Qty}</span>
            <button class="btn sm" data-inc="${idx}" title="Tambah">+</button>
          </div>
        </td>
        <td class="right">${rupiah(it.HargaSatuan * it.Qty)}</td>
        <td class="right">
          <button class="btn danger" data-rm="${idx}">Hapus</button>
        </td>
      </tr>
    `).join('');

    qsa('[data-rm]', cartBody).forEach(btn=>{
      btn.addEventListener('click', (e)=>{
        e.preventDefault();
        const idx = Number(btn.getAttribute('data-rm'));
        cart.splice(idx,1);
        render();
      });
    });

    // tombol +/- qty
    qsa('[data-inc]', cartBody).forEach(btn=>{
      btn.addEventListener('click', (e)=>{
        e.preventDefault();
        const idx = Number(btn.getAttribute('data-inc'));
        if(!Number.isFinite(idx) || !cart[idx]) return;
        cart[idx].Qty = Math.max(Number(cart[idx].Qty || 0) + 1, 1);
        render();
      });
    });

    qsa('[data-dec]', cartBody).forEach(btn=>{
      btn.addEventListener('click', (e)=>{
        e.preventDefault();
        const idx = Number(btn.getAttribute('data-dec'));
        if(!Number.isFinite(idx) || !cart[idx]) return;
        const next = Math.max(Number(cart[idx].Qty || 0) - 1, 0);
        if(next <= 0){
          cart.splice(idx,1);
        }else{
          cart[idx].Qty = next;
        }
        render();
      });
    });

    calc();
  }

  async function init(){
    // load kasir + produk
    const [k, p] = await Promise.all([
      api('api/kasir.php?action=list'),
      api('api/produk.php?action=list')
    ]);
    const kasirs = k.rows || [];
    products = p.rows || [];

    selKasir.innerHTML = kasirs.map(x=>`<option value="${x.KasirID}">${x.KasirID} - ${x.KasirNama}</option>`).join('');
    selProduct.innerHTML = products.map(x=>`<option value="${x.ProductID}">${x.ProductName} (Rp ${rupiah(x.HargaSatuan)})</option>`).join('');

    render();
  }

  btnAdd.addEventListener('click', (e)=>{
    e.preventDefault();
    const pid = Number(selProduct.value);
    const qty = Math.max(Number(inpQty.value || 1), 1);
    const p = products.find(x=> Number(x.ProductID) === pid);
    if(!p){ alert('Produk tidak ditemukan.'); return; }

    // jika sudah ada di cart, tambah qty
    const existing = cart.find(x=> Number(x.ProductID) === pid);
    if(existing){
      existing.Qty += qty;
    }else{
      cart.push({ ProductID: pid, ProductName: p.ProductName, HargaSatuan: Number(p.HargaSatuan), Qty: qty });
    }
    inpQty.value = 1;
    render();
  });

  inpPotongan.addEventListener('input', ()=>calc());
  inpTunai.addEventListener('input', ()=>calc());

  btnSave.addEventListener('click', async (e)=>{
    e.preventDefault();
    saveResult.textContent = '';
    if(cart.length === 0){
      alert('Keranjang masih kosong.');
      return;
    }
    const kasirId = selKasir.value;
    const pot = Number(inpPotongan.value || 0);
    const tunai = Number(inpTunai.value || 0);

    const payload = {
      KasirID: kasirId,
      Potongan: pot,
      Tunai: tunai,
      items: cart.map(it=>({ ProductID: it.ProductID, Qty: it.Qty }))
    };

    try{
      const res = await api('api/transaksi.php?action=create', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });

      toast('Transaksi tersimpan: ' + res.TransID);
      saveResult.innerHTML = `Transaksi tersimpan: <a class="btn" href="transaksi_detail.php?transid=${encodeURIComponent(res.TransID)}">Buka Struk</a>`;
      // reset
      cart = [];
      inpPotongan.value = 0;
      inpTunai.value = 0;
      render();
    }catch(err){
      alert('Gagal simpan transaksi: ' + err.message);
    }
  });

  init().catch(err=>{
    alert('Gagal init: ' + err.message);
  });
})();
