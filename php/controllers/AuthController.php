<?php
// php/controllers/AuthController.php

require_once __DIR__ . '/../core/Auth.php';

class AuthController {
    private $auth;
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->auth = new Auth($conn);
    }
    
    /**
     * Handle sending OTP
     * 
     * @param string $mobile Mobile number
     * @return array Response with success/failure and message
     */
    public function sendOTP($mobile) {
        // Validate mobile number
        if (empty($mobile) || !preg_match('/^[0-9]{10}$/', $mobile)) {
            return [
                'success' => false,
                'message' => 'Please provide a valid 10-digit mobile number'
            ];
        }
        
        // Send OTP
        $result = $this->auth->sendOTP($mobile);
        
        if ($result['success']) {
            // Store mobile in session
            $_SESSION['mobile'] = $mobile;
            $_SESSION['otp_sent'] = true;
            $_SESSION['otp_time'] = time();
        }
        
        return $result;
    }
    
    /**
     * Handle OTP verification
     * 
     * @param string $otp OTP to verify
     * @return array Response with success/failure, message, and user data
     */
    public function verifyOTP($otp) {
        // Check if OTP was sent
        if (!isset($_SESSION['otp_sent']) || !isset($_SESSION['mobile'])) {
            return [
                'success' => false,
                'message' => 'Please get an OTP first'
            ];
        }
        
        $mobile = $_SESSION['mobile'];
        
        // Validate OTP
        if (empty($otp) || !preg_match('/^[0-9]{6}$/', $otp)) {
            return [
                'success' => false,
                'message' => 'Please provide a valid 6-digit OTP'
            ];
        }
        
        // Verify OTP
        if ($this->auth->verifyOTP($mobile, $otp)) {
            // OTP verified, login or register user
            $user = $this->auth->loginOrRegisterUser($mobile);
            
            if ($user) {
                // Set user session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['mobile'] = $user['mobile'];
                $_SESSION['name'] = $user['name'] ?? null;
                $_SESSION['email'] = $user['email'] ?? null;
                $_SESSION['address'] = $user['address'] ?? null;
                $_SESSION['is_logged_in'] = true;
                
                // Clear OTP session data
                unset($_SESSION['otp_sent']);
                unset($_SESSION['otp_time']);
                
                return [
                    'success' => true,
                    'message' => 'Authentication successful',
                    'user' => $user
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create or retrieve user account'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Invalid or expired OTP. Please try again'
            ];
        }
    }
    
    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data Profile data to update
     * @return array Response with success/failure and message
     */
    public function updateProfile($userId, $data) {
        // Validate data
        if (!isset($data['name']) || empty(trim($data['name']))) {
            return [
                'success' => false,
                'message' => 'Name is required'
            ];
        }
        
        // Validate email if provided
        if (isset($data['email']) && !empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }
        
        // Prepare data for update
        $name = trim($data['name']);
        $email = isset($data['email']) ? trim($data['email']) : null;
        $address = isset($data['address']) ? trim($data['address']) : null;
        
        // Update user profile
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $address, $userId);
        
        if ($stmt->execute()) {
            // Update session data
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['address'] = $address;
            
            return [
                'success' => true,
                'message' => 'Profile updated successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update profile: ' . $this->conn->error
            ];
        }
    }
    
    /**
     * Get user by ID
     * 
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    public function getUserById($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Get user by mobile number
     * 
     * @param string $mobile Mobile number
     * @return array|null User data or null if not found
     */
    public function getUserByMobile($mobile) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE mobile = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}
?>