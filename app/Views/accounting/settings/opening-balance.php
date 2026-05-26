<div class="page-header">
    <div>
        <h1><i class="fas fa-balance-scale text-primary me-2"></i>Opening Balance (Saldo Awal)</h1>
        <p>Set initial balances for Asset, Liability, and Equity accounts</p>
    </div>
</div>

<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i><strong>One-Time Setup:</strong> Can only be set once. Debit must equal Credit. Only Balance Sheet accounts.</div>

<div class="card mb-3"><div class="card-body py-2">
    <div class="row text-center small text-muted">
        <div class="col"><strong class="text-info">Asset</strong> → Normal: <strong>Debit</strong></div>
        <div class="col"><strong class="text-warning">Liability</strong> → Normal: <strong>Credit</strong></div>
        <div class="col"><strong class="text-primary">Equity</strong> → Normal: <strong>Credit</strong></div>
    </div>
</div></div>

<div class="card"><div class="card-body">
    <form method="post" action="<?= url('accounting/opening-balance/save') ?>" id="obForm"><?= csrf_field() ?>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="<?= date('Y-m-01') ?>" required></div>
        </div>

        <div class="table-responsive mb-3">
            <table class="table align-middle" id="obTable">
                <thead><tr><th style="width:38%">Account</th><th style="width:12%">Type</th><th class="text-center" style="width:10%">Normal</th><th class="text-end" style="width:16%">Debit</th><th class="text-end" style="width:16%">Credit</th><th style="width:40px"></th></tr></thead>
                <tbody id="obBody">
                    <?php
                    // Build account options grouped by type
                    $allCoa = (new \App\Models\Coa())->query("SELECT id, code, name, type FROM coa WHERE type IN ('asset','liability','equity') AND is_active = 1 ORDER BY code ASC");
                    ?>
                </tbody>
                <tfoot><tr style="background:var(--gray-50)"><td colspan="3" class="fw-bold">Totals</td><td class="text-end fw-bold text-success" id="obTotalDebit">Rp 0</td><td class="text-end fw-bold text-danger" id="obTotalCredit">Rp 0</td><td></td></tr></tfoot>
            </table>
        </div>

        <button type="button" class="btn btn-outline btn-sm mb-3" onclick="addObRow()"><i class="fas fa-plus"></i>Add Account</button>

        <div class="row g-3 p-3 mb-3" style="background:var(--gray-50);border-radius:var(--radius)">
            <div class="col-md-4 text-end"><small class="text-muted">Total Debit</small><h4 class="text-success mb-0" id="obDiffDebit">Rp 0</h4></div>
            <div class="col-md-4 text-end"><small class="text-muted">Total Credit</small><h4 class="text-danger mb-0" id="obDiffCredit">Rp 0</h4></div>
            <div class="col-md-4 text-end"><small class="text-muted">Difference</small><h4 id="obDiff" class="text-danger">Rp 0</h4></div>
        </div>

        <div class="d-flex justify-content-end">
            <a href="<?= url('accounting/settings') ?>" class="btn btn-outline me-2">Cancel</a>
            <button type="submit" class="btn btn-primary" id="obSubmit" disabled><i class="fas fa-save"></i>Save Opening Balance</button>
        </div>
    </form>
</div></div>

<script>
// Account options built from PHP
var accountOptions = '<option value="">-- Select Account --</option>';
<?php
$byType = ['asset' => [], 'liability' => [], 'equity' => []];
foreach ($allCoa as $a) { $byType[$a['type']][] = $a; }
foreach ($byType as $t => $accs) {
    if (empty($accs)) continue;
    $label = ucfirst($t);
    echo "accountOptions += '<optgroup label=\"" . $label . "\">';\n";
    foreach ($accs as $a) {
        echo "accountOptions += '<option value=\"" . $a['id'] . "\" data-type=\"" . $a['type'] . "'>" . e($a['code'] . ' - ' . $a['name']) . "</option>';\n";
    }
    echo "accountOptions += '</optgroup>';\n";
}
?>

function getNormal(type) { return type === 'asset' ? 'debit' : 'credit'; }
function typeBadge(t) {
    var c = {asset:'info',liability:'warning',equity:'primary'};
    return '<span class="badge badge-' + c[t] + '">' + t.charAt(0).toUpperCase() + t.slice(1) + '</span>';
}

function addObRow() {
    var tbody = document.getElementById('obBody');
    var row = document.createElement('tr');
    row.className = 'ob-row';
    row.innerHTML = '<td><select name="coa_id[]" class="form-select" onchange="onCoaChange(this)">' + accountOptions + '</select></td>' +
        '<td class="normal-type"></td>' +
        '<td class="text-center normal-label"></td>' +
        '<td><input type="number" name="debit[]" class="form-control text-end ob-debit" step="0.01" min="0" value="0" onchange="obCalc()" disabled></td>' +
        '<td><input type="number" name="credit[]" class="form-control text-end ob-credit" step="0.01" min="0" value="0" onchange="obCalc()" disabled></td>' +
        '<td><button type="button" class="btn btn-sm btn-ghost text-danger" onclick="this.closest(\'tr\').remove();obCalc()"><i class="fas fa-times"></i></button></td>';
    tbody.appendChild(row);
}

function onCoaChange(sel) {
    var row = sel.closest('tr');
    var opt = sel.options[sel.selectedIndex];
    var type = opt.getAttribute('data-type') || '';
    var normal = getNormal(type);
    row.querySelector('.normal-type').innerHTML = type ? typeBadge(type) : '';
    row.querySelector('.normal-label').innerHTML = type ? '<span class="badge badge-' + (normal === 'debit' ? 'success' : 'info') + '">' + normal.toUpperCase() + '</span>' : '';
    var di = row.querySelector('.ob-debit'), ci = row.querySelector('.ob-credit');
    di.disabled = !type; ci.disabled = !type;
    if (normal === 'debit') { ci.value = 0; di.focus(); } else { di.value = 0; ci.focus(); }
    obCalc();
}

function obCalc() {
    var td = 0, tc = 0;
    document.querySelectorAll('.ob-row').forEach(function(row) {
        td += parseFloat(row.querySelector('.ob-debit').value) || 0;
        tc += parseFloat(row.querySelector('.ob-credit').value) || 0;
    });
    document.getElementById('obTotalDebit').textContent = 'Rp ' + td.toLocaleString('id-ID');
    document.getElementById('obTotalCredit').textContent = 'Rp ' + tc.toLocaleString('id-ID');
    document.getElementById('obDiffDebit').textContent = 'Rp ' + td.toLocaleString('id-ID');
    document.getElementById('obDiffCredit').textContent = 'Rp ' + tc.toLocaleString('id-ID');
    var diff = td - tc;
    var el = document.getElementById('obDiff');
    el.textContent = 'Rp ' + Math.abs(diff).toLocaleString('id-ID');
    el.className = Math.abs(diff) < 0.01 ? 'text-success' : 'text-danger';
    document.getElementById('obSubmit').disabled = Math.abs(diff) >= 0.01;
}

// Add 3 initial rows
addObRow(); addObRow(); addObRow();
</script>
