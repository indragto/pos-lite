<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>View and manage journal entries</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('accounting/journal/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>New Journal
        </a>
    </div>
</div>

<div class="toolbar">
    <form method="get" action="<?= url('accounting/journal') ?>" class="d-flex gap-2 flex-wrap align-items-center">
        <div>
            <label class="form-label mb-0 small">From</label>
            <input type="date" name="start_date" class="form-control"
                   value="<?= e($startDate ?? '') ?>">
        </div>
        <div>
            <label class="form-label mb-0 small">To</label>
            <input type="date" name="end_date" class="form-control"
                   value="<?= e($endDate ?? '') ?>">
        </div>
        <div>
            <label class="form-label mb-0 small">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Description or reference..."
                   value="<?= e($search ?? '') ?>">
        </div>
        <div class="align-self-end">
            <button type="submit" class="btn btn-outline">
                <i class="fas fa-filter"></i>Filter
            </button>
        </div>
    </form>
</div>

<?php if (!empty($entries)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Entry No</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Reference</th>
                    <th class="text-end">Total Debit</th>
                    <th class="text-end">Total Credit</th>
                    <th>Status</th>
                    <th style="width:120px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                <tr>
                    <td>
                        <a href="<?= url('accounting/journal/' . ($entry['id'] ?? '')) ?>" class="fw-bold text-primary text-decoration-none">
                            <?= e($entry['entry_no'] ?? '') ?>
                        </a>
                    </td>
                    <td><?= formatDate($entry['entry_date'] ?? '', 'd/m/Y') ?></td>
                    <td><?= e(truncate($entry['description'] ?? '', 40)) ?></td>
                    <td>
                        <?php if (!empty($entry['reference_type']) && !empty($entry['reference_id'])): ?>
                            <span class="badge badge-primary"><?= e($entry['reference_type']) ?></span>
                            <small class="text-muted"><?= e($entry['reference_id']) ?></small>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end fw-bold"><?= formatRupiah($entry['total_debit'] ?? 0) ?></td>
                    <td class="text-end fw-bold"><?= formatRupiah($entry['total_credit'] ?? 0) ?></td>
                    <td>
                        <?php
                        $statusBadge = match ($entry['status'] ?? 'draft') {
                            'posted' => 'badge-success',
                            'voided' => 'badge-danger',
                            'draft' => 'badge-warning',
                            default => 'badge-info',
                        };
                        $statusIcon = match ($entry['status'] ?? 'draft') {
                            'posted' => 'fa-check-circle',
                            'voided' => 'fa-times-circle',
                            'draft' => 'fa-pencil-alt',
                            default => 'fa-info-circle',
                        };
                        ?>
                        <span class="badge <?= $statusBadge ?>">
                            <i class="fas <?= $statusIcon ?> me-1"></i><?= e(ucfirst($entry['status'] ?? 'draft')) ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group-actions">
                            <a href="<?= url('accounting/journal/' . ($entry['id'] ?? '')) ?>" class="btn btn-outline btn-sm" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if (($entry['status'] ?? '') === 'posted'): ?>
                                <button type="button" class="btn btn-sm btn-outline text-danger"
                                        onclick="voidEntry(<?= (int)($entry['id'] ?? 0) ?>, '<?= e($entry['entry_no'] ?? '') ?>')"
                                        title="Void">
                                    <i class="fas fa-ban"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                <?php if ($i === ($page ?? 1)): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-book-open"></i>
        <h5>No Journal Entries</h5>
        <p>No journal entries match the current filters.</p>
        <a href="<?= url('accounting/journal/create') ?>" class="btn btn-primary btn-sm mt-3">
            <i class="fas fa-plus"></i>New Journal Entry
        </a>
    </div>
<?php endif; ?>

<!-- Void Confirmation Modal -->
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= url('accounting/journal/void') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-ban text-danger me-2"></i>Void Journal Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to void entry <strong id="voidEntryNo"></strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Reason for voiding..."></textarea>
                    </div>
                    <input type="hidden" name="entry_id" id="voidEntryId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i>Void Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function voidEntry(id, entryNo) {
    document.getElementById('voidEntryId').value = id;
    document.getElementById('voidEntryNo').textContent = entryNo;
    var modal = new bootstrap.Modal(document.getElementById('voidModal'));
    modal.show();
}
</script>
