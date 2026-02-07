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
$action = $data['action'] ?? 'add';

if ($drink_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID kopi tidak valid']);
    exit;
}

try {
    if ($action === 'remove') {
        // Remove from favorites
        $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND drink_id = ?");
        $stmt->execute([$_SESSION['user_id'], $drink_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Dihapus dari favorit',
                'action' => 'remove'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ditemukan di favorit']);
        }
    } else {
        // Check if already in favorites
        $check = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND drink_id = ?");
        $check->execute([$_SESSION['user_id'], $drink_id]);
        
        if ($check->rowCount() > 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'Sudah ada di favorit'
            ]);
        } else {
            // Add to favorites
            $stmt = $db->prepare("INSERT INTO favorites (user_id, drink_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $drink_id]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Ditambahkan ke favorit',
                'action' => 'add'
            ]);
        }
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?>