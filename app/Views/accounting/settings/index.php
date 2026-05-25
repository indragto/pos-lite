<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Configure accounting defaults and preferences</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= url('accounting/settings/update') ?>">
                    <?= csrf_field() ?>

                    <!-- Auto-post Setting -->
                    <div class="mb-4 p-3 rounded" style="background: var(--gray-50);">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="auto_post_pos" class="form-check-input" id="autoPostPos"
                                   value="1" <?= ($settings['auto_post_pos'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="autoPostPos">
                                Auto-post journal entries from POS transactions
                            </label>
                        </div>
                        <small class="text-muted">When enabled, each POS transaction will automatically create a posted journal entry.</small>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3"><i class="fas fa-cog text-primary me-2"></i>Default Accounts</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Default Sales Account</label>
                            <select name="default_sales_account" class="form-select">
                                <option value="">-- Select Account --</option>
                                <?php foreach ($coaOptions as $option): ?>
                                    <option value="<?= $option['id'] ?>"
                                            <?= ($settings['default_sales_account'] ?? '') == $option['id'] ? 'selected' : '' ?>>
                                        <?= e($option['code']) ?> - <?= e($option['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Used for sales revenue journal entries.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Default COGS Account</label>
                            <select name="default_cogs_account" class="form-select">
                                <option value="">-- Select Account --</option>
                                <?php foreach ($coaOptions as $option): ?>
                                    <option value="<?= $option['id'] ?>"
                                            <?= ($settings['default_cogs_account'] ?? '') == $option['id'] ? 'selected' : '' ?>>
                                        <?= e($option['code']) ?> - <?= e($option['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Used for cost of goods sold entries.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Default Tax Account</label>
                            <select name="default_tax_account" class="form-select">
                                <option value="">-- Select Account --</option>
                                <?php foreach ($coaOptions as $option): ?>
                                    <option value="<?= $option['id'] ?>"
                                            <?= ($settings['default_tax_account'] ?? '') == $option['id'] ? 'selected' : '' ?>>
                                        <?= e($option['code']) ?> - <?= e($option['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Used for tax collected on sales.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Default Cash Account</label>
                            <select name="default_cash_account" class="form-select">
                                <option value="">-- Select Account --</option>
                                <?php foreach ($coaOptions as $option): ?>
                                    <option value="<?= $option['id'] ?>"
                                            <?= ($settings['default_cash_account'] ?? '') == $option['id'] ? 'selected' : '' ?>>
                                        <?= e($option['code']) ?> - <?= e($option['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Used for cash and card payments.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Default Inventory Account</label>
                            <select name="default_inventory_account" class="form-select">
                                <option value="">-- Select Account --</option>
                                <?php foreach ($coaOptions as $option): ?>
                                    <option value="<?= $option['id'] ?>"
                                            <?= ($settings['default_inventory_account'] ?? '') == $option['id'] ? 'selected' : '' ?>>
                                        <?= e($option['code']) ?> - <?= e($option['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Used for inventory valuation adjustments.</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fiscal Year Start Month</label>
                            <select name="fiscal_year_start" class="form-select">
                                <?php
                                $months = [
                                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                                ];
                                foreach ($months as $num => $name): ?>
                                    <option value="<?= $num ?>"
                                            <?= ($settings['fiscal_year_start'] ?? 1) == $num ? 'selected' : '' ?>>
                                        <?= e($name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">The month your fiscal year begins.</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Help</h6>
                <p class="small text-muted mb-2">
                    Default accounts are used when the system creates automatic journal entries from POS transactions.
                    Make sure these accounts exist in your Chart of Accounts before enabling auto-posting.
                </p>
                <p class="small text-muted mb-0">
                    <strong>Sales Account:</strong> Revenue from product sales<br>
                    <strong>COGS Account:</strong> Cost of goods sold expense<br>
                    <strong>Tax Account:</strong> Tax liability collected<br>
                    <strong>Cash Account:</strong> Cash and bank assets<br>
                    <strong>Inventory Account:</strong> Inventory asset value
                </p>
            </div>
        </div>
    </div>
</div>
