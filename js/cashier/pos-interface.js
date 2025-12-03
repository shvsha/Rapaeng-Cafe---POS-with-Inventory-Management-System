/*
 full, single copy of pos-interface.js
 (keeps category filter, switch, cart, totals and kitchen queue persistence)
*/

document.addEventListener('DOMContentLoaded', function () {
    const $ = s => document.querySelector(s);
    const $$ = s => Array.from(document.querySelectorAll(s));
    function normalize(s){ return String(s||'').trim().toLowerCase(); }
    function parsePrice(str){ return parseFloat(String(str).replace(/[^0-9.-]+/g,'').trim()) || 0; }
    function fmt(n){ return '₱' + Number(n).toFixed(2); }

    // categories
    const categories = $$('.each-category');
    const cards = $$('.menu-card');
    const firstCat = categories[0] ? categories[0].dataset.category : '';

    function setActiveCategory(cat){ categories.forEach(c => c.classList.toggle('active', normalize(c.dataset.category) === normalize(cat))); }
    function filterToCategory(cat){ const sel = normalize(cat || ''); setActiveCategory(cat||''); cards.forEach(card=>{ const c = normalize(card.dataset.category||''); card.style.display = (!sel||c===sel) ? '' : 'none'; }); }
    filterToCategory(firstCat);
    categories.forEach(c => c.addEventListener('click', ()=> filterToCategory(c.dataset.category || '')));

    // switch
    const switchButtons = $$('.switch-btn');
    const switchIndicator = $('.switch-indicator');
    function setSwitchActive(i){ switchButtons.forEach((b, idx)=>{ const active=idx===i; b.classList.toggle('active', active); b.setAttribute('aria-pressed', active? 'true':'false'); }); if (switchIndicator) switchIndicator.style.transform = `translateX(${i*100}%)`; }
    switchButtons.forEach((b,i)=>{ b.addEventListener('click', ()=>setSwitchActive(i)); b.addEventListener('keydown', e=>{ if (e.key==='ArrowRight') setSwitchActive(Math.min(i+1, switchButtons.length-1)); if (e.key==='ArrowLeft') setSwitchActive(Math.max(i-1,0)); if (e.key==='Enter' || e.key===' '){ e.preventDefault(); setSwitchActive(i);} }); });
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

    function refreshTotals(){ let subtotal = 0; cart.forEach(it=> subtotal += it.price * it.qty); const tax = +(subtotal * 0.10).toFixed(2); const total = +(subtotal + tax).toFixed(2); subtotalEl.innerText = fmt(subtotal); taxEl.innerText = fmt(tax); totalEl.innerText = fmt(total); placeTotalEl.innerText = fmt(total); placeOrderBtn.disabled = subtotal <= 0; }

    function createOrderRow(item){
        const row = document.createElement('div'); row.className='order-item'; row.dataset.menuId = item.id;
        row.innerHTML = `
            <div class="item-thumb"><img src="${item.img || '../../images/menu-pics/no-image.png'}" alt="${item.name}"></div>
            <div class="item-meta"><div class="item-name">${item.name}</div><div class="item-sub">₱${item.price.toFixed(2)} × <span class="sub-qty">${item.qty}</span></div></div>
            <div class="item-right"><div class="item-price">${fmt(item.price * item.qty)}</div><div class="qty-controls"><button class="qty-btn qty-dec">−</button><div class="qty">${item.qty}</div><button class="qty-btn qty-inc">+</button></div></div>`;

        const dec = row.querySelector('.qty-dec'); const inc = row.querySelector('.qty-inc'); const qtyEl = row.querySelector('.qty'); const subQtyEl = row.querySelector('.sub-qty'); const priceEl = row.querySelector('.item-price');

        dec.addEventListener('click', ()=>{ if (item.qty <= 1){ cart.delete(item.id); row.remove(); } else { item.qty -= 1; qtyEl.innerText = item.qty; subQtyEl.innerText = item.qty; priceEl.innerText = fmt(item.price * item.qty); } if (cart.size === 0) emptyPlaceholder.style.display = ''; refreshTotals(); });
        inc.addEventListener('click', ()=>{ item.qty += 1; qtyEl.innerText = item.qty; subQtyEl.innerText = item.qty; priceEl.innerText = fmt(item.price * item.qty); emptyPlaceholder.style.display = 'none'; refreshTotals(); });

        return row; }

    function addToCart(menuId, name, price, img){ if (cart.has(menuId)){ const it = cart.get(menuId); it.qty += 1; const el = itemsContainer.querySelector(`.order-item[data-menu-id="${menuId}"]`); if (el){ el.querySelector('.qty').innerText = it.qty; el.querySelector('.sub-qty').innerText = it.qty; el.querySelector('.item-price').innerText = fmt(it.price * it.qty); } } else { const item = { id: menuId, name, price, qty: 1, img }; cart.set(menuId, item); const row = createOrderRow(item); itemsContainer.appendChild(row); emptyPlaceholder.style.display = 'none'; } refreshTotals(); }

    menuButtons.forEach(btn => btn.addEventListener('click', ()=>{ const id = String(btn.dataset.menuId || btn.getAttribute('data-menu-id')); const card = btn.closest('.menu-card'); const name = card.querySelector('.menu-name').innerText.trim(); const priceStr = card.querySelector('.menu-price').innerText.trim(); const price = parsePrice(priceStr); const img = card.querySelector('.menu-image img')?.src || ''; addToCart(id, name, price, img); }));

    // place order -> persist to kitchen queue and clear UI
    placeOrderBtn.addEventListener('click', ()=>{
        if (cart.size === 0) return;
        const order = { id: 'ORD-'+Date.now(), customer: customerInput.value || 'Guest', table: tableSelect.value || '', type: (switchButtons.find(b=>b.classList.contains('active'))||{innerText:'Dine'}).innerText, items: Array.from(cart.values()).map(it=>({ menu_id: it.id, name: it.name, price: it.price, quantity: it.qty })), subtotal: Number(subtotalEl.innerText.replace(/[^0-9.-]+/g,'')), tax: Number(taxEl.innerText.replace(/[^0-9.-]+/g,'')), total: Number(totalEl.innerText.replace(/[^0-9.-]+/g,'')) };

        placeOrderBtn.disabled = true; const labelEl = placeOrderBtn.querySelector('.label'); const prevLabel = labelEl ? labelEl.innerText : ''; if (labelEl) labelEl.innerText = 'Queued ✓';

        fetch('/POS-Inventory/api/orders/cashier-create.php',{ method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ table: order.table, customer: order.customer, items: order.items, subtotal: order.subtotal, tax: order.tax, total: order.total }) }).then(r=>r.json()).then(data=>{ if (data && data.success){ order.kitchen_id = data.id; order.created_at = data.created_at || new Date().toISOString(); orderQueue.push(order); console.log('Order queued (server):', order); } else { console.warn('Failed to queue order on server, keeping in-memory copy', data); orderQueue.push(order); } }).catch(err=>{ console.error('Failed to POST order to kitchen queue', err); orderQueue.push(order); }).finally(()=>{ setTimeout(()=>{ if (labelEl) labelEl.innerText = prevLabel || 'Place Order'; }, 1000); cart.clear(); itemsContainer.innerHTML=''; emptyPlaceholder.style.display = ''; refreshTotals(); });

    });

    refreshTotals();

});
    // --- helpers ---
    const $ = selector => document.querySelector(selector);
    const $$ = selector => Array.from(document.querySelectorAll(selector));
    function normalize(s) { return String(s || '').trim().toLowerCase(); }
    function parsePrice(str) { return parseFloat(String(str).replace(/[^0-9.-]+/g, '').trim()) || 0; }
    function fmt(n) { return '₱' + Number(n).toFixed(2); }

    // --- category filter ---
    const categories = $$('.each-category');
    const cards = $$('.menu-card');

    function setActiveCategory(catName) {
        categories.forEach(c => c.classList.toggle('active', normalize(c.dataset.category) === normalize(catName)));
    }

    function filterToCategory(cat) {
        const sel = normalize(cat || '');
        setActiveCategory(cat || '');
        cards.forEach(card => {
            const cardCat = normalize(card.dataset.category || '');
            card.style.display = (!sel || cardCat === sel) ? '' : 'none';
        });
    }

    const firstCat = categories[0] ? categories[0].dataset.category : '';
    filterToCategory(firstCat);
    categories.forEach(c => c.addEventListener('click', () => filterToCategory(c.dataset.category || '')));

    // --- accessible switch control ---
    const switchButtons = $$('.switch-btn');
    const switchIndicator = $('.switch-indicator');
    function setSwitchActive(i) {
        switchButtons.forEach((b, idx) => {
            const active = idx === i;
            b.classList.toggle('active', active);
            b.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        if (switchIndicator) switchIndicator.style.transform = `translateX(${i * 100}%)`;
    }
    switchButtons.forEach((b, i) => {
        b.addEventListener('click', () => setSwitchActive(i));
        b.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') setSwitchActive(Math.min(i + 1, switchButtons.length - 1));
            if (e.key === 'ArrowLeft') setSwitchActive(Math.max(i - 1, 0));
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setSwitchActive(i); }
        });

    });
    setSwitchActive(Math.max(0, switchButtons.findIndex(b => b.classList.contains('active'))));

    // --- cart / order handling ---
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

    function refreshTotals() {
        let subtotal = 0;
        cart.forEach(it => subtotal += it.price * it.qty);
        const tax = +(subtotal * 0.10).toFixed(2);
        const total = +(subtotal + tax).toFixed(2);
        subtotalEl.innerText = fmt(subtotal);
        taxEl.innerText = fmt(tax);
        totalEl.innerText = fmt(total);
        placeTotalEl.innerText = fmt(total);
        placeOrderBtn.disabled = subtotal <= 0;
    }

    function createOrderRow(item) {
        const row = document.createElement('div');
        row.className = 'order-item';
        row.dataset.menuId = item.id;

        row.innerHTML = `
            <div class="item-thumb"><img src="${item.img || '../../images/menu-pics/no-image.png'}" alt="${item.name}"></div>
            <div class="item-meta">
                <div class="item-name">${item.name}</div>
                <div class="item-sub">₱${item.price.toFixed(2)} × <span class="sub-qty">${item.qty}</span></div>
            </div>
            <div class="item-right">
                <div class="item-price">${fmt(item.price * item.qty)}</div>
                <div class="qty-controls">
                    <button class="qty-btn qty-dec">−</button>
                    <div class="qty">${item.qty}</div>
                    <button class="qty-btn qty-inc">+</button>
                </div>
            </div>
        `;

        const dec = row.querySelector('.qty-dec');
        const inc = row.querySelector('.qty-inc');
        const qtyEl = row.querySelector('.qty');
        const subQtyEl = row.querySelector('.sub-qty');
        const priceEl = row.querySelector('.item-price');

        dec.addEventListener('click', () => {
            if (item.qty <= 1) {
                cart.delete(item.id);
                row.remove();
            } else {
                item.qty -= 1;
                qtyEl.innerText = item.qty;
                subQtyEl.innerText = item.qty;
                priceEl.innerText = fmt(item.price * item.qty);
            }
            if (cart.size === 0) emptyPlaceholder.style.display = '';
            refreshTotals();
        });

        inc.addEventListener('click', () => {
            item.qty += 1;
            qtyEl.innerText = item.qty;
            subQtyEl.innerText = item.qty;
            priceEl.innerText = fmt(item.price * item.qty);
            emptyPlaceholder.style.display = 'none';
            refreshTotals();
        });

        return row;
    }

    function addToCart(menuId, name, price, img) {
        if (cart.has(menuId)) {
            const it = cart.get(menuId);
            it.qty += 1;
            const el = itemsContainer.querySelector(`.order-item[data-menu-id="${menuId}"]`);
            if (el) {
                el.querySelector('.qty').innerText = it.qty;
                el.querySelector('.sub-qty').innerText = it.qty;
                el.querySelector('.item-price').innerText = fmt(it.price * it.qty);
            }
        } else {
            const item = { id: menuId, name, price, qty: 1, img };
            cart.set(menuId, item);
            const row = createOrderRow(item);
            itemsContainer.appendChild(row);
            emptyPlaceholder.style.display = 'none';
        }
        refreshTotals();
    }

    menuButtons.forEach(btn => btn.addEventListener('click', () => {
        const id = String(btn.dataset.menuId || btn.getAttribute('data-menu-id'));
        const card = btn.closest('.menu-card');
        const name = card.querySelector('.menu-name').innerText.trim();
        const priceStr = card.querySelector('.menu-price').innerText.trim();
        const price = parsePrice(priceStr);
        const img = card.querySelector('.menu-image img')?.src || '';
        addToCart(id, name, price, img);
    }));

    // place order -> persist to kitchen queue and clear UI
    placeOrderBtn.addEventListener('click', () => {
        if (cart.size === 0) return;

        const order = {
            id: 'ORD-' + Date.now(),
            customer: customerInput.value || 'Guest',
            table: tableSelect.value || '',
            type: (switchButtons.find(b => b.classList.contains('active')) || { innerText: 'Dine' }).innerText,
            items: Array.from(cart.values()).map(it => ({ menu_id: it.id, name: it.name, price: it.price, quantity: it.qty })),
            subtotal: Number(subtotalEl.innerText.replace(/[^0-9.-]+/g, '')),
            tax: Number(taxEl.innerText.replace(/[^0-9.-]+/g, '')),
            total: Number(totalEl.innerText.replace(/[^0-9.-]+/g, '')),
        };

        placeOrderBtn.disabled = true;
        const labelEl = placeOrderBtn.querySelector('.label');
        const prevLabel = labelEl ? labelEl.innerText : '';
        if (labelEl) labelEl.innerText = 'Queued ✓';

        fetch('/POS-Inventory/api/orders/cashier-create.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ table: order.table, customer: order.customer, items: order.items, subtotal: order.subtotal, tax: order.tax, total: order.total })
        }).then(r => r.json()).then(data => {
            if (data && data.success) {
                order.kitchen_id = data.id;
                order.created_at = data.created_at || new Date().toISOString();
                orderQueue.push(order);
                console.log('Order queued (server):', order);
            } else {
                console.warn('Failed to queue order on server, keeping in-memory copy', data);
                orderQueue.push(order);
            }
        }).catch(err => {
            console.error('Failed to POST order to kitchen queue', err);
            orderQueue.push(order);
        }).finally(() => {
            setTimeout(() => { if (labelEl) labelEl.innerText = prevLabel || 'Place Order'; }, 1000);
            cart.clear();
            itemsContainer.innerHTML = '';
            emptyPlaceholder.style.display = '';
            refreshTotals();
        });
    });

    // final totals init
    refreshTotals();

});
    // categories filter
    const categories = Array.from(document.querySelectorAll('.each-category'));
    const cards = Array.from(document.querySelectorAll('.menu-card'));

    function normalize(s){ return String(s||'').trim().toLowerCase(); }

    function setActiveCategory(catName){
        categories.forEach(c => c.classList.toggle('active', normalize(c.dataset.category) === normalize(catName)));
    }

    const firstCat = categories[0] ? categories[0].dataset.category : null;
    function filterToCategory(cat) {
        const sel = normalize(cat || '');
        setActiveCategory(cat || '');
        cards.forEach(card => {
            const cardCat = normalize(card.dataset.category || '');
            card.style.display = (!sel || cardCat === sel) ? '' : 'none';
        });
    }
    filterToCategory(firstCat || '');
    categories.forEach(c => c.addEventListener('click', () => filterToCategory(c.dataset.category || '')));

    // switch (accessible)
    const buttons = Array.from(document.querySelectorAll('.switch-btn')) || [];
    const indicator = document.querySelector('.switch-indicator');
    function setActive(i){
        buttons.forEach((b, idx)=>{
            const active = idx===i;
            b.classList.toggle('active', active);
            b.setAttribute('aria-pressed', active?'true':'false');
        });
        if (indicator) indicator.style.transform = `translateX(${i*100}%)`;
    }
    buttons.forEach((b, i)=>{
        b.addEventListener('click', ()=> setActive(i));
        b.addEventListener('keydown', e=>{ if (e.key==='ArrowRight') setActive(Math.min(i+1, buttons.length-1)); if (e.key==='ArrowLeft') setActive(Math.max(i-1,0)); if (e.key==='Enter' || e.key===' ') { e.preventDefault(); setActive(i); } });
    });
    const initial = buttons.findIndex(b => b.classList.contains('active'));
    setActive(initial>=0?initial:0);

    // order handling
    const menuButtons = Array.from(document.querySelectorAll('.menu-add'));
    const itemsContainer = document.querySelector('.order-items');
    const emptyPlaceholder = document.querySelector('.order-list-empty');
    const subtotalEl = document.getElementById('subtotal');
    const taxEl = document.getElementById('tax');
    const totalEl = document.getElementById('total');
    const placeTotalEl = document.getElementById('placeTotal');
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    const customerInput = document.getElementById('customerName');
    const tableSelect = document.getElementById('tableSelect');

    const switchBtns = Array.from(document.querySelectorAll('.switch-btn'));
    const cart = new Map();
    const orderQueue = [];

    function parsePrice(str){ return parseFloat(String(str).replace(/[^0-9.-]+/g,'').trim()) || 0; }
    function fmt(n){ return '₱' + Number(n).toFixed(2); }

    function refreshTotals(){
        let subtotal = 0;
        cart.forEach(it => subtotal += it.price * it.qty);
        const tax = +(subtotal * 0.10).toFixed(2);
        const total = +(subtotal + tax).toFixed(2);
        subtotalEl.innerText = fmt(subtotal);
        taxEl.innerText = fmt(tax);
        totalEl.innerText = fmt(total);
        placeTotalEl.innerText = fmt(total);
        placeOrderBtn.disabled = subtotal <= 0;
    }

    function createOrderRow(item){
        const row = document.createElement('div');
        row.className = 'order-item';
        row.dataset.menuId = item.id;

        row.innerHTML = `
            <div class="item-thumb"><img src="${item.img || '../../images/menu-pics/no-image.png'}" alt="${item.name}"></div>
            <div class="item-meta">
                <div class="item-name">${item.name}</div>
                <div class="item-sub">₱${item.price.toFixed(2)} × <span class="sub-qty">${item.qty}</span></div>
            </div>
            <div class="item-right">
                <div class="item-price">${fmt(item.price * item.qty)}</div>
                <div class="qty-controls">
                    <button class="qty-btn qty-dec">−</button>
                    <div class="qty">${item.qty}</div>
                    <button class="qty-btn qty-inc">+</button>
                </div>
            </div>
        `;

        const dec = row.querySelector('.qty-dec');
        const inc = row.querySelector('.qty-inc');
        const qtyEl = row.querySelector('.qty');
        const subQtyEl = row.querySelector('.sub-qty');
        const priceEl = row.querySelector('.item-price');

        dec.addEventListener('click', () => {
            if (item.qty <= 1) {
                cart.delete(item.id);
                row.remove();
            } else {
                item.qty -= 1;
                qtyEl.innerText = item.qty;
                subQtyEl.innerText = item.qty;
                priceEl.innerText = fmt(item.price * item.qty);
            }
            if (cart.size === 0) emptyPlaceholder.style.display = '';
            refreshTotals();
        });

        inc.addEventListener('click', () => {
            item.qty += 1;
            qtyEl.innerText = item.qty;
            subQtyEl.innerText = item.qty;
            priceEl.innerText = fmt(item.price * item.qty);
            emptyPlaceholder.style.display = 'none';
            refreshTotals();
        });

        return row;
    }

    function addToCart(menuId, name, price, img){
        if (cart.has(menuId)){
            const it = cart.get(menuId);
            it.qty += 1;
            const el = itemsContainer.querySelector(`.order-item[data-menu-id="${menuId}"]`);
            if (el){
                el.querySelector('.qty').innerText = it.qty;
                el.querySelector('.sub-qty').innerText = it.qty;
                el.querySelector('.item-price').innerText = fmt(it.price * it.qty);
            }
        } else {
            const item = { id: menuId, name, price, qty: 1, img };
            cart.set(menuId, item);
            const row = createOrderRow(item);
            itemsContainer.appendChild(row);
            emptyPlaceholder.style.display = 'none';
        }
        refreshTotals();
    }

    menuButtons.forEach(btn => btn.addEventListener('click', (e) => {
        const id = String(btn.dataset.menuId || btn.getAttribute('data-menu-id'));
        const card = btn.closest('.menu-card');
        const name = card.querySelector('.menu-name').innerText.trim();
        const priceStr = card.querySelector('.menu-price').innerText.trim();
        const price = parsePrice(priceStr);
        const img = card.querySelector('.menu-image img')?.src || '';

        addToCart(id, name, price, img);
    }));

    // place order -> persist to kitchen queue and clear UI
    placeOrderBtn.addEventListener('click', () => {
        if (cart.size === 0) return;

        const order = {
            id: 'ORD-' + Date.now(),
            customer: customerInput.value || 'Guest',
            table: tableSelect.value || '',
            type: (switchBtns.find(b=>b.classList.contains('active'))||{innerText:'Dine'}).innerText,
            items: Array.from(cart.values()).map(it=>({ menu_id: it.id, name: it.name, price: it.price, quantity: it.qty })),
            subtotal: Number(subtotalEl.innerText.replace(/[^0-9.-]+/g,'')),
            tax: Number(taxEl.innerText.replace(/[^0-9.-]+/g,'')),
            total: Number(totalEl.innerText.replace(/[^0-9.-]+/g,'')),
        };

        placeOrderBtn.disabled = true;
        const labelEl = placeOrderBtn.querySelector('.label');
        const prevLabel = labelEl ? labelEl.innerText : '';
        if (labelEl) labelEl.innerText = 'Queued ✓';

        // persist order to the kitchen queue (server side). Keep fallback in-memory.
        fetch('/POS-Inventory/api/orders/cashier-create.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ table: order.table, customer: order.customer, items: order.items, subtotal: order.subtotal, tax: order.tax, total: order.total })
        }).then(r => r.json()).then(data => {
            if (data && data.success) {
                order.kitchen_id = data.id;
                order.created_at = data.created_at || new Date().toISOString();
                orderQueue.push(order);
                console.log('Order queued (server):', order);
            } else {
                console.warn('Failed to queue order on server, keeping in-memory copy', data);
                orderQueue.push(order);
            }
        }).catch(err => {
            console.error('Failed to POST order to kitchen queue', err);
            orderQueue.push(order);
        }).finally(() => {
            setTimeout(() => { if (labelEl) labelEl.innerText = prevLabel || 'Place Order'; }, 1000);
            cart.clear();
            itemsContainer.innerHTML = '';
            emptyPlaceholder.style.display = '';
            refreshTotals();
        });
    });

    refreshTotals();
});
});
(function(){
// Wait for DOM
document.addEventListener('DOMContentLoaded', function(){
    const categories = Array.from(document.querySelectorAll('.each-category'));
    const cards = Array.from(document.querySelectorAll('.menu-card'));

    function normalize(s){ return String(s||'').trim().toLowerCase(); }

    function setActiveCategory(catName){
        categories.forEach(c => c.classList.toggle('active', normalize(c.dataset.category) === normalize(catName)));
    }

    // default select the first category in the list (if any) and apply filter
    const firstCat = categories[0] ? categories[0].dataset.category : null;

    function filterToCategory(cat) {
        const sel = normalize(cat || '');
        setActiveCategory(cat || '');
        cards.forEach(card => {
            const cardCat = normalize(card.dataset.category || '');
            if (!sel || cardCat === sel) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    filterToCategory(firstCat || '');

    categories.forEach(c => {
        c.addEventListener('click', function(){
            const cat = c.dataset.category || 'All';
            filterToCategory(cat);
        });
    });
});
})();

// Switch / order type control - accessible behaviour and keyboard support
document.addEventListener('DOMContentLoaded', function(){
const buttons = Array.from(document.querySelectorAll('.switch-btn'));
const indicator = document.querySelector('.switch-indicator');

function setActive(index) {
    buttons.forEach((b, i) => {
        const active = i === index;
        b.classList.toggle('active', active);
        b.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
    if (indicator) indicator.style.transform = `translateX(${index * 100}%)`;
}

// click / keyboard handlers
buttons.forEach((btn, index) => {
    btn.addEventListener('click', () => setActive(index));
    btn.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') setActive(Math.min(index + 1, buttons.length - 1));
        if (e.key === 'ArrowLeft') setActive(Math.max(index - 1, 0));
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setActive(index); }
    });
});

// position indicator according to currently active button (fallback to 0)
const initial = buttons.findIndex(b => b.classList.contains('active'));
setActive(initial >= 0 ? initial : 0);
});

// Order handling: add items, update quantity, compute totals, place order queue
document.addEventListener('DOMContentLoaded', () => {
    const menuButtons = Array.from(document.querySelectorAll('.menu-add'));
    const itemsContainer = document.querySelector('.order-items');
    const emptyPlaceholder = document.querySelector('.order-list-empty');
    const subtotalEl = document.getElementById('subtotal');
    const taxEl = document.getElementById('tax');
    const totalEl = document.getElementById('total');
    const placeTotalEl = document.getElementById('placeTotal');
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    const customerInput = document.getElementById('customerName');
    const tableSelect = document.getElementById('tableSelect');
    const switchBtns = Array.from(document.querySelectorAll('.switch-btn'));

    const cart = new Map();
    const orderQueue = [];

    function parsePrice(str){ return parseFloat(String(str).replace(/[^0-9.-]+/g,'').trim()) || 0; }
    function fmt(n){ return '₱' + Number(n).toFixed(2); }

    function refreshTotals(){
        let subtotal = 0;
        cart.forEach(it => subtotal += it.price * it.qty);
        const tax = +(subtotal * 0.10).toFixed(2); // 10% tax
        const total = +(subtotal + tax).toFixed(2);
        subtotalEl.innerText = fmt(subtotal);
        taxEl.innerText = fmt(tax);
        totalEl.innerText = fmt(total);
        placeTotalEl.innerText = fmt(total);
        placeOrderBtn.disabled = subtotal <= 0;
    }

    function createOrderRow(item){
        const row = document.createElement('div');
        // attempt to persist order for kitchen queue
        row.className = 'order-item';
        row.dataset.menuId = item.id;

        row.innerHTML = `
            <div class="item-thumb"><img src="${item.img || '../../images/menu-pics/no-image.png'}" alt="${item.name}"></div>
            <div class="item-meta">
                <div class="item-name">${item.name}</div>
                <div class="item-sub">₱${item.price.toFixed(2)} × <span class="sub-qty">${item.qty}</span></div>
            </div>
            <div class="item-right">
                <div class="item-price">${fmt(item.price * item.qty)}</div>
                <div class="qty-controls">
                    <button class="qty-btn qty-dec">−</button>
                    <div class="qty">${item.qty}</div>
                    <button class="qty-btn qty-inc">+</button>
                </div>
            </div>
        `;

        // attach handlers
        const dec = row.querySelector('.qty-dec');
        const inc = row.querySelector('.qty-inc');
        const qtyEl = row.querySelector('.qty');
        const subQtyEl = row.querySelector('.sub-qty');
        const priceEl = row.querySelector('.item-price');

        dec.addEventListener('click', () => {
            if (item.qty <= 1) {
                cart.delete(item.id);
                row.remove();
            } else {
                item.qty -= 1;
                qtyEl.innerText = item.qty;
                subQtyEl.innerText = item.qty;
                priceEl.innerText = fmt(item.price * item.qty);
            }
        // this code is now handled in the fetch promise chain above
        return row;
    }

    function addToCart(menuId, name, price, img){
        if (cart.has(menuId)){
            const it = cart.get(menuId);
            it.qty += 1;
            const el = itemsContainer.querySelector(`.order-item[data-menu-id="${menuId}"]`);
            if (el){
                el.querySelector('.qty').innerText = it.qty;
                el.querySelector('.sub-qty').innerText = it.qty;
                el.querySelector('.item-price').innerText = fmt(it.price * it.qty);
            }
        } else {
            const item = { id: menuId, name, price, qty: 1, img };
            cart.set(menuId, item);
            const row = createOrderRow(item);
            itemsContainer.appendChild(row);
            emptyPlaceholder.style.display = 'none';
        }
        refreshTotals();
    }

    // hook menu add buttons
    menuButtons.forEach(btn => btn.addEventListener('click', (e) => {
        const id = String(btn.dataset.menuId || btn.getAttribute('data-menu-id'));
        const card = btn.closest('.menu-card');
        const name = card.querySelector('.menu-name').innerText.trim();
        const priceStr = card.querySelector('.menu-price').innerText.trim();
        const price = parsePrice(priceStr);
        const img = card.querySelector('.menu-image img')?.src || '';

        addToCart(id, name, price, img);
    }));

    // place order handler
    placeOrderBtn.addEventListener('click', () => {
        if (cart.size === 0) return;
        const order = {
            id: 'ORD-' + Date.now(),
            customer: customerInput.value || 'Guest',
            table: tableSelect.value || '',
            type: (switchBtns.find(b=>b.classList.contains('active'))||{innerText:'Dine'}).innerText,
            items: Array.from(cart.values()).map(it=>({ id: it.id, name: it.name, price: it.price, qty: it.qty })),
            subtotal: Number(subtotalEl.innerText.replace(/[^0-9.-]+/g,'')),
            tax: Number(taxEl.innerText.replace(/[^0-9.-]+/g,'')),
            total: Number(totalEl.innerText.replace(/[^0-9.-]+/g,'')),
        };

        orderQueue.push(order);

        console.log('Order queued:', order);

        placeOrderBtn.disabled = true;
        const labelEl = placeOrderBtn.querySelector('.label');
        const prevLabel = labelEl ? labelEl.innerText : '';
        if (labelEl) labelEl.innerText = 'Queued ✓';

        setTimeout(() => {
            if (labelEl) labelEl.innerText = prevLabel || 'Place Order';
        }, 1000);

        cart.clear();
        itemsContainer.innerHTML = '';
        emptyPlaceholder.style.display = '';
        refreshTotals();
    });

    refreshTotals();

});