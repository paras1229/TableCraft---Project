<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
$pageTitle = 'Custom Category | TableCraft';
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($pdo === null) {
        set_flash('danger', 'Database is not connected.');
        header('Location: category.php');
        exit;
    }
    $name = trim($_POST['name'] ?? '');
    $columnsText = trim($_POST['columns'] ?? '');
    $columns = normalize_columns_from_text($columnsText);

    if ($name === '' || count($columns) < 2) {
        set_flash('warning', 'Please provide a category name and at least 2 columns.');
        header('Location: category.php');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO custom_categories (user_id, name, columns_json, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([(int)$user['id'], $name, json_encode($columns, JSON_UNESCAPED_UNICODE)]);
    set_flash('success', 'Custom category created successfully.');
    header('Location: /TableCraft_Project/dashboard.php');
    exit;
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body p-4 p-lg-5">
        <div class="mb-4">
          <h2 class="fw-bold mb-2">Custom Category Editor</h2>
          <p class="small-muted mb-0">Define a reusable category for any table type. Use comma or pipe separated columns.</p>
        </div>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Category name</label>
            <input type="text" name="name" class="form-control" placeholder="Example: Hostel List, Event Attendance, Fees Record" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Columns</label>
            <textarea name="columns" class="form-control" rows="4" placeholder="Column 1, Column 2, Column 3, Column 4" required></textarea>
            <div class="form-text">Write columns separated by comma or pipe. Example: Item | Quantity | Rate | Total</div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary rounded-pill px-4">Save Custom Category</button>
            <a href="/TableCraft_Project/dashboard.php" class="btn btn-outline-primary rounded-pill px-4">Back to Dashboard</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
