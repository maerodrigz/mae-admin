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
$required_fields = ['name', 'email', 'department', 'member_type', 'status'];
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
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO members (name, email, department, member_type, status, profile_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['department'],
        $_POST['member_type'],
        $_POST['status'],
        $profile_image
    ]);
    
    $_SESSION['success'] = "Member added successfully";
} catch(PDOException $e) {
    $_SESSION['error'] = "Error adding member: " . $e->getMessage();
}

header("Location: members.php");
exit();
?> 