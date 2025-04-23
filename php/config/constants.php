<?php
// php/config/constants.php

// Base URL
define('BASE_URL', 'https://menu.desblooms.in/');  // Change to your actual domain

// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'u345095192_dineloo');
define('DB_PASS', 'Dineloo@788');
define('DB_NAME', 'u345095192_dineloodata');

// SMS Gateway (Fast2SMS)
define('SMS_API_KEY', 'TAGqacfC16QLoYmNyzeku0ORViPDv5x3pMlw9tZFj2gb8USnhKWNjdSIAXqC9PBzLlOmutsecngF61f5');  // Replace with actual API key
define('SMS_SENDER_ID', 'FSTSMS');  // Default for Fast2SMS

// OTP Settings
define('OTP_EXPIRY_MINUTES', 10);  // OTP valid for 10 minutes
define('OTP_RESEND_SECONDS', 60);  // Wait 60 seconds before resending

// Debug mode (set to false in production)
define('DEBUG_MODE', false);
?>