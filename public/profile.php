<?php
// public/profile.php
include_once '../php/config/constants.php';
include_once '../php/core/Session.php';

// Require login to access this page
requireLogin();

// Get user data
$userId = getCurrentUserId();
$mobile = getCurrentUserMobile();
$name = getCurrentUserName();

?>
            <!-- Profile Update Form -->
            <form action="../api/auth/update-profile.php" method="POST" class="mt-8 border-t pt-6">
                <h3 class="text-lg font-medium mb-4">Update Profile Information</h3>
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?: ''); ?>" 
                           class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Enter your full name">
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address (optional)</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                           class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Enter your email address">
                </div>
                
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 text-sm font-medium mb-2">Delivery Address (optional)</label>
                    <textarea id="address" name="address" rows="3" 
                              class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                              placeholder="Enter your delivery address"><?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?></textarea>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
                        Update Profile
                    </button>
                </div>
                
                <?php if (isset($_SESSION['profile_success'])): ?>
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded">
                    <?php echo $_SESSION['profile_success']; ?>
                    <?php unset($_SESSION['profile_success']); ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['profile_error'])): ?>
                <div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
                    <?php echo $_SESSION['profile_error']; ?>
                    <?php unset($_SESSION['profile_error']); ?>
                </div>
                <?php endif; ?>
            </form>
            
            <!-- Order History Link -->
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-medium mb-4">Account Actions</h3>
                <ul>
                    <li class="mb-2">
                        <a href="orders.php" class="text-indigo-600 hover:text-indigo-800">
                            View Order History
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>