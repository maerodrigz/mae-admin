<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login to continue";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header("Location: members.php");
    exit();
}

// Validate required fields
$required_fields = ['member_id', 'name', 'email', 'department', 'member_type', 'status'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "All fields are required";
        header("Location: members.php");
        exit();
    }
}

// Handle file upload
$profile_image = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/members/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $_SESSION['error'] = "Invalid file type. Allowed types: " . implode(', ', $allowed_extensions);
        header("Location: members.php");
        exit();
    }
    
    $file_name = uniqid() . '.' . $file_extension;
    $target_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
        $profile_image = $target_path;
        
        // Delete old profile image if exists
        try {
            $stmt = $pdo->prepare("SELECT profile_image FROM members WHERE id = ?");
            $stmt->execute([$_POST['member_id']]);
            $old_image = $stmt->fetchColumn();
            
            if ($old_image && file_exists($old_image)) {
                unlink($old_image);
            }
        } catch(PDOException $e) {
            // Log error but continue with update
            error_log("Error deleting old profile image: " . $e->getMessage());
        }
    }
}

try {
    if ($profile_image) {
        $stmt = $pdo->prepare("UPDATE members SET name = ?, email = ?, department = ?, member_type = ?, status = ?, profile_image = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['department'],
            $_POST['member_type'],
            $_POST['status'],
            $profile_image,
            $_POST['member_id']
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE members SET name = ?, email = ?, department = ?, member_type = ?, status = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['department'],
            $_POST['member_type'],
            $_POST['status'],
            $_POST['member_id']
        ]);
    }
    
    $_SESSION['success'] = "Member updated successfully";
} catch(PDOException $e) {
    $_SESSION['error'] = "Error updating member: " . $e->getMessage();
}

header("Location: members.php");
exit();
?> 