<div class="pos-container" style="height: calc(100vh - 120px);">
    <div class="row g-0 h-100">
        <!-- Left: Product Selection -->
        <div class="col-12 col-lg-7 col-xl-8 product-panel">
            <div class="d-flex flex-column h-100">
                <!-- Search Bar -->
                <div class="p-3 border-bottom bg-white">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control form-control-lg" id="productSearch"
                               placeholder="Search products or scan barcode..." autofocus>
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Category Tabs -->
                <div class="category-tabs p-3 border-bottom bg-white overflow-auto" style="white-space: nowrap;">
                    <button class="btn btn-sm btn-primary category-tab me-2 mb-1 active" data-category="">
                        <i class="fas fa-th me-1"></i>All
                    </button>
                    <?php foreach ($categories as $cat): ?>
                    <button class="btn btn-sm btn-outline-secondary category-tab me-2 mb-1"
                            data-category="<?= $cat['id'] ?>">
                        <?= e($cat['name']) ?>
                    </button>
                    <?php endforeach; ?>
                </div>

                <!-- Product Grid -->
                <div class="product-grid flex-grow-1 overflow-auto p-3" id="productGrid">
                    <div class="row g-2" id="productsContainer">
                        <!-- Products loaded via AJAX -->
                    </div>
                    <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="emptyState" class="text-center py-5 text-muted" style="display: none;">
                        <i class="fas fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                        <p>No products found</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Cart -->
        <div class="col-12 col-lg-5 col-xl-4 cart-panel border-start">
            <div class="d-flex flex-column h-100">
                <!-- Cart Header -->
                <div class="p-3 border-bottom bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-shopping-cart me-2"></i>Current Sale
                        </h6>
                        <button class="btn btn-sm btn-outline-danger" id="clearCartBtn" onclick="cart.clear()">
                            <i class="fas fa-trash me-1"></i>Clear
                        </button>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="cart-items flex-grow-1 overflow-auto p-2" id="cartItems">
                    <div class="text-center py-5 text-muted" id="emptyCart">
                        <i class="fas fa-cart-plus fa-3x mb-3 d-block opacity-50"></i>
                        <p>Cart is empty</p>
                        <small>Tap a product to add it</small>
                    </div>
                </div>

                <!-- Cart Footer -->
                <div class="cart-footer border-top bg-white p-3">
                    <!-- Discount Row -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Subtotal</small>
                        <span class="fw-semibold" id="subtotalDisplay">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Tax (<span id="taxRateDisplay">0</span>%)</small>
                        <span class="fw-semibold" id="taxDisplay">Rp 0</span>
                    </div>

                    <!-- Discount Input -->
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Discount</span>
                        <input type="number" class="form-control" id="discountValue" value="0" min="0" step="1">
                        <select class="form-select" id="discountType" style="max-width: 80px;">
                            <option value="fixed">Rp</option>
                            <option value="percentage">%</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted">Discount</small>
                        <span class="text-danger fw-semibold" id="discountDisplay">- Rp 0</span>
                    </div>

                    <!-- Total -->
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                        <h5 class="mb-0 fw-bold">Total</h5>
                        <h4 class="mb-0 fw-bold text-primary" id="totalDisplay">Rp 0</h4>
                    </div>

                    <!-- Pay Button -->
                    <button class="btn btn-success btn-lg w-100" id="payBtn" onclick="openPaymentModal()"
                            style="min-height: 56px;" disabled>
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
                <h5 class="modal-title"><i class="fas fa-wallet me-2"></i>Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Total Display -->
                <div class="text-center mb-4">
                    <small class="text-muted">Total Amount</small>
                    <h2 class="fw-bold text-primary" id="modalTotal">Rp 0</h2>
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Payment Method</label>
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payCash" value="cash" checked>
                            <label class="btn btn-outline-success w-100 py-3" for="payCash" style="min-height: 72px;">
                                <i class="fas fa-money-bill-wave d-block fa-lg mb-1"></i>
                                <small>Cash</small>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payCard" value="card">
                            <label class="btn btn-outline-primary w-100 py-3" for="payCard" style="min-height: 72px;">
                                <i class="fas fa-credit-card d-block fa-lg mb-1"></i>
                                <small>Card</small>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payQris" value="qris">
                            <label class="btn btn-outline-info w-100 py-3" for="payQris" style="min-height: 72px;">
                                <i class="fas fa-qrcode d-block fa-lg mb-1"></i>
                                <small>QRIS</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Amount Paid (for cash) -->
                <div id="cashPaymentSection">
                    <div class="mb-3">
                        <label for="amountPaid" class="form-label fw-semibold">Amount Paid</label>
                        <input type="number" class="form-control form-control-lg" id="amountPaid"
                               placeholder="Enter amount" min="0" step="1">
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="row g-2 mb-3" id="quickCashButtons">
                        <div class="col-4">
                            <button class="btn btn-outline-secondary w-100 quick-cash" data-amount="50000">50K</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-secondary w-100 quick-cash" data-amount="100000">100K</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-secondary w-100 quick-cash" data-amount="200000">200K</button>
                        </div>
                    </div>

                    <!-- Change -->
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-3">
                        <span class="fw-semibold">Change</span>
                        <span class="h5 mb-0 fw-bold text-success" id="changeDisplay">Rp 0</span>
                    </div>
                </div>

                <!-- Validation Error -->
                <div class="alert alert-danger" id="paymentError" style="display: none;">
                    <i class="fas fa-exclamation-circle me-2"></i><span id="paymentErrorText"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="min-height: 48px;">
                    Cancel
                </button>
                <button type="button" class="btn btn-success flex-grow-1" id="confirmPayBtn" onclick="processPayment()"
                        style="min-height: 48px;">
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
                <h4 class="fw-bold">Transaction Complete!</h4>
                <p class="text-muted" id="successInvoice"></p>
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <button class="btn btn-outline-primary" id="printReceiptBtn">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </button>
                    <button class="btn btn-success" data-bs-dismiss="modal" onclick="cart.clear(); loadProducts();">
                        <i class="fas fa-plus me-1"></i>New Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pos-container { margin: -1rem; }
    .product-panel, .cart-panel { overflow: hidden; }
    .product-grid .product-card {
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.1s;
        min-height: 100px;
        border: 2px solid transparent;
    }
    .product-grid .product-card:active {
        transform: scale(0.97);
    }
    .product-grid .product-card:hover {
        border-color: var(--bs-primary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .product-grid .product-card.out-of-stock {
        opacity: 0.5;
        pointer-events: none;
    }
    .cart-items .cart-item {
        border-bottom: 1px solid #eee;
        padding: 0.75rem 0;
    }
    .cart-items .cart-item:last-child {
        border-bottom: none;
    }
    .qty-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1rem;
    }
    .category-tabs .category-tab {
        min-height: 44px;
        border-radius: 20px;
    }
    .category-tabs .category-tab.active {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: #fff;
    }
    @media (max-width: 991.98px) {
        .pos-container { height: auto; }
        .product-panel { height: 50vh; }
        .cart-panel { height: 50vh; border-top: 2px solid #dee2e6; }
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
        return this.items.reduce((sum, item) => sum + item.subtotal, 0);
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

    render() {
        const container = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        const payBtn = document.getElementById('payBtn');

        if (this.items.length === 0) {
            container.innerHTML = '';
            container.appendChild(emptyCart);
            emptyCart.style.display = '';
            payBtn.disabled = true;
        } else {
            emptyCart.style.display = 'none';
            payBtn.disabled = false;

            let html = '';
            this.items.forEach(item => {
                html += `
                <div class="cart-item" data-product-id="${item.product_id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 me-2">
                            <div class="fw-semibold small">${this.escapeHtml(item.name)}</div>
                            <div class="text-muted small">${this.formatRupiah(item.price)} each</div>
                        </div>
                        <button class="btn btn-sm btn-outline-danger p-1" onclick="cart.remove(${item.product_id})"
                                style="min-width: 32px; min-height: 32px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn btn-sm btn-outline-secondary qty-btn"
                                    onclick="cart.updateQuantity(${item.product_id}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="fw-bold mx-2" style="min-width: 24px; text-align: center;">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline-secondary qty-btn"
                                    onclick="cart.updateQuantity(${item.product_id}, 1)"
                                    ${item.quantity >= item.stock ? 'disabled' : ''}>
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <span class="fw-bold">${this.formatRupiah(item.subtotal)}</span>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        }

        // Update totals
        document.getElementById('subtotalDisplay').textContent = this.formatRupiah(this.getSubtotal());
        document.getElementById('taxRateDisplay').textContent = this.taxRate;
        document.getElementById('taxDisplay').textContent = this.formatRupiah(this.getTax());
        document.getElementById('discountDisplay').textContent = '- ' + this.formatRupiah(this.getDiscount());
        document.getElementById('totalDisplay').textContent = this.formatRupiah(this.getTotal());
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Discount calculation
document.getElementById('discountValue').addEventListener('input', () => cart.render());
document.getElementById('discountType').addEventListener('change', () => cart.render());

// ===== Product Search & Loading =====
let currentCategory = '';
let searchTimeout = null;

function loadProducts(keyword = '', categoryId = '') {
    const container = document.getElementById('productsContainer');
    const loading = document.getElementById('loadingIndicator');
    const empty = document.getElementById('emptyState');

    loading.style.display = '';
    empty.style.display = 'none';

    const params = new URLSearchParams({ q: keyword, category_id: categoryId });

    fetch(`<?= url('products/search') ?>?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        loading.style.display = 'none';
        if (data.success && data.products.length > 0) {
            let html = '';
            data.products.forEach(product => {
                const outOfStock = product.stock <= 0;
                html += `
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="card product-card h-100 ${outOfStock ? 'out-of-stock' : ''}"
                         onclick="${outOfStock ? '' : `cart.add(${JSON.stringify({id: product.id, name: product.name.replace(/'/g, "\\'"), price: product.price, stock: product.stock})}`)">">
                        <div class="card-body p-2 text-center">
                            ${product.image ? `<img src="<?= url('uploads') ?>/${product.image}" class="img-fluid rounded mb-2" style="max-height: 60px; object-fit: cover;">` : '<div class="mb-2"><i class="fas fa-box fa-2x text-muted"></i></div>'}
                            <div class="fw-semibold small text-truncate">${cart.escapeHtml(product.name)}</div>
                            <div class="text-primary fw-bold small">${cart.formatRupiah(product.price)}</div>
                            <div class="badge ${outOfStock ? 'bg-danger' : 'bg-success'} mt-1">${product.stock}</div>
                        </div>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
            empty.style.display = 'none';
        } else {
            container.innerHTML = '';
            empty.style.display = '';
        }
    })
    .catch(() => {
        loading.style.display = 'none';
        empty.style.display = '';
    });
}

// Search input
document.getElementById('productSearch').addEventListener('input', function() {
    const keyword = this.value.trim();
    document.getElementById('clearSearch').style.display = keyword ? '' : 'none';

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadProducts(keyword, currentCategory), 300);
});

document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('productSearch').value = '';
    this.style.display = 'none';
    loadProducts('', currentCategory);
});

// Category tabs
document.querySelectorAll('.category-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.category-tab').forEach(b => {
            b.classList.remove('btn-primary', 'active');
            b.classList.add('btn-outline-secondary');
        });
        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-primary', 'active');

        currentCategory = this.dataset.category;
        loadProducts(document.getElementById('productSearch').value.trim(), currentCategory);
    });
});

// ===== Payment Modal =====
function openPaymentModal() {
    if (cart.items.length === 0) return;

    const total = cart.getTotal();
    document.getElementById('modalTotal').textContent = cart.formatRupiah(total);
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeDisplay').textContent = cart.formatRupiah(0);
    document.getElementById('paymentError').style.display = 'none';

    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

// Amount paid calculation
document.getElementById('amountPaid').addEventListener('input', function() {
    const paid = parseFloat(this.value) || 0;
    const total = cart.getTotal();
    const change = Math.max(0, paid - total);
    document.getElementById('changeDisplay').textContent = cart.formatRupiah(change);
});

// Quick cash buttons
document.querySelectorAll('.quick-cash').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('amountPaid').value = this.dataset.amount;
        document.getElementById('amountPaid').dispatchEvent(new Event('input'));
    });
});

// Payment method toggle
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('cashPaymentSection').style.display =
            this.value === 'cash' ? '' : 'none';
    });
});

// Process payment
function processPayment() {
    const method = document.querySelector('input[name="payment_method"]:checked').value;
    const total = cart.getTotal();
    let amountPaid, changeAmount;

    if (method === 'cash') {
        amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
        if (amountPaid < total) {
            const errorEl = document.getElementById('paymentError');
            document.getElementById('paymentErrorText').textContent = 'Insufficient payment amount';
            errorEl.style.display = '';
            return;
        }
        changeAmount = amountPaid - total;
    } else {
        amountPaid = total;
        changeAmount = 0;
    }

    const errorEl = document.getElementById('paymentError');
    errorEl.style.display = 'none';

    const btn = document.getElementById('confirmPayBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

    // Prepare transaction data
    const formData = new FormData();
    formData.append('csrf_token', '<?= csrf_token() ?>');
    formData.append('subtotal', cart.getSubtotal());
    formData.append('tax', cart.getTax());
    formData.append('discount', cart.getDiscount());
    formData.append('total', total);
    formData.append('payment_method', method);
    formData.append('amount_paid', amountPaid);
    formData.append('change_amount', changeAmount);

    cart.items.forEach((item, index) => {
        formData.append(`items[${index}][product_id]`, item.product_id);
        formData.append(`items[${index}][quantity]`, item.quantity);
        formData.append(`items[${index}][price]`, item.price);
        formData.append(`items[${index}][subtotal]`, item.subtotal);
    });

    fetch('<?= url('transactions/store') ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Payment';

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            document.getElementById('successInvoice').textContent = `Invoice: ${data.invoice_no || 'N/A'}`;

            document.getElementById('printReceiptBtn').onclick = function() {
                window.open(`<?= url('transactions/receipt') ?>/${data.transaction_id}`, '_blank');
            };

            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        } else {
            document.getElementById('paymentErrorText').textContent = data.message || 'Transaction failed';
            errorEl.style.display = '';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Payment';
        document.getElementById('paymentErrorText').textContent = 'Network error. Please try again.';
        errorEl.style.display = '';
    });
}

// Load initial products
document.addEventListener('DOMContentLoaded', () => loadProducts());

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
    if (e.key === 'Escape') {
        document.getElementById('paymentSearch')?.blur();
    }
});
</script>
