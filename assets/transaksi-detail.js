// assets/transaksi-detail.js
(function(){
  const transid = window.__TRANSID__ || '';
  const meta = qs('#meta');
  const itemsBody = qs('#items tbody');
  const summary = qs('#summary');
  const btnPrint = qs('#btnPrint');

  btnPrint.addEventListener('click', ()=> window.print());

  function line(label, value, bold=false){
    return `<div class="row"><div>${bold ? `<b>${label}</b>` : label}</div><div>${bold ? `<b>${value}</b>` : value}</div></div>`;
  }

  async function load(){
    if(!transid){
      meta.innerHTML = `<div class="muted">TransID tidak ada.</div>`;
      return;
    }
    const params = new URLSearchParams({ action:'get', transid });
    const data = await api('api/transaksi.php?' + params.toString());

    const t = data.transaksi;
    const rows = data.items || [];

    meta.innerHTML = `
      <div>TransID: <code>${t.TransID}</code></div>
      <div>Tanggal: ${t.Tanggal}</div>
      <div>Kasir: ${t.KasirID} - ${t.KasirNama}</div>
    `;

    if(rows.length === 0){
      itemsBody.innerHTML = `<tr><td colspan="3" class="muted">Tidak ada item.</td></tr>`;
    }else{
      itemsBody.innerHTML = rows.map(it=>`
        <tr>
          <td>${it.ProductName}</td>
          <td class="right">${it.Qty}</td>
          <td class="right">${rupiah(it.Subtotal)}</td>
        </tr>
      `).join('');
    }

    const total = Number(t.Total || 0);
    const potongan = Number(t.Potongan || 0);
    const dpp = Math.max(total - potongan, 0);
    const ppn = Number(data.ppn || 0); // dari function HitungPPN jika ada
    const bayarTanpaPPN = dpp;
    const bayarJikaPPN = dpp + ppn;

    summary.innerHTML = [
      line('Total', 'Rp ' + rupiah(total)),
      line('Potongan', 'Rp ' + rupiah(potongan)),
      line('DPP (Total - Potongan)', 'Rp ' + rupiah(dpp)),
      line('PPN 11% (HitungPPN)', 'Rp ' + rupiah(ppn)),
      `<div class="receipt__line"></div>`,
      line('Bayar (tanpa PPN)', 'Rp ' + rupiah(bayarTanpaPPN), true),
      line('Bayar (jika +PPN)', 'Rp ' + rupiah(bayarJikaPPN)),
      `<div class="receipt__line"></div>`,
      line('Tunai', 'Rp ' + rupiah(t.Tunai)),
      line('Kembali', 'Rp ' + rupiah(t.Kembali), true),
    ].join('');
  }

  load().catch(err=>{
    meta.innerHTML = `<div class="muted">Gagal load: ${err.message}</div>`;
  });
})();
