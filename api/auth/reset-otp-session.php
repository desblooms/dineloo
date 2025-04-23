<?php
// api/auth/reset-otp-session.php

include_once '../../php/config/constants.php';
include_once '../../php/core/Session.php';

// Set content type to JSON
header('Content-Type: application/json');

// Clear OTP session variables
unset($_SESSION['otp_sent']);
unset($_SESSION['otp_time']);

echo json_encode([
    'success' => true
]);
?>