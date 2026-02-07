<?php
session_start();
require_once '../includes/database_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    // Get user's last recommendation
    $stmt = $db->prepare("
        SELECT mood, weather, time_of_day 
        FROM recommendations 
        WHERE user_id = ? 
        ORDER BY recommended_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'preferences' => $preferences ?: []
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>