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

// Validate required fields
$required = ['name', 'description', 'type', 'preparation_time', 'difficulty', 'caffeine_level', 'ingredients'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Field $field harus diisi"]);
        exit;
    }
}

try {
    // Insert new drink
    $stmt = $db->prepare("
        INSERT INTO drinks (name, description, type, ingredients, preparation_time, difficulty, caffeine_level, popularity_score, is_available) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 50, 1)
    ");
    
    $stmt->execute([
        $data['name'],
        $data['description'],
        $data['type'],
        $data['ingredients'],
        $data['preparation_time'],
        $data['difficulty'],
        $data['caffeine_level']
    ]);
    
    $drink_id = $db->lastInsertId();
    $initial_stock = $data['initial_stock'] ?? 20;
    
    // Setup stock for next 7 days
    for ($i = 0; $i < 7; $i++) {
        $day = date('Y-m-d', strtotime("+$i days"));
        $stmt = $db->prepare("INSERT INTO stock (drink_id, day, available_quantity) VALUES (?, ?, ?)");
        $stmt->execute([$drink_id, $day, $initial_stock]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Kopi berhasil ditambahkan',
        'drink_id' => $drink_id,
        'drink_name' => $data['name']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>