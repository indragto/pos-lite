<div class="pos-wrapper">
    <div class="row g-0 vh-100">
        <!-- Left: Product Selection Panel -->
        <div class="col-12 col-lg-7 col-xl-8 pos-product-panel">
            <div class="d-flex flex-column h-100">
                <!-- Search Bar -->
                <div class="p-3 border-bottom bg-white">
                    <div class="toolbar mb-0">
                        <div class="position-relative flex-grow-1">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" class="form-control ps-5" id="productSearch"
                                   placeholder="Search products or scan barcode..." autocomplete="off">
                            <button class="btn btn-ghost btn-sm position-absolute top-50 end-0 translate-middle-y me-1"
                                    type="button" id="clearSearch" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category Tabs -->
                <div class="category-tabs p-3 border-bottom bg-white">
                    <div class="d-flex gap-2 overflow-auto pb-1" style="white-space: nowrap;">
                        <button class="btn btn-sm btn-primary category-tab active" data-category="">
                            <i class="fas fa-th me-1"></i>All
                        </button>
                        <?php foreach ($categories as $cat): ?>
                        <button class="btn btn-sm btn-outline category-tab"
                                data-category="<?= $cat['id'] ?>">
                            <?= e($cat['name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="product-grid flex-grow-1 overflow-auto p-3" id="productGrid">
                    <div class="row g-2" id="productsContainer"></div>
                    <div id="loadingIndicator" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="emptyState" class="empty-state" style="display: none;">
                        <i class="fas fa-box-open"></i>
                        <h5>No products found</h5>
                        <p>Try a different search or category.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Cart Panel -->
        <div class="col-12 col-lg-5 col-xl-4 pos-cart-panel border-start">
            <div class="d-flex flex-column h-100">
                <!-- Cart Header -->
                <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-shopping-cart me-2 text-primary"></i>Current Sale
                    </h6>
                    <button class="btn btn-sm btn-outline-danger" id="clearCartBtn" type="button">
                        <i class="fas fa-trash me-1"></i>Clear
                    </button>
                </div>

                <!-- Cart Items -->
                <div class="cart-items flex-grow-1 overflow-auto p-2" id="cartItems">
                    <div class="empty-state py-4" id="emptyCart">
                        <i class="fas fa-cart-plus"></i>
                        <h5>Cart is empty</h5>
                        <p>Tap a product to add it</p>
                    </div>
                </div>

                <!-- Cart Footer -->
                <div class="cart-footer border-top bg-white p-3">
                    <!-- Subtotal -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold" id="subtotalDisplay">Rp 0</span>
                    </div>
                    <!-- Tax -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tax (<span id="taxRateDisplay">0</span>%)</span>
                        <span class="fw-semibold" id="taxDisplay">Rp 0</span>
                    </div>

                    <!-- Discount Row -->
                    <div class="d-flex gap-2 mb-2">
                        <div class="flex-grow-1">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Discount</span>
                                <input type="number" class="form-control" id="discountValue" value="0"
                                       min="0" step="1" placeholder="0">
                            </div>
                        </div>
                        <div>
                            <select class="form-select form-select-sm" id="discountType" style="min-height: 44px;">
                                <option value="fixed">Rp</option>
                                <option value="percentage">%</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Discount</span>
                        <span class="text-danger fw-bold" id="discountDisplay">- Rp 0</span>
                    </div>

                    <!-- Total -->
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-3">
                        <span class="h6 mb-0 fw-bold">Total</span>
                        <span class="h5 mb-0 fw-bold text-primary" id="totalDisplay">Rp 0</span>
                    </div>

                    <!-- Pay Button -->
                    <button class="btn btn-success btn-lg btn-block" id="payBtn" type="button" disabled>
                        <i class="fas fa-cash-register me-2"></i>Pay Now
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-wallet me-2 text-primary"></i>Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Total Amount -->
                <div class="text-center mb-4 p-3 bg-light rounded">
                    <small class="text-muted d-block">Total Amount</small>
                    <h2 class="fw-bold text-primary mb-0" id="modalTotal">Rp 0</h2>
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Payment Method</label>
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method"
                                   id="payCash" value="cash" checked>
                            <label class="btn btn-outline-success btn-block" for="payCash"
                                   style="min-height: 80px;">
                                <i class="fas fa-money-bill-wave fa-lg mb-1 d-block"></i>
                                <small>Cash</small>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method"
                                   id="payCard" value="card">
                            <label class="btn btn-outline-primary btn-block" for="payCard"
                                   style="min-height: 80px;">
                                <i class="fas fa-credit-card fa-lg mb-1 d-block"></i>
                                <small>Card</small>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method"
                                   id="payQris" value="qris">
                            <label class="btn btn-outline-info btn-block" for="payQris"
                                   style="min-height: 80px;">
                                <i class="fas fa-qrcode fa-lg mb-1 d-block"></i>
                                <small>QRIS</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Cash Payment Section -->
                <div id="cashPaymentSection">
                    <div class="mb-3">
                        <label for="amountPaid" class="form-label fw-bold">Amount Paid</label>
                        <input type="number" class="form-control form-control-lg" id="amountPaid"
                               placeholder="Enter amount" min="0" step="1">
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="row g-2 mb-3" id="quickCashButtons"></div>

                    <!-- Change Display -->
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                        <span class="fw-bold">Change</span>
                        <span class="h5 mb-0 fw-bold text-success" id="changeDisplay">Rp 0</span>
                    </div>
                </div>

                <!-- Error Alert -->
                <div class="alert alert-danger mt-3" id="paymentError" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="paymentErrorText"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-success flex-grow-1" id="confirmPayBtn">
                    <i class="fas fa-check me-2"></i>Confirm Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body py-5">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold mb-1">Transaction Complete!</h4>
                <p class="text-muted mb-4" id="successInvoice"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-outline" id="printReceiptBtn" type="button">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                    <button class="btn btn-success" type="button" id="newSaleBtn">
                        <i class="fas fa-plus me-1"></i>New Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pos-wrapper { margin: -1rem; }
    .pos-product-panel, .pos-cart-panel { overflow: hidden; }
    .pos-cart-panel { background: var(--gray-50, #f9fafb); }

    .product-grid .product-card {
        cursor: pointer;
        transition: var(--transition);
        min-height: 110px;
        border: 2px solid var(--gray-200);
        user-select: none;
    }
    .product-grid .product-card:active {
        transform: scale(0.96);
    }
    .product-grid .product-card:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow);
    }
    .product-grid .product-card.out-of-stock {
        opacity: 0.5;
        pointer-events: none;
        cursor: not-allowed;
    }

    .cart-items .cart-item {
        background: white;
        border-radius: var(--radius);
        padding: 10px 12px;
        margin-bottom: 6px;
        border: 1px solid var(--gray-100);
    }

    .qty-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-sm);
        font-size: 14px;
        padding: 0;
    }

    .category-tabs .category-tab {
        min-height: 44px;
        border-radius: var(--radius-xl);
        padding: 8px 18px;
        font-weight: 500;
    }
    .category-tabs .category-tab.active {
        background-color: var(--primary);
        border-color: var(--primary);
        color: #fff;
        box-shadow: var(--shadow-primary);
    }

    .quick-cash-btn {
        min-height: 48px;
        font-weight: 600;
    }

    @media (max-width: 991.98px) {
        .pos-wrapper { height: auto; }
        .pos-product-panel { height: 50vh; }
        .pos-cart-panel { height: 50vh; border-top: 2px solid var(--gray-200); }
    }
</style>

<script>
(function() {
    'use strict';

    var taxRate = parseFloat('<?= setting("tax_rate", 0) ?>') || 0;
    var items = [];
    var currentCategory = '';
    var searchTimeout = null;

    // ===== Utility Functions =====
    function formatRupiah(amount) {
        return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showToast(message, type) {
        var container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        var toast = document.createElement('div');
        toast.className = 'toast toast-' + (type || 'success');
        var iconClass = type === 'error' ? 'exclamation-circle text-danger' : 'check-circle text-success';
        toast.innerHTML = '<i class="fas fa-' + iconClass + '"></i><span>' + message + '</span>';
        container.appendChild(toast);
        setTimeout(function() {
            toast.classList.add('removing');
            setTimeout(function() { toast.remove(); }, 300);
        }, 2500);
    }

    // ===== Cart Calculations =====
    function getSubtotal() {
        var sum = 0;
        for (var i = 0; i < items.length; i++) {
            sum += items[i].subtotal;
        }
        return sum;
    }

    function getTax() {
        return getSubtotal() * (taxRate / 100);
    }

    function getDiscount() {
        var discountEl = document.getElementById('discountValue');
        var typeEl = document.getElementById('discountType');
        if (!discountEl || !typeEl) return 0;
        var value = parseFloat(discountEl.value) || 0;
        var type = typeEl.value;
        if (type === 'percentage') {
            return (getSubtotal() + getTax()) * (value / 100);
        }
        return value;
    }

    function getTotal() {
        return getSubtotal() + getTax() - getDiscount();
    }

    // ===== Cart Actions =====
    function addToCart(id, name, price, stock) {
        id = parseInt(id);
        price = parseFloat(price);
        stock = parseInt(stock);

        var existing = null;
        for (var i = 0; i < items.length; i++) {
            if (items[i].product_id === id) {
                existing = items[i];
                break;
            }
        }

        if (existing) {
            if (existing.quantity < stock) {
                existing.quantity++;
                existing.subtotal = existing.quantity * existing.price;
            } else {
                showToast('Max stock reached for ' + name, 'warning');
                return;
            }
        } else {
            items.push({
                product_id: id,
                name: name,
                price: price,
                quantity: 1,
                stock: stock,
                subtotal: price
            });
        }
        renderCart();
        showToast(name + ' added to cart', 'success');
    }

    function removeFromCart(id) {
        id = parseInt(id);
        for (var i = items.length - 1; i >= 0; i--) {
            if (items[i].product_id === id) {
                items.splice(i, 1);
            }
        }
        renderCart();
    }

    function updateQty(id, delta) {
        id = parseInt(id);
        for (var i = 0; i < items.length; i++) {
            if (items[i].product_id === id) {
                var newQty = items[i].quantity + delta;
                if (newQty > 0 && newQty <= items[i].stock) {
                    items[i].quantity = newQty;
                    items[i].subtotal = items[i].quantity * items[i].price;
                } else if (newQty <= 0) {
                    removeFromCart(id);
                    return;
                } else if (newQty > items[i].stock) {
                    showToast('Max stock reached', 'warning');
                }
                break;
            }
        }
        renderCart();
    }

    function clearCart() {
        if (items.length === 0) return;
        if (confirm('Clear all items from cart?')) {
            items = [];
            renderCart();
        }
    }

    // ===== Render Cart =====
    function renderCart() {
        var container = document.getElementById('cartItems');
        var emptyCart = document.getElementById('emptyCart');
        var payBtn = document.getElementById('payBtn');

        if (!container) return;

        // Update totals
        var subtotalEl = document.getElementById('subtotalDisplay');
        var taxRateEl = document.getElementById('taxRateDisplay');
        var taxEl = document.getElementById('taxDisplay');
        var discountEl = document.getElementById('discountDisplay');
        var totalEl = document.getElementById('totalDisplay');

        if (subtotalEl) subtotalEl.textContent = formatRupiah(getSubtotal());
        if (taxRateEl) taxRateEl.textContent = taxRate;
        if (taxEl) taxEl.textContent = formatRupiah(getTax());
        if (discountEl) discountEl.textContent = '- ' + formatRupiah(getDiscount());
        if (totalEl) totalEl.textContent = formatRupiah(getTotal());

        if (items.length === 0) {
            container.innerHTML = '';
            if (emptyCart) {
                container.appendChild(emptyCart);
                emptyCart.style.display = '';
            }
            if (payBtn) payBtn.disabled = true;
            return;
        }

        if (emptyCart) emptyCart.style.display = 'none';
        if (payBtn) payBtn.disabled = false;

        var html = '';
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var eName = escapeHtml(item.name);
            var canAddMore = item.quantity < item.stock;

            html += '<div class="cart-item" data-id="' + item.product_id + '">';
            html += '  <div class="d-flex justify-content-between align-items-start">';
            html += '    <div class="flex-grow-1 me-2">';
            html += '      <div class="fw-semibold small">' + eName + '</div>';
            html += '      <div class="text-muted small">' + formatRupiah(item.price) + ' each</div>';
            html += '    </div>';
            html += '    <button class="btn btn-sm btn-outline-danger cart-remove p-1" type="button" data-id="' + item.product_id + '" style="min-width:32px;min-height:32px;">';
            html += '      <i class="fas fa-times"></i>';
            html += '    </button>';
            html += '  </div>';
            html += '  <div class="d-flex justify-content-between align-items-center mt-2">';
            html += '    <div class="d-flex align-items-center gap-1">';
            html += '      <button class="btn btn-sm btn-outline qty-btn qty-minus" type="button" data-id="' + item.product_id + '" style="width:36px;height:36px;">';
            html += '        <i class="fas fa-minus"></i>';
            html += '      </button>';
            html += '      <span class="fw-bold mx-2" style="min-width:24px;text-align:center;">' + item.quantity + '</span>';
            html += '      <button class="btn btn-sm btn-outline qty-btn qty-plus" type="button" data-id="' + item.product_id + '" style="width:36px;height:36px;"' + (canAddMore ? '' : ' disabled') + '>';
            html += '        <i class="fas fa-plus"></i>';
            html += '      </button>';
            html += '    </div>';
            html += '    <span class="fw-bold">' + formatRupiah(item.subtotal) + '</span>';
            html += '  </div>';
            html += '</div>';
        }
        container.innerHTML = html;
    }

    // ===== Load Products =====
    function loadProducts(keyword, categoryId) {
        keyword = keyword || '';
        categoryId = categoryId || '';

        var container = document.getElementById('productsContainer');
        var loading = document.getElementById('loadingIndicator');
        var empty = document.getElementById('emptyState');

        if (!container) return;

        container.innerHTML = '';
        if (loading) loading.style.display = '';
        if (empty) empty.style.display = 'none';

        var params = new URLSearchParams();
        if (keyword) params.set('q', keyword);
        if (categoryId) params.set('category_id', categoryId);

        var url = '<?= url('api/products/search') ?>?' + params.toString();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (loading) loading.style.display = 'none';
                if (!data.success || !data.products || data.products.length === 0) {
                    if (empty) empty.style.display = '';
                    return;
                }

                var html = '';
                for (var i = 0; i < data.products.length; i++) {
                    var p = data.products[i];
                    var outOfStock = parseInt(p.stock, 10) <= 0;
                    var eName = escapeHtml(p.name);
                    var pId = parseInt(p.id, 10);
                    var pPrice = parseFloat(p.price);
                    var pStock = parseInt(p.stock, 10);

                    html += '<div class="col-6 col-md-4 col-xl-3">';
                    html += '  <div class="card product-card h-100' + (outOfStock ? ' out-of-stock' : '') + '"';
                    html += '       data-product-id="' + pId + '"';
                    html += '       data-product-name="' + eName.replace(/"/g, '&quot;') + '"';
                    html += '       data-product-price="' + pPrice + '"';
                    html += '       data-product-stock="' + pStock + '"';
                    html += '       style="cursor:' + (outOfStock ? 'not-allowed' : 'pointer') + ';">';
                    html += '    <div class="card-body p-2 text-center">';
                    if (p.image) {
                        html += '      <img src="<?= url('uploads') ?>/' + p.image + '" class="img-fluid rounded mb-2" style="max-height:60px;object-fit:cover;">';
                    } else {
                        html += '      <div class="mb-2"><i class="fas fa-box fa-2x text-muted"></i></div>';
                    }
                    html += '      <div class="fw-semibold small text-truncate" title="' + eName + '">' + eName + '</div>';
                    html += '      <div class="text-primary fw-bold small">' + formatRupiah(pPrice) + '</div>';
                    html += '      <span class="badge badge-' + (outOfStock ? 'danger' : 'success') + ' mt-1">' + pStock + '</span>';
                    html += '    </div>';
                    html += '  </div>';
                    html += '</div>';
                }
                container.innerHTML = html;
                if (empty) empty.style.display = 'none';
            })
            .catch(function() {
                if (loading) loading.style.display = 'none';
                if (empty) empty.style.display = '';
            });
    }

    // ===== Payment Modal =====
    function openPaymentModal() {
        if (items.length === 0) return;
        var total = getTotal();
        var modalTotal = document.getElementById('modalTotal');
        if (modalTotal) modalTotal.textContent = formatRupiah(total);
        var amountPaid = document.getElementById('amountPaid');
        if (amountPaid) amountPaid.value = '';
        var changeDisp = document.getElementById('changeDisplay');
        if (changeDisp) changeDisp.textContent = formatRupiah(0);
        var payError = document.getElementById('paymentError');
        if (payError) payError.style.display = 'none';

        generateQuickCashButtons(total);

        var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    function generateQuickCashButtons(total) {
        var container = document.getElementById('quickCashButtons');
        if (!container) return;
        var amounts = [];
        var rounded = Math.ceil(total / 10000) * 10000;
        amounts.push(rounded);
        amounts.push(rounded + 10000);
        amounts.push(rounded + 50000);
        amounts.push(100000);
        amounts.push(200000);
        amounts = amounts.filter(function(v, i, a) { return a.indexOf(v) === i; }).sort(function(a, b) { return a - b; }).slice(0, 6);

        var html = '';
        for (var i = 0; i < amounts.length; i++) {
            var label = amounts[i] >= 1000 ? (amounts[i] / 1000) + 'K' : amounts[i];
            html += '<div class="col-4"><button class="btn btn-outline quick-cash-btn w-100" type="button" onclick="window._posQuickCash(' + amounts[i] + ')">' + label + '</button></div>';
        }
        container.innerHTML = html;
    }

    window._posQuickCash = function(amount) {
        var el = document.getElementById('amountPaid');
        if (el) {
            el.value = amount;
            el.dispatchEvent(new Event('input'));
        }
    };

    function processPayment() {
        var methodEl = document.querySelector('input[name="payment_method"]:checked');
        var method = methodEl ? methodEl.value : 'cash';
        var total = getTotal();
        var amountPaid, changeAmount;

        if (method === 'cash') {
            var paidEl = document.getElementById('amountPaid');
            amountPaid = paidEl ? (parseFloat(paidEl.value) || 0) : 0;
            if (amountPaid < total) {
                var errEl = document.getElementById('paymentError');
                var errText = document.getElementById('paymentErrorText');
                if (errText) errText.textContent = 'Insufficient payment amount.';
                if (errEl) errEl.style.display = '';
                return;
            }
            changeAmount = amountPaid - total;
        } else {
            amountPaid = total;
            changeAmount = 0;
        }

        var errEl = document.getElementById('paymentError');
        if (errEl) errEl.style.display = 'none';

        var btn = document.getElementById('confirmPayBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        }

        var formData = new FormData();
        formData.append('csrf_token', '<?= csrf_token() ?>');
        formData.append('subtotal', getSubtotal());
        formData.append('tax', getTax());
        formData.append('discount', getDiscount());
        formData.append('total', total);
        formData.append('payment_method', method);
        formData.append('amount_paid', amountPaid);
        formData.append('change_amount', changeAmount);

        for (var i = 0; i < items.length; i++) {
            formData.append('items[' + i + '][product_id]', items[i].product_id);
            formData.append('items[' + i + '][quantity]', items[i].quantity);
            formData.append('items[' + i + '][price]', items[i].price);
            formData.append('items[' + i + '][subtotal]', items[i].subtotal);
        }

        fetch('<?= url('transactions/store') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Payment';
            }
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                var invEl = document.getElementById('successInvoice');
                if (invEl) invEl.textContent = 'Invoice: ' + (data.invoice_no || 'N/A');

                document.getElementById('printReceiptBtn').onclick = function() {
                    window.open('<?= url('transactions/receipt') ?>/' + data.transaction_id, '_blank');
                };
                document.getElementById('newSaleBtn').onclick = function() {
                    items = [];
                    renderCart();
                    loadProducts();
                    bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
                };

                new bootstrap.Modal(document.getElementById('successModal')).show();
            } else {
                var errText = document.getElementById('paymentErrorText');
                if (errText) errText.textContent = data.message || 'Transaction failed.';
                if (errEl) errEl.style.display = '';
            }
        })
        .catch(function() {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Payment';
            }
            var errText = document.getElementById('paymentErrorText');
            if (errText) errText.textContent = 'Network error. Please try again.';
            if (errEl) errEl.style.display = '';
        });
    }

    // ===== Init on DOM Ready =====
    document.addEventListener('DOMContentLoaded', function() {
        // Clear cart button
        var clearBtn = document.getElementById('clearCartBtn');
        if (clearBtn) clearBtn.addEventListener('click', clearCart);

        // Cart button clicks (event delegation)
        var cartContainer = document.getElementById('cartItems');
        if (cartContainer) {
            cartContainer.addEventListener('click', function(e) {
                var removeBtn = e.target.closest('.cart-remove');
                if (removeBtn) {
                    removeFromCart(parseInt(removeBtn.getAttribute('data-id')));
                    return;
                }
                var qtyBtn = e.target.closest('.qty-minus, .qty-plus');
                if (qtyBtn) {
                    var delta = qtyBtn.classList.contains('qty-plus') ? 1 : -1;
                    updateQty(parseInt(qtyBtn.getAttribute('data-id')), delta);
                }
            });
        }

        // Product clicks (event delegation on container)
        var prodContainer = document.getElementById('productsContainer');
        if (prodContainer) {
            prodContainer.addEventListener('click', function(e) {
                var card = e.target.closest('.product-card');
                if (!card || card.classList.contains('out-of-stock')) return;
                addToCart(
                    parseInt(card.getAttribute('data-product-id')),
                    card.getAttribute('data-product-name'),
                    parseFloat(card.getAttribute('data-product-price')),
                    parseInt(card.getAttribute('data-product-stock'))
                );
            });
        }

        // Discount inputs
        var discValue = document.getElementById('discountValue');
        var discType = document.getElementById('discountType');
        if (discValue) discValue.addEventListener('input', renderCart);
        if (discType) discType.addEventListener('change', renderCart);

        // Search with debounce
        var searchInput = document.getElementById('productSearch');
        var clearSearch = document.getElementById('clearSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var kw = this.value.trim();
                if (clearSearch) clearSearch.style.display = kw ? '' : 'none';
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    loadProducts(kw, currentCategory);
                }, 300);
            });
        }
        if (clearSearch) {
            clearSearch.addEventListener('click', function() {
                if (searchInput) {
                    searchInput.value = '';
                    this.style.display = 'none';
                    loadProducts('', currentCategory);
                    searchInput.focus();
                }
            });
        }

        // Category tabs
        var catTabs = document.querySelectorAll('.category-tab');
        for (var i = 0; i < catTabs.length; i++) {
            catTabs[i].addEventListener('click', function() {
                for (var j = 0; j < catTabs.length; j++) {
                    catTabs[j].classList.remove('btn-primary', 'active');
                    catTabs[j].classList.add('btn-outline');
                }
                this.classList.remove('btn-outline');
                this.classList.add('btn-primary', 'active');
                currentCategory = this.getAttribute('data-category');
                var kw = searchInput ? searchInput.value.trim() : '';
                loadProducts(kw, currentCategory);
            });
        }

        // Pay button
        var payBtn = document.getElementById('payBtn');
        if (payBtn) payBtn.addEventListener('click', openPaymentModal);

        // Confirm payment
        var confirmBtn = document.getElementById('confirmPayBtn');
        if (confirmBtn) confirmBtn.addEventListener('click', processPayment);

        // Amount paid calculation
        var amountPaidEl = document.getElementById('amountPaid');
        if (amountPaidEl) {
            amountPaidEl.addEventListener('input', function() {
                var paid = parseFloat(this.value) || 0;
                var total = getTotal();
                var change = Math.max(0, paid - total);
                var changeDisp = document.getElementById('changeDisplay');
                if (changeDisp) changeDisp.textContent = formatRupiah(change);
            });
        }

        // Payment method toggle
        var radios = document.querySelectorAll('input[name="payment_method"]');
        for (var i = 0; i < radios.length; i++) {
            radios[i].addEventListener('change', function() {
                var cashSection = document.getElementById('cashPaymentSection');
                if (cashSection) cashSection.style.display = this.value === 'cash' ? '' : 'none';
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                if (searchInput) searchInput.focus();
            }
            if (e.key === 'F4' && items.length > 0) {
                e.preventDefault();
                openPaymentModal();
            }
        });

        // Load initial products
        loadProducts();
    });
})();
</script>
