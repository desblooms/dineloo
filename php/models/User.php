<?php
// php/models/User.php

class User {
    private $conn;
    
    // User properties
    public $id;
    public $mobile;
    public $name;
    public $email;
    public $address;
    public $created_at;
    public $last_login;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Load user by ID
     * 
     * @param int $id User ID
     * @return bool True if user found, false otherwise
     */
    public function loadById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $this->id = $user['id'];
            $this->mobile = $user['mobile'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->address = $user['address'];
            $this->created_at = $user['created_at'];
            $this->last_login = $user['last_login'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Load user by mobile number
     * 
     * @param string $mobile Mobile number
     * @return bool True if user found, false otherwise
     */
    public function loadByMobile($mobile) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE mobile = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $this->id = $user['id'];
            $this->mobile = $user['mobile'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->address = $user['address'];
            $this->created_at = $user['created_at'];
            $this->last_login = $user['last_login'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Update last login time
     * 
     * @return bool True on success, false on failure
     */
    public function updateLastLogin() {
        $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Update user profile
     * 
     * @param array $data Profile data to update
     * @return bool True on success, false on failure
     */
    public function update($data) {
        // Update properties
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['email'])) $this->email = $data['email'];
        if (isset($data['address'])) $this->address = $data['address'];
        
        // Update in database
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssi", $this->name, $this->email, $this->address, $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Create new user
     * 
     * @param string $mobile Mobile number
     * @param array $data Optional additional user data
     * @return int|bool New user ID on success, false on failure
     */
    public function create($mobile, $data = []) {
        $this->mobile = $mobile;
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->address = $data['address'] ?? null;
        
        $stmt = $this->conn->prepare("INSERT INTO users (mobile, name, email, address, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $this->mobile, $this->name, $this->email, $this->address);
        
        if ($stmt->execute()) {
            $this->id = $stmt->insert_id;
            
            // Load full user data
            $this->loadById($this->id);
            
            return $this->id;
        }
        
        return false;
    }
}
?>