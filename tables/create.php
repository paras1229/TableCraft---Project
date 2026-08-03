<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
$user = current_user();
$pageTitle = 'Generate Table | TableCraft';

$customCats = $pdo ? fetch_custom_categories($pdo, (int)$user['id']) : [];
$existing = null;
if (!empty($_GET['id']) && $pdo) {
    $existing = get_table_record($pdo, (int)$user['id'], (int)$_GET['id']);
}
$category = $_GET['category'] ?? ($existing['category'] ?? 'student_record');
$categoryOptions = category_list_for_select($customCats);
$columns = table_columns_for_category($pdo, (int)$user['id'], $category);
$rows = [];
$title = $existing['title'] ?? ('New ' . category_label($pdo, (int)$user['id'], $category));
$notes = $existing['notes'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($pdo === null) {
        set_flash('danger', 'Database is not connected.');
        header('Location: create.php');
        exit;
    }
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? 'student_record';
    $notes = trim($_POST['notes'] ?? '');
    $columnsText = trim($_POST['columns_text'] ?? '');
    $columns = $category === 'custom_manual' ? normalize_columns_from_text($columnsText) : table_columns_for_category($pdo, (int)$user['id'], $category);
    $raw = trim($_POST['raw_data'] ?? '');
    $rows = parse_rows($raw, $columns);
    if ($title === '' || !$rows) {
        set_flash('warning', 'Enter a table title and at least one row of data.');
        header('Location: create.php');
        exit;
    }
    $tableId = !empty($_POST['table_id']) ? (int)$_POST['table_id'] : null;
    if ($tableId) {
        $record = get_table_record($pdo, (int)$user['id'], $tableId);
        if (!$record) {
            set_flash('danger', 'Table record not found.');
            header('Location: create.php');
            exit;
        }
    }
    $savedId = save_table_record($pdo, (int)$user['id'], [
        'title' => $title,
        'category' => $category,
        'columns' => $columns,
        'rows' => $rows,
        'notes' => $notes,
        'theme_mode' => theme_mode(),
    ], $tableId);
    set_flash('success', 'Table saved successfully.');
    header('Location: view.php?id=' . $savedId);
    exit;
}

if ($existing) {
    $columns = json_decode($existing['columns_json'], true) ?: $columns;
    $rows = json_decode($existing['rows_json'], true) ?: [];
    $category = $existing['category'];
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="row g-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
          <div>
            <h2 class="fw-bold mb-1"><?php echo $existing ? 'Edit Table' : 'Generate New Table'; ?></h2>
            <div class="small-muted">Choose a category, type your data and preview the final formatted output instantly.</div>
          </div>
          <a href="/TableCraft_Project/dashboard.php" class="btn btn-outline-primary rounded-pill">Back to Dashboard</a>
        </div>

        <form method="post" id="tableForm" class="row g-3">
          <input type="hidden" name="table_id" value="<?php echo (int)($existing['id'] ?? 0); ?>">
          <div class="col-lg-4">
            <label class="form-label">Table Title</label>
            <input type="text" name="title" id="tableTitle" class="form-control" value="<?php echo e($title); ?>" required>
          </div>
          <div class="col-lg-4">
            <label class="form-label">Category</label>
            <select name="category" id="categorySelect" class="form-select" required>
              <?php foreach ($categoryOptions as $key => $label): ?>
                <option value="<?php echo e($key); ?>" <?php echo $key === $category ? 'selected' : ''; ?>><?php echo e($label); ?></option>
              <?php endforeach; ?>
              <option value="custom_manual">Custom Manual Columns</option>
            </select>
          </div>
          <div class="col-lg-4">
            <label class="form-label">Theme Mode</label>
            <input type="text" class="form-control" value="<?php echo e(theme_mode()); ?>" disabled>
          </div>

          <div class="col-12" id="customColumnsWrap" style="display:none;">
            <label class="form-label">Custom Columns</label>
            <input type="text" id="customColumns" name="columns_text" class="form-control" placeholder="Example: Name | Subject | Marks | Grade">
            <div class="form-text">Only used when "Custom Manual Columns" is selected.</div>
          </div>

          <div class="col-12">
            <label class="form-label">Data Input</label>
            <textarea name="raw_data" id="rawData" class="form-control" rows="7" placeholder="<?php echo e(implode(' | ', $columns)); ?>" required><?php
                if ($rows) {
                    foreach ($rows as $r) {
                        echo e(implode(' | ', array_values($r))) . "\n";
                    }
                }
            ?></textarea>
            <div class="form-text">Type one row per line. Separate values using pipe (|), comma or tab. Example: 101 | Rahul | BCA | Sem 6 | 9876543210</div>
          </div>

          <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Optional note for the project book or presentation"><?php echo e($notes); ?></textarea>
          </div>

          <div class="col-12 d-flex flex-wrap gap-2">
            <button class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-1"></i><?php echo $existing ? 'Update Table' : 'Save Table'; ?></button>
            <button type="button" id="previewBtn" class="btn btn-outline-primary rounded-pill px-4"><i class="bi bi-eye me-1"></i>Preview Table</button>
            <a href="/TableCraft_Project/custom/category.php" class="btn btn-outline-primary rounded-pill px-4">Create Custom Category</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h3 class="fw-bold mb-1">Live Preview</h3>
            <div class="small-muted">Use edit and delete actions on rows before saving the final table.</div>
          </div>
          <div class="small-muted">Columns: <span id="columnsText"><?php echo e(implode(', ', $columns)); ?></span></div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle mb-0" id="previewTable">
            <thead>
              <tr>
                <?php foreach ($columns as $column): ?>
                  <th><?php echo e($column); ?></th>
                <?php endforeach; ?>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $index => $row): ?>
                <tr data-index="<?php echo $index; ?>">
                  <?php foreach ($columns as $column): ?>
                    <td><?php echo e($row[$column] ?? ''); ?></td>
                  <?php endforeach; ?>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-primary js-edit-row me-1">Edit</button>
                    <button type="button" class="btn btn-sm btn-outline-danger js-delete-row">Delete</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="mt-3 small-muted">Tip: use the same column order while typing each row. The preview updates in the browser.</div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rowModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Row</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3" id="rowFields"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveRowBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>

<script>
const initialColumns = <?php echo json_encode(array_values($columns)); ?>;
let rows = <?php echo json_encode(array_values($rows), JSON_UNESCAPED_UNICODE); ?>;
let currentEditIndex = null;

function buildTable() {
  const table = document.getElementById('previewTable');
  const tbody = table.querySelector('tbody');
  const head = table.querySelector('thead tr');
  head.innerHTML = initialColumns.map(c => `<th>${escapeHtml(c)}</th>`).join('') + '<th class="text-end">Actions</th>';
  tbody.innerHTML = rows.map((row, index) => {
    const cells = initialColumns.map(c => `<td>${escapeHtml(row[c] || '')}</td>`).join('');
    return `<tr data-index="${index}">${cells}<td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary js-edit-row me-1">Edit</button><button type="button" class="btn btn-sm btn-outline-danger js-delete-row">Delete</button></td></tr>`;
  }).join('');
  bindActions();
}

function bindActions() {
  document.querySelectorAll('.js-edit-row').forEach(btn => btn.addEventListener('click', function() {
    const tr = this.closest('tr');
    currentEditIndex = parseInt(tr.dataset.index, 10);
    const row = rows[currentEditIndex];
    const fields = document.getElementById('rowFields');
    fields.innerHTML = initialColumns.map(c => `
      <div class="col-md-6">
        <label class="form-label">${escapeHtml(c)}</label>
        <input type="text" class="form-control edit-input" data-column="${escapeHtml(c)}" value="${escapeAttr(row[c] || '')}">
      </div>
    `).join('');
    new bootstrap.Modal(document.getElementById('rowModal')).show();
  }));
  document.querySelectorAll('.js-delete-row').forEach(btn => btn.addEventListener('click', function() {
    const tr = this.closest('tr');
    const idx = parseInt(tr.dataset.index, 10);
    rows.splice(idx, 1);
    buildTable();
  }));
}

document.getElementById('saveRowBtn').addEventListener('click', function() {
  const inputs = document.querySelectorAll('.edit-input');
  const updated = {};
  inputs.forEach(i => updated[i.dataset.column] = i.value.trim());
  rows[currentEditIndex] = updated;
  bootstrap.Modal.getInstance(document.getElementById('rowModal')).hide();
  buildTable();
});

document.getElementById('previewBtn').addEventListener('click', function() {
  const raw = document.getElementById('rawData').value.trim();
  const category = document.getElementById('categorySelect').value;
  const isCustom = category === 'custom_manual';
  const columns = isCustom
    ? (document.getElementById('customColumns').value.split(/\s*\|\s*|\s*,\s*/).map(v => v.trim()).filter(Boolean))
    : initialColumns;
  if (isCustom && columns.length < 2) {
    alert('Please enter at least two custom columns.');
    return;
  }
  rows = TableCraft.parseRows(raw, columns);
  initialColumns.length = 0;
  columns.forEach(c => initialColumns.push(c));
  document.getElementById('columnsText').textContent = columns.join(', ');
  buildTable();
});

const categorySelect = document.getElementById('categorySelect');
const customColumnsWrap = document.getElementById('customColumnsWrap');
categorySelect.addEventListener('change', function() {
  customColumnsWrap.style.display = this.value === 'custom_manual' ? 'block' : 'none';
  if (this.value !== 'custom_manual') {
    document.getElementById('columnsText').textContent = '<?php echo e(implode(', ', $columns)); ?>';
  }
});

bindActions();

function escapeHtml(value){return String(value).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;')}
function escapeAttr(value){return String(value).replace(/"/g,'&quot;')}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
