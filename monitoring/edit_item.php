<?php
// edit_item.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM monitored_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $address = $_POST['address'];
    $timer = intval($_POST['timer']);

    if ($type && $address && $timer > 0) {
        $stmt = $pdo->prepare("UPDATE monitored_items SET type = ?, address = ?, timer = ? WHERE id = ?");
        $stmt->execute([$type, $address, $timer, $id]);
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
    <title>Edit Monitor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Edit Monitor</h3>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="edit_item.php?id=<?= $id ?>">
        <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control" required>
                <option value="IP" <?= $item['type'] == 'IP' ? 'selected' : '' ?>>IP</option>
                <option value="Domain" <?= $item['type'] == 'Domain' ? 'selected' : '' ?>>Domain</option>
            </select>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($item['address']) ?>" required>
        </div>
        <div class="form-group">
            <label>Timer (in seconds)</label>
            <input type="number" name="timer" class="form-control" value="<?= $item['timer'] ?>" required min="1">
        </div>
        <button type="submit" class="btn btn-primary">Update Monitor</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
