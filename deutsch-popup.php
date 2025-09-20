<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MK Deutsch Academy News</title>
  <style>
    /* Responsive Deutsch Academy Popup */
    .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
      z-index: 9999;
      padding: 20px;
      box-sizing: border-box;
    }

    .popup-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .popup-container {
      background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 500px;
      padding: 30px;
      text-align: center;
      position: relative;
      transform: scale(0.8);
      opacity: 0;
      transition: transform 0.3s ease, opacity 0.3s ease;
      color: white;
      border: 3px solid #FFD700;
    }

    .popup-overlay.active .popup-container {
      transform: scale(1);
      opacity: 1;
    }

    /* Close Button - Large and Easy to Tap */
    .close-button {
      position: absolute;
      top: 15px;
      right: 15px;
      background: #DC143C;
      border: none;
      font-size: 24px;
      color: white;
      cursor: pointer;
      width: 45px;
      height: 45px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4);
      z-index: 10;
    }

    .close-button:hover {
      background: #B22222;
      transform: scale(1.1);
      box-shadow: 0 6px 20px rgba(220, 20, 60, 0.6);
    }

    .close-button:active {
      transform: scale(0.95);
    }

    /* Header */
    .popup-header {
      margin-bottom: 25px;
      margin-top: 20px;
    }

    .popup-header .emoji {
      font-size: 60px;
      margin-bottom: 15px;
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .popup-header h2 {
      font-size: 28px;
      color: #FFD700;
      margin: 0;
      font-weight: 700;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .popup-header h3 {
      font-size: 22px;
      color: white;
      margin: 10px 0;
      font-weight: 600;
    }

    /* Body */
    .popup-body {
      margin-bottom: 25px;
    }

    .popup-body p {
      font-size: 18px;
      color: white;
      line-height: 1.6;
      margin-bottom: 20px;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .highlight-box {
      background: rgba(255, 215, 0, 0.2);
      border: 2px solid #FFD700;
      border-radius: 15px;
      padding: 20px;
      margin: 20px 0;
    }

    .highlight-box h4 {
      color: #FFD700;
      font-size: 20px;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .highlight-box ul {
      text-align: left;
      color: white;
      font-size: 16px;
      line-height: 1.8;
    }

    .highlight-box li {
      margin-bottom: 8px;
    }

    /* Buttons */
    .popup-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 25px;
      flex-wrap: wrap;
    }

    .popup-button {
      padding: 15px 30px;
      border: none;
      border-radius: 30px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      min-width: 120px;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .popup-button.primary {
      background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
      color: #000;
      box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
    }

    .popup-button.primary:hover {
      background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.6);
    }

    .popup-button.secondary {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border: 2px solid rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(10px);
    }

    .popup-button.secondary:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: translateY(-3px);
      border-color: rgba(255, 255, 255, 0.5);
    }

    .popup-button:active {
      transform: translateY(-1px);
    }

    /* Mobile Optimizations */
    @media (max-width: 768px) {
      .popup-overlay {
        padding: 10px;
        align-items: flex-start;
        padding-top: 20px;
      }

      .popup-container {
        padding: 20px 15px;
        max-width: 100%;
        border-radius: 15px;
        margin: 0;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
      }

      .close-button {
        width: 50px;
        height: 50px;
        font-size: 26px;
        top: 8px;
        right: 8px;
      }

      .popup-header h2 {
        font-size: 22px;
        line-height: 1.2;
      }

      .popup-header h3 {
        font-size: 18px;
        margin: 8px 0;
      }

      .popup-body p {
        font-size: 15px;
        margin-bottom: 15px;
      }

      .highlight-box {
        padding: 12px;
        margin: 15px 0;
      }

      .highlight-box h4 {
        font-size: 16px;
        margin-bottom: 10px;
      }

      .highlight-box ul {
        font-size: 14px;
        line-height: 1.6;
      }

      .popup-buttons {
        flex-direction: column;
        gap: 10px;
        margin-top: 20px;
      }

      .popup-button {
        padding: 16px 20px;
        font-size: 15px;
        width: 100%;
      }
    }

    /* iPhone SE and similar small devices */
    @media (max-width: 414px) {
      .popup-overlay {
        padding: 5px;
        padding-top: 15px;
      }

      .popup-container {
        padding: 15px 12px;
        max-height: calc(100vh - 30px);
        border-radius: 12px;
      }

      .close-button {
        width: 48px;
        height: 48px;
        font-size: 24px;
        top: 6px;
        right: 6px;
      }

      .popup-header .emoji {
        font-size: 40px;
        margin-bottom: 10px;
      }

      .popup-header h2 {
        font-size: 20px;
        line-height: 1.1;
      }

      .popup-header h3 {
        font-size: 16px;
        margin: 6px 0;
      }

      .popup-body p {
        font-size: 14px;
        margin-bottom: 12px;
        line-height: 1.4;
      }

      .highlight-box {
        padding: 10px;
        margin: 12px 0;
      }

      .highlight-box h4 {
        font-size: 15px;
        margin-bottom: 8px;
      }

      .highlight-box ul {
        font-size: 13px;
        line-height: 1.5;
      }

      .highlight-box li {
        margin-bottom: 6px;
      }

      .popup-button {
        padding: 14px 18px;
        font-size: 14px;
        border-radius: 25px;
      }
    }

    /* Extra small devices (iPhone SE, etc.) */
    @media (max-width: 375px) {
      .popup-overlay {
        padding: 3px;
        padding-top: 10px;
      }

      .popup-container {
        padding: 12px 10px;
        max-height: calc(100vh - 20px);
        border-radius: 10px;
      }

      .close-button {
        width: 45px;
        height: 45px;
        font-size: 22px;
        top: 5px;
        right: 5px;
      }

      .popup-header {
        margin-bottom: 15px;
        margin-top: 15px;
      }

      .popup-header .emoji {
        font-size: 35px;
        margin-bottom: 8px;
      }

      .popup-header h2 {
        font-size: 18px;
        line-height: 1.1;
      }

      .popup-header h3 {
        font-size: 15px;
        margin: 5px 0;
      }

      .popup-body {
        margin-bottom: 15px;
      }

      .popup-body p {
        font-size: 13px;
        margin-bottom: 10px;
        line-height: 1.3;
      }

      .highlight-box {
        padding: 8px;
        margin: 10px 0;
      }

      .highlight-box h4 {
        font-size: 14px;
        margin-bottom: 6px;
      }

      .highlight-box ul {
        font-size: 12px;
        line-height: 1.4;
        padding-left: 15px;
      }

      .highlight-box li {
        margin-bottom: 4px;
      }

      .popup-buttons {
        gap: 8px;
        margin-top: 15px;
      }

      .popup-button {
        padding: 12px 16px;
        font-size: 13px;
        border-radius: 20px;
      }
    }

    /* Ultra small devices (320px width) */
    @media (max-width: 320px) {
      .popup-overlay {
        padding: 2px;
        padding-top: 8px;
      }

      .popup-container {
        padding: 10px 8px;
        max-height: calc(100vh - 16px);
        border-radius: 8px;
      }

      .close-button {
        width: 42px;
        height: 42px;
        font-size: 20px;
        top: 4px;
        right: 4px;
      }

      .popup-header {
        margin-bottom: 12px;
        margin-top: 12px;
      }

      .popup-header .emoji {
        font-size: 30px;
        margin-bottom: 6px;
      }

      .popup-header h2 {
        font-size: 16px;
        line-height: 1.1;
      }

      .popup-header h3 {
        font-size: 13px;
        margin: 4px 0;
      }

      .popup-body {
        margin-bottom: 12px;
      }

      .popup-body p {
        font-size: 12px;
        margin-bottom: 8px;
        line-height: 1.2;
      }

      .highlight-box {
        padding: 6px;
        margin: 8px 0;
      }

      .highlight-box h4 {
        font-size: 12px;
        margin-bottom: 4px;
      }

      .highlight-box ul {
        font-size: 11px;
        line-height: 1.3;
        padding-left: 12px;
      }

      .highlight-box li {
        margin-bottom: 3px;
      }

      .popup-buttons {
        gap: 6px;
        margin-top: 12px;
      }

      .popup-button {
        padding: 10px 14px;
        font-size: 12px;
        border-radius: 18px;
      }
    }

    /* Touch-friendly improvements */
    .popup-button,
    .close-button {
      -webkit-tap-highlight-color: transparent;
      touch-action: manipulation;
    }

    /* Prevent body scroll when popup is open */
    body.popup-open {
      overflow: hidden;
      height: 100%;
    }
  </style>
</head>
<body>

  <div class="popup-overlay">
    <div class="popup-container">
      <!-- Close Button - Large and Easy to Tap -->
      <button class="close-button" onclick="dismissPopup()" aria-label="Close popup">×</button>

      <!-- Header -->
      <div class="popup-header">
        <div class="emoji">🇩🇪</div>
        <h2>MK Deutsch Academy</h2>
        <h3>📢 Exciting News!</h3>
      </div>

      <!-- Body -->
      <div class="popup-body">
        <p>
          We're thrilled to announce the launch of our new <strong>German Language Academy</strong>!
        </p>
        
        <div class="highlight-box">
          <h4>🎓 What We Offer:</h4>
          <ul>
            <li>✅ German classes from A1 to B2 levels</li>
            <li>✅ Physical and Online learning options</li>
            <li>✅ Professional certified instructors</li>
            <li>✅ Flexible schedules (Weekdays & Weekends)</li>
            <li>✅ Affordable pricing: 200K Rwf (Physical) / 100K Rwf (Online)</li>
          </ul>
        </div>

        <p>
          <strong>Registration is now open for October 2025 intake!</strong><br>
        </p>
      </div>

      <!-- Buttons -->
      <div class="popup-buttons">
        <button class="popup-button secondary" onclick="dismissPopup()">Maybe Later</button>
        <a href="deutsch-academy" class="popup-button primary" onclick="continueAction()">Register Now!</a>
      </div>
    </div>
  </div>

  <script>
    // Deutsch Academy Popup Cookie Management
    const COOKIE_KEY = 'mk_deutsch_popup_2025';
    const MAX_DISMISSALS = 2; // Show only twice
    const COOKIE_EXPIRY_HOURS = 24; // Show twice in 24 hours
    const popupOverlay = document.querySelector('.popup-overlay');

    function setCookie(name, value, hours) {
      const expires = new Date();
      expires.setTime(expires.getTime() + hours * 60 * 60 * 1000);
      document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
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
      document.body.classList.add('popup-open');
      popupOverlay.style.display = 'flex';
      // Force reflow for mobile browsers
      popupOverlay.offsetHeight;
      setTimeout(() => popupOverlay.classList.add('active'), 100);
    }

    function closePopup() {
      popupOverlay.classList.remove('active');
      document.body.classList.remove('popup-open');
      setTimeout(() => popupOverlay.style.display = 'none', 300);
    }

    function dismissPopup() {
      let count = parseInt(getCookie(COOKIE_KEY) || '0');
      setCookie(COOKIE_KEY, ++count, COOKIE_EXPIRY_HOURS);
      closePopup();
    }

    function continueAction() {
      setCookie(COOKIE_KEY, '999', COOKIE_EXPIRY_HOURS); // Set high value to prevent popup from showing again
      closePopup();
      // Let the link navigate naturally
    }

    // Close popup when clicking/tapping outside
    popupOverlay.addEventListener('click', function(e) {
      if (e.target === popupOverlay) {
        dismissPopup();
      }
    });

    // Better touch handling for mobile
    popupOverlay.addEventListener('touchend', function(e) {
      if (e.target === popupOverlay) {
        e.preventDefault();
        dismissPopup();
      }
    });

    // Close popup with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && popupOverlay.classList.contains('active')) {
        dismissPopup();
      }
    });

    // Show popup on page load
    window.addEventListener('load', () => {
      const count = parseInt(getCookie(COOKIE_KEY) || '0');
      if (count < MAX_DISMISSALS) {
        setTimeout(showPopup, 1500); // Show after 1.5 seconds
      }
    });

    // Mobile-specific improvements
    document.addEventListener('DOMContentLoaded', function() {
      const popupOverlay = document.querySelector('.popup-overlay');
      const popupContainer = document.querySelector('.popup-container');
      
      // Swipe to close functionality
      let startY = 0;
      let currentY = 0;
      let isDragging = false;

      popupContainer.addEventListener('touchstart', function(e) {
        startY = e.touches[0].clientY;
        isDragging = true;
      }, { passive: true });

      popupContainer.addEventListener('touchmove', function(e) {
        if (!isDragging) return;
        currentY = e.touches[0].clientY;
        const diff = currentY - startY;
        
        // If swiping down more than 50px, close popup
        if (diff > 50) {
          dismissPopup();
          isDragging = false;
        }
      }, { passive: true });

      popupContainer.addEventListener('touchend', function() {
        isDragging = false;
      }, { passive: true });

      // Prevent zoom on double tap for iOS
      let lastTouchEnd = 0;
      document.addEventListener('touchend', function(event) {
        const now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
          event.preventDefault();
        }
        lastTouchEnd = now;
      }, false);

      // Improve close button accessibility on mobile
      const closeButton = document.querySelector('.close-button');
      closeButton.addEventListener('touchstart', function() {
        this.style.transform = 'scale(0.95)';
      }, { passive: true });
      
      closeButton.addEventListener('touchend', function() {
        this.style.transform = 'scale(1)';
      }, { passive: true });
    });
  </script>

</body>
</html>

