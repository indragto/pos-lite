<div class="page-header">
    <div><h1><i class="fas fa-sliders-h text-primary me-2"></i>Accounting Settings</h1><p>Configure defaults and preferences</p></div>
</div>

<?php if (!setting('opening_balance_done')): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i><strong>Opening Balance Required!</strong> You must set the opening balance before creating transactions or journal entries.<br>
<a href="<?= url('accounting/opening-balance') ?>" class="btn btn-warning btn-sm mt-2"><i class="fas fa-plus"></i>Set Opening Balance</a></div>
<?php else: ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i>Opening balance has been set.</div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card"><div class="card-body">
            <form method="post" action="<?= url('accounting/settings/update') ?>"><?= csrf_field() ?>
                <div class="mb-4 p-3" style="background:var(--gray-50);border-radius:var(--radius)">
                    <div class="form-check form-switch">
                        <input type="hidden" name="auto_post_journal" value="0">
                        <input type="checkbox" name="auto_post_journal" class="form-check-input" id="autoPost" value="1" <?= (setting('auto_post_journal') == '1') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="autoPost">Auto-post journal from POS transactions</label>
                    </div>
                </div>
                <h6 class="fw-bold mb-3"><i class="fas fa-sitemap text-primary me-2"></i>Default Accounts</h6>
                <div class="row g-3">
                    <?php $defaults = ['default_sales_account' => 'Sales Revenue','default_cogs_account' => 'COGS','default_tax_account' => 'Tax Payable','default_cash_account' => 'Cash/Bank','default_inventory_account' => 'Inventory'];
                    foreach ($defaults as $key => $label): ?>
                    <div class="col-md-6"><label class="form-label"><?= $label ?></label>
                        <select name="<?= $key ?>" class="form-select"><option value="">-- Select --</option>
                        <?php foreach ($coaOptions as $id => $name): ?><option value="<?= $id ?>" <?= setting($key) == $id ? 'selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?>
                        </select></div>
                    <?php endforeach; ?>
                    <div class="col-md-6"><label class="form-label">Fiscal Year Start</label>
                        <select name="fiscal_year_start" class="form-select">
                        <?php $months = ['1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December'];
                        foreach ($months as $n => $m): ?><option value="<?= $n ?>" <?= setting('fiscal_year_start') == $n ? 'selected' : '' ?>><?= $m ?></option><?php endforeach; ?>
                        </select></div>
                </div>
                <hr><div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Save</button></div>
            </form>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Quick Actions</h6>
            <?php if (!setting('opening_balance_done')): ?>
            <a href="<?= url('accounting/opening-balance') ?>" class="btn btn-warning w-100 mb-2"><i class="fas fa-plus-circle"></i>Set Opening Balance</a>
            <?php endif; ?>
            <a href="<?= url('accounting/closing-journal') ?>" class="btn btn-outline w-100 mb-2"><i class="fas fa-lock"></i>Closing Journal (Tutup Buku)</a>
            <hr>
            <p class="small text-muted mb-0">Default accounts are used for automatic journal entries from POS transactions.</p>
        </div></div>
    </div>
</div>
