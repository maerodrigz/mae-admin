<?php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Service ID is required']);
    exit();
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($service) {
        echo json_encode($service);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Service request not found']);
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 