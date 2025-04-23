<?php


// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'u345095192_dineloo');
define('DB_PASS', 'Dineloo@788');
define('DB_NAME', 'u345095192_dineloodata');

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>