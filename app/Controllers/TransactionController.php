<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Transaction;
use App\Models\Category;

class TransactionController extends Controller
{
    private Transaction $transactionModel;

    public function __construct()
    {
        parent::__construct();
        $this->transactionModel = new Transaction();
    }

    /**
     * POS Interface
     */
    public function pos(): void
    {
        $categories = $this->model('Category')->getCategories();

        $this->view('transactions/pos', [
            'title' => 'Point of Sale',
            'categories' => $categories,
        ]);
    }

    /**
     * Store new transaction
     */
    public function store(): void
    {
        $this->requireCsrf();

        $input = $this->allInput();

        $items = $input['items'] ?? [];
        $subtotal = (float)($input['subtotal'] ?? 0);
        $tax = (float)($input['tax'] ?? 0);
        $discount = (float)($input['discount'] ?? 0);
        $total = (float)($input['total'] ?? 0);
        $paymentMethod = $input['payment_method'] ?? 'cash';
        $amountPaid = (float)($input['amount_paid'] ?? 0);
        $changeAmount = (float)($input['change_amount'] ?? 0);

        // Validation
        if (empty($items)) {
            $this->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        if ($amountPaid < $total) {
            $this->json(['success' => false, 'message' => 'Insufficient payment amount'], 400);
        }

        if (!in_array($paymentMethod, ['cash', 'card', 'qris'])) {
            $this->json(['success' => false, 'message' => 'Invalid payment method'], 400);
        }

        // Prepare transaction data
        $transactionData = [
            'user_id' => currentUser()['id'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => $paymentMethod,
            'amount_paid' => $amountPaid,
            'change_amount' => $changeAmount,
            'status' => 'completed',
        ];

        // Prepare items
        $transactionItems = [];
        foreach ($items as $item) {
            $transactionItems[] = [
                'product_id' => (int)$item['product_id'],
                'quantity' => (int)$item['quantity'],
                'price' => (float)$item['price'],
                'subtotal' => (float)$item['subtotal'],
            ];
        }

        try {
            $transactionId = $this->transactionModel->createTransaction($transactionData, $transactionItems);

            // Auto-post journal if enabled and opening balance is set
            if (setting('auto_post_journal') === '1') {
                try {
                    $this->autoPostJournal($transactionId, $transactionData, $items);
                } catch (\Exception $e) {
                    error_log('Auto-post journal failed: ' . $e->getMessage());
                    // Don't fail the transaction, just log the error
                }
            }

            $this->json([
                'success' => true,
                'message' => 'Transaction completed successfully',
                'transaction_id' => $transactionId,
                'invoice_no' => generateInvoiceNo(),
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to process transaction: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Auto-post journal from POS transaction
     */
    private function autoPostJournal(int $transactionId, array $transactionData, array $items): void
    {
        $cashAccount = (int)setting('default_cash_account', 0);
        $salesAccount = (int)setting('default_sales_account', 0);
        $taxAccount = (int)setting('default_tax_account', 0);
        $cogsAccount = (int)setting('default_cogs_account', 0);
        $inventoryAccount = (int)setting('default_inventory_account', 0);

        if (!$cashAccount || !$salesAccount) {
            return; // Can't post without default accounts
        }

        $lines = [];

        // Debit: Cash/Bank
        $lines[] = [
            'coa_id' => $cashAccount,
            'debit' => $transactionData['amount_paid'],
            'credit' => 0,
            'description' => 'Payment: ' . ucfirst($transactionData['payment_method']),
        ];

        // Credit: Sales Revenue
        $salesTotal = $transactionData['subtotal'];
        if ($salesTotal > 0) {
            $lines[] = [
                'coa_id' => $salesAccount,
                'debit' => 0,
                'credit' => $salesTotal,
                'description' => 'Sales revenue',
            ];
        }

        // Credit: Tax Payable (if tax > 0)
        $taxAmount = $transactionData['tax'];
        if ($taxAmount > 0 && $taxAccount) {
            $lines[] = [
                'coa_id' => $taxAccount,
                'debit' => 0,
                'credit' => $taxAmount,
                'description' => 'Tax payable',
            ];
        }

        // Debit: COGS & Credit: Inventory (perpetual method)
        if ($cogsAccount && $inventoryAccount) {
            foreach ($items as $item) {
                $productId = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];

                // Get product cost
                $product = $this->model('Product')->find($productId);
                if ($product && $product['cost'] > 0) {
                    $cogsAmount = (float)$product['cost'] * $quantity;

                    $lines[] = [
                        'coa_id' => $cogsAccount,
                        'debit' => $cogsAmount,
                        'credit' => 0,
                        'description' => 'COGS: ' . ($product['name'] ?? '') . ' x' . $quantity,
                    ];

                    $lines[] = [
                        'coa_id' => $inventoryAccount,
                        'debit' => 0,
                        'credit' => $cogsAmount,
                        'description' => 'Inventory reduction: ' . ($product['name'] ?? '') . ' x' . $quantity,
                    ];
                }
            }
        }

        if (count($lines) > 0) {
            $journalModel = new \App\Models\JournalEntry();
            $journalModel->createEntry([
                'date' => date('Y-m-d'),
                'description' => 'POS Sale: ' . generateInvoiceNo(),
                'reference_type' => 'pos_sale',
                'reference_id' => $transactionId,
            ], $lines, currentUser()['id'] ?? null);
        }
    }

    /**
     * Transaction history
     */
    public function index(): void
    {
        $page = (int)($this->input('page') ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $startDate = $this->input('start_date') ?? date('Y-m-01');
        $endDate = $this->input('end_date') ?? date('Y-m-d');
        $userId = $this->input('user_id');
        $userId = $userId !== '' && $userId !== null ? (int)$userId : null;

        $transactions = $this->transactionModel->getTransactionsWithFilters(
            $startDate,
            $endDate,
            $userId,
            'completed',
            't.id DESC',
            $perPage,
            $offset
        );

        $users = $this->model('User')->getUsersWithRole();

        $this->view('transactions/index', [
            'title' => 'Transactions',
            'transactions' => $transactions,
            'users' => $users,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userId' => $userId,
            'page' => $page,
        ]);
    }

    /**
     * Show transaction detail
     */
    public function show(string $id): void
    {
        $transaction = $this->transactionModel->getTransactionWithDetails((int)$id);

        if (!$transaction) {
            $this->setFlash('error', 'Transaction not found');
            $this->redirect('transactions');
        }

        $this->view('transactions/detail', [
            'title' => 'Transaction Detail',
            'transaction' => $transaction,
        ]);
    }

    /**
     * Void transaction
     */
    public function void(string $id): void
    {
        $id = (int)$id;
        $reason = trim($this->input('void_reason') ?? '');

        if (empty($reason)) {
            $this->setFlash('error', 'Void reason is required');
            $this->redirect("transactions/{$id}");
        }

        $success = $this->transactionModel->voidTransaction($id, $reason);

        if ($success) {
            $this->setFlash('success', 'Transaction voided successfully');
        } else {
            $this->setFlash('error', 'Failed to void transaction');
        }

        $this->redirect('transactions');
    }

    /**
     * Receipt view/print
     */
    public function receipt(string $id): void
    {
        $transaction = $this->transactionModel->getTransactionWithDetails((int)$id);

        if (!$transaction) {
            die('Transaction not found');
        }

        $this->viewOnly('transactions/receipt', [
            'transaction' => $transaction,
            'title' => 'Receipt #' . $transaction['invoice_no'],
        ]);
    }

    /**
     * Calculate cart total (AJAX)
     */
    public function calculateCart(): void
    {
        $items = $this->allInput()['items'] ?? [];
        $discountType = $this->input('discount_type') ?? 'fixed';
        $discountValue = (float)($this->input('discount_value') ?? 0);

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
        }

        $taxRate = (float)setting('tax_rate', 0);
        $tax = $subtotal * ($taxRate / 100);

        $discount = 0;
        if ($discountType === 'percentage') {
            $discount = ($subtotal + $tax) * ($discountValue / 100);
        } else {
            $discount = $discountValue;
        }

        $total = $subtotal + $tax - $discount;

        $this->json([
            'success' => true,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_rate' => $taxRate,
            'discount' => $discount,
            'total' => $total,
        ]);
    }
}
