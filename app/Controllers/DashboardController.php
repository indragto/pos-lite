<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Transaction;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(): void
    {
        $transactionModel = new Transaction();
        $productModel = new Product();

        $today = date('Y-m-d');
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $startOfMonth = date('Y-m-01');

        // Today's summary
        $todaySummary = $transactionModel->getSalesSummary($today, $today);

        // This week summary
        $weekSummary = $transactionModel->getSalesSummary($startOfWeek, $today);

        // This month summary
        $monthSummary = $transactionModel->getSalesSummary($startOfMonth, $today);

        // Recent transactions
        $recentTransactions = $transactionModel->getRecentTransactions(10);

        // Low stock products
        $lowStockProducts = $productModel->getLowStock(10);

        // Top products today
        $topProducts = $transactionModel->getTopProducts($today, $today, 5);

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'todaySummary' => $todaySummary,
            'weekSummary' => $weekSummary,
            'monthSummary' => $monthSummary,
            'recentTransactions' => $recentTransactions,
            'lowStockProducts' => $lowStockProducts,
            'topProducts' => $topProducts,
        ]);
    }
}
