<?php
$u = current_user();
$theme = theme_mode();
$pageTitle = $pageTitle ?? app_name();
?>
<!doctype html>
<html lang="en" data-theme="<?php echo e($theme); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($pageTitle); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/TableCraft_Project/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg tc-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/TableCraft_Project/dashboard.php">
      <span class="brand-badge"><i class="bi bi-grid-1x2-fill"></i><?php echo e(app_name()); ?></span>
    </a>
    <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navBar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navBar">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <?php if ($u): ?>
          <li class="nav-item"><a class="nav-link" href="/TableCraft_Project/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/TableCraft_Project/tables/create.php">Generate Table</a></li>
          <li class="nav-item"><a class="nav-link" href="/TableCraft_Project/custom/category.php">Custom Category</a></li>
          <li class="nav-item"><a class="nav-link" href="/TableCraft_Project/export/help.php">Export Guide</a></li>
          <li class="nav-item ms-lg-2">
            <button class="btn btn-light btn-sm rounded-pill px-3" type="button" id="themeToggle"><i class="bi bi-moon-stars-fill me-1"></i><span class="mode-text">Dark Mode</span></button>
          </li>
          <li class="nav-item ms-lg-2"><a class="btn btn-outline-light btn-sm rounded-pill px-3" href="/TableCraft_Project/auth/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item ms-lg-2"><button class="btn btn-light btn-sm rounded-pill px-3" type="button" id="themeToggle"><i class="bi bi-moon-stars-fill me-1"></i><span class="mode-text">Dark Mode</span></button></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="py-4">
  <div class="container">
    <?php echo render_flash(); ?>
