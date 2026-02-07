<?php
session_start();
require_once '../includes/database_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$review_id = $data['review_id'] ?? null;

if (!$review_id) {
    echo json_encode(['success' => false, 'message' => 'ID review tidak valid']);
    exit;
}

try {
    // Check if review belongs to user
    $stmt = $db->prepare("SELECT id FROM reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$review_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Review tidak ditemukan atau bukan milik Anda']);
        exit;
    }
    
    // Delete review
    $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$review_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Review berhasil dihapus'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>