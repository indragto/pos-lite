<div class="pos-layout">
    <!-- Products Panel -->
    <div class="pos-products">
        <div class="pos-search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="productSearch" placeholder="Search products..." autocomplete="off">
            <button class="pos-clear" id="clearSearch" style="display:none;"><i class="fas fa-times"></i></button>
        </div>
        <div class="pos-categories">
            <button class="pos-cat active" data-category=""><i class="fas fa-th"></i>All</button>
            <?php foreach ($categories as $cat): ?>
            <button class="pos-cat" data-category="<?= $cat['id'] ?>"><?= e($cat['name']) ?></button>
            <?php endforeach; ?>
        </div>
        <div class="pos-grid-wrap" id="productGrid">
            <div id="productsContainer"></div>
            <div id="loadingIndicator" class="pos-loading" style="display:none;"><div class="spinner"></div></div>
            <div id="emptyState" class="empty-state" style="display:none;">
                <i class="fas fa-box-open"></i><h5>No products found</h5><p>Try a different search</p>
            </div>
        </div>
    </div>

    <!-- Cart Panel -->
    <div class="pos-cart">
        <div class="pos-cart-header">
            <span><i class="fas fa-shopping-cart"></i> Current Sale</span>
            <button class="btn btn-sm btn-ghost text-danger" id="clearCartBtn"><i class="fas fa-trash"></i></button>
        </div>
        <div class="pos-cart-body" id="cartItems">
            <div class="empty-state" id="emptyCart">
                <i class="fas fa-cart-plus"></i><h5>Cart is empty</h5><p>Tap a product to add</p>
            </div>
        </div>
        <div class="pos-cart-footer">
            <div class="pos-row"><span>Subtotal</span><span class="fw-bold" id="subtotalDisplay">Rp 0</span></div>
            <div class="pos-row"><span>Tax (<span id="taxRateDisplay">0</span>%)</span><span id="taxDisplay">Rp 0</span></div>
            <div class="pos-disc-row">
                <input type="number" id="discountValue" value="0" min="0" step="1" placeholder="0">
                <select id="discountType"><option value="fixed">Rp</option><option value="percentage">%</option></select>
            </div>
            <div class="pos-row"><span>Discount</span><span class="text-danger fw-bold" id="discountDisplay">- Rp 0</span></div>
            <div class="pos-total"><span>Total</span><span id="totalDisplay">Rp 0</span></div>
            <button class="btn btn-success btn-lg" id="payBtn" disabled><i class="fas fa-cash-register"></i> Pay Now</button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title fw-bold"><i class="fas fa-wallet text-primary me-2"></i>Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="pos-modal-total"><small>Total</small><h2 id="modalTotal">Rp 0</h2></div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Payment Method</label>
                    <div class="pos-pay-methods">
                        <input type="radio" class="btn-check" name="payment_method" id="payCash" value="cash" checked>
                        <label class="pos-pay-label btn btn-outline-success" for="payCash"><i class="fas fa-money-bill-wave"></i>Cash</label>
                        <input type="radio" class="btn-check" name="payment_method" id="payCard" value="card">
                        <label class="pos-pay-label btn btn-outline-primary" for="payCard"><i class="fas fa-credit-card"></i>Card</label>
                        <input type="radio" class="btn-check" name="payment_method" id="payQris" value="qris">
                        <label class="pos-pay-label btn btn-outline-info" for="payQris"><i class="fas fa-qrcode"></i>QRIS</label>
                    </div>
                </div>
                <div id="cashPaymentSection">
                    <label class="form-label fw-bold">Amount Paid</label>
                    <input type="number" class="form-control form-control-lg" id="amountPaid" placeholder="Enter amount" min="0" step="1">
                    <div class="pos-quick-cash" id="quickCashButtons"></div>
                    <div class="pos-change"><span>Change</span><span id="changeDisplay">Rp 0</span></div>
                </div>
                <div class="alert alert-danger mt-3" id="paymentError" style="display:none;"><i class="fas fa-exclamation-circle"></i><span id="paymentErrorText"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success flex-grow-1" id="confirmPayBtn"><i class="fas fa-check me-2"></i>Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body py-5">
                <i class="fas fa-check-circle text-success" style="font-size:4rem;"></i>
                <h4 class="fw-bold mb-1 mt-3">Transaction Complete!</h4>
                <p class="text-muted mb-4" id="successInvoice"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-outline" id="printReceiptBtn"><i class="fas fa-print me-1"></i>Print</button>
                    <button class="btn btn-success" id="newSaleBtn"><i class="fas fa-plus me-1"></i>New Sale</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pos-layout { display:flex; height:calc(100vh - var(--topbar-height) - 1px); gap:0; overflow:hidden; }
    .pos-products { flex:1; min-width:0; display:flex; flex-direction:column; background:var(--gray-50); }
    .pos-cart { width:360px; min-width:320px; background:white; border-left:1px solid var(--gray-200); display:flex; flex-direction:column; flex-shrink:0; }

    .pos-search-bar { position:relative; padding:12px 16px; background:white; border-bottom:1px solid var(--gray-200); }
    .pos-search-bar i { position:absolute; left:26px; top:50%; transform:translateY(-50%); color:var(--gray-400); }
    .pos-search-bar input { width:100%; padding:10px 40px 10px 40px; border-radius:var(--radius); min-height:44px; }
    .pos-clear { position:absolute; right:22px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--gray-400); cursor:pointer; padding:4px 8px; min-height:36px; }

    .pos-categories { display:flex; gap:8px; padding:10px 16px; background:white; border-bottom:1px solid var(--gray-200); overflow-x:auto; }
    .pos-cat { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border:1.5px solid var(--gray-300); border-radius:var(--radius-xl); background:white; font-size:13px; font-weight:500; cursor:pointer; min-height:40px; white-space:nowrap; transition:var(--transition); }
    .pos-cat.active { background:var(--primary); border-color:var(--primary); color:white; box-shadow:var(--shadow-primary); }

    .pos-grid-wrap { flex:1; overflow-y:auto; padding:12px; }
    #productsContainer { display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:10px; }

    .product-card { cursor:pointer; transition:var(--transition); border:2px solid var(--gray-200); border-radius:var(--radius); padding:12px; text-align:center; min-height:110px; background:white; user-select:none; }
    .product-card:hover { border-color:var(--primary); box-shadow:var(--shadow); transform:translateY(-2px); }
    .product-card:active { transform:scale(0.97); }
    .product-card.out-of-stock { opacity:0.4; pointer-events:none; }
    .product-card .card-body { padding:0 !important; }

    .pos-cart-header { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--gray-100); font-weight:600; }
    .pos-cart-header i { color:var(--primary); margin-right:8px; }
    .pos-cart-body { flex:1; overflow-y:auto; padding:8px; }
    .pos-cart-item { background:var(--gray-50); border:1px solid var(--gray-100); border-radius:var(--radius); padding:10px 12px; margin-bottom:6px; }
    .pos-cart-footer { padding:16px; border-top:1px solid var(--gray-200); background:white; }
    .pos-row { display:flex; justify-content:space-between; padding:4px 0; font-size:14px; }
    .pos-disc-row { display:flex; gap:8px; margin:8px 0; }
    .pos-disc-row input { flex:1; min-height:40px; }
    .pos-disc-row select { min-height:40px; border-radius:var(--radius); }
    .pos-total { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:var(--primary-bg); border-radius:var(--radius); margin:12px 0; }
    .pos-total span:last-child { font-size:22px; font-weight:800; color:var(--primary); }
    .pos-cart-footer .btn { width:100%; }

    .pos-modal-total { text-align:center; padding:16px; background:var(--gray-50); border-radius:var(--radius); margin-bottom:20px; }
    .pos-modal-total small { color:var(--gray-500); }
    .pos-modal-total h2 { color:var(--primary); margin:0; }
    .pos-pay-methods { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
    .pos-pay-label { min-height:70px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; border-radius:var(--radius); }
    .pos-pay-label i { font-size:20px; }
    .pos-quick-cash { display:grid; grid-template-columns:repeat(4,1fr); gap:6px; margin:12px 0; }
    .quick-cash-btn { min-height:44px; font-weight:600; border-radius:var(--radius); }
    .pos-change { display:flex; justify-content:space-between; padding:12px 16px; background:var(--success-light); border-radius:var(--radius); }
    .pos-change span:last-child { font-size:20px; font-weight:700; color:#065f46; }

    /* Tablet Landscape */
    @media (max-width:1024px) {
        .pos-cart { width:300px; min-width:280px; }
        #productsContainer { grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:8px; }
    }

    /* Tablet Portrait */
    @media (max-width:834px) {
        .pos-layout { flex-direction:column; height:auto; }
        .pos-products { height:55vh; min-height:280px; }
        .pos-cart { width:100%; max-width:none; min-width:0; height:45vh; min-height:250px; border-left:none; border-top:2px solid var(--gray-200); }
        #productsContainer { grid-template-columns:repeat(auto-fill,minmax(95px,1fr)); gap:8px; }
    }
</style>

<script>
(function() {
    'use strict';
    var taxRate = parseFloat('<?= setting("tax_rate", 0) ?>') || 0;
    var items = [];
    var currentCategory = '';
    var searchTimeout = null;

    function formatRupiah(a) { return 'Rp ' + Math.round(a).toLocaleString('id-ID'); }
    function escapeHtml(t) { var d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

    function showToast(msg, type) {
        var c = document.querySelector('.toast-container');
        if (!c) { c = document.createElement('div'); c.className = 'toast-container'; document.body.appendChild(c); }
        var t = document.createElement('div');
        t.className = 'toast toast-' + (type || 'success');
        var ic = type === 'error' ? 'exclamation-circle text-danger' : type === 'warning' ? 'exclamation-triangle text-warning' : 'check-circle text-success';
        t.innerHTML = '<i class="fas fa-' + ic + '"></i><span>' + msg + '</span>';
        c.appendChild(t);
        setTimeout(function() { t.classList.add('removing'); setTimeout(function() { t.remove(); }, 300); }, 2500);
    }

    function getSubtotal() { var s = 0; for (var i = 0; i < items.length; i++) s += items[i].subtotal; return s; }
    function getTax() { return getSubtotal() * (taxRate / 100); }
    function getDiscount() {
        var v = parseFloat(document.getElementById('discountValue').value) || 0;
        var t = document.getElementById('discountType').value;
        return t === 'percentage' ? (getSubtotal() + getTax()) * (v / 100) : v;
    }
    function getTotal() { return getSubtotal() + getTax() - getDiscount(); }

    function addToCart(id, name, price, stock) {
        id = parseInt(id); price = parseFloat(price); stock = parseInt(stock);
        var existing = null;
        for (var i = 0; i < items.length; i++) { if (items[i].product_id === id) { existing = items[i]; break; } }
        if (existing) {
            if (existing.quantity < stock) { existing.quantity++; existing.subtotal = existing.quantity * existing.price; }
            else { showToast('Max stock: ' + name, 'warning'); return; }
        } else {
            items.push({ product_id: id, name: name, price: price, quantity: 1, stock: stock, subtotal: price });
        }
        renderCart();
        showToast(name + ' added', 'success');
    }

    function removeFromCart(id) {
        id = parseInt(id);
        for (var i = items.length - 1; i >= 0; i--) { if (items[i].product_id === id) items.splice(i, 1); }
        renderCart();
    }

    function updateQty(id, delta) {
        id = parseInt(id);
        for (var i = 0; i < items.length; i++) {
            if (items[i].product_id === id) {
                var nq = items[i].quantity + delta;
                if (nq > 0 && nq <= items[i].stock) { items[i].quantity = nq; items[i].subtotal = items[i].quantity * items[i].price; }
                else if (nq <= 0) { removeFromCart(id); return; }
                else { showToast('Max stock reached', 'warning'); }
                break;
            }
        }
        renderCart();
    }

    function renderCart() {
        var c = document.getElementById('cartItems'), e = document.getElementById('emptyCart'), p = document.getElementById('payBtn');
        if (!c) return;
        var se = document.getElementById('subtotalDisplay'), te = document.getElementById('taxRateDisplay'),
            tx = document.getElementById('taxDisplay'), de = document.getElementById('discountDisplay'), tl = document.getElementById('totalDisplay');
        if (se) se.textContent = formatRupiah(getSubtotal());
        if (te) te.textContent = taxRate;
        if (tx) tx.textContent = formatRupiah(getTax());
        if (de) de.textContent = '- ' + formatRupiah(getDiscount());
        if (tl) tl.textContent = formatRupiah(getTotal());

        if (items.length === 0) {
            c.innerHTML = ''; if (e) { c.appendChild(e); e.style.display = ''; }
            if (p) p.disabled = true; return;
        }
        if (e) e.style.display = 'none';
        if (p) p.disabled = false;

        var h = '';
        for (var i = 0; i < items.length; i++) {
            var it = items[i], en = escapeHtml(it.name), ok = it.quantity < it.stock;
            h += '<div class="pos-cart-item">';
            h += '<div class="d-flex justify-content-between align-items-start"><div class="flex-grow-1 me-2"><div class="fw-semibold small">' + en + '</div><div class="text-muted small">' + formatRupiah(it.price) + ' each</div></div>';
            h += '<button class="btn btn-sm btn-ghost text-danger p-0" onclick="window._posRemove(' + it.product_id + ')" style="min-width:32px;min-height:32px;"><i class="fas fa-times"></i></button></div>';
            h += '<div class="d-flex justify-content-between align-items-center mt-2"><div class="d-flex align-items-center gap-1">';
            h += '<button class="btn btn-sm btn-outline" style="width:32px;height:32px;padding:0;" onclick="window._posQty(' + it.product_id + ',-1)"><i class="fas fa-minus"></i></button>';
            h += '<span class="fw-bold mx-2" style="min-width:20px;text-align:center;">' + it.quantity + '</span>';
            h += '<button class="btn btn-sm btn-outline" style="width:32px;height:32px;padding:0;"' + (ok ? '' : ' disabled') + ' onclick="window._posQty(' + it.product_id + ',1)"><i class="fas fa-plus"></i></button>';
            h += '</div><span class="fw-bold">' + formatRupiah(it.subtotal) + '</span></div></div>';
        }
        c.innerHTML = h;
    }

    window._posRemove = removeFromCart;
    window._posQty = updateQty;

    function loadProducts(kw, cat) {
        kw = kw || ''; cat = cat || '';
        var c = document.getElementById('productsContainer'), l = document.getElementById('loadingIndicator'), em = document.getElementById('emptyState');
        if (!c) return;
        c.innerHTML = ''; if (l) l.style.display = ''; if (em) em.style.display = 'none';
        var p = new URLSearchParams();
        if (kw) p.set('q', kw); if (cat) p.set('category_id', cat);
        fetch('<?= url('api/products/search') ?>?' + p.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (l) l.style.display = 'none';
                if (!data.success || !data.products || data.products.length === 0) { if (em) em.style.display = ''; return; }
                var h = '';
                for (var i = 0; i < data.products.length; i++) {
                    var pr = data.products[i], oos = parseInt(pr.stock, 10) <= 0;
                    var en = escapeHtml(pr.name), pi = parseInt(pr.id, 10), pp = parseFloat(pr.price), ps = parseInt(pr.stock, 10);
                    h += '<div class="product-card' + (oos ? ' out-of-stock' : '') + '" onclick="window._posCart(' + pi + ',\'' + en.replace(/'/g, "\\'") + '\',' + pp + ',' + ps + ')">';
                    if (pr.image) h += '<img src="<?= url('uploads') ?>/' + pr.image + '" style="width:100%;height:60px;object-fit:cover;border-radius:var(--radius-sm);margin-bottom:6px;">';
                    else h += '<div style="margin-bottom:6px;"><i class="fas fa-box fa-2x text-muted"></i></div>';
                    h += '<div class="fw-semibold small text-truncate" title="' + en + '">' + en + '</div>';
                    h += '<div class="text-primary fw-bold" style="font-size:13px;">' + formatRupiah(pp) + '</div>';
                    h += '<span class="badge badge-' + (oos ? 'danger' : 'success') + '" style="font-size:11px;">' + ps + '</span></div>';
                }
                c.innerHTML = h; if (em) em.style.display = 'none';
            })
            .catch(function() { if (l) l.style.display = 'none'; if (em) em.style.display = ''; });
    }

    window._posCart = addToCart;

    function openPayment() {
        if (items.length === 0) return;
        var t = getTotal();
        var mt = document.getElementById('modalTotal'); if (mt) mt.textContent = formatRupiah(t);
        var ap = document.getElementById('amountPaid'); if (ap) ap.value = '';
        var cd = document.getElementById('changeDisplay'); if (cd) cd.textContent = formatRupiah(0);
        var pe = document.getElementById('paymentError'); if (pe) pe.style.display = 'none';
        genQuickCash(t);
        new bootstrap.Modal(document.getElementById('paymentModal')).show();
    }

    function genQuickCash(total) {
        var c = document.getElementById('quickCashButtons'); if (!c) return;
        var r = Math.ceil(total / 10000) * 10000;
        var a = [r, r + 10000, r + 50000, 100000, 200000].filter(function(v, i, s) { return s.indexOf(v) === i; }).slice(0, 6);
        var h = '';
        for (var i = 0; i < a.length; i++) {
            var lb = a[i] >= 1000 ? (a[i] / 1000) + 'K' : a[i];
            h += '<button class="btn btn-outline quick-cash-btn" onclick="window._posQc(' + a[i] + ')">' + lb + '</button>';
        }
        c.innerHTML = h;
    }

    window._posQc = function(a) { var e = document.getElementById('amountPaid'); if (e) { e.value = a; e.dispatchEvent(new Event('input')); } };

    function processPayment() {
        var m = document.querySelector('input[name="payment_method"]:checked');
        var method = m ? m.value : 'cash', total = getTotal(), paid, change;
        if (method === 'cash') {
            paid = parseFloat(document.getElementById('amountPaid').value) || 0;
            if (paid < total) { document.getElementById('paymentErrorText').textContent = 'Insufficient amount'; document.getElementById('paymentError').style.display = ''; return; }
            change = paid - total;
        } else { paid = total; change = 0; }
        document.getElementById('paymentError').style.display = 'none';
        var btn = document.getElementById('confirmPayBtn');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        var fd = new FormData();
        fd.append('csrf_token', '<?= csrf_token() ?>');
        fd.append('subtotal', getSubtotal()); fd.append('tax', getTax()); fd.append('discount', getDiscount());
        fd.append('total', total); fd.append('payment_method', method); fd.append('amount_paid', paid); fd.append('change_amount', change);
        for (var i = 0; i < items.length; i++) {
            fd.append('items[' + i + '][product_id]', items[i].product_id);
            fd.append('items[' + i + '][quantity]', items[i].quantity);
            fd.append('items[' + i + '][price]', items[i].price);
            fd.append('items[' + i + '][subtotal]', items[i].subtotal);
        }
        fetch('<?= url('transactions/store') ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                btn.disabled = false; btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm';
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                    document.getElementById('successInvoice').textContent = 'Invoice: ' + (data.invoice_no || 'N/A');
                    document.getElementById('printReceiptBtn').onclick = function() { window.open('<?= url('transactions/receipt') ?>/' + data.transaction_id, '_blank'); };
                    document.getElementById('newSaleBtn').onclick = function() { items = []; renderCart(); loadProducts(); bootstrap.Modal.getInstance(document.getElementById('successModal')).hide(); };
                    new bootstrap.Modal(document.getElementById('successModal')).show();
                } else { document.getElementById('paymentErrorText').textContent = data.message || 'Failed'; document.getElementById('paymentError').style.display = ''; }
            })
            .catch(function() { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm'; document.getElementById('paymentErrorText').textContent = 'Network error'; document.getElementById('paymentError').style.display = ''; });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var cb = document.getElementById('clearCartBtn'); if (cb) cb.onclick = function() { if (items.length && confirm('Clear cart?')) { items = []; renderCart(); } };
        var si = document.getElementById('productSearch'), cs = document.getElementById('clearSearch');
        if (si) si.addEventListener('input', function() { var k = this.value.trim(); if (cs) cs.style.display = k ? '' : 'none'; clearTimeout(searchTimeout); searchTimeout = setTimeout(function() { loadProducts(k, currentCategory); }, 300); });
        if (cs) cs.onclick = function() { if (si) { si.value = ''; this.style.display = 'none'; loadProducts('', currentCategory); si.focus(); } };
        document.querySelectorAll('.pos-cat').forEach(function(b) {
            b.addEventListener('click', function() {
                document.querySelectorAll('.pos-cat').forEach(function(x) { x.classList.remove('active'); });
                this.classList.add('active');
                currentCategory = this.getAttribute('data-category');
                loadProducts(si ? si.value.trim() : '', currentCategory);
            });
        });
        var pb = document.getElementById('payBtn'); if (pb) pb.onclick = openPayment;
        var cp = document.getElementById('confirmPayBtn'); if (cp) cp.onclick = processPayment;
        var ap = document.getElementById('amountPaid');
        if (ap) ap.addEventListener('input', function() { var p = parseFloat(this.value) || 0, t = getTotal(), c = document.getElementById('changeDisplay'); if (c) c.textContent = formatRupiah(Math.max(0, p - t)); });
        document.querySelectorAll('input[name="payment_method"]').forEach(function(r) { r.addEventListener('change', function() { var s = document.getElementById('cashPaymentSection'); if (s) s.style.display = this.value === 'cash' ? '' : 'none'; }); });
        var dv = document.getElementById('discountValue'), dt = document.getElementById('discountType');
        if (dv) dv.addEventListener('input', renderCart); if (dt) dt.addEventListener('change', renderCart);
        loadProducts();
    });
})();
</script>
