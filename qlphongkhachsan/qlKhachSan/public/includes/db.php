<?php
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "";
    $port = 3306;

    try {
        $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return null;
    }
}