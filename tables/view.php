<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
$user = current_user();
$id = (int)($_GET['id'] ?? 0);
$table = $pdo ? get_table_record($pdo, (int)$user['id'], $id) : null;
if (!$table) {
    set_flash('danger', 'Table not found.');
    header('Location: /TableCraft_Project/dashboard.php');
    exit;
}
$columns = json_decode($table['columns_json'], true) ?: [];
$rows = json_decode($table['rows_json'], true) ?: [];
$pageTitle = $table['title'] . ' | TableCraft';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="card mb-4">
  <div class="card-body p-4 p-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
      <div>
        <div class="badge badge-soft rounded-pill mb-3"><?php echo e(category_label($pdo, (int)$user['id'], $table['category'])); ?></div>
        <h2 class="fw-bold mb-1"><?php echo e($table['title']); ?></h2>
        <div class="small-muted">Generated on <?php echo e(date('d M Y, h:i A', strtotime($table['created_at']))); ?></div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <a href="/TableCraft_Project/tables/create.php?id=<?php echo (int)$table['id']; ?>" class="btn btn-outline-primary rounded-pill px-4">Edit Table</a>
        <button class="btn btn-primary rounded-pill px-4" onclick="TableCraft.exportPDF('exportArea','<?php echo addslashes($table['title']); ?>')">Download PDF</button>
        <button class="btn btn-outline-primary rounded-pill px-4" onclick="TableCraft.exportPNG('exportArea','<?php echo addslashes($table['title']); ?>')">Download PNG</button>
        <button class="btn btn-outline-primary rounded-pill px-4" onclick="TableCraft.exportDOC('exportArea','<?php echo addslashes($table['title']); ?>')">Download DOC</button>
      </div>
    </div>
  </div>
</div>

<div class="card" id="exportArea">
  <div class="card-body p-4 p-lg-5">
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle mb-0">
        <thead>
          <tr>
            <?php foreach ($columns as $col): ?>
              <th><?php echo e($col); ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <?php foreach ($columns as $col): ?>
                <td><?php echo e($row[$col] ?? ''); ?></td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if (!empty($table['notes'])): ?>
      <div class="mt-4 p-3 rounded-4 border" style="border-color: var(--tc-border) !important;">
        <div class="fw-semibold mb-1">Notes</div>
        <div class="small-muted"><?php echo e($table['notes']); ?></div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
