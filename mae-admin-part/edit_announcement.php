<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'edit':
                if (isset($_POST['id'])) {
                    $id = $_POST['id'];
                    $title = $_POST['title'];
                    $category = $_POST['category'];
                    $content = $_POST['content'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];
                    $priority = $_POST['priority'];
                    $status = $_POST['status'];
                    
                    try {
                        $pdo->beginTransaction();
                        
                        // Update announcement
                        $stmt = $pdo->prepare("UPDATE announcements SET title = ?, category = ?, content = ?, start_date = ?, end_date = ?, priority = ?, status = ? WHERE id = ?");
                        $stmt->execute([$title, $category, $content, $start_date, $end_date, $priority, $status, $id]);
                        
                        // Handle new file uploads
                        if (!empty($_FILES['attachments']['name'][0])) {
                            $upload_dir = 'uploads/announcements/';
                            
                            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                                $file_name = $_FILES['attachments']['name'][$key];
                                $file_type = $_FILES['attachments']['type'][$key];
                                $file_size = $_FILES['attachments']['size'][$key];
                                
                                // Generate unique filename
                                $unique_filename = uniqid() . '_' . $file_name;
                                $file_path = $upload_dir . $unique_filename;
                                
                                if (move_uploaded_file($tmp_name, $file_path)) {
                                    $stmt = $pdo->prepare("INSERT INTO announcement_attachments (announcement_id, file_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?)");
                                    $stmt->execute([$id, $file_name, $file_path, $file_type, $file_size]);
                                }
                            }
                        }
                        
                        $pdo->commit();
                        header("Location: announcements.php?success=3");
                        exit();
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        header("Location: announcements.php?error=" . urlencode($e->getMessage()));
                        exit();
                    }
                }
                break;
                
            case 'delete_attachment':
                if (isset($_POST['id'])) {
                    try {
                        // Get file path before deleting
                        $stmt = $pdo->prepare("SELECT file_path FROM announcement_attachments WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        $file_path = $stmt->fetchColumn();
                        
                        // Delete from database
                        $stmt = $pdo->prepare("DELETE FROM announcement_attachments WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        
                        // Delete file
                        if ($file_path && file_exists($file_path)) {
                            unlink($file_path);
                        }
                        
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true]);
                        exit();
                    } catch (Exception $e) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                        exit();
                    }
                }
                break;
        }
    }
}

// Get announcement details
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT a.*, GROUP_CONCAT(at.id, ':', at.file_name, ':', at.file_path) as attachments 
                          FROM announcements a 
                          LEFT JOIN announcement_attachments at ON a.id = at.announcement_id 
                          WHERE a.id = ? 
                          GROUP BY a.id");
    $stmt->execute([$_GET['id']]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($announcement) {
        header('Content-Type: application/json');
        echo json_encode($announcement);
        exit();
    }
    http_response_code(404);
    exit();
}
?> 