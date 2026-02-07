<?php
session_start();
require_once '../includes/database_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

$drink_id = $_POST['drink_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? '';

if (!$drink_id || !$rating) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    // Check if user already reviewed this drink
    $stmt = $db->prepare("SELECT id FROM reviews WHERE user_id = ? AND drink_id = ?");
    $stmt->execute([$_SESSION['user_id'], $drink_id]);
    
    if ($stmt->rowCount() > 0) {
        // Update existing review
        $stmt = $db->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE user_id = ? AND drink_id = ?");
        $stmt->execute([$rating, $comment, $_SESSION['user_id'], $drink_id]);
        $action = 'updated';
    } else {
        // Insert new review
        $stmt = $db->prepare("INSERT INTO reviews (user_id, drink_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $drink_id, $rating, $comment]);
        $action = 'added';
        
        // Increase popularity score
        $stmt = $db->prepare("UPDATE drinks SET popularity_score = popularity_score + 5 WHERE id = ?");
        $stmt->execute([$drink_id]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Review berhasil ' . ($action == 'added' ? 'ditambahkan' : 'diupdate'),
        'action' => $action
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>