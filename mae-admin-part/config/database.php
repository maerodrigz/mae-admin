<?php
function getConnection() {
    $host = 'localhost';
    $dbname = 'access_db';
    $username = 'root';
    $password = '';
    
    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?> 