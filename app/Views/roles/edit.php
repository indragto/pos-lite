<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Update role details</p>
    </div>
    <a href="<?= url('roles') ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>Back to Roles
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-user-shield text-primary me-2"></i>Role Details</h6>
            </div>
            <div class="card-body">
                <form action="<?= url("roles/update/{$role['id']}") ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= e($role['name']) ?>" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="3" maxlength="500"><?= e($role['description'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>Update Role
                        </button>
                        <a href="<?= url('roles') ?>" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
