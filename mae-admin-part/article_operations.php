<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            createArticle($pdo);
            break;
        case 'update':
            updateArticle($pdo);
            break;
        case 'delete':
            deleteArticle($pdo);
            break;
    }
}

function createArticle($pdo) {
    try {
        $title = $_POST['title'];
        $category = $_POST['category'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        
        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO articles (title, category, content, image_path, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $content, $image_path, $status]);
        
        header('Location: reports.php?success=1');
    } catch (PDOException $e) {
        header('Location: reports.php?error=' . urlencode($e->getMessage()));
    }
}

function updateArticle($pdo) {
    try {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $category = $_POST['category'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        
        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
            }
        }
        
        if ($image_path) {
            $stmt = $pdo->prepare("UPDATE articles SET title = ?, category = ?, content = ?, image_path = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $category, $content, $image_path, $status, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE articles SET title = ?, category = ?, content = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $category, $content, $status, $id]);
        }
        
        header('Location: reports.php?success=1');
    } catch (PDOException $e) {
        header('Location: reports.php?error=' . urlencode($e->getMessage()));
    }
}

function deleteArticle($pdo) {
    try {
        $id = $_POST['id'];
        
        // Get image path before deleting
        $stmt = $pdo->prepare("SELECT image_path FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        // Delete the article
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        
        // Delete the image file if it exists
        if ($article && $article['image_path'] && file_exists($article['image_path'])) {
            unlink($article['image_path']);
        }
        
        header('Location: reports.php?success=1');
    } catch (PDOException $e) {
        header('Location: reports.php?error=' . urlencode($e->getMessage()));
    }
}
?> 