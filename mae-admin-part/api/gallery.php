<?php
require_once '../config/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Get database connection
$db = getConnection();

// Handle different actions
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        getImages();
        break;
    case 'upload':
        uploadImages();
        break;
    case 'delete':
        deleteImage();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// Function to get images
function getImages() {
    global $db;
    
    $event = $_GET['event'] ?? '';
    $date = $_GET['date'] ?? '';
    $id = $_GET['id'] ?? '';
    
    $query = "SELECT * FROM gallery";
    $params = [];
    
    if ($id) {
        $query .= " WHERE id = ?";
        $params[] = $id;
    } else {
        $conditions = [];
        
        if ($event) {
            $conditions[] = "event = ?";
            $params[] = $event;
        }
        
        if ($date) {
            $conditions[] = "DATE(upload_date) = ?";
            $params[] = $date;
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY upload_date DESC";
    }
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    
    if ($id) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode($result);
}

// Function to upload images
function uploadImages() {
    global $db;
    
    if (!isset($_POST['event']) || !isset($_POST['title']) || !isset($_FILES['images'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    $event = $_POST['event'];
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $uploadDate = date('Y-m-d H:i:s');
    
    try {
        $db->beginTransaction();
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                throw new Exception('Error uploading file: ' . $_FILES['images']['name'][$key]);
            }

            $fileName = $_FILES['images']['name'][$key];
            $fileType = $_FILES['images']['type'][$key];
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Invalid file type: ' . $fileName . '. Only JPG, PNG, and GIF images are allowed.');
            }
            
            // Generate unique filename
            $uniqueName = uniqid() . '_' . $fileName;
            $uploadPath = '../uploads/gallery/' . $uniqueName;
            
            // Create directory if it doesn't exist
            if (!file_exists('../uploads/gallery')) {
                mkdir('../uploads/gallery', 0777, true);
            }
            
            // Move uploaded file
            if (!move_uploaded_file($tmp_name, $uploadPath)) {
                throw new Exception('Failed to move uploaded file: ' . $fileName);
            }
            
            // Insert into database
            $stmt = $db->prepare("INSERT INTO gallery (title, description, event, path, upload_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $event, 'uploads/gallery/' . $uniqueName, $uploadDate]);
        }
        
        $db->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Function to delete image
function deleteImage() {
    global $db;
    
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing image ID']);
        return;
    }
    
    $id = $_GET['id'];
    
    try {
        // Get image path first
        $stmt = $db->prepare("SELECT path FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            // Delete file
            $filePath = '../' . $image['path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Delete from database
            $stmt = $db->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Image not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} 