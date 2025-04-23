<!-- public/login.php -->
<?php include_once '../php/config/constants.php'; include_once '../php/core/Session.php'; 

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Food Ordering App</title>
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
        <div class="text-center mb-6">
            <img src="assets/images/branding/logo.png" alt="Logo" class="h-16 mx-auto">
            <h1 class="text-2xl font-bold mt-4">Welcome Back</h1>
            <p class="text-gray-600">Login with your mobile number</p>
        </div>

        <div id="login-form" class="<?php echo isset($_SESSION['otp_sent']) ? 'hidden' : ''; ?>">
            <form id="mobile-form" method="POST" action="../api/auth/send-otp.php">
                <div class="mb-4">
                    <label for="mobile" class="block text-gray-700 text-sm font-medium mb-2">Mobile Number</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 bg-gray-200 text-gray-600 border border-r-0 border-gray-300 rounded-l-md">
                            +91
                        </span>
                        <input type="tel" id="mobile" name="mobile" pattern="[0-9]{10}" required 
                               class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="10-digit mobile number">
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
                        Send OTP
                    </button>
                </div>
            </form>
        </div>

        <div id="otp-form" class="<?php echo isset($_SESSION['otp_sent']) ? '' : 'hidden'; ?>">
            <form method="POST" action="../api/auth/verify-otp.php">
                <div class="mb-4">
                    <p class="text-gray-600 text-sm mb-2">We've sent an OTP to <span id="display-mobile" class="font-medium"><?php echo isset($_SESSION['mobile']) ? $_SESSION['mobile'] : ''; ?></span></p>
                    <label for="otp" class="block text-gray-700 text-sm font-medium mb-2">Enter OTP</label>
                    <input type="text" id="otp" name="otp" maxlength="6" required 
                           class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="6-digit OTP">
                </div>
                <div class="mt-6">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
                        Verify OTP
                    </button>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Didn't receive OTP? <a href="#" id="resend-otp" class="text-indigo-600 hover:text-indigo-500">Resend OTP</a>
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        <a href="#" id="change-mobile" class="text-indigo-600 hover:text-indigo-500">Change Mobile Number</a>
                    </p>
                </div>
            </form>
        </div>

        <?php if (isset($_SESSION['auth_error'])): ?>
            <div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
                <?php echo $_SESSION['auth_error']; ?>
                <?php unset($_SESSION['auth_error']); ?>
            </div>
        <?php endif; ?>

    </div>

    <script src="assets/js/auth.js"></script>
</body>
</html>