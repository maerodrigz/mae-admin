<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($article) {
            header('Content-Type: application/json');
            echo json_encode($article);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Article not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No article ID provided']);
}
?> 