<?php
require_once 'config/database.php';

try {
    // Check services table
    $stmt = $conn->query("SELECT * FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Services found: " . count($services) . "\n";
    print_r($services);

    // Check feedback table
    $stmt = $conn->query("SELECT * FROM feedback");
    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nFeedback entries found: " . count($feedback) . "\n";
    print_r($feedback);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 