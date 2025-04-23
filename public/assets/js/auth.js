// public/assets/js/auth.js

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const loginForm = document.getElementById('login-form');
    const otpForm = document.getElementById('otp-form');
    const resendOtp = document.getElementById('resend-otp');
    const changeMobile = document.getElementById('change-mobile');
    const displayMobile = document.getElementById('display-mobile');
    const mobileForm = document.getElementById('mobile-form');

    // If mobile form is submitted via AJAX
    if (mobileForm) {
        mobileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const mobile = document.getElementById('mobile').value;
            if (mobile.length !== 10) {
                alert('Please enter a valid 10-digit mobile number');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            // Send AJAX request to send OTP
            const formData = new FormData(this);
            
            fetch('../api/auth/send-otp.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show OTP form
                    loginForm.classList.add('hidden');
                    otpForm.classList.remove('hidden');
                    
                    // Update displayed mobile number
                    if (displayMobile) {
                        displayMobile.textContent = mobile;
                    }
                } else {
                    // Show error
                    alert(data.message || 'Failed to send OTP. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Handle resend OTP
    if (resendOtp) {
        resendOtp.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading state
            this.textContent = 'Sending...';
            this.style.pointerEvents = 'none';
            
            fetch('../api/auth/resend-otp.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('OTP resent successfully!');
                } else {
                    alert(data.message || 'Failed to resend OTP. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                // Reset link state
                resendOtp.textContent = 'Resend OTP';
                resendOtp.style.pointerEvents = '';
                
                // Start countdown for resend (optional)
                // startResendCountdown();
            });
        });
    }

    // Handle change mobile number
    if (changeMobile) {
        changeMobile.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Switch back to mobile input form
            otpForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
            
            // Reset mobile form
            if (mobileForm) {
                mobileForm.reset();
            }
            
            // Clear session OTP flag via AJAX
            fetch('../api/auth/reset-otp-session.php')
            .catch(error => console.error('Error:', error));
        });
    }
});