<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">Add Product</h5>
            <p class="text-muted mb-0">Create a new product</p>
        </div>
        <a href="<?= url('products') ?>" class="btn btn-outline-secondary" style="min-height: 44px;">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('products/store') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <!-- SKU -->
                            <div class="col-12 col-md-4">
                                <label for="sku" class="form-label fw-semibold">
                                    <i class="fas fa-barcode me-1 text-primary"></i>SKU
                                </label>
                                <input type="text" class="form-control" id="sku" name="sku"
                                       value="<?= e($autoSKU) ?>" maxlength="50"
                                       pattern="[A-Za-z0-9\-]+" title="Alphanumeric characters and hyphens only">
                                <div class="form-text">Auto-generated, you can edit if needed</div>
                            </div>

                            <!-- Product Name -->
                            <div class="col-12 col-md-8">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="fas fa-box me-1 text-primary"></i>Product Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="Enter product name" required maxlength="200" autofocus>
                            </div>

                            <!-- Category -->
                            <div class="col-12 col-md-6">
                                <label for="category_id" class="form-label fw-semibold">
                                    <i class="fas fa-tags me-1 text-primary"></i>Category
                                </label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Price -->
                            <div class="col-6 col-md-3">
                                <label for="price" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-1 text-success"></i>Price
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="price" name="price"
                                       placeholder="0" required min="0" step="1">
                            </div>

                            <!-- Cost -->
                            <div class="col-6 col-md-3">
                                <label for="cost" class="form-label fw-semibold">
                                    <i class="fas fa-coins me-1 text-warning"></i>Cost
                                </label>
                                <input type="number" class="form-control" id="cost" name="cost"
                                       placeholder="0" min="0" step="1">
                                <div class="form-text">Optional, for profit calculation</div>
                            </div>

                            <!-- Stock -->
                            <div class="col-6 col-md-3">
                                <label for="stock" class="form-label fw-semibold">
                                    <i class="fas fa-warehouse me-1 text-info"></i>Stock Quantity
                                </label>
                                <input type="number" class="form-control" id="stock" name="stock"
                                       value="0" min="0" step="1">
                            </div>

                            <!-- Active Toggle -->
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                           value="1" checked style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="col-12">
                                <label for="image" class="form-label fw-semibold">
                                    <i class="fas fa-image me-1 text-primary"></i>Product Image
                                </label>
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-8">
                                        <input type="file" class="form-control" id="image" name="image"
                                               accept="image/jpeg,image/png,image/webp,image/gif"
                                               onchange="previewImage(this)">
                                        <div class="form-text">Accepted: JPG, PNG, WebP, GIF. Max 2MB.</div>
                                    </div>
                                    <div class="col-12 col-md-4 text-center mt-3 mt-md-0">
                                        <div id="imagePreview" class="border rounded p-2"
                                             style="max-width: 120px; min-height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <span class="text-muted"><i class="fas fa-image fa-2x"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" style="min-height: 48px;">
                                <i class="fas fa-save me-2"></i>Create Product
                            </button>
                            <a href="<?= url('products') ?>" class="btn btn-outline-secondary" style="min-height: 48px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 80px;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
