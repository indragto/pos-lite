<div class="page-header">
    <div>
        <h1>Add Product</h1>
        <p>Create a new product</p>
    </div>
    <a href="<?= url('products') ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= url('products/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-3">
                <!-- SKU -->
                <div class="col-12 col-md-4">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" class="form-control" id="sku" name="sku"
                           value="<?= e($autoSKU) ?>" maxlength="50"
                           pattern="[A-Za-z0-9\-]+" title="Alphanumeric and hyphens only">
                    <div class="text-muted" style="font-size: 12px; margin-top: 4px;">Auto-generated, editable</div>
                </div>

                <!-- Product Name -->
                <div class="col-12 col-md-8">
                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                           placeholder="Enter product name" required maxlength="200" autofocus>
                </div>

                <!-- Category -->
                <div class="col-12 col-md-6">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $id => $name): ?>
                        <option value="<?= $id ?>"><?= e($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Price -->
                <div class="col-6 col-md-3">
                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="price" name="price"
                           placeholder="0" required min="0" step="1">
                </div>

                <!-- Cost -->
                <div class="col-6 col-md-3">
                    <label for="cost" class="form-label">Cost</label>
                    <input type="number" class="form-control" id="cost" name="cost"
                           placeholder="0" min="0" step="1">
                    <div class="text-muted" style="font-size: 12px; margin-top: 4px;">For profit calculation</div>
                </div>

                <!-- Stock -->
                <div class="col-6 col-md-4">
                    <label for="stock" class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" id="stock" name="stock"
                           value="0" min="0" step="1">
                </div>

                <!-- Active Toggle -->
                <div class="col-6 col-md-4">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                               value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="col-12">
                    <label for="image" class="form-label">Product Image</label>
                    <div class="row align-items-center">
                        <div class="col-12 col-md-8">
                            <input type="file" class="form-control" id="image" name="image"
                                   accept="image/jpeg,image/png,image/webp,image/gif"
                                   onchange="previewImage(this)">
                            <div class="text-muted" style="font-size: 12px; margin-top: 4px;">JPG, PNG, WebP, GIF. Max 2MB.</div>
                        </div>
                        <div class="col-12 col-md-4 text-center mt-3 mt-md-0">
                            <div id="imagePreview" class="border rounded d-flex align-items-center justify-content-center"
                                 style="width: 120px; height: 80px; margin: 0 auto;">
                                <i class="fas fa-image fa-2x text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-save"></i>Create Product
                </button>
                <a href="<?= url('products') ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="rounded" style="max-width: 100%; max-height: 80px;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
