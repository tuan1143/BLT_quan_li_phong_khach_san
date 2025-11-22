<?php
// db.php - Helper functions for database connection and queries

function getDatabaseConnection() {
    $db_host = 'localhost';
    $db_name = 'hotel_booking_system'; // Change this to your database name
    $db_user = 'root'; // Change this to your database username
    $db_pass = ''; // Change this to your database password

    try {
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function executeQuery($query, $params = []) {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt;
}
?>