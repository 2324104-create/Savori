<?php
session_start();
require_once '../includes/database_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$drink_id = $data['drink_id'] ?? 0;

try {
    // Check current stock
    $stmt = $db->prepare("SELECT available_quantity FROM stock WHERE drink_id = ? AND day = CURDATE()");
    $stmt->execute([$drink_id]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$stock || $stock['available_quantity'] <= 0) {
        echo json_encode(['success' => false, 'message' => 'Stok habis untuk hari ini']);
        exit;
    }
    
    // Reduce stock
    $stmt = $db->prepare("UPDATE stock SET available_quantity = available_quantity - 1 WHERE drink_id = ? AND day = CURDATE() AND available_quantity > 0");
    $stmt->execute([$drink_id]);
    
    if ($stmt->rowCount() > 0) {
        // Increase popularity
        $db->prepare("UPDATE drinks SET popularity_score = popularity_score + 1 WHERE id = ?")->execute([$drink_id]);
        
        // Get remaining stock
        $stmt = $db->prepare("SELECT available_quantity FROM stock WHERE drink_id = ? AND day = CURDATE()");
        $stmt->execute([$drink_id]);
        $remaining = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Pesanan berhasil!',
            'remaining_stock' => $remaining
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memesan']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>