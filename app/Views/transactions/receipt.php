<?php
$items = $transaction['items'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — <?= e($transaction['invoice_no']) ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', monospace;
            background: #f0f0f0;
            padding: 16px;
        }
        .receipt {
            max-width: 80mm;
            margin: 0 auto;
            background: #fff;
            padding: 16px 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .receipt-header { text-align: center; border-bottom: 1px dashed #333; padding-bottom: 12px; margin-bottom: 12px; }
        .receipt-header h4 { font-size: 16px; margin-bottom: 4px; }
        .receipt-header p { font-size: 11px; margin: 2px 0; color: #555; }
        .receipt-meta { font-size: 11px; margin-bottom: 12px; }
        .receipt-meta div { display: flex; justify-content: space-between; margin-bottom: 2px; }
        table.items { width: 100%; font-size: 11px; border-collapse: collapse; margin-bottom: 12px; }
        table.items th { text-align: left; border-bottom: 1px dashed #333; padding: 4px 0; font-size: 10px; }
        table.items td { padding: 4px 0; vertical-align: top; }
        table.items .num { text-align: right; }
        table.items .ctr { text-align: center; }
        .totals { border-top: 1px dashed #333; padding-top: 8px; font-size: 11px; }
        .totals div { display: flex; justify-content: space-between; margin-bottom: 2px; }
        .totals .grand { font-size: 14px; font-weight: bold; border-top: 1px solid #333; padding-top: 6px; margin-top: 6px; }
        .receipt-footer { text-align: center; border-top: 1px dashed #333; padding-top: 12px; margin-top: 12px; font-size: 11px; }
        .void-stamp { color: #e63946; font-weight: bold; font-size: 14px; border: 2px solid #e63946; padding: 4px 12px; display: inline-block; margin: 8px 0; }
        .no-print { text-align: center; margin-bottom: 16px; }
        .no-print button, .no-print a {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 18px; font-size: 14px; font-weight: 600;
            border-radius: 8px; border: 1.5px solid transparent; cursor: pointer;
            text-decoration: none; min-height: 44px;
        }
        .no-print .btn-print { background: #4361ee; color: #fff; }
        .no-print .btn-back { background: #fff; color: #333; border-color: #ccc; margin-left: 8px; }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { box-shadow: none; padding: 8px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i>Print Receipt
        </button>
        <a href="<?= url("transactions/{$transaction['id']}") ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i>Back
        </a>
    </div>

    <div class="receipt">
        <div class="receipt-header">
            <h4><?= e(setting('store_name', 'POS System')) ?></h4>
            <?php if (setting('store_address')): ?>
            <p><?= e(setting('store_address')) ?></p>
            <?php endif; ?>
            <?php if (setting('store_phone')): ?>
            <p>Tel: <?= e(setting('store_phone')) ?></p>
            <?php endif; ?>
        </div>

        <div class="receipt-meta">
            <div><span>Invoice:</span><span><strong><?= e($transaction['invoice_no']) ?></strong></span></div>
            <div><span>Date:</span><span><?= formatDate($transaction['created_at'], 'd/m/Y H:i') ?></span></div>
            <div><span>Cashier:</span><span><?= e($transaction['cashier_name'] ?? '—') ?></span></div>
            <div><span>Payment:</span><span><?= ucfirst($transaction['payment_method']) ?></span></div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:45%;">Item</th>
                    <th class="ctr">Qty</th>
                    <th class="num">Price</th>
                    <th class="num">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['product_name']) ?></td>
                    <td class="ctr"><?= number_format($item['quantity']) ?></td>
                    <td class="num"><?= formatRupiah($item['price']) ?></td>
                    <td class="num"><?= formatRupiah($item['subtotal']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div><span>Subtotal:</span><span><?= formatRupiah($transaction['subtotal'] ?? 0) ?></span></div>
            <?php if (!empty($transaction['tax'])): ?>
            <div><span>Tax:</span><span><?= formatRupiah($transaction['tax']) ?></span></div>
            <?php endif; ?>
            <?php if (!empty($transaction['discount'])): ?>
            <div><span>Discount:</span><span>-<?= formatRupiah($transaction['discount']) ?></span></div>
            <?php endif; ?>
            <div class="grand"><span>TOTAL:</span><span><?= formatRupiah($transaction['total']) ?></span></div>
            <?php if (($transaction['payment_method'] ?? '') === 'cash'): ?>
            <div><span>Paid:</span><span><?= formatRupiah($transaction['amount_paid'] ?? 0) ?></span></div>
            <div><span>Change:</span><span><?= formatRupiah(($transaction['amount_paid'] ?? 0) - ($transaction['total'] ?? 0)) ?></span></div>
            <?php endif; ?>
        </div>

        <div class="receipt-footer">
            <p><?= e(setting('receipt_footer', 'Thank you for your purchase!')) ?></p>
            <?php if (($transaction['status'] ?? '') === 'voided'): ?>
            <div class="void-stamp">VOIDED</div>
            <p style="font-size:10px;"><?= e($transaction['void_reason'] ?? '') ?></p>
            <?php endif; ?>
            <p style="font-size:9px; margin-top:8px; color:#999;">Computer-generated receipt — no signature required</p>
        </div>
    </div>
</body>
</html>
