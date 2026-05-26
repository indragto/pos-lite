<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Add a new account to the chart of accounts</p>
    </div>
    <a href="<?= url('accounting/coa') ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>Back
    </a>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= url('accounting/coa/store') ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Account Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. 1010" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Cash on Hand" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Account Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                <?php foreach ($types as $key => $label): ?>
                                    <option value="<?= e($key) ?>"><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Parent Account</label>
                            <select name="parent_id" class="form-select">
                                <option value="">-- None (Top Level) --</option>
                                <?php foreach ($parents as $parent): ?>
                                    <option value="<?= $parent['id'] ?>">
                                        <?= e($parent['code']) ?> - <?= e($parent['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" checked>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                            <small class="text-muted">Inactive accounts will not appear in new transactions.</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= url('accounting/coa') ?>" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>Save Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Account Types</h6>
                <dl class="small mb-0">
                    <dt class="fw-semibold"><span class="badge badge-info me-2">Asset</span></dt>
                    <dd class="mb-2">Resources owned by the business (cash, inventory, receivables)</dd>

                    <dt class="fw-semibold"><span class="badge badge-warning me-2">Liability</span></dt>
                    <dd class="mb-2">Obligations owed to others (payables, loans)</dd>

                    <dt class="fw-semibold"><span class="badge badge-primary me-2">Equity</span></dt>
                    <dd class="mb-2">Owner's residual interest in the business</dd>

                    <dt class="fw-semibold"><span class="badge badge-success me-2">Revenue</span></dt>
                    <dd class="mb-2">Income earned from business operations</dd>

                    <dt class="fw-semibold"><span class="badge badge-danger me-2">Expense</span></dt>
                    <dd class="mb-2">Costs incurred in running the business</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
