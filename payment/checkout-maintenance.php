<?php
// Optional: keep session for showing user info if needed later
include('../config/session.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Under Maintenance – MK Scholars</title>
    <link rel="shortcut icon" href="../images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --whatsapp-green: #25d366;
            --phone-blue: #007bff;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.1);
            --shadow-xl: 0 25px 50px rgba(0,0,0,0.15);
            --radius-xl: 20px;
        }

        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-primary);
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 600px;
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .logo-section {
            position: relative;
            z-index: 1;
            margin-bottom: 20px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .content {
            padding: 40px 30px;
        }

        .maintenance-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #fff7ed, #fed7aa);
            color: #9a3412;
            border: 1px solid #fed7aa;
            padding: 12px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(154, 52, 18, 0.1);
        }

        .info-card {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .info-card h3 {
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card p {
            color: var(--text-secondary);
            line-height: 1.7;
            font-size: 1rem;
            margin-bottom: 25px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .contact-item {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            padding: 25px 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .contact-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .contact-item.phone {
            border-color: var(--phone-blue);
        }

        .contact-item.whatsapp {
            border-color: var(--whatsapp-green);
        }

        .contact-item.payment {
            border-color: var(--success-color);
        }

        .contact-item.support {
            border-color: var(--primary-color);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
            color: white;
        }

        .contact-icon.phone {
            background: linear-gradient(135deg, var(--phone-blue), #0056b3);
        }

        .contact-icon.whatsapp {
            background: linear-gradient(135deg, var(--whatsapp-green), #128c7e);
        }

        .contact-icon.payment {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }

        .contact-icon.support {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        }

        .contact-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .contact-details {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .contact-details a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
        }

        .contact-details a:hover {
            text-decoration: underline;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 24px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, var(--whatsapp-green), #128c7e);
            color: #fff;
            box-shadow: 0 4px 15px rgba(18, 140, 126, 0.3);
        }

        .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(18, 140, 126, 0.4);
        }

        .btn-outline {
            background: white;
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .footer-note {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 16px;
            }

            .header {
                padding: 30px 20px;
            }

            .content {
                padding: 30px 20px;
            }

            .title {
                font-size: 1.6rem;
            }

            .contact-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .contact-item {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    <img src="../images/logo/logoRound.png" alt="MK Scholars Logo">
                </div>
                <h1 class="title">Payments Under Maintenance</h1>
                <p class="subtitle">We're improving our checkout system to provide excellent support and reliability</p>
            </div>
        </div>
        
        <div class="content">
            <div class="maintenance-badge">
                <i class="fas fa-tools"></i>
                Temporary Maintenance
            </div>

            <div class="info-card">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    What you can do now
                </h3>
                <p>
                    You can still enroll or get assistance right away. Please reach us via any option below
                    and our team will take care of your registration and support immediately.
                </p>
                
                <div class="contact-grid">
                    <div class="contact-item phone">
                        <div class="contact-icon phone">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-title">Call Us</div>
                        <div class="contact-details">
                            <a href="tel:+250798611161">+250 798 611 161</a>
                        </div>
                    </div>
                    
                    <div class="contact-item support">
                        <div class="contact-icon support">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="contact-title">Support Chat</div>
                        <div class="contact-details">
                            <a href="../conversations.php">Open Support Chat</a>
                        </div>
                    </div>
                    
                    <div class="contact-item payment">
                        <div class="contact-icon payment">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="contact-title">Pay to this number</div>
                        <div class="contact-details">+250 798 611 161</div>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a class="btn btn-whatsapp" href="https://wa.me/250798611161" target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp"></i>
                    Open WhatsApp Chat
                </a>
                <a class="btn btn-outline" href="../e-learning.php">
                    <i class="fas fa-arrow-left"></i>
                    Back to Courses
                </a>
            </div>

            <div class="footer-note">
                We will get back to you as soon as everything is solved.
            </div>
        </div>
    </div>
</body>
</html>


