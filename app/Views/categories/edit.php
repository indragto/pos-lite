<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Update category information</p>
    </div>
    <a href="<?= url('categories') ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>Back to Categories
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-tag text-primary me-2"></i>Category Details</h6>
            </div>
            <div class="card-body">
                <form action="<?= url("categories/update/{$category['id']}") ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= e($category['name']) ?>"
                               required maxlength="100" autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="4" placeholder="Optional description of this category"
                                  maxlength="500"><?= e($category['description'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>Update Category
                        </button>
                        <a href="<?= url('categories') ?>" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
