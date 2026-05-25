 <?php
/**
 * Transaction Receipt View (Print-friendly)
 * Variables: $title, $transaction, $items
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?= e($transaction['invoice_no']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .receipt-container { padding: 0; }
        }
        body {
            background: #f5f5f5;
            font-family: 'Courier New', monospace;
        }
        .receipt-container {
            max-width: 320px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .receipt-header h4 { font-size: 1.2rem; margin-bottom: 5px; }
        .receipt-header p { font-size: 0.8rem; margin: 2px 0; }
        .receipt-info { font-size: 0.85rem; margin-bottom: 15px; }
        .receipt-info .row { margin-bottom: 4px; }
        .items-table { width: 100%; font-size: 0.85rem; margin-bottom: 15px; }
        .items-table th { border-bottom: 1px dashed #333; padding: 5px 0; text-align: left; font-size: 0.8rem; }
        .items-table td { padding: 6px 0; }
        .items-table .text-end { text-align: right; }
        .items-table .text-center { text-align: center; }
        .totals { border-top: 2px dashed #333; padding-top: 10px; font-size: 0.85rem; }
        .totals .total-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .totals .grand-total { font-size: 1.1rem; font-weight: bold; border-top: 1px dashed #333; padding-top: 8px; margin-top: 8px; }
        .receipt-footer { text-align: center; border-top: 2px dashed #333; padding-top: 15px; margin-top: 15px; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="no-print text-center py-3">
        <button onclick="window.print()" class="btn btn-primary btn-lg" style="min-height: 48px;">
            <i class="fas fa-print me-2"></i>Print Receipt
        </button>
        <a href="<?= url('transactions/' . $transaction['id']) ?>" class="btn btn-outline-secondary btn-lg ms-2" style="min-height: 48px;">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="receipt-container">
        <div class="receipt-header">
            <h4><i class="fas fa-cash-register me-2"></i><?= e(setting('store_name', 'POS System')) ?></h4>
            <?php if (setting('store_address')): ?>
            <p><?= e(setting('store_address')) ?></p>
            <?php endif; ?>
            <?php if (setting('store_phone')): ?>
            <p>Tel: <?= e(setting('store_phone')) ?></p>
            <?php endif; ?>
        </div>

        <div class="receipt-info">
            <div class="row"><span>Invoice:</span><span class="ms-auto fw-bold"><?= e($transaction['invoice_no']) ?></span></div>
            <div class="row"><span>Date:</span><span class="ms-auto"><?= formatDate($transaction['created_at'], 'd/m/Y H:i') ?></span></div>
            <div class="row"><span>Cashier:</span><span class="ms-auto"><?= e($transaction['cashier_name'] ?? '-') ?></span></div>
            <div class="row"><span>Payment:</span><span class="ms-auto"><?= ucfirst($transaction['payment_method']) ?></span></div>
        </div>

        <table class="items-table">
            <thead>
                <tr><th style="width: 45%;">Item</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Total</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['product_name']) ?></td>
                    <td class="text-center"><?= number_format($item['quantity']) ?></td>
                    <td class="text-end"><?= formatRupiah($item['price']) ?></td>
                    <td class="text-end"><?= formatRupiah($item['subtotal']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row"><span>Subtotal:</span><span><?= formatRupiah($transaction['subtotal'] ?? 0) ?></span></div>
            <?php if (!empty($transaction['discount'])): ?>
            <div class="total-row"><span>Discount:</span><span>-<?= formatRupiah($transaction['discount']) ?></span></div>
            <?php endif; ?>
            <div class="total-row grand-total"><span>TOTAL:</span><span><?= formatRupiah($transaction['total']) ?></span></div>
            <?php if ($transaction['payment_method'] === 'cash'): ?>
            <div class="total-row"><span>Paid:</span><span><?= formatRupiah($transaction['amount_paid'] ?? 0) ?></span></div>
            <div class="total-row"><span>Change:</span><span><?= formatRupiah(($transaction['amount_paid'] ?? 0) - $transaction['total']) ?></span></div>
            <?php endif; ?>
        </div>

        <div class="receipt-footer">
            <p class="mb-2">Thank you for your purchase!</p>
            <small class="text-muted">This receipt is computer-generated and does not require a signature.</small>
            <?php if ($transaction['status'] === 'voided'): ?>
            <div class="mt-3 p-2 border border-danger">
                <strong class="text-danger">VOIDED</strong><br><small><?= e($transaction['void_reason'] ?? '') ?></small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>