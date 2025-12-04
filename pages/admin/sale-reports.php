<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="../../css/side-bar.css">
    <link rel="stylesheet" href="../../css/admin/sales-report.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/rapaeng-logo.png">
</head>
<body>
    <?php include('../side-bar-admin.php'); ?>
    <section class="whole-container">
    <div class="right-panel">
        <div>
            <h1>Sales Report</h1>
        </div>

        <div class="report-actions">
            <label for="report-mode">View by:</label>
            <select id="report-mode">
                <option value="month" selected>Month</option>
                <option value="year">Year</option>
            </select>

            <label for="report-month">Month:</label>
            <select id="report-month">
                <?php $months = array('January','February','March','April','May','June','July','August','September','October','November','December'); ?>
                <?php for($m=1;$m<=12;$m++): $mn = $months[$m-1]; ?>
                    <option value="<?php echo $m; ?>" <?php echo ($m==intval(date('n')))?'selected':''; ?>><?php echo $mn; ?></option>
                <?php endfor; ?>
            </select>

            <label for="report-year">Year:</label>
            <select id="report-year">
                <?php $y = intval(date('Y')); for($i=$y;$i>=$y-5;$i--): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <button id="load-report">Load</button>
            <button id="export-pdf" style="margin-left:8px;">Export PDF</button>
        </div>

        <div class="report-container">
            <div class="report-table-wrap">
                <div class="report-card">
                    <table class="report-table" id="sales-report-table">
                        <thead>
                            <tr>
                                <th>Menu ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="6">Loading…</td></tr>
                        </tbody>
                    </table>
                    <div id="sales-summary" style="margin-top:24px;">
                        <h3 style="color:#333;margin-bottom:12px;">Summary:</h3>
                        <table style="width:100%;border-collapse:collapse;color:#333;">
                            <tbody>
                                <tr style="border-bottom:1px solid #ddd;">
                                    <td style="padding:8px 0;"><strong>Total Items Sold:</strong></td>
                                    <td style="padding:8px 0;text-align:right;"><span id="summary-total-items">0</span></td>
                                    <td style="width:40%;"></td>
                                    <td style="padding:8px 0;"><strong>Best-selling food item:</strong></td>
                                    <td style="padding:8px 0;text-align:right;"><span id="summary-best-food">—</span></td>
                                </tr>
                                <tr style="border-bottom:1px solid #ddd;">
                                    <td style="padding:8px 0;"><strong>Total Sales:</strong></td>
                                    <td style="padding:8px 0;text-align:right;"><span id="summary-total-sales">₱0.00</span></td>
                                    <td style="width:40%;"></td>
                                    <td style="padding:8px 0;"><strong>Best-selling drink:</strong></td>
                                    <td style="padding:8px 0;text-align:right;"><span id="summary-best-drink">—</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
<!-- Include jsPDF and autotable for client-side PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script>
(function(){
    const apiBase = '../../api/reports/get-sales.php';

    function formatCurrency(v){ return '₱' + Number(v||0).toFixed(2); }

    function setLoading(loading){
        const tbody = document.querySelector('#sales-report-table tbody');
        if(loading){ tbody.innerHTML = '<tr><td colspan="6">Loading…</td></tr>'; }
    }

    async function loadSales(){
        const mode = document.getElementById('report-mode').value || 'month';
        const month = document.getElementById('report-month').value || '';
        const year = document.getElementById('report-year').value || '';
        const url = apiBase + `?mode=${encodeURIComponent(mode)}&month=${encodeURIComponent(month)}&year=${encodeURIComponent(year)}`;
        setLoading(true);
        try{
            const res = await fetch(url);
            const j = await res.json();
            const tbody = document.querySelector('#sales-report-table tbody');
            tbody.innerHTML = '';
            if(!j || !j.status){ tbody.innerHTML = '<tr><td colspan="6">No data</td></tr>'; return; }
            const items = (j.data && j.data.items) ? j.data.items : [];
            if(items.length===0){ tbody.innerHTML = '<tr><td colspan="6">No data</td></tr>'; }
            items.forEach(it => {
                const tr = document.createElement('tr');
                const menuId = it.menu_id || it.id || '';
                const name = it.name || it.menu_name || '';
                const cat = it.category_name || it.category || '';
                const qty = Number(it.quantity||0);
                const unit = Number(it.unit_price||it.price||0);
                const total = Number(it.total_sales||qty * unit || 0);
                tr.innerHTML = `<td>${menuId}</td><td>${escapeHtml(name)}</td><td>${escapeHtml(cat)}</td><td>${qty}</td><td>${formatCurrency(unit)}</td><td>${formatCurrency(total)}</td>`;
                tbody.appendChild(tr);
            });
            const s = (j.data && j.data.summary) ? j.data.summary : {};
            // populate summary fields
            document.getElementById('summary-total-items').textContent = s.total_items || 0;
            document.getElementById('summary-total-sales').textContent = formatCurrency(s.total_sales||0);
            // find best sellers by category
            let bestFood = null, bestDrink = null;
            items.forEach(it => {
                const cat = (it.category_name || it.category || '').toLowerCase();
                const qty = Number(it.quantity||0);
                const name = it.name || it.menu_name || '';
                // common food categories
                if (cat.includes('food') || cat.includes('snack') || cat.includes('pastry') || cat.includes('meal')) {
                    if (!bestFood || qty > bestFood.qty) bestFood = {name, qty, cat};
                }
                // common drink categories
                else if (cat.includes('coffee') || cat.includes('drink') || cat.includes('beverage') || cat.includes('tea')) {
                    if (!bestDrink || qty > bestDrink.qty) bestDrink = {name, qty, cat};
                }
            });
            document.getElementById('summary-best-food').textContent = bestFood ? bestFood.name : '—';
            document.getElementById('summary-best-drink').textContent = bestDrink ? bestDrink.name : '—';
            // save last loaded items for export and include computed best-sellers
            const enhancedSummary = Object.assign({}, s || {});
            enhancedSummary.best_food = bestFood ? bestFood.name : (enhancedSummary.best_food || '—');
            enhancedSummary.best_drink = bestDrink ? bestDrink.name : (enhancedSummary.best_drink || '—');
            window.__salesReportLast = {items: items, summary: enhancedSummary, meta:{mode,month,year}};
        }catch(err){
            const tbody = document.querySelector('#sales-report-table tbody');
            tbody.innerHTML = '<tr><td colspan="6">Error loading data</td></tr>';
            console.error('Load sales error', err);
        }
    }

    function escapeHtml(text){
        if(!text) return '';
        return String(text).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m]; });
    }

    async function downloadPDF(){
        const items = (window.__salesReportLast && window.__salesReportLast.items) ? window.__salesReportLast.items : [];
        const meta = (window.__salesReportLast && window.__salesReportLast.meta) ? window.__salesReportLast.meta : {};
        const summary = (window.__salesReportLast && window.__salesReportLast.summary) ? window.__salesReportLast.summary : {};
        if(!items || items.length===0){ alert('No data to export'); return; }

        // use jsPDF UMD
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({unit:'pt', format:'a4'});

        const title = 'Sales Report';
        doc.setFontSize(16);
        doc.text(title, 40, 48);

        const metaText = `Mode: ${meta.mode || 'month'}   Month: ${meta.month || ''}   Year: ${meta.year || ''}`;
        doc.setFontSize(10);
        doc.text(metaText, 40, 66);

        // prepare table
        const head = [['Menu ID','Name','Category','Quantity','Unit Price','Total Sales']];
        const body = items.map(it => {
            const menuId = it.menu_id || it.id || '';
            const name = (it.name || it.menu_name || '') || '';
            const cat = (it.category_name || it.category) || '';
            const qty = Number(it.quantity||0);
            const unit = Number(it.unit_price||it.price||0).toFixed(2);
            const total = Number(it.total_sales||qty*unit||0).toFixed(2);
            // use ASCII currency label (PHP) because some PDF fonts don't render the peso symbol
            return [String(menuId), name, cat, String(qty), 'PHP ' + unit, 'PHP ' + total];
        });

        // autoTable will handle paging
        doc.autoTable({
            startY: 82,
            head: head,
            body: body,
            styles: { fontSize: 10 },
            headStyles: { fillColor: [122,78,45] },
            alternateRowStyles: { fillColor: [245,245,245] },
            margin: { left: 40, right: 40 }
        });

        // add summary below table (if space allow, will be on next page)
        let finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 20 : 40;
        doc.setFontSize(12);
        doc.text('Summary', 40, finalY);
        doc.setFontSize(10);
        const smLines = [
            `Total Items Sold: ${summary.total_items || 0}`,
            `Total Sales: PHP ${Number(summary.total_sales||0).toFixed(2)}`,
            `Best-selling food item: ${summary.best_food || '—'}`,
            `Best-selling drink: ${summary.best_drink || '—'}`
        ];
        let y = finalY + 14;
        smLines.forEach(line => { doc.text(line, 40, y); y += 14; });

        const ts = new Date().toISOString().slice(0,19).replace(/[:T]/g,'-');
        const filename = `sales-report-${meta.mode||'report'}-${meta.year||''}-${meta.month||''}-${ts}.pdf`;
        doc.save(filename);
    }

    document.getElementById('load-report').addEventListener('click', loadSales);
    document.getElementById('export-pdf').addEventListener('click', downloadPDF);

    // initial load
    loadSales();
})();
</script>
</body>
</html>