<?php
require_once __DIR__ . '/../includes/bootstrap.php';
session_unset();
session_destroy();
header('Location: /TableCraft_Project/auth/login.php');
exit;
