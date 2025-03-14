<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Cards</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
        }

        .scholarship-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            width: 300px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .scholarship-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .card-title {
            color: #2c3e50;
            margin: 0;
            font-size: 1.5rem;
        }

        .card-subtitle {
            color: #34495e;
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        .card-content {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .deadline {
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
            padding: 0.5rem;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .apply-button {
            background: rgba(52, 152, 219, 0.8);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 1rem;
            backdrop-filter: blur(5px);
        }

        .apply-button:hover {
            background: rgba(41, 128, 185, 0.9);
        }

        .status-indicator {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-top: 1rem;
        }

        .status-open {
            background: rgba(46, 204, 113, 0.15);
            color: #27ae60;
        }

        .status-closed {
            background: rgba(231, 76, 60, 0.15);
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="scholarship-card">
        <div class="card-header">
            <h2 class="card-title">STEM Innovation Scholarship</h2>
            <p class="card-subtitle">For Future Technology Leaders</p>
        </div>
        <div class="card-content">
            <p>🎓 Up to $10,000 annual award<br>
            📚 Open to undergraduate students<br>
            ⏳ 2 years commitment</p>
            <div class="deadline">Deadline: March 15, 2024</div>
            <span class="status-indicator status-open">Open for Applications</span>
        </div>
        <button class="apply-button">Apply Now</button>
    </div>

    <div class="scholarship-card">
        <div class="card-header">
            <h2 class="card-title">Arts & Culture Fellowship</h2>
            <p class="card-subtitle">Supporting Creative Minds</p>
        </div>
        <div class="card-content">
            <p>🎓 $5,000 - $15,000 awards<br>
            📚 All education levels welcome<br>
            ⏳ Portfolio required</p>
            <div class="deadline">Deadline: April 1, 2024</div>
            <span class="status-indicator status-open">Open for Applications</span>
        </div>
        <button class="apply-button">Apply Now</button>
    </div>

    <div class="scholarship-card">
        <div class="card-header">
            <h2 class="card-title">Community Service Award</h2>
            <p class="card-subtitle">Recognizing Social Impact</p>
        </div>
        <div class="card-content">
            <p>🎓 $2,500 one-time grant<br>
            📚 High school seniors<br>
            ⏳ Minimum 100 service hours</p>
            <div class="deadline">Deadline Passed</div>
            <span class="status-indicator status-closed">Applications Closed</span>
        </div>
        <button class="apply-button" disabled>Apply Now</button>
    </div>
</body>
</html>