document.addEventListener('DOMContentLoaded', function(){
    const ordersRegion = document.querySelector('.orders-container');

    // helper to format time using simple local formatting
    function formatTime(iso){
        try { const d = new Date(iso); return d.toLocaleTimeString([], {hour:'numeric', minute:'2-digit'}); } catch(e){ return iso; }
    }

    // timers for auto-remove when ready (id => timeout)
    const removeTimers = new Map();

    function renderOrders(list){
        // clear existing dynamic entries but keep wrapper
        // remove everything inside ordersRegion except first (if you want to preserve static). We'll empty and re-render.
        ordersRegion.innerHTML = '';

        if (!list || list.length === 0) {
            ordersRegion.innerHTML = '<div class="no-orders">No orders in queue</div>';
            return;
        }

        list.forEach(order => {
            const card = document.createElement('div');
            card.className = 'orders-card';

            const statusText = order.status === '1' ? 'New Order' : (order.status === '2' ? 'Preparing' : (order.status === '3' ? 'Ready' : (order.status === '4' ? 'On Hold' : 'Unknown')));

            // top
            const top = document.createElement('div');
            top.className = 'top-container';
            top.innerHTML = `<div style="display:flex;justify-content:space-between;width:100%"><h2>Table <span>${order.table_name || order.table || 'TBD'}</span></h2><div class="order-status status-${order.status}">${statusText}</div></div>`;

            const receipt = document.createElement('div');
            receipt.className = 'receipt-time-container';
            receipt.innerHTML = `<p>${order.order_ref || '#'+order.id}</p><p style="color: gray;"><span>${formatTime(order.created_at)}</span></p>`;

            // items
            const menuWrap = document.createElement('div');
            menuWrap.className = 'menu-container';
            order.items.forEach(it => {
                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                row.style.padding = '8px 0';
                row.innerHTML = `<p class="menu-name">${escapeHtml(it.name||it.menu_name||'Item')}</p><p class="menu-qty">x${it.quantity||it.qty||1}</p>`;
                menuWrap.appendChild(row);
            });

            // buttons
            const btns = document.createElement('div');
            btns.className = 'cook-ready-hold-btns';

            const cookBtn = document.createElement('button');
            cookBtn.className = 'cook-hold-btn';
            cookBtn.innerText = order.status === '2' ? 'Mark Ready' : 'Start Cooking';

            const holdBtn = document.createElement('button');
            holdBtn.className = 'hold-btn';
            holdBtn.innerText = order.status === '4' ? 'Resume' : 'Hold';

            // Remove button (only shown for Ready status)
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerText = 'Remove';
            removeBtn.style.display = order.status === '3' ? '' : 'none';

            btns.appendChild(cookBtn);
            btns.appendChild(holdBtn);
            btns.appendChild(removeBtn);

            card.appendChild(top);
            card.appendChild(receipt);
            card.appendChild(menuWrap);
            card.appendChild(btns);

            // attach events
            cookBtn.addEventListener('click', () => {
                // if new(1) or hold(4) => preparing(2) ; if preparing(2) => ready(3)
                let newStatus = '2';
                if (order.status === '2') newStatus = '3';
                updateOrderStatus(order.id, newStatus).then(ok => {
                    if (ok) {
                        order.status = newStatus;
                        cookBtn.innerText = newStatus === '2' ? 'Mark Ready' : 'Start Cooking';
                        // update badge
                        const badge = card.querySelector('.order-status');
                        if (badge) {
                            badge.innerText = newStatus === '2' ? 'Preparing' : 'Ready';
                            badge.className = 'order-status status-' + newStatus;
                        }
                        // show remove button when ready
                        if (newStatus === '3') {
                            removeBtn.style.display = '';
                            // auto remove after 30s (configurable)
                            if (removeTimers.has(order.id)) clearTimeout(removeTimers.get(order.id));
                            const t = setTimeout(() => {
                                deleteOrder(order.id).then(ok => { if (ok) card.remove(); });
                            }, 30 * 1000);
                            removeTimers.set(order.id, t);
                        } else {
                            removeBtn.style.display = 'none';
                            if (removeTimers.has(order.id)) { clearTimeout(removeTimers.get(order.id)); removeTimers.delete(order.id); }
                        }
                    }
                });
            });

            // hold button toggles hold/resume
            holdBtn.addEventListener('click', () => {
                // if currently on hold -> resume to preparing
                const target = order.status === '4' ? '2' : '4';
                updateOrderStatus(order.id, target).then(ok => {
                    if (!ok) return;
                    order.status = target;
                    const badge = card.querySelector('.order-status');
                    if (badge) {
                        badge.innerText = target === '4' ? 'On Hold' : 'Preparing';
                        badge.className = 'order-status status-' + target;
                    }
                    holdBtn.innerText = target === '4' ? 'Resume' : 'Hold';
                    // cookBtn label should reflect that we can start cooking (from hold/resume)
                    cookBtn.innerText = target === '2' ? 'Mark Ready' : 'Start Cooking';
                });
            });

            // remove button for ready orders
            removeBtn.addEventListener('click', () => {
                deleteOrder(order.id).then(ok => { if (ok) card.remove(); });
            });

            ordersRegion.appendChild(card);
        });
    }

    function escapeHtml(s){ return String(s||'').replace(/[&<>\"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c])); }

    async function updateOrderStatus(id, status){
        try {
            const res = await fetch('/POS-Inventory/api/orders/update-status.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ id, status }) });
            const j = await res.json();
            return !!(j && j.success);
        } catch(e) { console.error(e); return false; }
    }

    async function deleteOrder(id){
        try {
            const r = await fetch('/POS-Inventory/api/orders/delete.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ id }) });
            const j = await r.json();
            return !!(j && j.success);
        } catch (e) { console.error('delete error', e); return false; }
    }

    async function loadOrders(){
        try {
            const r = await fetch('/POS-Inventory/api/orders/get-queue.php');
            const j = await r.json();
            if (j && j.success) {
                renderOrders(j.orders);
                document.getElementById('active-orders').innerText = j.orders.filter(o=>o.status==='1' || o.status==='2').length;
                // compute average prep time (quick heuristic: difference between now and created_at in minutes for preparing orders)
                const prepping = j.orders.filter(o=>o.status==='2');
                let avg = 0;
                if (prepping.length > 0) {
                    const now = Date.now();
                    const mins = prepping.map(o => Math.max(0, Math.round((now - (new Date(o.created_at)).getTime())/60000)));
                    avg = Math.round(mins.reduce((a,b)=>a+b,0)/mins.length);
                }
                document.getElementById('avg-prep-time').innerText = avg;
            }
        } catch (e) { console.error('Failed to load orders', e); }
    }

    // first load and poll
    loadOrders();
    setInterval(loadOrders, 5000);
});
