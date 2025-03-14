<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK Scholars Popup</title>
    <style>
        /* Popup Overlay */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            z-index: 200 !important;
        }

        .popup-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Popup Container */
        .popup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            padding: 30px;
            text-align: center;
            position: relative;
            transform: scale(0.8);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .popup-overlay.active .popup-container {
            transform: scale(1);
            opacity: 1;
        }

        /* Popup Header */
        .popup-header {
            margin-bottom: 20px;
        }

        .popup-header h2 {
            font-size: 24px;
            color: #333;
            margin: 0;
        }

        .popup-header p {
            font-size: 14px;
            color: #666;
            margin: 10px 0 0;
        }

        /* Popup Body */
        .popup-body {
            margin-bottom: 20px;
        }

        .popup-body p {
            font-size: 16px;
            color: #444;
            line-height: 1.5;
        }

        .emoji {
            font-size: 40px;
            margin-bottom: 15px;
            animation: float 3s ease-in-out infinite;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* Popup Buttons */
        .popup-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .popup-button {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .popup-button.primary {
            background: #4bc2c5;
            color: white;
        }

        .popup-button.primary:hover {
            background: #3aa9ac;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(75, 194, 197, 0.4);
        }

        .popup-button.secondary {
            background: #ff7a7a;
            color: white;
        }

        .popup-button.secondary:hover {
            background: #e66a6a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 122, 122, 0.4);
        }

        /* Close Button */
        .close-button {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            color: #fff;
            cursor: pointer;
            transition: color 0.3s ease;
            background-color: #e66a6a;
            padding: 0 10px;
            width: .9cm;
            height: .9cm;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50px;
        }

        .close-button:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Popup Overlay -->
    <div class="popup-overlay">
        <div class="popup-container">
            <!-- Close Button -->
            <button class="close-button" onclick="dismissPopup()">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                <div class="emoji">
                    <img src="http://localhost/mkscholars/images/logo/logoRound.png" alt="MK Scholars Logo" width="200" height="200">
                </div>
                <h2>📢 <br> Exciting News from <br> MK SCHOLARS!</h2>
            </div>

            <!-- Popup Body -->
            <div class="popup-body">
                <p>We are now offering coaching for the following: <br>
                    <div style="text-align: left; padding: 10px 20px">1. University Clinical Aptitude Test (UCAT)<br>2.  Language classes in English, French, and German, along with <br>3. coding classes!

                    </div></p>
            </div>
            <div class="popup-header">
                
                <h5>Register now and take the first step toward success!</h5>
            </div>
            <!-- Popup Buttons -->
            <div class="popup-buttons">
                <button class="popup-button secondary" onclick="dismissPopup()">No Thanks</button>
                <button class="popup-button primary" onclick="continueAction()">Register Now!</button>
            </div>
        </div>
    </div>

    <script>
        // Cookie management functions
        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
            document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
        }

        function getCookie(name) {
            const nameEQ = name + '=';
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        // Popup control functions
        const popupOverlay = document.querySelector('.popup-overlay');
        const popupContainer = document.querySelector('.popup-container');
        const MAX_DISMISSALS = 2;
        const COOKIE_EXPIRY_DAYS = 30;

        function showPopup() {
            popupOverlay.style.display = 'flex';
            setTimeout(() => {
                popupOverlay.classList.add('active');
            }, 100);
        }

        function closePopup() {
            popupOverlay.classList.remove('active');
            setTimeout(() => {
                popupOverlay.style.display = 'none';
            }, 300); // Match the transition duration
        }

        function dismissPopup() {
            // Get current dismissal count
            let dismissalCount = parseInt(getCookie('mkscholars_popup_dismissed') || '0');
            dismissalCount++;
            
            // Update cookie with new count
            setCookie('mkscholars_popup_dismissed', dismissalCount, COOKIE_EXPIRY_DAYS);
            
            closePopup();
        }

        function continueAction() {
            // Reset dismissal count when user engages
            setCookie('mkscholars_popup_dismissed', '0', COOKIE_EXPIRY_DAYS);
            
            // Redirect or continue flow
            
            closePopup();
            
            // You can replace the alert with an actual redirect:
            window.open('courses', '_blank');
        }

        // Check if popup should be shown on page load
        window.onload = () => {
            const dismissalCount = parseInt(getCookie('mkscholars_popup_dismissed') || '0');
            
            // Only show if dismissed less than MAX_DISMISSALS times
            if (dismissalCount < MAX_DISMISSALS) {
                // Add a small delay for better UX
                setTimeout(showPopup, 1000);
            }
        };
    </script>
</body>
</html>