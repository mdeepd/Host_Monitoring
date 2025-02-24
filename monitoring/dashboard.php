<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Fetch all monitored items from the database
$stmt = $pdo->query("SELECT * FROM monitored_items");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Monitoring Dashboard</a>
  <div class="ml-auto">
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
  </div>
</nav>
<div class="container mt-4">
    <div class="mb-3">
        <a href="add_item.php" class="btn btn-success">Add New Monitor</a>
    </div>
    <table class="table table-bordered" id="monitorTable">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Address</th>
                <th>Timer (sec)</th>
                <th>Status</th>
                <th>Latency (ms)</th>
                <th>Last Checked</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr id="item-<?= $item['id'] ?>">
                <td><?= $item['id'] ?></td>
                <td><?= $item['type'] ?></td>
                <td><?= htmlspecialchars($item['address']) ?></td>
                <td><?= $item['timer'] ?></td>
                <td class="status"><?= $item['status'] ?></td>
                <td class="latency"><?= $item['latency'] ?></td>
                <td class="last_checked"><?= $item['last_checked'] ?></td>
                <td>
                    <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="delete_item.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- jQuery for AJAX calls -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
// For each monitored item, set an interval to call the check_status endpoint
<?php foreach ($items as $item): ?>
    setInterval(function(){
        $.ajax({
            url: 'check_status.php',
            method: 'POST',
            data: { id: <?= $item['id'] ?> },
            dataType: 'json',
            success: function(response) {
                if(response.success){
                    var row = $("#item-<?= $item['id'] ?>");
                    row.find(".status").text(response.status);
                    row.find(".latency").text(response.latency);
                    row.find(".last_checked").text(response.last_checked);
                }
            }
        });
    }, <?= $item['timer'] * 1000 ?>);
<?php endforeach; ?>
</script>
</body>
</html>
