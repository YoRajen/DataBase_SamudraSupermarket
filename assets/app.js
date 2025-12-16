// assets/app.js
// Util kecil untuk UI.

function rupiah(n){
  const num = Number(n || 0);
  return num.toLocaleString('id-ID');
}

function qs(sel, root=document){ return root.querySelector(sel); }
function qsa(sel, root=document){ return Array.from(root.querySelectorAll(sel)); }

function toast(msg){
  // toast sederhana (tanpa library)
  const el = document.createElement('div');
  el.className = 'toast';
  el.textContent = msg;
  Object.assign(el.style, {
    position:'fixed', right:'16px', bottom:'16px',
    background:'rgba(18,26,50,.96)', color:'#e9edff',
    border:'1px solid rgba(255,255,255,.14)',
    padding:'10px 12px', borderRadius:'12px',
    boxShadow:'0 14px 34px rgba(0,0,0,.35)',
    zIndex: 9999, maxWidth:'min(420px, 92vw)'
  });
  document.body.appendChild(el);
  setTimeout(()=>{ el.style.opacity='0'; el.style.transition='opacity .25s'; }, 1800);
  setTimeout(()=>{ el.remove(); }, 2200);
}

async function api(url, options={}){
  const res = await fetch(url, options);
  let data = null;
  try { data = await res.json(); } catch(e){}
  if(!res.ok){
    const msg = (data && data.error) ? data.error : `HTTP ${res.status}`;
    throw new Error(msg);
  }
  return data;
}
