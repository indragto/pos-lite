 <?php
/**
 * Settings Index View
 * Variables: $title, $settings
 */
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-cog me-2"></i>Settings</h5>
            <p class="text-muted mb-0">Configure your POS system settings</p>
        </div>
    </div>

    <form action="<?= url('settings/update') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row g-3">
            <!-- Store Information -->
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-store me-2 text-primary"></i>Store Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="store_name" class="form-label fw-semibold">Store Name</label>
                            <input type="text" class="form-control" id="store_name" name="store_name"
                                   value="<?= e($settings['store_name'] ?? 'POS System') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="store_address" class="form-label fw-semibold">Address</label>
                            <textarea class="form-control" id="store_address" name="store_address" rows="2"><?= e($settings['store_address'] ?? '') ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="store_phone" class="form-label fw-semibold">Phone</label>
                                <input type="text" class="form-control" id="store_phone" name="store_phone"
                                       value="<?= e($settings['store_phone'] ?? '') ?>">
                            </div>
                            <div class="col-6">
                                <label for="store_email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="store_email" name="store_email"
                                       value="<?= e($settings['store_email'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt Settings -->
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-receipt me-2 text-success"></i>Receipt Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="receipt_footer" class="form-label fw-semibold">Receipt Footer Text</label>
                            <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="2"><?= e($settings['receipt_footer'] ?? 'Thank you for your purchase!') ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="tax_rate" class="form-label fw-semibold">Tax Rate (%)</label>
                                <input type="number" class="form-control" id="tax_rate" name="tax_rate"
                                       value="<?= e($settings['tax_rate'] ?? '0') ?>" min="0" max="100" step="0.01">
                            </div>
                            <div class="col-6">
                                <label for="invoice_prefix" class="form-label fw-semibold">Invoice Prefix</label>
                                <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix"
                                       value="<?= e($settings['invoice_prefix'] ?? 'INV') ?>" maxlength="5">
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enable_tax" name="enable_tax"
                                       value="1" <?= ($settings['enable_tax'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="enable_tax">Enable Tax on Receipts</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-sliders-h me-2 text-warning"></i>System Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode"
                                           value="1" <?= ($settings['maintenance_mode'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="maintenance_mode">Maintenance Mode</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_negative_stock" name="allow_negative_stock"
                                           value="1" <?= ($settings['allow_negative_stock'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="allow_negative_stock">Allow Negative Stock</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="low_stock_threshold" class="form-label fw-semibold">Low Stock Threshold</label>
                                <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold"
                                       value="<?= e($settings['low_stock_threshold'] ?? '5') ?>" min="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary flex-grow-1" style="min-height: 48px;">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
            <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary" style="min-height: 48px;">
                Cancel
            </a>
        </div>
    </form>
</div>