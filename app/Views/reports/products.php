<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Product sales performance</p>
    </div>
    <a href="<?= url('reports/products') ?>?start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>&export=1" class="btn btn-outline">
        <i class="fas fa-file-csv"></i>Export CSV
    </a>
</div>

<!-- Toolbar -->
<form method="GET" action="<?= url('reports/products') ?>" class="toolbar mb-4">
    <input type="date" class="form-control" name="start_date" value="<?= e($startDate) ?>">
    <input type="date" class="form-control" name="end_date" value="<?= e($endDate) ?>">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-filter"></i>Filter
    </button>
    <a href="<?= url('reports/products') ?>" class="btn btn-outline">
        <i class="fas fa-undo"></i>Reset
    </a>
</form>

<!-- Products Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th class="text-center">Qty Sold</th>
                    <th class="text-end">Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($topProducts)): ?>
                    <?php $rank = 1; foreach ($topProducts as $product): ?>
                    <?php
                        $rankBadge = match ($rank) {
                            1 => 'badge-warning',
                            2 => 'badge-primary',
                            3 => 'badge-info',
                            default => 'badge-secondary',
                        };
                    ?>
                    <tr>
                        <td><span class="badge <?= $rankBadge ?>"><?= $rank ?></span></td>
                        <td class="fw-bold"><?= e($product['product_name']) ?></td>
                        <td><span class="badge bg-light text-dark"><?= e($product['sku']) ?></span></td>
                        <td class="text-center">
                            <span class="badge badge-success"><?= number_format($product['total_qty']) ?></span>
                        </td>
                        <td class="text-end fw-bold text-success"><?= formatRupiah($product['total_revenue']) ?></td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-chart-bar"></i>
                                <h5>No sales data</h5>
                                <p>No products sold in the selected period</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
