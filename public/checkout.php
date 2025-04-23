<?php
// public/checkout.php

include_once '../php/config/constants.php';
include_once '../php/core/Session.php';

// Require login to proceed with checkout
requireLogin('checkout.php');

// Get user data
$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userMobile = getCurrentUserMobile();
$userAddress = isset($_SESSION['address']) ? $_SESSION['address'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Food Ordering App</title>
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header/Navigation -->
    <!-- ... header code ... -->
    
    <!-- Checkout Form -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Checkout</h2>
            
            <!-- Order Summary -->
            <!-- ... order summary code ... -->
            
            <!-- Delivery Information (pre-filled from user profile) -->
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-medium mb-4">Delivery Information</h3>
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userName ?: ''); ?>" required
                           class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div class="mb-4">
                    <label for="mobile" class="block text-gray-700 text-sm font-medium mb-2">Mobile Number</label>
                    <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($userMobile); ?>" readonly
                           class="block w-full rounded-md border-gray-300 bg-gray-100 sm:text-sm">
                    <p class="text-xs text-gray-500 mt-1">Mobile number cannot be changed. This is your login ID.</p>
                </div>
                
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 text-sm font-medium mb-2">Delivery Address</label>
                    <textarea id="address" name="address" rows="3" required
                              class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($userAddress); ?></textarea>
                </div>
            </div>
            
            <!-- Payment Options -->
            <!-- ... payment options code ... -->
            
            <!-- Submit Order Button -->
            <div class="mt-8">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
                    Place Order
                </button>
            </div>
        </div>
    </div>
</body>
</html>