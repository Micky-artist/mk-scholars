<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horizontal Scholarship Cards</title>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #f8f9ff 100%);
            min-height: 100vh;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            align-items: center;
        }

        .scholarship-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 2rem;
            width: 800px;
            max-width: 90%;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            gap: 2rem;
            position: relative;
            overflow: hidden;
        }

        .scholarship-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, 
                rgba(255, 255, 255, 0.1) 0%, 
                rgba(255, 255, 255, 0.3) 50%, 
                rgba(255, 255, 255, 0.1) 100%);
            transform: rotate(15deg);
            pointer-events: none;
        }

        .scholarship-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
        }

        .card-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            color: #1a1a1a;
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .card-subtitle {
            color: #4a4a4a;
            margin: 0;
            font-size: 1rem;
            font-weight: 500;
        }

        .card-details {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.95rem;
        }

        .detail-item span {
            background: rgba(0, 0, 0, 0.05);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .progress-bar {
            height: 6px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 3px;
            overflow: hidden;
            margin-top: auto;
        }

        .progress-fill {
            height: 100%;
            width: 65%;
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .apply-button {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            align-self: flex-start;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .apply-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }

        .deadline {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .status-tag {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(76, 175, 80, 0.15);
            color: #4CAF50;
            padding: 0.3rem 1rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="scholarship-card">
        <div class="card-icon">🚀</div>
        <div class="card-content">
            <div class="card-header">
                <div>
                    <h2 class="card-title">STEM Innovation Grant</h2>
                    <p class="card-subtitle">Empowering Future Tech Leaders</p>
                </div>
                <button class="apply-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                    </svg>
                    Apply Now
                </button>
            </div>
            <div class="card-details">
                <div class="detail-item">
                    🏆 Award: <span>$5k - $15k</span>
                </div>
                <div class="detail-item">
                    📚 Level: <span>Undergrad</span>
                </div>
                <div class="detail-item">
                    ⏳ Duration: <span>2 Years</span>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <p class="deadline">⏰ Days Remaining: 42</p>
        </div>
        <div class="status-tag">Open</div>
    </div>

    <div class="scholarship-card">
        <div class="card-icon">🎨</div>
        <div class="card-content">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Creative Arts Fellowship</h2>
                    <p class="card-subtitle">Nurturing Artistic Vision</p>
                </div>
                <button class="apply-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                    </svg>
                    Apply Now
                </button>
            </div>
            <div class="card-details">
                <div class="detail-item">
                    🏆 Award: <span>$2k - $10k</span>
                </div>
                <div class="detail-item">
                    📚 Level: <span>All Levels</span>
                </div>
                <div class="detail-item">
                    ⏳ Deadline: <span>Apr 1, 2024</span>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 85%"></div>
            </div>
            <p class="deadline">⏰ Days Remaining: 58</p>
        </div>
        <div class="status-tag" style="background: rgba(255, 193, 7, 0.15); color: #FFA000;">
            Limited
        </div>
    </div>
</body>
</html>