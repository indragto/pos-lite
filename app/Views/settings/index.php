<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Configure your POS system settings</p>
    </div>
</div>

<form action="<?= url('settings/update') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="row g-3 mb-4">
        <!-- Store Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-store text-primary me-2"></i>Store Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="store_name" class="form-label">Store Name</label>
                        <input type="text" class="form-control" id="store_name" name="store_name"
                               value="<?= e($settings['store_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="store_address" class="form-label">Address</label>
                        <textarea class="form-control" id="store_address" name="store_address"
                                  rows="2"><?= e($settings['store_address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="store_phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="store_phone" name="store_phone"
                               value="<?= e($settings['store_phone'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt & Tax -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-receipt text-success me-2"></i>Receipt &amp; Tax</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                            <input type="number" class="form-control" id="tax_rate" name="tax_rate"
                                   value="<?= e($settings['tax_rate'] ?? '0') ?>" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-6">
                            <label for="currency" class="form-label">Currency</label>
                            <input type="text" class="form-control" id="currency" name="currency"
                                   value="<?= e($settings['currency'] ?? 'Rp') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="receipt_footer" class="form-label">Receipt Footer Text</label>
                        <textarea class="form-control" id="receipt_footer" name="receipt_footer"
                                  rows="3"><?= e($settings['receipt_footer'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- System -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-sliders-h text-warning me-2"></i>System</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                        <input type="number" class="form-control" id="session_timeout" name="session_timeout"
                               value="<?= e($settings['session_timeout'] ?? '30') ?>" min="5" max="480">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>Save Settings
        </button>
        <a href="<?= url('dashboard') ?>" class="btn btn-ghost">Cancel</a>
    </div>
</form>
