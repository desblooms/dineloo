<?php
// api/auth/update-profile.php

include_once '../../php/config/constants.php';
include_once '../../php/config/db.php';
include_once '../../php/core/Session.php';

// Require login
requireLogin();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['profile_error'] = 'Invalid request method';
    header('Location: ../../public/profile.php');
    exit;
}

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

// Validate name
if (empty($name)) {
    $_SESSION['profile_error'] = 'Name is required';
    header('Location: ../../public/profile.php');
    exit;
}

// Validate email if provided
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['profile_error'] = 'Invalid email format';
    header('Location: ../../public/profile.php');
    exit;
}

// Get user ID from session
$userId = getCurrentUserId();

// Initialize database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    $_SESSION['profile_error'] = 'Database connection failed';
    header('Location: ../../public/profile.php');
    exit;
}

// Update user profile
$stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, address = ? WHERE id = ?");
$stmt->bind_param("sssi", $name, $email, $address, $userId);

if ($stmt->execute()) {
    // Update session data
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['address'] = $address;
    
    $_SESSION['profile_success'] = 'Profile updated successfully';
} else {
    $_SESSION['profile_error'] = 'Failed to update profile: ' . $conn->error;
}

$conn->close();

// Redirect back to profile page
header('Location: ../../public/profile.php');
exit;
?>