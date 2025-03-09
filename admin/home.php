<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            min-height: 250px;
        }
        
        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .gradient-1 {
            background: linear-gradient(45deg, #4e73df, #224abe);
        }
        
        .gradient-2 {
            background: linear-gradient(45deg, #1cc88a, #13855c);
        }
        
        .gradient-3 {
            background: linear-gradient(45deg, #f6c23e, #dda20a);
        }
        
        .gradient-4 {
            background: linear-gradient(45deg, #e74a3b, #c03526);
        }
        
        .gradient-5 {
            background: linear-gradient(45deg, #36b9cc, #258391);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-5">Admin Dashboard</h1>
        
        <div class="row g-4">
            <!-- Message Students Card -->
            <div class="col-md-3">
                <a href="message-students.html" class="text-decoration-none">
                    <div class="card dashboard-card gradient-1 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-comments card-icon"></i>
                            <h3 class="card-title mb-3">Message Students</h3>
                            <p class="card-text">Send announcements and individual messages to students</p>
                            <span class="badge bg-light text-dark mt-2">New Messages: 5</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Student Applications Card -->
            <div class="col-md-3">
                <a href="student-applications.html" class="text-decoration-none">
                    <div class="card dashboard-card gradient-2 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-file-alt card-icon"></i>
                            <h3 class="card-title mb-3">Applications</h3>
                            <p class="card-text">Review and manage student scholarship applications</p>
                            <span class="badge bg-light text-dark mt-2">Pending: 12</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- User Logs Card -->
            <div class="col-md-3">
                <a href="user-logs.html" class="text-decoration-none">
                    <div class="card dashboard-card gradient-3 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-clipboard-list card-icon"></i>
                            <h3 class="card-title mb-3">User Logs</h3>
                            <p class="card-text">Monitor system activities and user interactions</p>
                            <span class="badge bg-light text-dark mt-2">Today's Logs: 42</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Manage Users Card -->
            <div class="col-md-3">
                <a href="manage-users.html" class="text-decoration-none">
                    <div class="card dashboard-card gradient-4 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-users card-icon"></i>
                            <h3 class="card-title mb-3">Manage Users</h3>
                            <p class="card-text">View and manage all system users and permissions</p>
                            <span class="badge bg-light text-dark mt-2">Total Users: 154</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Scholarships Card -->
            <div class="col-md-3">
                <a href="manage-scholarships.html" class="text-decoration-none">
                    <div class="card dashboard-card gradient-5 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-graduation-cap card-icon"></i>
                            <h3 class="card-title mb-3">Scholarships</h3>
                            <p class="card-text">Manage scholarship programs and opportunities</p>
                            <span class="badge bg-light text-dark mt-2">Active: 23</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>