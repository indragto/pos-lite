  <?php
/**
 * Role Edit View
 * Variables: $title, $role
 */
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-user-shield me-2"></i>Edit Role</h5>
            <p class="text-muted mb-0">Update role details</p>
        </div>
        <a href="<?= url('roles') ?>" class="btn btn-outline-secondary" style="min-height: 44px;">
            <i class="fas fa-arrow-left me-2"></i>Back to Roles
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('roles/update/' . $role['id']) ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i>Role Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name"
                                   value="<?= e(old('name', $role['name'])) ?>" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-primary"></i>Description
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      maxlength="500"><?= e(old('description', $role['description'] ?? '')) ?></textarea>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default"
                                       value="1" <?= ($role['is_default'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="is_default">
                                    <i class="fas fa-star me-1 text-warning"></i>Default Role
                                </label>
                            </div>
                            <div class="form-text">New users will automatically receive this role</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" style="min-height: 48px;">
                                <i class="fas fa-save me-2"></i>Update Role
                            </button>
                            <a href="<?= url('roles') ?>" class="btn btn-outline-secondary" style="min-height: 48px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>