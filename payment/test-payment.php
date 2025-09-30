<!DOCTYPE html>
<html>
<head>
    <title>Test Flutterwave v4 Payment</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"], input[type="email"], input[type="number"], select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="number"]:focus, select:focus {
            outline: none;
            border-color: #3498db;
        }
        button {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        button:hover {
            background: #2980b9;
        }
        .info {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Test Flutterwave v4 Payment</h2>
        
        <div class="info">
            <strong>Note:</strong> This is a test form to verify Flutterwave v4 integration. 
            Use test credentials and test card numbers for testing.
        </div>
        
        <form method="POST" action="checkout.php">
            <div class="form-group">
                <label>Course ID:</label>
                <input type="number" name="course" value="1" required>
            </div>
            
            <div class="form-group">
                <label>Payment Code Name:</label>
                <select name="subscription" required>
                    <option value="basic">Basic Package</option>
                    <option value="premium">Premium Package</option>
                    <option value="complete">Complete Package</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Test Amount (RWF):</label>
                <input type="number" name="test_amount" value="1000" min="100">
            </div>
            
            <div class="form-group">
                <label>Currency:</label>
                <select name="test_currency">
                    <option value="RWF">RWF - Rwandan Franc</option>
                    <option value="USD">USD - US Dollar</option>
                    <option value="EUR">EUR - Euro</option>
                </select>
            </div>
            
            <button type="submit">Test Payment</button>
        </form>
        
        <div class="info" style="margin-top: 20px;">
            <strong>Test Cards (Flutterwave):</strong><br>
            • Visa: 4187427415564246<br>
            • Mastercard: 5438898014560229<br>
            • CVV: Any 3 digits<br>
            • Expiry: Any future date
        </div>
    </div>
</body>
</html>
