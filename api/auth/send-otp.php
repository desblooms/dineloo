<?php
// api/auth/send-otp.php

include_once '../../php/config/constants.php';
include_once '../../php/config/db.php';
include_once '../../php/core/Session.php';
include_once '../../php/controllers/AuthController.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get mobile number from POST data
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';

// Initialize database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    error_log("Database connection failed: " . $conn->connect_error);
    exit;
}

// Initialize AuthController
$authController = new AuthController($conn);

// Send OTP
$result = $authController->sendOTP($mobile);

echo json_encode($result);

$conn->close();
?>