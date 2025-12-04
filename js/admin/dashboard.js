document.addEventListener('DOMContentLoaded', function(){
  const ids = {
    totalProducts: document.getElementById('stat-total-products'),
    totalStocks: document.getElementById('stat-total-stocks'),
    orderedToday: document.getElementById('stat-ordered-today'),
    outOfStock: document.getElementById('stat-out-of-stock'),
    highestSale: document.getElementById('stat-highest-sale'),
    lowStocks: document.getElementById('stat-low-stocks')
  };

  function safeText(el, value){ if (!el) return; el.textContent = value; }

  // Fetch stats
  fetch('../../api/dashboard/get-stats.php').then(r=>r.json()).then(j=>{
    if (!j || !j.status) return;
    const d = j.data || {};
    safeText(ids.totalProducts, d.total_products ?? '—');
    safeText(ids.totalStocks, d.total_stocks ?? '—');
    safeText(ids.orderedToday, d.stocks_ordered_today ?? 0);
    safeText(ids.outOfStock, d.out_of_stock ?? 0);
    if (d.highest_sale && d.highest_sale.name) {
      safeText(ids.highestSale, `${d.highest_sale.name} (${d.highest_sale.total_qty || 0})`);
    } else {
      safeText(ids.highestSale, '—');
    }
    safeText(ids.lowStocks, d.low_stocks_count ?? 0);
  }).catch(err=>{ console.error('Failed to load dashboard stats', err); });

  // Load sales report for chart (top items this month)
  fetch('../../api/reports/get-sales.php?mode=month').then(r=>r.json()).then(j=>{
    if (!j || !j.status) return;
    const items = j.data.items || [];
    const top = items.slice(0,6);
    const labels = top.map(i=>i.name || ('Item '+(i.menu_id||'')));
    const data = top.map(i=>Number(i.total_sales||0));

    const ctx = document.getElementById('monthlyPercentChart');
    if (!ctx) return;
    try {
      const chart = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
          labels,
          datasets: [{ label: 'Top sales (₱)', data, backgroundColor: ['#7A4E2D','#A65D3A','#D08B6A','#C97E50','#8F4B2A','#E3BFA5'] }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    } catch (e) { console.error('Chart error', e); }
  }).catch(err=>{ console.error('Failed to load sales items for chart', err); });

});