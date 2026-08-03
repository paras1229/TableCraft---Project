<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$pageTitle = 'Dashboard | TableCraft';
$user = current_user();
$stats = user_stats($pdo, (int)$user['id']);
$recent = $pdo ? fetch_recent_tables($pdo, (int)$user['id'], 6) : [];
$customCategories = $pdo ? fetch_custom_categories($pdo, (int)$user['id']) : [];
$templates = category_templates();
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="row g-4 align-items-stretch">
  <div class="col-12">
    <div class="card dashboard-bg position-relative overflow-hidden">
      <div class="glow"></div>
      <div class="card-body p-4 p-lg-5 position-relative">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
          <div>
            <span class="badge rounded-pill bg-light text-dark mb-3">College Project Demo</span>
            <h1 class="fw-bold display-6 mb-2">Welcome, <?php echo e($user['name']); ?>!</h1>
            <p class="mb-0 opacity-75">Create smart tables, customize their design, and export them instantly in multiple formats.</p>
          </div>
          <div class="text-lg-end">
            <div class="small opacity-75">Logged in as</div>
            <div class="fw-semibold"><?php echo e($user['email']); ?></div>
            <div class="mt-2"><span class="pulse-dot me-2"></span><span class="small">System ready for live demo</span></div>
          </div>
        </div>
        <div class="canvas-wrap mt-4">
          <canvas id="heroCanvas"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card metric h-100">
      <div class="card-body p-4">
        <div class="icon-box mb-3"><i class="bi bi-table fs-4"></i></div>
        <div class="small-muted">Saved Tables</div>
        <div class="display-6 fw-bold counter" data-target="<?php echo (int)$stats['tables']; ?>">0</div>
        <div class="small-muted">All generated tables stored in MySQL.</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card metric h-100">
      <div class="card-body p-4">
        <div class="icon-box mb-3"><i class="bi bi-sliders fs-4"></i></div>
        <div class="small-muted">Custom Categories</div>
        <div class="display-6 fw-bold counter" data-target="<?php echo (int)$stats['custom']; ?>">0</div>
        <div class="small-muted">Create your own column sets anytime.</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card metric h-100">
      <div class="card-body p-4">
        <div class="icon-box mb-3"><i class="bi bi-collection fs-4"></i></div>
        <div class="small-muted">Total Data Rows</div>
        <div class="display-6 fw-bold counter" data-target="<?php echo (int)$stats['rows']; ?>">0</div>
        <div class="small-muted">Rows formatted inside saved tables.</div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
          <div>
            <h3 class="fw-bold mb-1">Popular Categories</h3>
            <div class="small-muted">One-click formats for common college and business data.</div>
          </div>
          <a href="/TableCraft_Project/tables/create.php" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus-circle me-1"></i>Create New Table</a>
        </div>
        <div class="row g-3">
          <?php foreach ($templates as $key => $item): ?>
            <div class="col-md-6 col-xl-4">
              <div class="border rounded-4 p-3 h-100" style="border-color: var(--tc-border) !important;">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div class="icon-box"><i class="bi <?php echo e($item['icon']); ?> fs-4"></i></div>
                  <div>
                    <div class="fw-semibold"><?php echo e($item['label']); ?></div>
                    <div class="small-muted"><?php echo e($item['hint']); ?></div>
                  </div>
                </div>
                <div class="small text-uppercase text-muted fw-semibold">Columns</div>
                <div class="small"><?php echo e(implode(' • ', $item['columns'])); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h3 class="fw-bold mb-1">Recent Tables</h3>
            <div class="small-muted">Quick access to edit, open and export.</div>
          </div>
          <a href="/TableCraft_Project/tables/create.php" class="btn btn-outline-primary rounded-pill">Open Builder</a>
        </div>
        <?php if (!$recent): ?>
          <div class="text-center py-5 small-muted">No tables created yet. Start with a category on the builder page.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead><tr><th>Title</th><th>Category</th><th>Rows</th><th>Updated</th><th class="text-end">Action</th></tr></thead>
              <tbody>
              <?php foreach ($recent as $table): $rows = json_decode($table['rows_json'], true) ?: []; ?>
                <tr>
                  <td class="fw-semibold"><?php echo e($table['title']); ?></td>
                  <td><?php echo e(category_label($pdo, (int)$user['id'], $table['category'])); ?></td>
                  <td><?php echo count($rows); ?></td>
                  <td><?php echo e(date('d M Y', strtotime($table['updated_at']))); ?></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="/TableCraft_Project/tables/view.php?id=<?php echo (int)$table['id']; ?>">Open</a>
                    <a class="btn btn-sm btn-outline-secondary" href="/TableCraft_Project/tables/create.php?id=<?php echo (int)$table['id']; ?>">Edit</a>
                    <a class="btn btn-sm btn-outline-danger" href="/TableCraft_Project/tables/delete.php?id=<?php echo (int)$table['id']; ?>" onclick="return confirm('Delete this table?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h3 class="fw-bold mb-1">Custom Categories</h3>
            <div class="small-muted">Build reusable column profiles for future tables.</div>
          </div>
          <a href="/TableCraft_Project/custom/category.php" class="btn btn-outline-primary rounded-pill">Add New</a>
        </div>
        <?php if (!$customCategories): ?>
          <div class="text-center py-5 small-muted">No custom categories yet. Create one for your next project table.</div>
        <?php else: ?>
          <div class="list-group list-group-flush">
            <?php foreach ($customCategories as $cat): ?>
              <div class="list-group-item d-flex justify-content-between align-items-start px-0">
                <div>
                  <div class="fw-semibold"><?php echo e($cat['name']); ?></div>
                  <div class="small-muted"><?php echo e(implode(' • ', json_decode($cat['columns_json'], true) ?: [])); ?></div>
                </div>
                <a href="/TableCraft_Project/tables/create.php?category=custom_<?php echo (int)$cat['id']; ?>" class="btn btn-sm btn-primary rounded-pill">Use</a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
