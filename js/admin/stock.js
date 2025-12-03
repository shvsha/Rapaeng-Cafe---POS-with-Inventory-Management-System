let stockCache = [];

// small notification helper (local) - falls back if global version not present
function showNotification(type, message, duration=3000){
    try{
        // if global exists use it
        if (typeof window.showNotificationGlobal === 'function') return window.showNotificationGlobal(type, message, duration);
    } catch(e){}
    let el = document.querySelector('.notification');
    if (!el) { el = document.createElement('div'); el.className = 'notification hidden'; el.setAttribute('role','status'); document.body.appendChild(el); }
    el.className = `notification ${type}`;
    el.innerHTML = `<div class="notif-icon">${type==='success'?'✔':'✖'}</div><div class="notif-body">${message}</div>`;
    el.classList.remove('hidden');
    clearTimeout(el._hideTimeout); el._hideTimeout = setTimeout(()=> el.classList.add('hidden'), duration);
}

// small debounce helper
function debounce(fn, wait=220){ let t=null; return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); }; }

const getMenus = () => {
    $.ajax({
        url: '../../api/stocks/get-stocks.php',
        type: 'GET',
        success: (res) => {
            console.log(res);
                if (res.status) {
                    stockCache = res.data || [];
                    if (!stockCache.length) {
                            $('#stock-data-body').html(`<tr><td style="text-align:center;" colspan="5">No stocks found</td></tr>`);
                    } else {
                        // if order page present, render cards, otherwise render table UI
                        if (document.getElementById('orderGrid')) renderOrderCards(stockCache);
                        else renderUI(stockCache);
                    }
                } else {
                        $('#stock-data-body').html(`<tr><td style="text-align:center;" colspan="5">No stocks found</td></tr>`);
                }
        },
        error: () => {
            $('#menu-data-body').html(`<tr><td colspan="5">Error loading data</td></tr>`);
        }
    });
};

const renderUI = (stocks) => {
    const container = $('#stock-data-body');
    container.empty();

    stocks.forEach(stock => {
        // image field in DB may be 'image' or 'stock_images' depending on table
        const imgStr = (stock.image || stock.stock_images || stock.stock_image || '').toString().trim();
        let imgSrc = '';
        if (imgStr) {
            // already a full URL
            if (/^https?:\/\//i.test(imgStr)) imgSrc = imgStr;
            // if contains 'images/' assume relative path and ensure leading ../../
            else if (/^images\//i.test(imgStr) || imgStr.includes('/')) imgSrc = '../../' + imgStr.replace(/^\.\/?/, '');
            // otherwise it's a filename stored in DB, build path into images/stocks-images
            else imgSrc = '../../images/stocks-images/' + imgStr;
        }

        const imgTag = imgSrc ? `<img src="${imgSrc}" alt="${(stock.item_name||'image')}" style="width:106px;height:106px;border-radius:6px;object-fit:cover;">` : '<div style="width:56px;height:56px;border-radius:6px;background:#f1eee8;display:flex;align-items:center;justify-content:center;color:#7a4e2d">—</div>';

        const row = `
        <tr>
            <td>${stock.item_name}</td>
            <td>${imgTag}</td>
            <td>${Number(stock.total_stocks || 0)}</td>
            <td>${Number(stock.stock_avai || 0)}</td>
            <td>₱${Number(stock.item_price || stock.price || stock.unit_price || 0).toFixed(2)}</td>
        </tr>`;
        container.append(row);
    });

    // attach handlers after render (if present)
    if (typeof attachRowHandlers === 'function') attachRowHandlers();
};

// render order cards (used on order-stock.php)
function renderOrderCards(stocks){
    const grid = document.getElementById('orderGrid');
    if (!grid) return;
    grid.innerHTML = '';
    stocks.forEach(stock => {
        const imgStr = (stock.image || stock.stock_images || stock.stock_image || '').toString().trim();
        let imgSrc = '';
        if (imgStr) {
            if (/^https?:\/\//i.test(imgStr)) imgSrc = imgStr;
            else if (/^images\//i.test(imgStr) || imgStr.includes('/')) imgSrc = '../../' + imgStr.replace(/^\.\/?/, '');
            else imgSrc = '../../images/stocks-images/' + imgStr;
        }
        const thumb = imgSrc ? `<img src="${imgSrc}" alt="${(stock.item_name||'image')}" style="max-height:120px;object-fit:contain;">` : '<div style="width:100%;height:120px;border-radius:8px;background:#f1eee8;display:flex;align-items:center;justify-content:center;color:#7a4e2d">No image</div>';

        const card = document.createElement('div'); card.className = 'stock-card';
        card.dataset.id = stock.stock_id;
        card.innerHTML = `
            <div class="stock-image">${thumb}</div>
            <div class="stock-meta">
                <div class="stock-name">${stock.item_name}</div>
                <div class="stock-stats">Available: <strong>${Number(stock.stock_avai||0)}</strong></div>
            </div>
            <div class="stock-action">
                <input type="number" min="1" value="1" class="order-qty" style="width:84px;padding:8px;border-radius:6px;border:1px solid #ddd;" />
                <div style="min-width:110px;padding:8px;border-radius:6px;border:1px solid #f0f0f0;background:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;color:#7a4e2d">₱${Number(stock.item_price || stock.price || stock.unit_price || stock.cost || stock.default_price || 0).toFixed(2)}</div>
            </div>
            <div class="stock-action">
                <button class="btn-add-order" data-id="${stock.stock_id}">Add to Order</button>
            </div>
        `;
        grid.appendChild(card);
    });

    // attach add handlers
    grid.querySelectorAll('.btn-add-order').forEach(btn => btn.addEventListener('click', function(e){
        const id = this.dataset.id;
        const card = this.closest('.stock-card');
        const qty = Math.max(1, Number(card.querySelector('.order-qty').value || 1));
        // derive price from the stock object (preferred) — fall back to 0 if not provided
        const item = stockCache.find(s => Number(s.stock_id) === Number(id));
        const priceVal = Number(item && (item.item_price || item.price || item.unit_price || item.cost || item.default_price) ? (item.item_price || item.price || item.unit_price || item.cost || item.default_price) : 0);
        addToCart(Number(id), qty, priceVal);
    }));
}

// cart state
const orderCart = new Map();

function addToCart(stockId, qty = 1, price = 0){
    // find stock
    const item = stockCache.find(s => Number(s.stock_id) === Number(stockId));
    if (!item) { console.warn('stock not found', stockId); return; }
    const key = String(stockId);
    if (orderCart.has(key)){
        const cur = orderCart.get(key); cur.qty = Number(cur.qty) + Number(qty); cur.price = Number(price || cur.price); orderCart.set(key, cur);
    } else {
        // prefer the stock's own price if available (unit price stored on item), otherwise use supplied price
        const stockPrice = Number(item.item_price ?? item.price ?? item.unit_price ?? item.cost ?? item.default_price ?? 0);
        orderCart.set(key, { stock_id: Number(stockId), name: item.item_name, qty: Number(qty), price: Number(price || stockPrice || 0), image: item.image || item.stock_images || '' });
    }
    updateCartUI();
    showNotification && showNotification('success', 'Added to order');
}

function updateCartUI(){
    const container = document.getElementById('cartItems');
    if (!container) return;
    container.innerHTML = '';
    if (orderCart.size === 0){
        container.innerHTML = '<div class="cart-empty">No items added</div>';
        // ensure totals are cleared when cart is empty
        const subtotalEl = document.getElementById('cartSubtotal'); if (subtotalEl) subtotalEl.innerText = '₱0.00';
        const taxEl = document.getElementById('cartTax'); if (taxEl) taxEl.innerText = '₱0.00';
        const totalEl = document.getElementById('cartTotal'); if (totalEl) totalEl.innerText = '₱0.00';
        const placeBtn = document.getElementById('placeOrderBtn'); if (placeBtn) { placeBtn.disabled = true; if (placeBtn.innerText !== 'Place Order') placeBtn.innerText = 'Place Order'; }
        return;
    }
    let subtotal = 0;
    orderCart.forEach((it, k) => {
        const imgStr = (it.image || '').toString().trim();
        let imgSrc = '';
        if (imgStr) {
            if (/^https?:\/\//i.test(imgStr)) imgSrc = imgStr;
            else if (/^images\//i.test(imgStr) || imgStr.includes('/')) imgSrc = '../../' + imgStr.replace(/^\.\/?/, '');
            else imgSrc = '../../images/stocks-images/' + imgStr;
        }
        const thumb = imgSrc ? `<img src="${imgSrc}" class="c-thumb" />` : `<div class="c-thumb" style="background:#f1eee8;display:flex;align-items:center;justify-content:center;color:#7a4e2d">—</div>`;
        const itemTotal = Number(it.qty) * Number(it.price || 0); subtotal += itemTotal;
        const div = document.createElement('div'); div.className = 'cart-item';
        div.innerHTML = `<div class="c-left">${thumb}<div><div style="font-weight:700">${escapeHtml(it.name)}</div><div style="font-size:12px;color:#666">₱${Number(it.price||0).toFixed(2)} each</div></div></div><div class="c-right"><div class="qty-controls"><button class="qty-btn" data-op="dec" data-id="${k}">−</button><span style="min-width:28px;display:inline-block;text-align:center">${it.qty}</span><button class="qty-btn" data-op="inc" data-id="${k}">+</button></div><div style="margin-top:6px;font-weight:700;">₱${itemTotal.toFixed(2)}</div><div style="margin-top:6px;text-align:right"><button data-remove="${k}" class="qty-btn" style="background:#da4d4d;color:#fff;border-radius:6px">Remove</button></div></div>`;
        container.appendChild(div);
    });
    const tax = +(subtotal * 0.10).toFixed(2); const total = +(subtotal + tax).toFixed(2);
    document.getElementById('cartSubtotal').innerText = '₱' + subtotal.toFixed(2);
    document.getElementById('cartTax').innerText = '₱' + tax.toFixed(2);
    document.getElementById('cartTotal').innerText = '₱' + total.toFixed(2);
    document.getElementById('placeOrderBtn').disabled = orderCart.size === 0;

    // attach qty handlers
    container.querySelectorAll('.qty-btn').forEach(b=> b.addEventListener('click', (e)=>{
        const id = b.dataset.id; const op = b.dataset.op;
        if (b.dataset.remove){ orderCart.delete(b.dataset.remove); updateCartUI(); return; }
        if (!orderCart.has(id)) return;
        const cur = orderCart.get(id);
        if (op==='inc') cur.qty = Number(cur.qty) + 1;
        if (op==='dec') cur.qty = Math.max(0, Number(cur.qty) - 1);
        if (cur.qty <=0) orderCart.delete(id); else orderCart.set(id, cur);
        updateCartUI();
    }));
}

function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, function(ch){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[ch]; }); }

// cancel/clear cart
document.addEventListener('click', function(e){ if (e.target && e.target.id === 'cancelCartBtn'){ orderCart.clear(); updateCartUI(); } });

// place order handler
document.addEventListener('click', async function(e){ if (!e.target) return; if (e.target.id === 'placeOrderBtn'){
    if (orderCart.size === 0) return showNotification && showNotification('error','Cart is empty');
    const items = Array.from(orderCart.values()).map(it=>({ stock_id: it.stock_id, quantity: Number(it.qty), unit_price: Number(it.price || 0) }));
    const subtotal = Number(Array.from(orderCart.values()).reduce((s,it)=> s + (it.qty * (it.price||0)), 0).toFixed(2));
    const tax = +(subtotal * 0.10).toFixed(2); const total = +(subtotal + tax).toFixed(2);
    // send to server
    e.target.disabled = true; e.target.innerText = 'Placing...';
    try{
        const res = await fetch('/POS-Inventory/api/stocks/create-order.php',{ method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ items, subtotal, tax, total }) });
        const j = await res.json();
            if (j && j.success) {
                // show modal with success message
                try { openOrderPlacedModal(j.order_id, j.message || ''); } catch(e) {}
                orderCart.clear(); updateCartUI();
            } else showNotification && showNotification('error','Failed: '+(j.message||'Unknown'));
    } catch(err){ console.error(err); showNotification && showNotification('error','Network error'); }
    e.target.disabled = false; e.target.innerText = 'Place Order';
 }});

// when on order-stock page, we should render cards instead of table rows
function initOrderPage() {
    const grid = document.getElementById('orderGrid');
    if (!grid) return;
    // ensure we have stockCache loaded - if not, fetch
    if (stockCache && stockCache.length) renderOrderCards(stockCache);
    // wire low-stock filter
    const low = document.getElementById('filterLow');
    if (low) low.addEventListener('change', (e)=>{
        const isOn = e.target.checked;
        const items = isOn ? stockCache.filter(s => Number(s.stock_avai||0) <= 5) : stockCache;
        renderOrderCards(items);
    });
}

// init order page when script loads
try { initOrderPage(); } catch(e) { /* ignore if not on that page */ }

getMenus();

// add live search for stocks by item_name
try {
    const searchEl = document.querySelector('.search-menu');
            if (searchEl) {
        const runSearch = (val) => {
            const q = (val || '').toString().trim().toLowerCase();
            // if on order page render cards, else render table rows
            if (!q) return document.getElementById('orderGrid') ? renderOrderCards(stockCache) : renderUI(stockCache);
            const filtered = stockCache.filter(s => (s.item_name || '').toString().toLowerCase().includes(q));
            if (document.getElementById('orderGrid')) return renderOrderCards(filtered);
            // show filtered inline
            $('#stock-data-body').empty();
            filtered.forEach(stock => {
                let imgStr = (stock.image || stock.stock_images || stock.stock_image || '').toString().trim();
                let imgSrc = '';
                if (imgStr) {
                    if (/^https?:\/\//i.test(imgStr)) imgSrc = imgStr;
                    else if (/^images\//i.test(imgStr) || imgStr.includes('/')) imgSrc = '../../' + imgStr.replace(/^\.\/?/, '');
                    else imgSrc = '../../images/stocks-images/' + imgStr;
                }
                const imgTag = imgSrc ? `<img src="${imgSrc}" alt="${(stock.item_name||'image')}" style="width:56px;height:56px;border-radius:6px;object-fit:cover;">` : '<div style="width:56px;height:56px;border-radius:6px;background:#f1eee8;display:flex;align-items:center;justify-content:center;color:#7a4e2d">—</div>';
                const row = `<tr><td>${stock.item_name}</td><td>${imgTag}</td><td>${Number(stock.total_stocks||0)}</td><td>${Number(stock.stock_avai||0)}</td><td>₱${Number(stock.item_price || stock.price || stock.unit_price || 0).toFixed(2)}</td></tr>`;
                $('#stock-data-body').append(row);
            });
            if (!filtered.length) {
                if (document.getElementById('orderGrid')) document.getElementById('orderGrid').innerHTML = '<div class="empty-state">No stocks found</div>';
                else $('#stock-data-body').html(`<tr><td style="text-align:center;" colspan="5">No stocks found</td></tr>`);
            }
        }
        searchEl.addEventListener('input', debounce((e)=> runSearch(e.target.value), 140));
    }
} catch (e) { console.error('stock search init error', e); }


// roder new stock

// modal helpers for placed order
function openOrderPlacedModal(orderId, message){
    const modal = document.getElementById('orderPlacedModal');
    if (!modal) return;
    modal.querySelector('.modal-msg').innerText = message ? message : 'Your stock order was successfully submitted to the system.';
    modal.classList.remove('hide');
    modal.classList.add('visible');
}

function closeOrderPlacedModal(){
    const modal = document.getElementById('orderPlacedModal'); if (!modal) return; modal.classList.add('hide'); modal.classList.remove('visible');
}

// hook modal buttons
document.addEventListener('click', function(e){
    if (!e.target) return;
    if (e.target.id === 'modalCloseBtn' || e.target.id === 'modalOkayBtn') { closeOrderPlacedModal(); }
    if (e.target.id === 'viewOrderBtn') {
        // try to navigate to stock orders screen — fallback to closing the modal
        const url = '/POS-Inventory/pages/admin/stock-orders.php';
        // if page exists redirect, else simply close
        fetch(url, { method: 'HEAD' }).then(r => { if (r.ok) window.location.href = url; else closeOrderPlacedModal(); }).catch(()=> closeOrderPlacedModal());
    }
});

