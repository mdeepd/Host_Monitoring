<?php
// add_item.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $address = $_POST['address'];
    $timer = intval($_POST['timer']);

    if ($type && $address && $timer > 0) {
        $stmt = $pdo->prepare("INSERT INTO monitored_items (type, address, timer) VALUES (?, ?, ?)");
        $stmt->execute([$type, $address, $timer]);
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Please fill all fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Monitor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Add New Monitor</h3>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="add_item.php">
        <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control" required>
                <option value="IP">IP</option>
                <option value="Domain">Domain</option>
            </select>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Timer (in seconds)</label>
            <input type="number" name="timer" class="form-control" required min="1">
        </div>
        <button type="submit" class="btn btn-success">Add Monitor</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
