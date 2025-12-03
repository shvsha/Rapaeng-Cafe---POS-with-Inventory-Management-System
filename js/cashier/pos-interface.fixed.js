/* pos-interface.fixed.js - clean, single implementation for POS UI */

document.addEventListener('DOMContentLoaded', () => {
  const $ = s => document.querySelector(s);
  const $$ = s => Array.from(document.querySelectorAll(s));

  const normalize = s => String(s||'').trim().toLowerCase();
  const parsePrice = str => parseFloat(String(str).replace(/[^0-9.-]+/g,'').trim()) || 0;
  const fmt = n => '₱' + Number(n).toFixed(2);

  // category filter
  const categories = $$('.each-category');
  const cards = $$('.menu-card');
  const firstCat = categories[0] ? categories[0].dataset.category : '';
  function setActiveCategory(name){ categories.forEach(c=> c.classList.toggle('active', normalize(c.dataset.category) === normalize(name))); }
  function filterToCategory(name){ const sel = normalize(name||''); setActiveCategory(name||''); cards.forEach(card=>{ const c = normalize(card.dataset.category||''); card.style.display = (!sel || c===sel) ? '' : 'none'; }); }
  filterToCategory(firstCat);
  categories.forEach(c => c.addEventListener('click', ()=> filterToCategory(c.dataset.category || '')));

  // accessible switch
  const switchButtons = $$('.switch-btn');
  const switchIndicator = $('.switch-indicator');
  function setSwitchActive(i){ switchButtons.forEach((b, idx)=>{ const active = idx===i; b.classList.toggle('active', active); b.setAttribute('aria-pressed', active ? 'true' : 'false'); }); if (switchIndicator) switchIndicator.style.transform = `translateX(${i*100}%)`; }
  switchButtons.forEach((b,i)=>{ b.addEventListener('click', ()=> setSwitchActive(i)); b.addEventListener('keydown', e=>{ if (e.key==='ArrowRight') setSwitchActive(Math.min(i+1, switchButtons.length-1)); if (e.key==='ArrowLeft') setSwitchActive(Math.max(i-1,0)); if (e.key==='Enter' || e.key===' '){ e.preventDefault(); setSwitchActive(i);} }); });
  setSwitchActive(Math.max(0, switchButtons.findIndex(b => b.classList.contains('active'))));

  // cart
  const menuButtons = $$('.menu-add');
  const itemsContainer = $('.order-items');
  const emptyPlaceholder = $('.order-list-empty');
  const subtotalEl = $('#subtotal');
  const taxEl = $('#tax');
  const totalEl = $('#total');
  const placeTotalEl = $('#placeTotal');
  const placeOrderBtn = $('#placeOrderBtn');
  const customerInput = $('#customerName');
  const tableSelect = $('#tableSelect');

  const cart = new Map();
  const orderQueue = [];

  function refreshTotals(){ let subtotal=0; cart.forEach(it=> subtotal+= it.price*it.qty); const tax = +(subtotal * 0.10).toFixed(2); const total = +(subtotal + tax).toFixed(2); subtotalEl.innerText = fmt(subtotal); taxEl.innerText = fmt(tax); totalEl.innerText = fmt(total); placeTotalEl.innerText = fmt(total); placeOrderBtn.disabled = subtotal <= 0; }

  function createOrderRow(item){
    const row = document.createElement('div'); row.className='order-item'; row.dataset.menuId = item.id;
    row.innerHTML = `
      <div class="item-thumb"><img src="${item.img || '../../images/menu-pics/no-image.png'}" alt="${item.name}"></div>
      <div class="item-meta"><div class="item-name">${item.name}</div><div class="item-sub">₱${item.price.toFixed(2)} × <span class="sub-qty">${item.qty}</span></div></div>
      <div class="item-right"><div class="item-price">${fmt(item.price * item.qty)}</div><div class="qty-controls"><button class="qty-btn qty-dec">−</button><div class="qty">${item.qty}</div><button class="qty-btn qty-inc">+</button></div></div>`;

    const dec = row.querySelector('.qty-dec'); const inc = row.querySelector('.qty-inc'); const qtyEl = row.querySelector('.qty'); const subQtyEl = row.querySelector('.sub-qty'); const priceEl = row.querySelector('.item-price');
    dec.addEventListener('click', ()=>{ if (item.qty <= 1){ cart.delete(item.id); row.remove(); } else { item.qty -= 1; qtyEl.innerText = item.qty; subQtyEl.innerText = item.qty; priceEl.innerText = fmt(item.price * item.qty); } if (cart.size === 0) emptyPlaceholder.style.display = ''; refreshTotals(); });
    inc.addEventListener('click', ()=>{ item.qty += 1; qtyEl.innerText = item.qty; subQtyEl.innerText = item.qty; priceEl.innerText = fmt(item.price * item.qty); emptyPlaceholder.style.display = 'none'; refreshTotals(); });
    return row;
  }

  function addToCart(menuId,name,price,img){ if (cart.has(menuId)){ const it = cart.get(menuId); it.qty += 1; const el = itemsContainer.querySelector(`.order-item[data-menu-id="${menuId}"]`); if (el){ el.querySelector('.qty').innerText = it.qty; el.querySelector('.sub-qty').innerText = it.qty; el.querySelector('.item-price').innerText = fmt(it.price * it.qty); } } else { const item = { id: menuId, name, price, qty: 1, img }; cart.set(menuId, item); itemsContainer.appendChild(createOrderRow(item)); emptyPlaceholder.style.display = 'none'; } refreshTotals(); }

  menuButtons.forEach(btn => btn.addEventListener('click', ()=>{ const id = String(btn.dataset.menuId || btn.getAttribute('data-menu-id')); const card = btn.closest('.menu-card'); const name = card.querySelector('.menu-name').innerText.trim(); const price = parsePrice(card.querySelector('.menu-price').innerText.trim()); const img = card.querySelector('.menu-image img')?.src || ''; addToCart(id, name, price, img); }));

  placeOrderBtn.addEventListener('click', ()=>{
    if (cart.size === 0) return;
    const order = { id:'ORD-'+Date.now(), customer: customerInput.value || 'Guest', table: tableSelect.value || '', items: Array.from(cart.values()).map(it=>({menu_id: it.id, name: it.name, price: it.price, quantity: it.qty})), subtotal: Number(subtotalEl.innerText.replace(/[^0-9.-]+/g,'')), tax: Number(taxEl.innerText.replace(/[^0-9.-]+/g,'')), total: Number(totalEl.innerText.replace(/[^0-9.-]+/g,'')) };

    placeOrderBtn.disabled = true; const labelEl = placeOrderBtn.querySelector('.label'); const prevLabel = labelEl ? labelEl.innerText : ''; if (labelEl) labelEl.innerText = 'Queued ✓';

    fetch('/POS-Inventory/api/orders/cashier-create.php',{ method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ table: order.table, customer: order.customer, items: order.items, subtotal: order.subtotal, tax: order.tax, total: order.total }) }).then(r=>r.json()).then(data=>{ if (data && data.success){ order.kitchen_id = data.id; order.created_at = data.created_at || new Date().toISOString(); orderQueue.push(order); } else { orderQueue.push(order); } }).catch(e=>{ console.error('kitchen queue error', e); orderQueue.push(order); }).finally(()=>{ setTimeout(()=>{ if (labelEl) labelEl.innerText = prevLabel || 'Place Order'; }, 1000); cart.clear(); itemsContainer.innerHTML = ''; emptyPlaceholder.style.display = ''; refreshTotals(); });
  });

  refreshTotals();

  // profile dropdown behavior (top-right)
  const profileBtn = $('.profile-btn');
  const profileMenu = $('.profile-menu');
  if (profileBtn && profileMenu) {
    function closeProfileMenu() {
      profileBtn.setAttribute('aria-expanded','false');
      profileMenu.classList.remove('open');
      profileMenu.setAttribute('aria-hidden','true');
    }
    function openProfileMenu() {
      profileBtn.setAttribute('aria-expanded','true');
      profileMenu.classList.add('open');
      profileMenu.setAttribute('aria-hidden','false');
    }

    profileBtn.addEventListener('click', (e) => {
      const expanded = profileBtn.getAttribute('aria-expanded') === 'true';
      if (expanded) closeProfileMenu(); else openProfileMenu();
    });

    // click outside closes menu
    document.addEventListener('click', (e) => {
      if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
        if (profileMenu.classList.contains('open')) closeProfileMenu();
      }
    });

    // ESC closes
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeProfileMenu(); });
  }

});
