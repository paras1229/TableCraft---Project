<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (!empty($_SESSION['user'])) {
    header('Location: /TableCraft_Project/dashboard.php');
} else {
    header('Location: /TableCraft_Project/auth/login.php');
}
exit;
