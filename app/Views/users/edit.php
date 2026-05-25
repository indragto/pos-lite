<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Update user information</p>
    </div>
    <a href="<?= url('users') ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>Back to Users
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-user-edit text-primary me-2"></i>Edit User</h6>
            </div>
            <div class="card-body">
                <form action="<?= url("users/update/{$user['id']}") ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= e($user['username']) ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               value="<?= e($user['full_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($user['email'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= ($user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                <?= e($role['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr>
                    <p class="text-muted mb-3" style="font-size:13px;">
                        <i class="fas fa-info-circle me-1"></i>Leave password blank to keep current password.
                    </p>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="6">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>Update User
                        </button>
                        <a href="<?= url('users') ?>" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
