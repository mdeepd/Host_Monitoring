<?php
// delete_item.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM monitored_items WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: dashboard.php");
exit();
?>
