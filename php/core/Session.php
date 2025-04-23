<?php
// php/core/Session.php (enhanced version)

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access to session cookie
    ini_set('session.use_only_cookies', 1); // Force use of cookies for session
    ini_set('session.cookie_samesite', 'Lax'); // Protect against CSRF
    
    // Use secure cookies in production environment over HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    // Set session name to something less obvious than PHPSESSID
    session_name('food_app_session');
    
    // Regenerate session ID periodically to prevent session fixation
    session_start();
    
    // Regenerate session ID every 30 minutes
    if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
    
    // Implement IP binding for extra security (optional)
    if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
        // Possible session hijacking attempt
        session_unset();
        session_destroy();
        session_start();
    } else if (isset($_SESSION['user_id'])) {
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    }
}

// Rest of the Session.php file (isLoggedIn, requireLogin, etc.) remains the same


function isLoggedIn() {
    return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
}

/**
 * Require user to be logged in
 * Redirects to login page if not logged in
 * 
 * @param string $redirect Optional page to redirect to after login
 */
function requireLogin($redirect = null) {
    if (!isLoggedIn()) {
        if ($redirect) {
            $_SESSION['redirect_after_login'] = $redirect;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        header('Location: ' . BASE_URL . 'public/login.php');
        exit;
    }
}

/**
 * Get current logged in user's ID
 * 
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current logged in user's mobile
 * 
 * @return string|null Mobile number or null if not logged in
 */
function getCurrentUserMobile() {
    return isset($_SESSION['mobile']) ? $_SESSION['mobile'] : null;
}

/**
 * Get current logged in user's name
 * 
 * @return string|null User name or null if not logged in or name not set
 */
function getCurrentUserName() {
    return isset($_SESSION['name']) ? $_SESSION['name'] : null;
}
?>