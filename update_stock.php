<?php
session_start();
require_once '../includes/database_connect.php';

header('Content-Type: application/json');

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak! Hanya admin']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$drink_id = $data['drink_id'] ?? null;
$quantity = $data['quantity'] ?? null;
$day = $data['day'] ?? date('Y-m-d');

if (!$drink_id || $quantity === null) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    // Check if stock exists
    $stmt = $db->prepare("SELECT id FROM stock WHERE drink_id = ? AND day = ?");
    $stmt->execute([$drink_id, $day]);
    
    if ($stmt->rowCount() > 0) {
        // Update existing stock
        $stmt = $db->prepare("UPDATE stock SET available_quantity = ? WHERE drink_id = ? AND day = ?");
        $stmt->execute([$quantity, $drink_id, $day]);
    } else {
        // Insert new stock
        $stmt = $db->prepare("INSERT INTO stock (drink_id, day, available_quantity) VALUES (?, ?, ?)");
        $stmt->execute([$drink_id, $day, $quantity]);
    }
    
    // Get drink name for response
    $stmt = $db->prepare("SELECT name FROM drinks WHERE id = ?");
    $stmt->execute([$drink_id]);
    $drink_name = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'message' => "Stok $drink_name berhasil diupdate",
        'drink_name' => $drink_name,
        'quantity' => $quantity,
        'day' => $day
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>