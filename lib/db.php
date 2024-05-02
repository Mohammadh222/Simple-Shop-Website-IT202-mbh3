<?php
// lib/db.php
function getDB() {
    global $db;
    if (!isset($db)) {
        try {
            $dbhost = "localhost";  // Your database host
            $dbuser = "username";   // Your database username
            $dbpass = "password";   // Your database password
            $dbdatabase = "databasename"; // Your database name

            $connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
            $db = new PDO($connection_string, $dbuser, $dbpass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    return $db;
}
?>
