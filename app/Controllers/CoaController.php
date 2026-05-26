<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Coa;
use App\Models\JournalEntry;

class CoaController extends Controller
{
    private Coa $coaModel;

    public function __construct()
    {
        parent::__construct();
        $this->coaModel = new Coa();
    }

    public function index(): void
    {
        $type = $this->input('type');
        $view = $this->input('view') ?? 'tree';

        if ($type) {
            $accounts = $this->coaModel->query(
                "SELECT c.*, p.name as parent_name,
                 COALESCE((SELECT SUM(jl.debit) - SUM(jl.credit)
                   FROM journal_lines jl
                   INNER JOIN journal_entries je ON jl.entry_id = je.id
                   WHERE jl.coa_id = c.id AND je.status = 'posted'), 0) as balance
                 FROM coa c
                 LEFT JOIN coa p ON c.parent_id = p.id
                 WHERE c.type = :type AND c.is_active = 1 ORDER BY c.code ASC",
                ['type' => $type]
            );
        } else {
            $accounts = $this->coaModel->query(
                "SELECT c.*, p.name as parent_name,
                 COALESCE((SELECT SUM(jl.debit) - SUM(jl.credit)
                   FROM journal_lines jl
                   INNER JOIN journal_entries je ON jl.entry_id = je.id
                   WHERE jl.coa_id = c.id AND je.status = 'posted'), 0) as balance
                 FROM coa c
                 LEFT JOIN coa p ON c.parent_id = p.id
                 WHERE c.is_active = 1 ORDER BY c.code ASC"
            );
        }

        $this->view('accounting/coa/index', [
            'title' => 'Chart of Accounts',
            'accounts' => $accounts,
            'tree' => $this->coaModel->getTree($type ?: null),
            'type' => $type,
            'viewMode' => $view,
        ]);
    }

    public function create(): void
    {
        $this->view('accounting/coa/create', [
            'title' => 'Add Account',
            'types' => ['asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expense'],
            'parents' => $this->coaModel->query("SELECT id, code, name, type FROM coa WHERE is_active = 1 ORDER BY code ASC"),
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $code = trim($this->input('code'));
        $name = trim($this->input('name'));
        $type = $this->input('type');
        $parentId = $this->input('parent_id');
        $isActive = $this->input('is_active') ? 1 : 0;

        if (empty($code) || empty($name) || empty($type)) {
            $this->setFlash('error', 'Code, name, and type are required');
            $this->redirect('accounting/coa/create');
        }

        // Check duplicate code
        $existing = $this->coaModel->queryOne("SELECT id FROM coa WHERE code = :code", ['code' => $code]);
        if ($existing) {
            $this->setFlash('error', 'Account code already exists');
            $this->redirect('accounting/coa/create');
        }

        $this->coaModel->create([
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'parent_id' => $parentId ?: null,
            'is_active' => $isActive,
        ]);

        $this->setFlash('success', 'Account created successfully');
        $this->redirect('accounting/coa');
    }

    public function edit(string $id): void
    {
        $account = $this->coaModel->queryOne(
            "SELECT c.*, p.name as parent_name,
             COALESCE((SELECT SUM(jl.debit) - SUM(jl.credit)
               FROM journal_lines jl
               INNER JOIN journal_entries je ON jl.entry_id = je.id
               WHERE jl.coa_id = c.id AND je.status = 'posted'), 0) as balance
             FROM coa c
             LEFT JOIN coa p ON c.parent_id = p.id
             WHERE c.id = :id",
            ['id' => (int)$id]
        );
        if (!$account) {
            $this->setFlash('error', 'Account not found');
            $this->redirect('accounting/coa');
        }

        $this->view('accounting/coa/edit', [
            'title' => 'Edit Account',
            'account' => $account,
            'types' => ['asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expense'],
            'parents' => $this->coaModel->query("SELECT id, code, name, type FROM coa WHERE is_active = 1 AND id != :id ORDER BY code ASC", ['id' => (int)$id]),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();
        $id = (int)$id;

        $code = trim($this->input('code'));
        $name = trim($this->input('name'));
        $type = $this->input('type');
        $parentId = $this->input('parent_id');
        $isActive = $this->input('is_active') ? 1 : 0;

        if (empty($code) || empty($name) || empty($type)) {
            $this->setFlash('error', 'Code, name, and type are required');
            $this->redirect("accounting/coa/edit/{$id}");
        }

        // Check duplicate code (exclude current)
        $existing = $this->coaModel->queryOne("SELECT id FROM coa WHERE code = :code AND id != :id", ['code' => $code, 'id' => $id]);
        if ($existing) {
            $this->setFlash('error', 'Account code already exists');
            $this->redirect("accounting/coa/edit/{$id}");
        }

        $this->coaModel->update($id, [
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'parent_id' => $parentId ?: null,
            'is_active' => $isActive,
        ]);

        $this->setFlash('success', 'Account updated successfully');
        $this->redirect('accounting/coa');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        $id = (int)$id;

        if ($this->coaModel->hasJournalEntries($id)) {
            $this->setFlash('error', 'Cannot delete account that has journal entries');
            $this->redirect('accounting/coa');
        }

        // Check if has children
        $children = $this->coaModel->getChildren($id);
        if (!empty($children)) {
            $this->setFlash('error', 'Cannot delete account that has child accounts');
            $this->redirect('accounting/coa');
        }

        $this->coaModel->delete($id);
        $this->setFlash('success', 'Account deleted successfully');
        $this->redirect('accounting/coa');
    }
}
