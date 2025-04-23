<?php
// php/core/Auth.php

require_once __DIR__ . '/../services/SMSService.php';

class Auth {
    private $conn;
    private $smsService;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->smsService = new SMSService();
    }
    
    /**
     * Generate a random OTP
     * 
     * @return string 6-digit OTP
     */
    public function generateOTP() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Store OTP in database with expiry time
     * 
     * @param string $mobile Mobile number
     * @param string $otp OTP to store
     * @return bool Success/failure
     */
    public function storeOTP($mobile, $otp) {
        // OTP valid for 10 minutes
        $expiryTime = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // First delete any existing OTPs for this mobile
        $stmt = $this->conn->prepare("DELETE FROM otp_verifications WHERE mobile = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        
        // Now insert new OTP
        $stmt = $this->conn->prepare("INSERT INTO otp_verifications (mobile, otp, expiry_time, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $mobile, $otp, $expiryTime);
        
        $result = $stmt->execute();
        
        if ($result) {
            // Log OTP generation (for development only)
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("OTP generated for $mobile: $otp (expires: $expiryTime)");
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Verify the provided OTP against stored OTP
     * 
     * @param string $mobile Mobile number
     * @param string $otp OTP to verify
     * @return bool True if valid, false otherwise
     */
    public function verifyOTP($mobile, $otp) {
        $stmt = $this->conn->prepare("SELECT * FROM otp_verifications WHERE mobile = ? AND otp = ? AND expiry_time > NOW() ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param("ss", $mobile, $otp);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // OTP is valid, delete it to prevent reuse
            $stmt = $this->conn->prepare("DELETE FROM otp_verifications WHERE mobile = ?");
            $stmt->bind_param("s", $mobile);
            $stmt->execute();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Send OTP to mobile number
     * 
     * @param string $mobile Mobile number
     * @return array Success/failure with message
     */
    public function sendOTP($mobile) {
        // Generate OTP
        $otp = $this->generateOTP();
        
        // Store OTP
        if (!$this->storeOTP($mobile, $otp)) {
            return [
                'success' => false,
                'message' => 'Failed to generate OTP. Please try again.'
            ];
        }
        
        // Send OTP via SMS
        return $this->smsService->sendOTP($mobile, $otp);
    }
    
    /**
     * Login or register user based on verified mobile
     * 
     * @param string $mobile Verified mobile number
     * @return array User data
     */
    public function loginOrRegisterUser($mobile) {
        // Check if user exists
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE mobile = ? LIMIT 1");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // User exists, return user data
            $user = $result->fetch_assoc();
            
            // Update last login
            $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            
            return $user;
        } else {
            // User doesn't exist, create new user
            $stmt = $this->conn->prepare("INSERT INTO users (mobile, created_at) VALUES (?, NOW())");
            $stmt->bind_param("s", $mobile);
            
            if ($stmt->execute()) {
                $userId = $stmt->insert_id;
                
                // Fetch the newly created user
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }
        }
        
        return null;
    }
}
?>