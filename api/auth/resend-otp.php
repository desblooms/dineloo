<?php
// api/auth/resend-otp.php

include_once '../../php/config/constants.php';
include_once '../../php/config/db.php';
include_once '../../php/core/Session.php';
include_once '../../php/core/Auth.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if OTP was previously sent
if (!isset($_SESSION['mobile'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No mobile number found in session'
    ]);
    exit;
}

// Get mobile from session
$mobile = $_SESSION['mobile'];

// Check for rate limiting (prevent OTP spam)
if (isset($_SESSION['otp_time'])) {
    $timeSinceLastOTP = time() - $_SESSION['otp_time'];
    
    // Require 60 seconds between OTP requests
    if ($timeSinceLastOTP < 60) {
        echo json_encode([
            'success' => false,
            'message' => 'Please wait ' . (60 - $timeSinceLastOTP) . ' seconds before requesting another OTP',
            'retry_after' => 60 - $timeSinceLastOTP
        ]);
        exit;
    }
}

// Initialize database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Initialize Auth class
$auth = new Auth($conn);

// Send new OTP
$result = $auth->sendOTP($mobile);

if ($result['success']) {
    // Update OTP time in session
    $_SESSION['otp_time'] = time();
    
    echo json_encode([
        'success' => true,
        'message' => 'OTP resent successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message']
    ]);
}

$conn->close();
?>