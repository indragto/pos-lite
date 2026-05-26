<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Coa;
use App\Models\JournalEntry;

class AccountingReportController extends Controller
{
    private Coa $coaModel;
    private JournalEntry $journalModel;

    public function __construct()
    {
        parent::__construct();
        $this->coaModel = new Coa();
        $this->journalModel = new JournalEntry();
    }

    /**
     * General Ledger
     */
    public function ledger(): void
    {
        $coaId = (int)($this->input('coa_id') ?? 0);
        $startDate = $this->input('start_date') ?? date('Y-m-01');
        $endDate = $this->input('end_date') ?? date('Y-m-d');

        $account = $coaId ? $this->coaModel->find($coaId) : null;
        $accounts = $this->coaModel->getLeafOptions();

        $lines = [];
        if ($coaId && $account) {
            $lines = $this->coaModel->query(
                "SELECT je.id as entry_id, je.entry_no, je.date, je.description as entry_desc, jl.debit, jl.credit, jl.description, jl.coa_id,
                        c.code as coa_code, c.name as coa_name
                 FROM journal_lines jl
                 INNER JOIN journal_entries je ON jl.entry_id = je.id
                 INNER JOIN coa c ON jl.coa_id = c.id
                 WHERE jl.coa_id = :coa_id AND je.status = 'posted'
                 AND je.date BETWEEN :start_date AND :end_date
                 ORDER BY je.date ASC, je.id ASC",
                ['coa_id' => $coaId, 'start_date' => $startDate, 'end_date' => $endDate]
            );

            // Calculate running balance
            $runningBalance = 0;
            foreach ($lines as &$line) {
                $runningBalance += $line['debit'] - $line['credit'];
                $line['balance'] = $runningBalance;
            }
        }

        $this->view('accounting/reports/ledger', [
            'title' => 'General Ledger',
            'account' => $account,
            'accounts' => $accounts,
            'lines' => $lines,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'coaId' => $coaId,
        ]);
    }

    /**
     * Trial Balance
     */
    public function trialBalance(): void
    {
        $endDate = $this->input('end_date') ?? date('Y-m-d');

        $allAccounts = $this->coaModel->getAllWithBalances(null, $endDate);

        // Filter to only accounts with balances
        $accounts = array_filter($allAccounts, function($a) {
            return abs((float)$a['total_debit'] - (float)$a['total_credit']) > 0.01;
        });

        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($accounts as &$a) {
            $balance = (float)$a['total_debit'] - (float)$a['total_credit'];
            if ($balance > 0) {
                $a['debit_balance'] = $balance;
                $a['credit_balance'] = 0;
                $totalDebit += $balance;
            } else {
                $a['debit_balance'] = 0;
                $a['credit_balance'] = abs($balance);
                $totalCredit += abs($balance);
            }
        }

        $this->view('accounting/reports/trial-balance', [
            'title' => 'Trial Balance',
            'accounts' => $accounts,
            'endDate' => $endDate,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
        ]);
    }

    /**
     * Income Statement
     */
    public function incomeStatement(): void
    {
        $startDate = $this->input('start_date') ?? date('Y-m-01');
        $endDate = $this->input('end_date') ?? date('Y-m-d');

        $revenues = $this->coaModel->query(
            "SELECT c.id, c.code, c.name,
             COALESCE(SUM(CASE WHEN je.status = 'posted' AND je.date BETWEEN :start_date AND :end_date THEN jl.credit ELSE 0 END), 0) as total
             FROM coa c
             LEFT JOIN journal_lines jl ON c.id = jl.coa_id
             LEFT JOIN journal_entries je ON jl.entry_id = je.id
             WHERE c.type = 'revenue' AND c.is_active = 1
             GROUP BY c.id ORDER BY c.code ASC",
            ['start_date' => $startDate, 'end_date' => $endDate]
        );

        $expenses = $this->coaModel->query(
            "SELECT c.id, c.code, c.name,
             COALESCE(SUM(CASE WHEN je.status = 'posted' AND je.date BETWEEN :start_date AND :end_date THEN jl.debit ELSE 0 END), 0) as total
             FROM coa c
             LEFT JOIN journal_lines jl ON c.id = jl.coa_id
             LEFT JOIN journal_entries je ON jl.entry_id = je.id
             WHERE c.type = 'expense' AND c.is_active = 1
             GROUP BY c.id ORDER BY c.code ASC",
            ['start_date' => $startDate, 'end_date' => $endDate]
        );

        $totalRevenue = array_sum(array_column($revenues, 'total'));
        $totalExpense = array_sum(array_column($expenses, 'total'));
        $netIncome = $totalRevenue - $totalExpense;

        $this->view('accounting/reports/income-statement', [
            'title' => 'Income Statement',
            'revenues' => $revenues,
            'expenses' => $expenses,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netIncome' => $netIncome,
        ]);
    }

    /**
     * Balance Sheet
     */
    public function balanceSheet(): void
    {
        $endDate = $this->input('end_date') ?? date('Y-m-d');

        $getBalances = function($type) use ($endDate) {
            return $this->coaModel->query(
                "SELECT c.id, c.code, c.name,
                 COALESCE(SUM(CASE WHEN je.status = 'posted' AND je.date <= :end_date THEN jl.debit ELSE 0 END), 0)
                 - COALESCE(SUM(CASE WHEN je.status = 'posted' AND je.date <= :end_date THEN jl.credit ELSE 0 END), 0) as balance
                 FROM coa c
                 LEFT JOIN journal_lines jl ON c.id = jl.coa_id
                 LEFT JOIN journal_entries je ON jl.entry_id = je.id
                 WHERE c.type = :type AND c.is_active = 1
                 GROUP BY c.id ORDER BY c.code ASC",
                ['type' => $type, 'end_date' => $endDate]
            );
        };

        $assets = $getBalances('asset');
        $liabilities = $getBalances('liability');
        $equity = $getBalances('equity');

        // Calculate net income and add to equity
        $netIncome = $this->getNetIncome(null, $endDate);
        $totalAssets = array_sum(array_column($assets, 'balance'));
        $totalLiabilities = array_sum(array_column($liabilities, 'balance'));
        $totalEquity = array_sum(array_column($equity, 'balance')) + $netIncome;

        $this->view('accounting/reports/balance-sheet', [
            'title' => 'Balance Sheet',
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'endDate' => $endDate,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'netIncome' => $netIncome,
        ]);
    }

    /**
     * Cash Flow Statement
     */
    public function cashFlow(): void
    {
        $startDate = $this->input('start_date') ?? date('Y-m-01');
        $endDate = $this->input('end_date') ?? date('Y-m-d');

        // Get cash accounts
        $cashAccounts = $this->coaModel->query(
            "SELECT id, code, name FROM coa WHERE code LIKE '11%' AND is_active = 1"
        );

        $cashFlow = [];
        foreach ($cashAccounts as $cash) {
            $inflows = (float) $this->coaModel->queryOne(
                "SELECT COALESCE(SUM(jl.debit), 0) as total FROM journal_lines jl
                 INNER JOIN journal_entries je ON jl.entry_id = je.id
                 WHERE jl.coa_id = :coa_id AND je.status = 'posted'
                 AND je.date BETWEEN :start_date AND :end_date",
                ['coa_id' => $cash['id'], 'start_date' => $startDate, 'end_date' => $endDate]
            )['total'];

            $outflows = (float) $this->coaModel->queryOne(
                "SELECT COALESCE(SUM(jl.credit), 0) as total FROM journal_lines jl
                 INNER JOIN journal_entries je ON jl.entry_id = je.id
                 WHERE jl.coa_id = :coa_id AND je.status = 'posted'
                 AND je.date BETWEEN :start_date AND :end_date",
                ['coa_id' => $cash['id'], 'start_date' => $startDate, 'end_date' => $endDate]
            )['total'];

            $cashFlow[] = [
                'code' => $cash['code'],
                'name' => $cash['name'],
                'inflows' => $inflows,
                'outflows' => $outflows,
                'net' => $inflows - $outflows,
            ];
        }

        $totalInflows = array_sum(array_column($cashFlow, 'inflows'));
        $totalOutflows = array_sum(array_column($cashFlow, 'outflows'));
        $netCashFlow = $totalInflows - $totalOutflows;

        $this->view('accounting/reports/cash-flow', [
            'title' => 'Cash Flow Statement',
            'cashFlow' => $cashFlow,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalInflows' => $totalInflows,
            'totalOutflows' => $totalOutflows,
            'netCashFlow' => $netCashFlow,
        ]);
    }

    /**
     * Get net income for a period
     */
    private function getNetIncome(?string $startDate, string $endDate): float
    {
        $sql = "SELECT COALESCE(SUM(jl.credit), 0) as total
             FROM journal_lines jl
             INNER JOIN journal_entries je ON jl.entry_id = je.id
             INNER JOIN coa c ON jl.coa_id = c.id
             WHERE c.type = 'revenue' AND je.status = 'posted'
             AND je.date <= :end_date";
        $params = ['end_date' => $endDate];

        if ($startDate) {
            $sql .= " AND je.date >= :start_date";
            $params['start_date'] = $startDate;
        }

        $revenue = (float) $this->coaModel->queryOne($sql, $params)['total'];

        $sql2 = "SELECT COALESCE(SUM(jl.debit), 0) as total
             FROM journal_lines jl
             INNER JOIN journal_entries je ON jl.entry_id = je.id
             INNER JOIN coa c ON jl.coa_id = c.id
             WHERE c.type = 'expense' AND je.status = 'posted'
             AND je.date <= :end_date";
        $params2 = ['end_date' => $endDate];

        if ($startDate) {
            $sql2 .= " AND je.date >= :start_date";
            $params2['start_date'] = $startDate;
        }

        $expense = (float) $this->coaModel->queryOne($sql2, $params2)['total'];

        return $revenue - $expense;
    }

    /**
     * Accounting Settings
     */
    public function settings(): void
    {
        $settings = setting('auto_post_journal') ? [
            'auto_post_journal' => setting('auto_post_journal'),
            'default_sales_account' => setting('default_sales_account'),
            'default_cogs_account' => setting('default_cogs_account'),
            'default_tax_account' => setting('default_tax_account'),
            'default_cash_account' => setting('default_cash_account'),
            'default_inventory_account' => setting('default_inventory_account'),
            'fiscal_year_start' => setting('fiscal_year_start'),
        ] : (new \App\Models\Setting())->getAllSettings();

        $this->view('accounting/settings/index', [
            'title' => 'Accounting Settings',
            'settings' => $settings,
            'coaOptions' => $this->coaModel->getLeafOptions(),
        ]);
    }

    public function updateSettings(): void
    {
        $this->requireCsrf();

        $data = [
            'auto_post_journal' => $this->input('auto_post_journal') ? '1' : '0',
            'default_sales_account' => $this->input('default_sales_account') ?? '',
            'default_cogs_account' => $this->input('default_cogs_account') ?? '',
            'default_tax_account' => $this->input('default_tax_account') ?? '',
            'default_cash_account' => $this->input('default_cash_account') ?? '',
            'default_inventory_account' => $this->input('default_inventory_account') ?? '',
            'fiscal_year_start' => $this->input('fiscal_year_start') ?? '1',
        ];

        (new \App\Models\Setting())->updateMultiple($data);
        $this->setFlash('success', 'Accounting settings updated');
        $this->redirect('accounting/settings');
    }
}
