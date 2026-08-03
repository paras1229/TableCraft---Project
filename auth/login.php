<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pageTitle = 'Login | TableCraft';

if (!empty($_SESSION['user'])) {
    header('Location: /TableCraft_Project/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($pdo === null) {
        set_flash('danger', 'Database is not connected.');
        header('Location: login.php');
        exit;
    }
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ];
        set_flash('success', 'Welcome back, ' . $user['name'] . '!');
        header('Location: /TableCraft_Project/dashboard.php');
        exit;
    }
    set_flash('danger', 'Invalid email or password.');
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="en" data-theme="<?php echo e(theme_mode()); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($pageTitle); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/TableCraft_Project/assets/css/style.css">
</head>
<body>
<div class="auth-shell">
  <div class="card auth-card bg-anim">
    <div class="row g-0">
      <div class="col-lg-5 p-4 p-lg-5 dashboard-bg position-relative">
        <div class="glow"></div>
        <div class="position-relative h-100 d-flex flex-column justify-content-between">
          <div>
            <div class="brand-badge mb-4"><i class="bi bi-grid-1x2-fill"></i>TableCraft</div>
            <h1 class="fw-black display-6 mb-3">Automated table creation for every category.</h1>
            <p class="opacity-75 mb-4">Turn typed data into beautifully formatted tables with export support, theme switch, custom categories and editing tools.</p>
          </div>
          <div class="canvas-wrap">
            <canvas id="heroCanvas"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-7 p-4 p-lg-5">
        <?php echo render_flash(); ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">Login</h2>
            <div class="small-muted">Access the dashboard and saved tables.</div>
          </div>
          <button class="btn btn-outline-primary rounded-pill" type="button" id="themeToggle"><i class="bi bi-moon-stars-fill me-1"></i><span class="mode-text">Dark Mode</span></button>
        </div>
        <form method="post" class="needs-validation" novalidate>
          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
          </div>
          <button class="btn btn-primary w-100 py-3 rounded-pill fw-semibold">Login</button>
        </form>
        <div class="text-center mt-4 small-muted">
          New user? <a href="/TableCraft_Project/auth/register.php" class="fw-semibold">Create account</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/TableCraft_Project/assets/js/app.js"></script>
</body>
</html>
