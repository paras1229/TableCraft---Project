<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pageTitle = 'Register | TableCraft';

if (!empty($_SESSION['user'])) {
    header('Location: /TableCraft_Project/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($pdo === null) {
        set_flash('danger', 'Database is not connected.');
        header('Location: register.php');
        exit;
    }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        set_flash('warning', 'All fields are required.');
        header('Location: register.php');
        exit;
    }
    if ($password !== $confirm) {
        set_flash('warning', 'Password and confirm password do not match.');
        header('Location: register.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        set_flash('warning', 'This email is already registered.');
        header('Location: register.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $hash]);
    set_flash('success', 'Registration successful. Please login.');
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
            <h1 class="fw-black display-6 mb-3">Create your account and start formatting data.</h1>
            <p class="opacity-75 mb-4">Use student records, employee info, inventory tables and custom category templates in one system.</p>
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
            <h2 class="fw-bold mb-1">Register</h2>
            <div class="small-muted">Create a new account for project demo.</div>
          </div>
          <button class="btn btn-outline-primary rounded-pill" type="button" id="themeToggle"><i class="bi bi-moon-stars-fill me-1"></i><span class="mode-text">Dark Mode</span></button>
        </div>
        <form method="post" class="needs-validation" novalidate>
          <div class="mb-3">
            <label class="form-label">Full name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Create password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
          </div>
          <button class="btn btn-primary w-100 py-3 rounded-pill fw-semibold">Create account</button>
        </form>
        <div class="text-center mt-4 small-muted">
          Already registered? <a href="/TableCraft_Project/auth/login.php" class="fw-semibold">Login here</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/TableCraft_Project/assets/js/app.js"></script>
</body>
</html>
