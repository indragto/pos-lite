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
// ===== Cart Management =====
const cart = {
    items: [],
    taxRate: parseFloat('<?= setting("tax_rate", 0) ?>'),

    add(product) {
        const existing = this.items.find(i => i.product_id === product.id);
        if (existing) {
            if (existing.quantity < product.stock) {
                existing.quantity++;
                existing.subtotal = existing.quantity * existing.price;
            }
        } else {
            this.items.push({
                product_id: product.id,
                name: product.name,
                price: product.price,
                quantity: 1,
                stock: product.stock,
                subtotal: product.price
            });
        }
        this.render();
        showToast(product.name + ' added to cart', 'success');
    },

    remove(productId) {
        this.items = this.items.filter(i => i.product_id !== productId);
        this.render();
    },

    updateQuantity(productId, delta) {
        const item = this.items.find(i => i.product_id === productId);
        if (item) {
            const newQty = item.quantity + delta;
            if (newQty > 0 && newQty <= item.stock) {
                item.quantity = newQty;
                item.subtotal = item.quantity * item.price;
            } else if (newQty <= 0) {
                this.remove(productId);
                return;
            }
        }
        this.render();
    },

    clear() {
        this.items = [];
        this.render();
    },

    getSubtotal() {
        return this.items.reduce(function(sum, item) { return sum + item.subtotal; }, 0);
    },

    getTax() {
        return this.getSubtotal() * (this.taxRate / 100);
    },

    getDiscount() {
        const value = parseFloat(document.getElementById('discountValue').value) || 0;
        const type = document.getElementById('discountType').value;
        if (type === 'percentage') {
            return (this.getSubtotal() + this.getTax()) * (value / 100);
        }
        return value;
    },

    getTotal() {
        return this.getSubtotal() + this.getTax() - this.getDiscount();
    },

    formatRupiah(amount) {
        return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
    },

    escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    render() {
        var container = document.getElementById('cartItems');
        var emptyCart = document.getElementById('emptyCart');
        var payBtn = document.getElementById('payBtn');

        if (this.items.length === 0) {
            container.innerHTML = '';
            container.appendChild(emptyCart);
            emptyCart.style.display = '';
            payBtn.disabled = true;
        } else {
            emptyCart.style.display = 'none';
            payBtn.disabled = false;

            var html = '';
            for (var i = 0; i < this.items.length; i++) {
                var item = this.items[i];
                var escapedName = this.escapeHtml(item.name);
                var disabledPlus = item.quantity >= item.stock ? 'disabled' : '';

                html += '<div class="cart-item" data-product-id="' + item.product_id + '">';
                html += '  <div class="d-flex justify-content-between align-items-start">';
                html += '    <div class="flex-grow-1 me-2">';
                html += '      <div class="fw-semibold small">' + escapedName + '</div>';
                html += '      <div class="text-muted small">' + this.formatRupiah(item.price) + ' each</div>';
                html += '    </div>';
                html += '    <button class="btn btn-sm btn-outline-danger p-1" type="button"';
                html += '            onclick="cart.remove(' + item.product_id + ')"';
                html += '            style="min-width:32px;min-height:32px;">';
                html += '      <i class="fas fa-times"></i>';
                html += '    </button>';
                html += '  </div>';
                html += '  <div class="d-flex justify-content-between align-items-center mt-2">';
                html += '    <div class="d-flex align-items-center gap-1">';
                html += '      <button class="btn btn-sm btn-outline qty-btn" type="button"';
                html += '              onclick="cart.updateQuantity(' + item.product_id + ', -1)">';
                html += '        <i class="fas fa-minus"></i>';
                html += '      </button>';
                html += '      <span class="fw-bold mx-2" style="min-width:24px;text-align:center;">' + item.quantity + '</span>';
                html += '      <button class="btn btn-sm btn-outline qty-btn" type="button"';
                html += '              onclick="cart.updateQuantity(' + item.product_id + ', 1)" ' + disabledPlus + '>';
                html += '        <i class="fas fa-plus"></i>';
                html += '      </button>';
                html += '    </div>';
                html += '    <span class="fw-bold">' + this.formatRupiah(item.subtotal) + '</span>';
                html += '  </div>';
                html += '</div>';
            }
            container.innerHTML = html;
        }

        document.getElementById('subtotalDisplay').textContent = this.formatRupiah(this.getSubtotal());
        document.getElementById('taxRateDisplay').textContent = this.taxRate;
        document.getElementById('taxDisplay').textContent = this.formatRupiah(this.getTax());
        document.getElementById('discountDisplay').textContent = '- ' + this.formatRupiah(this.getDiscount());
        document.getElementById('totalDisplay').textContent = this.formatRupiah(this.getTotal());
    }
};

// ===== Toast Notification =====
function showToast(message, type) {
    var container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    var toast = document.createElement('div');
    toast.className = 'toast toast-' + (type || 'success');
    toast.innerHTML = '<i class="fas fa-' + (type === 'error' ? 'exclamation-circle text-danger' : 'check-circle text-success') + '"></i><span>' + message + '</span>';
    container.appendChild(toast);
    setTimeout(function() {
        toast.classList.add('removing');
        setTimeout(function() { toast.remove(); }, 300);
    }, 2500);
}

// ===== Discount Calculation Events =====
document.getElementById('discountValue').addEventListener('input', function() { cart.render(); });
document.getElementById('discountType').addEventListener('change', function() { cart.render(); });

// ===== Clear Cart =====
document.getElementById('clearCartBtn').addEventListener('click', function() {
    if (cart.items.length === 0) return;
    if (confirm('Clear all items from cart?')) {
        cart.clear();
    }
});

// ===== Product Search & Loading =====
var currentCategory = '';
var searchTimeout = null;

function loadProducts(keyword, categoryId) {
    keyword = keyword || '';
    categoryId = categoryId || '';

    var container = document.getElementById('productsContainer');
    var loading = document.getElementById('loadingIndicator');
    var empty = document.getElementById('emptyState');

    container.innerHTML = '';
    loading.style.display = '';
    empty.style.display = 'none';

    var params = new URLSearchParams();
    if (keyword) params.set('q', keyword);
    if (categoryId) params.set('category_id', categoryId);

    fetch('<?= url('api/products/search') ?>?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        loading.style.display = 'none';
        if (data.success && data.products && data.products.length > 0) {
            var html = '';
            for (var i = 0; i < data.products.length; i++) {
                var product = data.products[i];
                var outOfStock = product.stock <= 0;
                var escapedName = cart.escapeHtml(product.name);
                var productJson = JSON.stringify({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    stock: parseInt(product.stock, 10)
                }).replace(/'/g, '&#39;');

                html += '<div class="col-6 col-md-4 col-xl-3">';
                html += '  <div class="card product-card h-100' + (outOfStock ? ' out-of-stock' : '') + '"';
                html += '       onclick="' + (outOfStock ? '' : 'cart.add(' + productJson + ')') + '">';
                html += '    <div class="card-body p-2 text-center">';
                if (product.image) {
                    html += '      <img src="<?= url('uploads') ?>/' + product.image + '" class="img-fluid rounded mb-2" style="max-height:60px;object-fit:cover;">';
                } else {
                    html += '      <div class="mb-2"><i class="fas fa-box fa-2x text-muted"></i></div>';
                }
                html += '      <div class="fw-semibold small text-truncate" title="' + escapedName + '">' + escapedName + '</div>';
                html += '      <div class="text-primary fw-bold small">' + cart.formatRupiah(parseFloat(product.price)) + '</div>';
                html += '      <span class="badge badge-' + (outOfStock ? 'danger' : 'success') + ' mt-1">' + product.stock + '</span>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
            }
            container.innerHTML = html;
            empty.style.display = 'none';
        } else {
            container.innerHTML = '';
            empty.style.display = '';
        }
    })
    .catch(function() {
        loading.style.display = 'none';
        empty.style.display = '';
    });
}

// Search input with debounce
document.getElementById('productSearch').addEventListener('input', function() {
    var keyword = this.value.trim();
    document.getElementById('clearSearch').style.display = keyword ? '' : 'none';

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        loadProducts(keyword, currentCategory);
    }, 300);
});

document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('productSearch').value = '';
    this.style.display = 'none';
    loadProducts('', currentCategory);
    document.getElementById('productSearch').focus();
});

// Category tabs
document.querySelectorAll('.category-tab').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.category-tab').forEach(function(b) {
            b.classList.remove('btn-primary', 'active');
            b.classList.add('btn-outline');
        });
        this.classList.remove('btn-outline');
        this.classList.add('btn-primary', 'active');

        currentCategory = this.dataset.category;
        loadProducts(document.getElementById('productSearch').value.trim(), currentCategory);
    });
});

// ===== Payment Modal =====
function openPaymentModal() {
    if (cart.items.length === 0) return;

    var total = cart.getTotal();
    document.getElementById('modalTotal').textContent = cart.formatRupiah(total);
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeDisplay').textContent = cart.formatRupiah(0);
    document.getElementById('paymentError').style.display = 'none';

    // Generate quick cash buttons based on total
    generateQuickCashButtons(total);

    var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function generateQuickCashButtons(total) {
    var container = document.getElementById('quickCashButtons');
    var amounts = [];
    var rounded = Math.ceil(total / 10000) * 10000;

    amounts.push(rounded);
    if (rounded < total + 10000) {
        amounts.push(rounded + 10000);
    }
    amounts.push(rounded + 50000);
    amounts.push(100000);
    amounts.push(200000);

    // Deduplicate and sort
    amounts = amounts.filter(function(v, i, a) { return a.indexOf(v) === i; }).sort(function(a, b) { return a - b; });
    amounts = amounts.slice(0, 6);

    var html = '';
    for (var i = 0; i < amounts.length; i++) {
        var label = amounts[i] >= 1000 ? (amounts[i] / 1000) + 'K' : amounts[i];
        html += '<div class="col-4">';
        html += '  <button class="btn btn-outline quick-cash-btn w-100" type="button" data-amount="' + amounts[i] + '">';
        html += label;
        html += '  </button>';
        html += '</div>';
    }
    container.innerHTML = html;

    // Attach click events
    container.querySelectorAll('.quick-cash-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('amountPaid').value = this.dataset.amount;
            document.getElementById('amountPaid').dispatchEvent(new Event('input'));
        });
    });
}

// Amount paid calculation
document.getElementById('amountPaid').addEventListener('input', function() {
    var paid = parseFloat(this.value) || 0;
    var total = cart.getTotal();
    var change = Math.max(0, paid - total);
    document.getElementById('changeDisplay').textContent = cart.formatRupiah(change);
});

// Payment method toggle
document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.getElementById('cashPaymentSection').style.display =
            this.value === 'cash' ? '' : 'none';
    });
});

// Pay button
document.getElementById('payBtn').addEventListener('click', function() {
    openPaymentModal();
});

// Confirm payment
document.getElementById('confirmPayBtn').addEventListener('click', function() {
    processPayment();
});

function processPayment() {
    var method = document.querySelector('input[name="payment_method"]:checked').value;
    var total = cart.getTotal();
    var amountPaid, changeAmount;

    if (method === 'cash') {
        amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
        if (amountPaid < total) {
            document.getElementById('paymentErrorText').textContent = 'Insufficient payment amount.';
            document.getElementById('paymentError').style.display = '';
            return;
        }
        changeAmount = amountPaid - total;
    } else {
        amountPaid = total;
        changeAmount = 0;
    }

    var errorEl = document.getElementById('paymentError');
    errorEl.style.display = 'none';

    var btn = document.getElementById('confirmPayBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

    var formData = new FormData();
    formData.append('csrf_token', '<?= csrf_token() ?>');
    formData.append('subtotal', cart.getSubtotal());
    formData.append('tax', cart.getTax());
    formData.append('discount', cart.getDiscount());
    formData.append('total', total);
    formData.append('payment_method', method);
    formData.append('amount_paid', amountPaid);
    formData.append('change_amount', changeAmount);

    for (var i = 0; i < cart.items.length; i++) {
        var item = cart.items[i];
        formData.append('items[' + i + '][product_id]', item.product_id);
        formData.append('items[' + i + '][quantity]', item.quantity);
        formData.append('items[' + i + '][price]', item.price);
        formData.append('items[' + i + '][subtotal]', item.subtotal);
    }

    fetch('<?= url('transactions/store') ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Payment';

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            document.getElementById('successInvoice').textContent = 'Invoice: ' + (data.invoice_no || 'N/A');

            document.getElementById('printReceiptBtn').onclick = function() {
                window.open('<?= url('transactions/receipt') ?>/' + data.transaction_id, '_blank');
            };

            document.getElementById('newSaleBtn').onclick = function() {
                cart.clear();
                loadProducts();
                bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
            };

            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        } else {
            document.getElementById('paymentErrorText').textContent = data.message || 'Transaction failed.';
            errorEl.style.display = '';
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Payment';
        document.getElementById('paymentErrorText').textContent = 'Network error. Please try again.';
        errorEl.style.display = '';
    });
}

// Load initial products
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'F2') {
        e.preventDefault();
        document.getElementById('productSearch').focus();
    }
    if (e.key === 'F4' && cart.items.length > 0) {
        e.preventDefault();
        openPaymentModal();
    }
});
</script>
