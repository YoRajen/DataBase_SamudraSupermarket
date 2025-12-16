// assets/dashboard.js
(function(){
  const tbl = qs('#tblTransaksi tbody');
  const q = qs('#q');
  const dateFrom = qs('#dateFrom');
  const dateTo = qs('#dateTo');
  const btnRefresh = qs('#btnRefresh');

  async function load(){
    tbl.innerHTML = `<tr><td colspan="8" class="muted">Memuat...</td></tr>`;
    const params = new URLSearchParams();
    params.set('action','list');
    if(q.value.trim()) params.set('q', q.value.trim());
    if(dateFrom.value) params.set('from', dateFrom.value);
    if(dateTo.value) params.set('to', dateTo.value);

    try{
      const data = await api('api/transaksi.php?' + params.toString());
      const rows = data.rows || [];
      if(rows.length === 0){
        tbl.innerHTML = `<tr><td colspan="8" class="muted">Tidak ada data.</td></tr>`;
        return;
      }
      tbl.innerHTML = rows.map(r => `
        <tr>
          <td><code>${r.TransID}</code></td>
          <td>${r.Tanggal}</td>
          <td>${r.KasirNama ?? '-'}</td>
          <td class="right">${rupiah(r.Total)}</td>
          <td class="right">${rupiah(r.Potongan)}</td>
          <td class="right">${rupiah(r.Tunai)}</td>
          <td class="right">${rupiah(r.Kembali)}</td>
          <td>
            <a class="btn" href="transaksi_detail.php?transid=${encodeURIComponent(r.TransID)}">Lihat Struk</a>
          </td>
        </tr>
      `).join('');
    }catch(err){
      tbl.innerHTML = `<tr><td colspan="8" class="muted">Gagal load: ${err.message}</td></tr>`;
    }
  }

  btnRefresh.addEventListener('click', load);
  q.addEventListener('keydown', (e)=>{ if(e.key==='Enter'){ e.preventDefault(); load(); }});

  load();
})();
