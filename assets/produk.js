// assets/produk.js
(function(){
  const tbl = qs('#tblProduk tbody');
  const form = qs('#formProduk');
  const ProductID = qs('#ProductID');
  const ProductName = qs('#ProductName');
  const HargaSatuan = qs('#HargaSatuan');
  const btnReset = qs('#btnReset');

  function fillForm(p){
    ProductID.value = p.ProductID;
    ProductName.value = p.ProductName;
    HargaSatuan.value = p.HargaSatuan;
  }
  function resetForm(){
    ProductID.value = '';
    ProductName.value = '';
    HargaSatuan.value = '';
    ProductName.focus();
  }

  async function load(){
    tbl.innerHTML = `<tr><td colspan="4" class="muted">Memuat...</td></tr>`;
    try{
      const data = await api('api/produk.php?action=list');
      const rows = data.rows || [];
      if(rows.length === 0){
        tbl.innerHTML = `<tr><td colspan="4" class="muted">Belum ada produk.</td></tr>`;
        return;
      }
      tbl.innerHTML = rows.map(p => `
        <tr data-id="${p.ProductID}">
          <td>${p.ProductID}</td>
          <td>${p.ProductName}</td>
          <td class="right">${rupiah(p.HargaSatuan)}</td>
          <td class="right">
            <button class="btn danger" data-del="${p.ProductID}">Hapus</button>
          </td>
        </tr>
      `).join('');

      // klik baris untuk edit
      qsa('tr[data-id]', tbl).forEach(tr=>{
        tr.addEventListener('click', (e)=>{
          if(e.target && e.target.matches('[data-del]')) return;
          const id = tr.getAttribute('data-id');
          const p = rows.find(x=> String(x.ProductID)===String(id));
          if(p) fillForm(p);
        });
      });

      // hapus
      qsa('[data-del]', tbl).forEach(btn=>{
        btn.addEventListener('click', async (e)=>{
          e.preventDefault();
          e.stopPropagation();
          const id = btn.getAttribute('data-del');
          if(!confirm('Hapus produk ID ' + id + '?')) return;
          try{
            await api('api/produk.php?action=delete', {
              method: 'POST',
              headers: {'Content-Type':'application/json'},
              body: JSON.stringify({ ProductID: id })
            });
            toast('Produk dihapus.');
            resetForm();
            load();
          }catch(err){
            alert('Gagal hapus: ' + err.message);
          }
        });
      });

    }catch(err){
      tbl.innerHTML = `<tr><td colspan="4" class="muted">Gagal load: ${err.message}</td></tr>`;
    }
  }

  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const payload = {
      ProductID: ProductID.value ? Number(ProductID.value) : null,
      ProductName: ProductName.value.trim(),
      HargaSatuan: Number(HargaSatuan.value || 0)
    };
    if(!payload.ProductName){ alert('Nama produk wajib.'); return; }

    try{
      if(payload.ProductID){
        await api('api/produk.php?action=update', {
          method:'POST',
          headers:{'Content-Type':'application/json'},
          body: JSON.stringify(payload)
        });
        toast('Produk diupdate.');
      }else{
        await api('api/produk.php?action=create', {
          method:'POST',
          headers:{'Content-Type':'application/json'},
          body: JSON.stringify(payload)
        });
        toast('Produk ditambah.');
      }
      resetForm();
      load();
    }catch(err){
      alert('Gagal simpan: ' + err.message);
    }
  });

  btnReset.addEventListener('click', (e)=>{ e.preventDefault(); resetForm(); });

  load();
})();
