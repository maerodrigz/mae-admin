<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Member ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$member) {
        http_response_code(404);
        echo json_encode(['error' => 'Member not found']);
        exit();
    }
    
    echo json_encode($member);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 