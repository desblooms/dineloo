<?php
// public/index.php

include_once '../php/config/constants.php';
include_once '../php/core/Session.php';

// Check if user is logged in - for personalized content
$isLoggedIn = isLoggedIn();
$userName = $isLoggedIn ? getCurrentUserName() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering App</title>
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header/Navigation -->
    <div class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Food Ordering App</h1>
            <div>
                <?php if ($isLoggedIn): ?>
                    <span class="mr-4">Hello, <?php echo htmlspecialchars($userName ?: 'User'); ?></span>
                    <a href="profile.php" class="text-indigo-600 hover:text-indigo-800 mr-4">My Profile</a>
                    <a href="../api/auth/logout.php" class="text-indigo-600 hover:text-indigo-800">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-indigo-600 hover:text-indigo-800">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Content -->
    <!-- ... rest of your homepage content ... -->
</body>
</html>