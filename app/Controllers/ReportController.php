<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Transaction;

class ReportController extends Controller
{
    private Transaction $transactionModel;

    public function __construct()
    {
        parent::__construct();
        $this->transactionModel = new Transaction();
    }

    /**
     * Daily sales report
     */
    public function daily(): void
    {
        $selectedDate = $this->input('date') ?? date('Y-m-d');

        $summary = $this->transactionModel->getSalesSummary($selectedDate, $selectedDate);
        $paymentMethods = $this->transactionModel->getSalesByPaymentMethod($selectedDate, $selectedDate);
        $transactions = $this->transactionModel->getTransactionsWithFilters(
            $selectedDate, $selectedDate, null, 'completed', 't.id DESC', 50
        );

        $this->view('reports/daily', [
            'title' => 'Daily Sales Report',
            'selectedDate' => $selectedDate,
            'summary' => $summary,
            'paymentMethods' => $paymentMethods,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Monthly sales report
     */
    public function monthly(): void
    {
        $selectedMonth = $this->input('month') ?? date('Y-m');
        $startDate = date('Y-m-01', strtotime($selectedMonth . '-01'));
        $endDate = date('Y-m-t', strtotime($selectedMonth . '-01'));

        $summary = $this->transactionModel->getSalesSummary($startDate, $endDate);
        $paymentMethods = $this->transactionModel->getSalesByPaymentMethod($startDate, $endDate);
        $dailySales = $this->transactionModel->getDailySales($startDate, $endDate);

        $this->view('reports/monthly', [
            'title' => 'Monthly Sales Report',
            'selectedMonth' => $selectedMonth,
            'summary' => $summary,
            'paymentMethods' => $paymentMethods,
            'dailySales' => $dailySales,
        ]);
    }

    /**
     * Product sales report
     */
    public function products(): void
    {
        $startDate = $this->input('start_date') ?? date('Y-m-01');
        $endDate = $this->input('end_date') ?? date('Y-m-d');

        $topProducts = $this->transactionModel->getTopProducts($startDate, $endDate, 50);

        $this->view('reports/products', [
            'title' => 'Product Sales Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'topProducts' => $topProducts,
        ]);
    }

    /**
     * Export daily report to CSV
     */
    public function exportDaily(): void
    {
        $selectedDate = $this->input('date') ?? date('Y-m-d');
        $transactions = $this->transactionModel->getTransactionsWithFilters(
            $selectedDate, $selectedDate, null, 'completed'
        );

        $filename = "daily_report_{$selectedDate}.csv";
        $this->outputCSV($filename, $transactions, [
            'Invoice No', 'Date', 'Cashier', 'Subtotal', 'Tax', 'Discount', 'Total', 'Payment Method'
        ], function ($row) {
            return [
                $row['invoice_no'],
                $row['created_at'],
                $row['cashier_name'] ?? '-',
                $row['subtotal'],
                $row['tax'],
                $row['discount'],
                $row['total'],
                ucfirst($row['payment_method']),
            ];
        });
    }

    /**
     * Export monthly report to CSV
     */
    public function exportMonthly(): void
    {
        $selectedMonth = $this->input('month') ?? date('Y-m');
        $startDate = date('Y-m-01', strtotime($selectedMonth . '-01'));
        $endDate = date('Y-m-t', strtotime($selectedMonth . '-01'));

        $dailySales = $this->transactionModel->getDailySales($startDate, $endDate);

        $filename = "monthly_report_{$selectedMonth}.csv";
        $this->outputCSV($filename, $dailySales, [
            'Date', 'Transactions', 'Total Sales'
        ], function ($row) {
            return [
                $row['date'],
                $row['transaction_count'],
                $row['daily_total'],
            ];
        });
    }

    /**
     * Export product sales to CSV
     */
    public function exportProducts(): void
    {
        $startDate = $this->input('start_date') ?? date('Y-m-01');
        $endDate = $this->input('end_date') ?? date('Y-m-d');

        $topProducts = $this->transactionModel->getTopProducts($startDate, $endDate, 100);

        $filename = "product_sales_{$startDate}_{$endDate}.csv";
        $this->outputCSV($filename, $topProducts, [
            'SKU', 'Product', 'Qty Sold', 'Revenue'
        ], function ($row) {
            return [
                $row['sku'],
                $row['product_name'],
                $row['total_qty'],
                $row['total_revenue'],
            ];
        });
    }

    /**
     * AJAX sales summary
     */
    public function summary(): void
    {
        $period = $this->input('period') ?? 'today';

        $today = date('Y-m-d');
        $startDate = match ($period) {
            'today' => $today,
            'week' => date('Y-m-d', strtotime('monday this week')),
            'month' => date('Y-m-01'),
            default => $today,
        };

        $summary = $this->transactionModel->getSalesSummary($startDate, $today);
        $topProducts = $this->transactionModel->getTopProducts($startDate, $today, 5);

        $this->json([
            'success' => true,
            'summary' => $summary,
            'top_products' => $topProducts,
        ]);
    }

    /**
     * Output CSV download
     */
    private function outputCSV(string $filename, array $data, array $headers, callable $transformRow): void
    {
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write headers
        fputcsv($output, $headers);

        // Write data
        foreach ($data as $row) {
            fputcsv($output, $transformRow($row));
        }

        fclose($output);
        exit;
    }
}
