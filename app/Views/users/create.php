  <?php
/**
 * User Create View
 * Variables: $title, $roles
 */
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-user-plus me-2"></i>Add User</h5>
            <p class="text-muted mb-0">Create a new system user</p>
        </div>
        <a href="<?= url('users') ?>" class="btn btn-outline-secondary" style="min-height: 44px;">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('users/store') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="full_name" class="form-label fw-semibold">
                                <i class="fas fa-user me-1 text-primary"></i>Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="full_name" name="full_name"
                                   value="<?= e(old('full_name', '')) ?>" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">
                                <i class="fas fa-at me-1 text-primary"></i>Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="username" name="username"
                                   value="<?= e(old('username', '')) ?>" required>
                            <div class="form-text">Used for login. Only letters, numbers, and underscores.</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-primary"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= e(old('email', '')) ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-1 text-primary"></i>Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password"
                                       required minlength="6">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="password_confirm" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-1 text-primary"></i>Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password_confirm"
                                       name="password_confirm" required minlength="6">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label fw-semibold">
                                <i class="fas fa-user-shield me-1 text-primary"></i>Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="role_id" name="role_id" required>
                                <option value="">Select a role</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                    <?= e($role['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" <?= old('is_active', '1') ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    <i class="fas fa-check-circle me-1 text-success"></i>Active
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" style="min-height: 48px;">
                                <i class="fas fa-save me-2"></i>Create User
                            </button>
                            <a href="<?= url('users') ?>" class="btn btn-outline-secondary" style="min-height: 48px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>