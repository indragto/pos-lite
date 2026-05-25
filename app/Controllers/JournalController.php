<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\JournalEntry;
use App\Models\Coa;

class JournalController extends Controller
{
    private JournalEntry $journalModel;

    public function __construct()
    {
        parent::__construct();
        $this->journalModel = new JournalEntry();
    }

    public function index(): void
    {
        $page = (int)($this->input('page') ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $startDate = $this->input('start_date') ?? date('Y-m-01');
        $endDate = $this->input('end_date') ?? date('Y-m-d');
        $search = trim($this->input('search') ?? '');

        $entries = $this->journalModel->getEntries($startDate, $endDate, $search, $perPage, $offset);
        $totalEntries = $this->journalModel->countEntries($startDate, $endDate);
        $totalPages = ceil($totalEntries / $perPage);

        $this->view('accounting/journal/index', [
            'title' => 'Journal Entries',
            'entries' => $entries,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function create(): void
    {
        $coaModel = new Coa();

        $this->view('accounting/journal/create', [
            'title' => 'New Journal Entry',
            'coaOptions' => $coaModel->getLeafOptions(),
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $date = $this->input('date');
        $description = trim($this->input('description'));
        $refType = trim($this->input('reference_type') ?? '');
        $refId = $this->input('reference_id');

        // Parse lines
        $coaIds = $this->input('coa_id') ?? [];
        $debits = $this->input('debit') ?? [];
        $credits = $this->input('credit') ?? [];
        $lineDesc = $this->input('line_description') ?? [];

        $lines = [];
        for ($i = 0; $i < count($coaIds); $i++) {
            if (empty($coaIds[$i])) continue;

            $debit = (float)($debits[$i] ?? 0);
            $credit = (float)($credits[$i] ?? 0);

            if ($debit == 0 && $credit == 0) continue;
            if ($debit > 0 && $credit > 0) {
                $this->setFlash('error', 'Each line can only have Debit OR Credit, not both');
                $this->redirect('accounting/journal/create');
            }

            $lines[] = [
                'coa_id' => (int)$coaIds[$i],
                'debit' => $debit,
                'credit' => $credit,
                'description' => $lineDesc[$i] ?? '',
            ];
        }

        if (empty($lines)) {
            $this->setFlash('error', 'Journal entry must have at least one line');
            $this->redirect('accounting/journal/create');
        }

        $entryData = [
            'date' => $date ?: date('Y-m-d'),
            'description' => $description,
            'reference_type' => $refType ?: null,
            'reference_id' => $refId ?: null,
        ];

        try {
            $entryId = $this->journalModel->createEntry($entryData, $lines, currentUser()['id'] ?? null);
            $this->setFlash('success', 'Journal entry created successfully');
            $this->redirect('accounting/journal');
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('accounting/journal/create');
        }
    }

    public function show(string $id): void
    {
        $entry = $this->journalModel->getEntry((int)$id);
        if (!$entry) {
            $this->setFlash('error', 'Journal entry not found');
            $this->redirect('accounting/journal');
        }

        $this->view('accounting/journal/show', [
            'title' => 'Journal Entry Detail',
            'entry' => $entry,
        ]);
    }

    public function void(string $id): void
    {
        $this->requireCsrf();
        $id = (int)$id;
        $reason = trim($this->input('void_reason') ?? '');

        if (empty($reason)) {
            $this->setFlash('error', 'Void reason is required');
            $this->redirect("accounting/journal/{$id}");
        }

        try {
            $this->journalModel->voidEntry($id, $reason);
            $this->setFlash('success', 'Journal entry voided successfully');
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('accounting/journal');
    }
}
