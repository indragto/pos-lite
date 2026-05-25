<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Manage your chart of accounts</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('accounting/coa/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>Add Account
        </a>
    </div>
</div>

<div class="toolbar">
    <form method="get" action="<?= url('accounting/coa') ?>" class="d-flex gap-2 flex-wrap align-items-center">
        <select name="type" class="form-select" style="min-width:180px" onchange="this.form.submit()">
            <option value="">All Types</option>
            <?php
            $typeLabels = [
                'asset' => 'Asset',
                'liability' => 'Liability',
                'equity' => 'Equity',
                'revenue' => 'Revenue',
                'expense' => 'Expense',
            ];
            foreach ($typeLabels as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= ($type ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="view-toggle">
            <button type="button" class="btn btn-outline view-toggle-btn <?= ($viewMode ?? 'list') === 'list' ? 'active' : '' ?>" data-view="list" data-container="viewContainer">
                <i class="fas fa-list"></i>
            </button>
            <button type="button" class="btn btn-outline view-toggle-btn <?= ($viewMode ?? 'list') === 'grid' ? 'active' : '' ?>" data-view="grid" data-container="viewContainer">
                <i class="fas fa-th-large"></i>
            </button>
        </div>
        <button type="submit" class="btn btn-outline">
            <i class="fas fa-filter"></i>Filter
        </button>
    </form>
</div>

<div id="viewContainer" class="<?= ($viewMode ?? 'list') === 'grid' ? 'view-grid' : 'view-list' ?>">
    <!-- List View -->
    <div class="view-list">
        <?php if (!empty($accounts)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Parent</th>
                            <th class="text-end">Balance</th>
                            <th style="width:120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td>
                                <span class="fw-bold text-primary"><?= e($account['code']) ?></span>
                                <?php if ((int)($account['level'] ?? 0) > 1): ?>
                                    <span class="text-muted" style="margin-left:<?= (int)($account['level'] ?? 1) * 16 ?>px">
                                        <?php for ($i = 1; $i < (int)($account['level'] ?? 1); $i++): ?>
                                            &nbsp;&nbsp;
                                        <?php endfor; ?>
                                        <i class="fas fa-angle-right text-muted"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($account['name']) ?></td>
                            <td>
                                <?php
                                $typeBadge = match ($account['type'] ?? '') {
                                    'asset' => 'badge-info',
                                    'liability' => 'badge-warning',
                                    'equity' => 'badge-primary',
                                    'revenue' => 'badge-success',
                                    'expense' => 'badge-danger',
                                    default => 'badge-info',
                                };
                                ?>
                                <span class="badge <?= $typeBadge ?>"><?= e(ucfirst($account['type'] ?? '')) ?></span>
                            </td>
                            <td><?= e($account['parent_name'] ?? '-') ?></td>
                            <td class="text-end fw-bold"><?= formatRupiah($account['balance'] ?? 0) ?></td>
                            <td>
                                <div class="btn-group-actions">
                                    <a href="<?= url('accounting/coa/edit/' . ($account['id'] ?? '')) ?>" class="btn btn-outline btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="post" action="<?= url('accounting/coa/delete/' . ($account['id'] ?? '')) ?>" class="d-inline"
                                          onsubmit="return confirm('Delete this account? This action cannot be undone.')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline text-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book"></i>
                <h5>No Accounts Found</h5>
                <p>No chart of accounts match the current filter.</p>
                <a href="<?= url('accounting/coa/create') ?>" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-plus"></i>Add Account
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Grid View -->
    <div class="view-grid">
        <?php if (!empty($accounts)): ?>
            <?php foreach ($accounts as $account): ?>
            <div class="grid-card">
                <h5><span class="text-primary"><?= e($account['code']) ?></span> <?= e($account['name']) ?></h5>
                <p><?= e($account['parent_name'] ?? 'No parent') ?></p>
                <div class="grid-meta">
                    <?php
                    $typeBadge = match ($account['type'] ?? '') {
                        'asset' => 'badge-info',
                        'liability' => 'badge-warning',
                        'equity' => 'badge-primary',
                        'revenue' => 'badge-success',
                        'expense' => 'badge-danger',
                        default => 'badge-info',
                    };
                    ?>
                    <span class="badge <?= $typeBadge ?>"><?= e(ucfirst($account['type'] ?? '')) ?></span>
                    <span class="badge badge-primary"><?= formatRupiah($account['balance'] ?? 0) ?></span>
                </div>
                <div class="grid-actions">
                    <a href="<?= url('accounting/coa/edit/' . ($account['id'] ?? '')) ?>" class="btn btn-outline btn-sm">
                        <i class="fas fa-edit"></i>Edit
                    </a>
                    <form method="post" action="<?= url('accounting/coa/delete/' . ($account['id'] ?? '')) ?>" class="d-inline"
                          onsubmit="return confirm('Delete this account?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline text-danger">
                            <i class="fas fa-trash"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-book"></i>
                    <h5>No Accounts Found</h5>
                    <p>No chart of accounts match the current filter.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
