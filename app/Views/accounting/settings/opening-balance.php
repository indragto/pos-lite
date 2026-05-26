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
            <table class="table align-middle">
                <thead><tr><th style="width:38%">Account</th><th style="width:12%">Type</th><th class="text-center" style="width:10%">Normal</th><th class="text-end" style="width:16%">Debit</th><th class="text-end" style="width:16%">Credit</th><th style="width:40px"></th></tr></thead>
                <tbody id="obBody"></tbody>
                <tfoot><tr style="background:var(--gray-50)"><td colspan="3" class="fw-bold">Totals</td><td class="text-end fw-bold text-success" id="obTotalDebit">Rp 0</td><td class="text-end fw-bold text-danger" id="obTotalCredit">Rp 0</td><td></td></tr></tfoot>
            </table>
        </div>

        <button type="button" class="btn btn-outline btn-sm mb-3" id="btnAddOb"><i class="fas fa-plus"></i>Add Account</button>

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
var coaAccounts = [];
<?php
$allCoa = (new \App\Models\Coa())->query("SELECT id, code, name, type FROM coa WHERE type IN ('asset','liability','equity') AND is_active = 1 ORDER BY code ASC");
foreach ($allCoa as $a) {
    $code = htmlspecialchars($a['code'], ENT_QUOTES);
    $name = htmlspecialchars($a['name'], ENT_QUOTES);
    $type = $a['type'];
    $id = (int)$a['id'];
    echo "coaAccounts.push({id:{$id},code:'{$code}',name:'{$name}',type:'{$type}'});\n";
}
?>

function buildOptions() {
    var groups = {asset:[],liability:[],equity:[]};
    coaAccounts.forEach(function(a) { groups[a.type].push(a); });
    var html = '<option value="">-- Select Account --</option>';
    ['asset','liability','equity'].forEach(function(t) {
        if (groups[t].length === 0) return;
        html += '<optgroup label="' + t.charAt(0).toUpperCase() + t.slice(1) + '">';
        groups[t].forEach(function(a) {
            html += '<option value="' + a.id + '" data-type="' + a.type + '">' + a.code + ' - ' + a.name + '</option>';
        });
        html += '</optgroup>';
    });
    return html;
}

var optionsHtml = buildOptions();

function getNormal(t) { return t === 'asset' ? 'debit' : 'credit'; }

function addObRow() {
    var tbody = document.getElementById('obBody');
    if (!tbody) return;
    var row = document.createElement('tr');
    row.className = 'ob-row';

    var sel = '<select name="coa_id[]" class="form-select" onchange="obOnSelect(this)">' + optionsHtml + '</select>';
    row.innerHTML = '<td>' + sel + '</td>' +
        '<td class="ob-type"></td>' +
        '<td class="text-center ob-normal"></td>' +
        '<td><input type="number" name="debit[]" class="form-control text-end ob-d" step="0.01" min="0" value="0" oninput="obCalc()" disabled></td>' +
        '<td><input type="number" name="credit[]" class="form-control text-end ob-c" step="0.01" min="0" value="0" oninput="obCalc()" disabled></td>' +
        '<td><button type="button" class="btn btn-sm btn-ghost text-danger" onclick="obRemove(this)"><i class="fas fa-times"></i></button></td>';

    tbody.appendChild(row);
}

function obRemove(btn) {
    var row = btn.closest('tr');
    if (row) row.remove();
    obCalc();
}

function obOnSelect(sel) {
    var row = sel.closest('tr');
    var opt = sel.options[sel.selectedIndex];
    var type = opt.getAttribute('data-type') || '';
    var normal = getNormal(type);

    var typeCell = row.querySelector('.ob-type');
    var normalCell = row.querySelector('.ob-normal');

    var badgeColors = {asset:'info',liability:'warning',equity:'primary'};
    var normalColors = {debit:'success',credit:'info'};

    typeCell.innerHTML = type ? '<span class="badge badge-' + badgeColors[type] + '">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>' : '';
    normalCell.innerHTML = type ? '<span class="badge badge-' + normalColors[normal] + '">' + normal.toUpperCase() + '</span>' : '';

    var di = row.querySelector('.ob-d');
    var ci = row.querySelector('.ob-c');
    di.disabled = !type;
    ci.disabled = !type;

    if (normal === 'debit') { ci.value = 0; di.focus(); }
    else { di.value = 0; ci.focus(); }

    obCalc();
}

function obCalc() {
    var td = 0, tc = 0;
    document.querySelectorAll('.ob-row').forEach(function(row) {
        td += parseFloat(row.querySelector('.ob-d').value) || 0;
        tc += parseFloat(row.querySelector('.ob-c').value) || 0;
    });

    var fmt = function(v) { return 'Rp ' + v.toLocaleString('id-ID'); };
    document.getElementById('obTotalDebit').textContent = fmt(td);
    document.getElementById('obTotalCredit').textContent = fmt(tc);
    document.getElementById('obDiffDebit').textContent = fmt(td);
    document.getElementById('obDiffCredit').textContent = fmt(tc);

    var diff = Math.abs(td - tc);
    var el = document.getElementById('obDiff');
    el.textContent = fmt(diff);
    el.className = diff < 0.01 ? 'text-success' : 'text-danger';

    var btn = document.getElementById('obSubmit');
    if (btn) btn.disabled = diff >= 0.01;
}

// Add button
document.getElementById('btnAddOb').addEventListener('click', addObRow);

// Initial rows
addObRow();
addObRow();
addObRow();
</script>
