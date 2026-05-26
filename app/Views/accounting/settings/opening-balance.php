<div class="page-header">
    <div><h1><i class="fas fa-balance-scale text-primary me-2"></i>Opening Balance (Saldo Awal)</h1><p>Set initial account balances before starting transactions</p></div>
</div>

<div class="alert alert-info"><i class="fas fa-info-circle"></i><strong>Important:</strong> This can only be done once. Total Debit must equal Total Credit. After saving, you cannot change the opening balance.</div>

<div class="card"><div class="card-body">
    <form method="post" action="<?= url('accounting/opening-balance/save') ?>" id="obForm"><?= csrf_field() ?>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="<?= date('Y-m-01') ?>" required></div>
        </div>

        <div class="table-responsive mb-3">
            <table class="table" id="obTable">
                <thead><tr><th style="width:45%">Account</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th style="width:50px"></th></tr></thead>
                <tbody id="obBody">
                    <tr class="ob-row"><td><select name="coa_id[]" class="form-select" required><option value="">-- Select --</option><?php foreach ($coaOptions as $id => $name): ?><option value="<?= $id ?>"><?= e($name) ?></option><?php endforeach; ?></select></td><td><input type="number" name="debit[]" class="form-control text-end" step="0.01" min="0" value="0" onchange="obCalc()"></td><td><input type="number" name="credit[]" class="form-control text-end" step="0.01" min="0" value="0" onchange="obCalc()"></td><td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();obCalc()"><i class="fas fa-times"></i></button></td></tr>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-outline btn-sm mb-3" onclick="addObRow()"><i class="fas fa-plus"></i>Add Line</button>

        <div class="row g-3 p-3" style="background:var(--gray-50);border-radius:var(--radius)">
            <div class="col-md-4 text-end"><small class="text-muted">Total Debit</small><h4 class="text-success mb-0" id="obTotalDebit">Rp 0</h4></div>
            <div class="col-md-4 text-end"><small class="text-muted">Total Credit</small><h4 class="text-danger mb-0" id="obTotalCredit">Rp 0</h4></div>
            <div class="col-md-4 text-end"><small class="text-muted">Difference</small><h4 id="obDiff">Rp 0</h4></div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="<?= url('accounting/settings') ?>" class="btn btn-outline me-2">Cancel</a>
            <button type="submit" class="btn btn-primary" id="obSubmit" disabled><i class="fas fa-save"></i>Save Opening Balance</button>
        </div>
    </form>
</div></div>

<script>
function obCalc() {
    var td = 0, tc = 0;
    document.querySelectorAll('.ob-row').forEach(function(row) {
        td += parseFloat(row.querySelector('[name="debit[]"]').value) || 0;
        tc += parseFloat(row.querySelector('[name="credit[]"]').value) || 0;
    });
    document.getElementById('obTotalDebit').textContent = 'Rp ' + td.toLocaleString('id-ID');
    document.getElementById('obTotalCredit').textContent = 'Rp ' + tc.toLocaleString('id-ID');
    var diff = td - tc;
    var el = document.getElementById('obDiff');
    el.textContent = 'Rp ' + Math.abs(diff).toLocaleString('id-ID');
    el.style.color = Math.abs(diff) < 0.01 ? 'var(--success)' : 'var(--danger)';
    document.getElementById('obSubmit').disabled = Math.abs(diff) >= 0.01;
}

function addObRow() {
    var tbody = document.getElementById('obBody');
    var row = tbody.rows[0].cloneNode(true);
    row.querySelectorAll('input').forEach(function(i) { i.value = 0; });
    tbody.appendChild(row);
}
</script>
