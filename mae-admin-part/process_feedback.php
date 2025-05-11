<?php
require_once 'config/database.php';
$conn = getConnection();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        if (!isset($data['requester_name']) || !isset($data['service_id']) || 
            !isset($data['rating']) || !isset($data['feedback_text'])) {
            throw new Exception('Missing required fields');
        }

        // Prepare and execute the insert statement
        $stmt = $conn->prepare("INSERT INTO feedback (requester_name, service_id, rating, feedback_text) 
                               VALUES (:requester_name, :service_id, :rating, :feedback_text)");
        
        $stmt->execute([
            ':requester_name' => $data['requester_name'],
            ':service_id' => $data['service_id'],
            ':rating' => $data['rating'],
            ':feedback_text' => $data['feedback_text']
        ]);

        echo json_encode(['success' => true, 'message' => 'Feedback submitted successfully']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $where = [];
        $params = [];

        // Handle single feedback request
        if (isset($_GET['id'])) {
            $where[] = "f.id = :id";
            $params[':id'] = $_GET['id'];
        }

        // Handle event filter
        if (!empty($_GET['event'])) {
            $where[] = "s.id = :event_id";
            $params[':event_id'] = $_GET['event'];
        }

        // Handle date filter
        if (!empty($_GET['date'])) {
            $where[] = "DATE(f.created_at) = :date";
            $params[':date'] = $_GET['date'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // Get feedback with service information
        $query = "
            SELECT f.*, s.name as service_name 
            FROM feedback f
            JOIN services s ON f.service_id = s.id
            $whereClause
            ORDER BY f.created_at DESC
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $feedback]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            throw new Exception('Feedback ID is required');
        }

        $stmt = $conn->prepare("DELETE FROM feedback WHERE id = :id");
        $stmt->execute([':id' => $data['id']]);

        echo json_encode(['success' => true, 'message' => 'Feedback deleted successfully']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?> 