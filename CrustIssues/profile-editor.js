(() => {
  const css = `
  .pe-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.35);display:flex;align-items:center;justify-content:center;z-index:9999}
  .pe-card{background:#fff;width:720px;max-width:92vw;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,.18);padding:28px;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto}
  .pe-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
  .pe-title{font-weight:800;font-size:24px}
  .pe-close{border:1.5px solid #e11d48;color:#e11d48;background:#fff;padding:8px 12px;border-radius:12px;font-weight:600;cursor:pointer}
  .pe-line{height:2px;background:#e11d48;opacity:.5;margin:12px 0 18px}
  .pe-grid{display:grid;gap:16px;grid-template-columns:1fr 1fr}
  .pe-field{display:flex;flex-direction:column;gap:6px}
  .pe-label{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280}
  .pe-input,.pe-text{border:1.5px solid #e5e7eb;border-radius:12px;padding:12px 14px;font-size:15px;outline:none;transition:.15s}
  .pe-input:focus,.pe-text:focus{border-color:#e11d48;box-shadow:0 0 0 4px rgba(225,29,72,.1)}
  .pe-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}
  .pe-btn{border:none;border-radius:14px;padding:12px 16px;font-weight:700;cursor:pointer}
  .pe-btn.cancel{background:#f3f4f6;color:#374151}
  .pe-btn.save{background:#e11d48;color:#fff}
  .pe-error{color:#b00020;font-weight:700;margin-bottom:8px}
  @media (max-width:720px){.pe-grid{grid-template-columns:1fr}}
  `;
  const style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  const $ = s => document.querySelector(s);
  const getText = id => (($(id)||{}).textContent||'').trim();
  const setText = (id, v) => { const el=$(id); if(el) el.textContent = v || '—'; };

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  function escapeHtml(s){
    return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  }

  function openEditor(){
    const data = {
      full_name: getText('#full_name_value') || getText('#greeting_name').replace(/!$/,''),
      username : getText('#username_value'),
      email    : getText('#email_value'),
      phone    : getText('#phone_value'),
      address  : getText('#address_value') || getText('#card_address')
    };

    const wrap = document.createElement('div');
    wrap.className = 'pe-backdrop';
    wrap.innerHTML = `
      <div class="pe-card" role="dialog" aria-modal="true">
        <div class="pe-header">
          <div class="pe-title">Edit Profile</div>
          <button class="pe-close" type="button">Close</button>
        </div>
        <div class="pe-line"></div>
        <form id="pe-form" novalidate>
          <div id="pe-error" class="pe-error" style="display:none"></div>
          <div class="pe-grid">
            <div class="pe-field">
              <label class="pe-label">Full Name</label>
              <input class="pe-input" name="full_name" value="${escapeHtml(data.full_name)}" required>
            </div>
            <div class="pe-field">
              <label class="pe-label">Username</label>
              <input class="pe-input" name="username" value="${escapeHtml(data.username)}" required>
            </div>
            <div class="pe-field">
              <label class="pe-label">Email Address</label>
              <input class="pe-input" type="email" name="email" value="${escapeHtml(data.email)}" required>
            </div>
            <div class="pe-field">
              <label class="pe-label">Phone</label>
              <input class="pe-input" type="tel" name="phone" value="${escapeHtml(data.phone)}" required>
            </div>
            <div class="pe-field" style="grid-column:1/-1">
              <label class="pe-label">Address</label>
              <textarea class="pe-text" rows="2" name="address" required>${escapeHtml(data.address)}</textarea>
            </div>
          </div>
          <div class="pe-actions">
            <button type="button" class="pe-btn cancel">Cancel</button>
            <button type="submit" class="pe-btn save">Save Changes</button>
          </div>
        </form>
      </div>
    `;
    document.body.appendChild(wrap);

    const close = () => wrap.remove();
    wrap.addEventListener('click', e => { if (e.target === wrap) close(); });
    wrap.querySelector('.pe-close').addEventListener('click', close);
    wrap.querySelector('.cancel').addEventListener('click', close);

    wrap.querySelector('#pe-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const errBox = wrap.querySelector('#pe-error');
      const fd = new FormData(e.currentTarget);
      if (csrf) fd.append('csrf', csrf);

      for (const k of ['full_name','username','email','phone','address']) {
        if (!String(fd.get(k)||'').trim()) {
          errBox.style.display='block'; errBox.textContent = 'Please fill out all fields.'; return;
        }
      }

      try {
        const res = await fetch('crustissuesprofile_edit.php', {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: fd
        });
        const text = await res.text();
        let ok = false;
        try { ok = !!JSON.parse(text).ok; } catch { ok = res.ok; }
        if (!ok) throw new Error('Update failed.');

        setText('#full_name_value', fd.get('full_name'));
        setText('#username_value',  fd.get('username'));
        setText('#email_value',     fd.get('email'));
        setText('#phone_value',     fd.get('phone'));
        setText('#address_value',   fd.get('address'));
        setText('#card_full_name',  fd.get('full_name'));
        setText('#card_address',    fd.get('address'));
        const gn = document.querySelector('#greeting_name');
        if (gn) gn.textContent = `${fd.get('full_name')}!`;

        close();
        toast('Profile updated successfully.');
      } catch (err) {
        errBox.style.display='block';
        errBox.textContent = err.message || 'Something went wrong.';
      }
    });
  }

  function toast(msg){
    const el = document.createElement('div');
    el.textContent = msg;
    Object.assign(el.style, {
      position:'fixed', right:'18px', bottom:'18px',
      background:'#10b981', color:'#fff', padding:'12px 14px',
      borderRadius:'12px', fontWeight:'700', zIndex:'10000',
      boxShadow:'0 10px 30px rgba(0,0,0,.2)'
    });
    document.body.appendChild(el);
    setTimeout(()=>el.remove(),2200);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', (e) => { e.preventDefault(); openEditor(); });
    });
  });
})();
