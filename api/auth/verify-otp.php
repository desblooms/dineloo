<?php
// api/auth/verify-otp.php

include_once '../../php/config/constants.php';
include_once '../../php/config/db.php';
include_once '../../php/core/Session.php';
include_once '../../php/controllers/AuthController.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['auth_error'] = 'Invalid request method';
    header('Location: ../../public/login.php');
    exit;
}

// Get OTP from POST data
$otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

// Initialize database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    $_SESSION['auth_error'] = 'Database connection failed';
    header('Location: ../../public/login.php');
    exit;
}

// Initialize AuthController
$authController = new AuthController($conn);

// Verify OTP
$result = $authController->verifyOTP($otp);

if ($result['success']) {
    // Redirect to home page or previous page
    $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '../../public/index.php';
    unset($_SESSION['redirect_after_login']);
    
    header('Location: ' . $redirect);
    exit;
} else {
    $_SESSION['auth_error'] = $result['message'];
    header('Location: ../../public/login.php');
    exit;
}

$conn->close();
?>