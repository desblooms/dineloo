<?php
// php/services/SMSService.php

class SMSService {
    private $apiKey;
    
    public function __construct() {
        // Load API key from configuration or environment
        $this->apiKey = getenv('FAST2SMS_API_KEY') ?: 'YOUR_FAST2SMS_API_KEY'; // Replace with your actual API key
    }
    
    /**
     * Send OTP via Fast2SMS
     * 
     * @param string $mobile Mobile number to send OTP to
     * @param string $otp The OTP to be sent
     * @return array Response with success/failure and message
     */
    public function sendOTP($mobile, $otp) {
        // Remove any prefix (like +91)
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        // Ensure mobile is a 10-digit number
        if (strlen($mobile) != 10) {
            return [
                'success' => false,
                'message' => 'Invalid mobile number format'
            ];
        }
        
        // Prepare message
        $message = urlencode("Your OTP for Food Ordering App is: $otp. Valid for 10 minutes. Do not share this OTP with anyone.");
        
        // Prepare API call for Fast2SMS
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "variables_values=$otp&route=otp&numbers=$mobile",
            CURLOPT_HTTPHEADER => [
                "authorization: " . $this->apiKey,
                "accept: */*",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            // Log error
            error_log("Fast2SMS API Error: " . $err);
            
            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ];
        }
        
        // Process response
        $responseData = json_decode($response, true);
        
        if (isset($responseData['return']) && $responseData['return'] === true) {
            // Log success
            error_log("OTP sent successfully to $mobile");
            
            return [
                'success' => true,
                'message' => 'OTP sent successfully'
            ];
        } else {
            // Log failure
            error_log("Failed to send OTP to $mobile: " . json_encode($responseData));
            
            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ];
        }
    }
}
?>