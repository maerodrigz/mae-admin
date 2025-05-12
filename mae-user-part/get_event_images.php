<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // your password
$dbname = "access_db"; // change to your DB name
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get event from query parameter
$event = isset($_GET['event']) ? $_GET['event'] : '';

if (empty($event)) {
    echo json_encode([]);
    exit;
}

// Prepare and execute query
$sql = "SELECT * FROM gallery WHERE event = ? ORDER BY upload_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $event);
$stmt->execute();
$result = $stmt->get_result();

$images = [];
$base_url = "http://localhost/mae-admin-part/uploads/gallery/";

while ($row = $result->fetch_assoc()) {
    $image_filename = basename($row["path"]);
    $images[] = [
        'path' => $base_url . $image_filename,
        'title' => $row["title"],
        'event' => $row["event"]
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($images);

$conn->close();
?> 