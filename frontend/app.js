/* CandyCove — app.js */

// ── API Config ──────────────────────────────────
const API_BASE = 'https://candycove.up.railway.app/api/';

function getToken()     { return localStorage.getItem('cc_token'); }
function setToken(t)    { localStorage.setItem('cc_token', t); }
function removeToken()  { localStorage.removeItem('cc_token'); }
function getUser()      { return JSON.parse(localStorage.getItem('cc_user') || 'null'); }
function setUser(u)     { localStorage.setItem('cc_user', JSON.stringify(u)); }

async function apiFetch(path, opts = {}) {
  const headers = { 'Content-Type': 'application/json', ...(opts.headers || {}) };
  const token = getToken();
  if (token) headers['Authorization'] = `Bearer ${token}`;
  const res = await fetch(`${API_BASE}${path}`, { ...opts, headers });
  const data = await res.json().catch(() => ({}));
  return { ok: res.ok, status: res.status, data };
}

// ── Toast ───────────────────────────────────────
function showToast(msg, type = 'success') {
  const icons = { success: '✅', error: '❌', warning: '⚠️', info: '💡' };
  let el = document.getElementById('globalToast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'globalToast';
    el.className = 'toast';
    el.innerHTML = `<span class="toast-icon"></span><span class="toast-msg"></span>`;
    document.body.appendChild(el);
  }
  el.querySelector('.toast-icon').textContent = icons[type] || '💬';
  el.querySelector('.toast-msg').textContent  = msg;
  el.className = `toast ${type}`;
  requestAnimationFrame(() => { el.classList.add('show'); });
  setTimeout(() => el.classList.remove('show'), 3500);
}

// ── Cart Badge — uses API, updates all badge elements ──
async function updateCartBadge() {
  if (!getToken()) {
    document.querySelectorAll('#cartCount, #cartBadge').forEach(el => {
      el.textContent = '0';
      el.style.display = 'none';
    });
    return;
  }
  const res = await apiFetch('/cart');
  if (res.ok) {
    const count = res.data.product_details?.length || 0;
    document.querySelectorAll('#cartCount, #cartBadge').forEach(el => {
      el.textContent = count;
      el.style.display = count ? 'flex' : 'none';
    });
  }
}

// ── Add to Cart ─────────────────────────────────
async function addToCart(productId, qty = 1) {
  if (!getToken()) {
    showToast('Please sign in to add items to cart 🔐', 'warning');
    setTimeout(() => { window.location.href = 'login.html'; }, 1500);
    return;
  }
  const res = await apiFetch('/cart', {
    method: 'POST',
    body: JSON.stringify({ product_id: productId, quantity: qty })
  });
  if (res.ok) {
    showToast('Added to cart! 🛒', 'success');
    await updateCartBadge();
  } else {
    showToast(res.data.message || 'Failed to add to cart', 'error');
  }
}

// ── Auth Guards ─────────────────────────────────
function requireAuth() {
  if (!getToken()) { window.location.href = 'login.html'; return false; }
  return true;
}
function requireGuest() {
  if (getToken()) { window.location.href = 'index.html'; }
}

// ── Logout ──────────────────────────────────────
async function logout() {
  await apiFetch('/logout', { method: 'POST' });
  removeToken();
  localStorage.removeItem('cc_user');
  showToast('Logged out. See you soon! 👋');
  setTimeout(() => { window.location.href = 'index.html'; }, 1000);
}

// ── Product Card Renderer ────────────────────────
function productCardHTML(p) {
  const imgUrl = p.images?.find(i => i.is_primary === 1)?.url || p.images?.[0]?.url || '';
  const imgHtml = imgUrl
    ? `<img src="${imgUrl}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-lg);">`
    : `<div style="display:flex;align-items:center;justify-content:center;font-size:40px;height:100%;background:linear-gradient(135deg,var(--magenta-pale),var(--lavender-pale));border-radius:var(--radius-lg);">🍬</div>`;

  return `
    <div class="product-card">
      <a href="product.html?id=${p.id}">
        <div class="product-card-img" style="width:100%;aspect-ratio:1;overflow:hidden;">
          ${imgHtml}
        </div>
      </a>
      <div class="product-card-body">
        <div class="product-card-cat">${p.category?.name || ''}</div>
        <a href="product.html?id=${p.id}">
          <div class="product-card-name">${p.name}</div>
        </a>
        <div class="product-card-footer">
          <span class="product-price">$${parseFloat(p.price).toFixed(2)}</span>
          <button class="add-to-cart-btn" onclick="event.stopPropagation(); addToCart(${p.id})">+</button>
        </div>
      </div>
    </div>`;
}

function renderProducts(containerId, products) {
  const el = document.getElementById(containerId);
  if (!el) return;
  if (!products || products.length === 0) {
    el.innerHTML = `<div class="empty-state" style="grid-column:1/-1;text-align:center;">
      <div style="font-size:64px;margin-bottom:16px;">🍬</div>
      <h3>No sweets found!</h3>
      <p>Try adjusting your filters or browse all categories.</p>
    </div>`;
    return;
  }
  el.innerHTML = products.map(productCardHTML).join('');
}

// ── Modal Helpers ────────────────────────────────
function openModal(id)  { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }

document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('open');
  }
});

// ── Navbar auth state ────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  // Hamburger
  const btn  = document.getElementById('hamburger');
  const menu = document.getElementById('mobileMenu');
  if (btn && menu) {
    btn.addEventListener('click', () => menu.classList.toggle('open'));
  }

  // Update nav based on login state
  const user = getUser();
  const navActions = document.querySelector('.nav-actions');
  const isAdmin = user?.role === 'admin';
  const isGuest = !user;

  // Hide "My Orders" link for admins and guests
  document.querySelectorAll('.nav-link').forEach(link => {
    if (link.getAttribute('href') === 'orders.html') {
      link.style.display = (isAdmin || isGuest) ? 'none' : '';
    }
  });

  if (user && navActions) {
    navActions.innerHTML = `
      ${!isAdmin ? `
      <a href="cart.html" class="nav-icon-btn" title="Cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        <span class="cart-badge" id="cartBadge">0</span>
      </a>` : ''}
      <a href="${isAdmin ? 'orders.html' : 'orders.html'}" class="btn-nav-user" id="navAuthBtn">
        Hi, ${user.name?.split(' ')[0]} 👋
      </a>
      ${isAdmin ? `<a href="admin.html" class="btn btn-primary btn-sm" style="font-size:13px;display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Admin Dashboard
      </a>` : ''}
      <button class="btn btn-ghost btn-sm" style="font-size:13px;" onclick="logout()">Logout</button>
    `;
  }

  updateCartBadge();
});

// ── Custom Cursor ────────────────────────────────
document.addEventListener('mousemove', e => {
  const dot   = document.getElementById('cursorDot');
  const outer = document.getElementById('cursorOuter');
  if (!dot || !outer) return;
  dot.style.left   = e.clientX + 'px';
  dot.style.top    = e.clientY + 'px';
  outer.style.left = e.clientX + 'px';
  outer.style.top  = e.clientY + 'px';
});

document.querySelectorAll('button, a').forEach(el => {
  el.addEventListener('mouseenter', () => {
    const outer = document.getElementById('cursorOuter');
    if (outer) outer.style.transform = 'translate(-50%, -50%) scale(1.5)';
  });
  el.addEventListener('mouseleave', () => {
    const outer = document.getElementById('cursorOuter');
    if (outer) outer.style.transform = 'translate(-50%, -50%) scale(1)';
  });
});