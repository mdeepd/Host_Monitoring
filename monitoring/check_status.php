<?php
// check_status.php
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}
require 'config.php';

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'No ID provided']);
    exit();
}

$id = $_POST['id'];
$stmt = $pdo->prepare("SELECT * FROM monitored_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode(['success' => false, 'error' => 'Item not found']);
    exit();
}

$address = escapeshellarg($item['address']);
$pingCount = 1;
$timeout = 1;

// For Linux: ping -c 1 -W 1 address
$command = "ping -c $pingCount -W $timeout $address";
$output = [];
exec($command, $output, $status);

$result = [
    'success' => true,
    'status' => 'down',
    'latency' => null,
    'last_checked' => date('Y-m-d H:i:s')
];

if ($status === 0) {
    // Try to extract latency (e.g., "time=0.123 ms")
    $latency = null;
    foreach ($output as $line) {
        if (strpos($line, 'time=') !== false) {
            preg_match('/time=([\d\.]+)\s*ms/', $line, $matches);
            if (isset($matches[1])) {
                $latency = floatval($matches[1]);
            }
            break;
        }
    }
    $result['status'] = 'up';
    $result['latency'] = $latency;
}

// Update the item record with the latest check info
$stmt = $pdo->prepare("UPDATE monitored_items SET status = ?, latency = ?, last_checked = ? WHERE id = ?");
$stmt->execute([$result['status'], $result['latency'], $result['last_checked'], $id]);

echo json_encode($result);
?>
