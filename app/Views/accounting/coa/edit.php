<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Edit account: <?= e($account['code']) ?> - <?= e($account['name']) ?></p>
    </div>
    <a href="<?= url('accounting/coa') ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>Back
    </a>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= url('accounting/coa/update/' . ($account['id'] ?? '')) ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Account Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control"
                                   value="<?= e($account['code'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= e($account['name'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Account Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= e($type) ?>"
                                            <?= ($account['type'] ?? '') === $type ? 'selected' : '' ?>>
                                        <?= e(ucfirst($type)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Parent Account</label>
                            <select name="parent_id" class="form-select">
                                <option value="">-- None (Top Level) --</option>
                                <?php foreach ($parents as $parent): ?>
                                    <option value="<?= $parent['id'] ?>"
                                            <?= ($account['parent_id'] ?? '') == $parent['id'] ? 'selected' : '' ?>>
                                        <?= e($parent['code']) ?> - <?= e($parent['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive"
                                       value="1" <?= ($account['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                            <small class="text-muted">Inactive accounts will not appear in new transactions.</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= url('accounting/coa') ?>" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>Update Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Account Details</h6>
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">Created</td>
                            <td class="text-end"><?= formatDate($account['created_at'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Last Updated</td>
                            <td class="text-end"><?= formatDate($account['updated_at'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Current Balance</td>
                            <td class="text-end fw-bold"><?= formatRupiah($account['balance'] ?? 0) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
