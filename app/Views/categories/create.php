<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">Add Category</h5>
            <p class="text-muted mb-0">Create a new product category</p>
        </div>
        <a href="<?= url('categories') ?>" class="btn btn-outline-secondary" style="min-height: 44px;">
            <i class="fas fa-arrow-left me-2"></i>Back to Categories
        </a>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('categories/store') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i>Category Name
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name"
                                   placeholder="e.g., Makanan, Minuman, Snack" required
                                   maxlength="100" autofocus>
                            <div class="form-text">Enter a unique name for this category</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-primary"></i>Description
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                      placeholder="Optional description of this category"
                                      maxlength="500"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" style="min-height: 48px;">
                                <i class="fas fa-save me-2"></i>Create Category
                            </button>
                            <a href="<?= url('categories') ?>" class="btn btn-outline-secondary" style="min-height: 48px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
