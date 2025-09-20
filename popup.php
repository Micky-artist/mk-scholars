new<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MK Scholars Popup</title>
  <style>
    .popup-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
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

    .popup-header {
      margin-bottom: 20px;
    }

    .popup-header h2 {
      font-size: 24px;
      color: #333;
      margin: 0;
    }

    .popup-header h5 {
      margin-top: 10px;
      color: #444;
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
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .popup-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
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

    .close-button {
      position: absolute;
      top: 15px;
      right: 15px;
      background: #e66a6a;
      border: none;
      font-size: 20px;
      color: #fff;
      cursor: pointer;
      width: 0.9cm;
      height: 0.9cm;
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

  <div class="popup-overlay">
    <div class="popup-container">
      <!-- Close Button -->
      <button class="close-button" onclick="dismissPopup()">×</button>

      <!-- Header -->
      <div class="popup-header">
        <div class="emoji">
          <img src="https://mkscholars.com/images/logo/logoRound.png" alt="MK Scholars" width="200" height="200">
        </div>
        <h2>📢 <br> New Programs at <br> MK SCHOLARS!</h2>
      </div>

      <!-- Body -->
      <div class="popup-body">
        <p>
          We are excited to announce two new live training programs:
        </p>
        <div style="text-align: left; padding: 10px 20px;">
          <strong>1. 30-Day Coding Bootcamp</strong><br>
          <!-- Learn HTML, CSS, JS, React, MySQL & Node.js. Evening classes.<br><br> -->
          <strong>2. English Communication Course</strong><br>
          <!-- Improve fluency, writing, speaking & grammar. 2-month program. -->
        </div>
      </div>

      <div class="popup-header">
        <h5>🎓 Seats are limited — register today and build your future!</h5>
      </div>

      <!-- Buttons -->
      <div class="popup-buttons">
        <button class="popup-button secondary" onclick="dismissPopup()">Not Now</button>
        <button class="popup-button primary" onclick="continueAction()">Register Now!</button>
      </div>
    </div>
  </div>

  <script>
    // Unique new cookie key for reset
    const COOKIE_KEY = 'mks_popup_reset_2025';
    const MAX_DISMISSALS = 2;
    const COOKIE_EXPIRY_DAYS = 30;
    const popupOverlay = document.querySelector('.popup-overlay');

    function setCookie(name, value, days) {
      const expires = new Date();
      expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
      document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
    }

    function getCookie(name) {
      const nameEQ = name + '=';
      const ca = document.cookie.split(';');
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
      }
      return null;
    }

    function showPopup() {
      popupOverlay.style.display = 'flex';
      setTimeout(() => popupOverlay.classList.add('active'), 100);
    }

    function closePopup() {
      popupOverlay.classList.remove('active');
      setTimeout(() => popupOverlay.style.display = 'none', 300);
    }

    function dismissPopup() {
      let count = parseInt(getCookie(COOKIE_KEY) || '0');
      setCookie(COOKIE_KEY, ++count, COOKIE_EXPIRY_DAYS);
      closePopup();
    }

    function continueAction() {
      setCookie(COOKIE_KEY, '0', COOKIE_EXPIRY_DAYS);
      closePopup();
      window.open('courses', '_blank'); // adjust link if needed
    }

    window.onload = () => {
      const count = parseInt(getCookie(COOKIE_KEY) || '0');
      if (count < MAX_DISMISSALS) {
        setTimeout(showPopup, 1000);
      }
    };
  </script>

</body>
</html>
