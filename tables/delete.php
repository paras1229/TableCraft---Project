<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
$user = current_user();
$id = (int)($_GET['id'] ?? 0);
if ($pdo && $id) {
    $stmt = $pdo->prepare("DELETE FROM saved_tables WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, (int)$user['id']]);
    set_flash('success', 'Table deleted successfully.');
}
header('Location: /TableCraft_Project/dashboard.php');
exit;
