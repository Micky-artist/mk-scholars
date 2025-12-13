<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MK Driving School News</title>
  <style>
    /* Modern MK Driving School Popup - Redesigned */
    .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
      z-index: 9999;
      padding: 15px;
      box-sizing: border-box;
    }

    .popup-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .popup-container {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
      width: 100%;
      max-width: 380px;
      padding: 24px;
      text-align: left;
      position: relative;
      transform: scale(0.9) translateY(20px);
      opacity: 0;
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
      color: #1a1a1a;
    }

    .popup-overlay.active .popup-container {
      transform: scale(1) translateY(0);
      opacity: 1;
    }

    /* Close Button - Minimal Design */
    .close-button {
      position: absolute;
      top: 12px;
      right: 12px;
      background: transparent;
      border: none;
      font-size: 22px;
      color: #6b7280;
      cursor: pointer;
      width: 32px;
      height: 32px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 8px;
      transition: all 0.2s ease;
      z-index: 10;
      line-height: 1;
    }

    .close-button:hover {
      background: #f3f4f6;
      color: #1a1a1a;
    }

    .close-button:active {
      transform: scale(0.95);
    }

    /* Header */
    .popup-header {
      margin-bottom: 16px;
      margin-top: 0;
      padding-right: 30px;
    }

    .popup-header h2 {
      font-size: 22px;
      color: #1a1a1a;
      margin: 0 0 6px 0;
      font-weight: 700;
      letter-spacing: -0.5px;
    }

    .popup-header h3 {
      font-size: 14px;
      color: #6b7280;
      margin: 0;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Body */
    .popup-body {
      margin-bottom: 20px;
    }

    .popup-body p {
      font-size: 14px;
      color: #4b5563;
      line-height: 1.6;
      margin-bottom: 16px;
    }

    .popup-body strong {
      color: #1a1a1a;
      font-weight: 600;
    }

    .highlight-box {
      background: #f8fafc;
      border: 1px solid #e5e7eb;
      border-left: 3px solid #6366f1;
      border-radius: 8px;
      padding: 16px;
      margin: 16px 0;
    }

    .highlight-box h4 {
      color: #1a1a1a;
      font-size: 15px;
      margin-bottom: 12px;
      font-weight: 600;
    }

    .highlight-box ul {
      text-align: left;
      color: #4b5563;
      font-size: 13px;
      line-height: 1.7;
      margin: 0;
      padding-left: 20px;
      list-style: none;
    }

    .highlight-box li {
      margin-bottom: 8px;
      position: relative;
      padding-left: 20px;
    }

    .highlight-box li::before {
      content: '•';
      position: absolute;
      left: 0;
      color: #6366f1;
      font-weight: bold;
      font-size: 18px;
    }

    /* Buttons */
    .popup-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    .popup-button {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .popup-button.primary {
      background: #6366f1;
      color: #ffffff;
      box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    }

    .popup-button.primary:hover {
      background: #4f46e5;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .popup-button.secondary {
      background: #f3f4f6;
      color: #4b5563;
      border: 1px solid #e5e7eb;
    }

    .popup-button.secondary:hover {
      background: #e5e7eb;
      color: #1a1a1a;
    }

    .popup-button:active {
      transform: translateY(0);
    }

    /* Mobile Optimizations */
    @media (max-width: 768px) {
      .popup-overlay {
        padding: 10px;
        align-items: flex-start;
        padding-top: 20px;
      }

      .popup-container {
        padding: 20px;
        max-width: 100%;
        border-radius: 12px;
        margin: 0;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
      }

      .close-button {
        width: 32px;
        height: 32px;
        font-size: 20px;
        top: 10px;
        right: 10px;
      }

      .popup-header h2 {
        font-size: 20px;
      }

      .popup-header h3 {
        font-size: 13px;
      }

      .popup-body p {
        font-size: 13px;
        margin-bottom: 14px;
      }

      .highlight-box {
        padding: 14px;
        margin: 14px 0;
      }

      .highlight-box h4 {
        font-size: 14px;
        margin-bottom: 10px;
      }

      .highlight-box ul {
        font-size: 12px;
      }

      .popup-buttons {
        flex-direction: column;
        gap: 8px;
        margin-top: 18px;
      }

      .popup-button {
        padding: 12px 18px;
        font-size: 14px;
        width: 100%;
      }
    }

    @media (max-width: 480px) {
      .popup-container {
        padding: 18px;
        max-width: 100%;
      }

      .popup-header h2 {
        font-size: 18px;
      }

      .popup-header h3 {
        font-size: 12px;
      }

      .popup-body p {
        font-size: 12px;
      }

      .highlight-box {
        padding: 12px;
      }

      .highlight-box h4 {
        font-size: 13px;
      }

      .highlight-box ul {
        font-size: 11px;
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
        <h3>New Program</h3>
        <h2>MK Driving School</h2>
      </div>

      <!-- Body -->
      <div class="popup-body">
        <p>
          We're thrilled to announce the launch of our new <strong>MK Driving School</strong>!
        </p>
        
        <div class="highlight-box">
          <h4>What We Offer</h4>
          <ul>
            <li>Professional driving training</li>
            <li>Theory exam preparation (Provisoire)</li>
            <li>Real past exam questions and answers</li>
            <li>Study smarter and faster</li>
            <li>Pass the first time</li>
          </ul>
        </div>

        <p>
          <strong>Learn. Practice. Pass.</strong>
        </p>
      </div>

      <!-- Buttons -->
      <div class="popup-buttons">
        <button class="popup-button secondary" onclick="dismissPopup()">Maybe Later</button>
        <a href="subscription?course=3" class="popup-button primary" onclick="continueAction()">Register Now!</a>
      </div>
    </div>
  </div>

  <script>
    // MK Driving School Popup Cookie Management
    const COOKIE_KEY = 'mk_driving_popup_2025';
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


