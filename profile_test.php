<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['userId'];
$userName = $_SESSION['userName'] ?? $_SESSION['username'] ?? '';
$userEmail = $_SESSION['userEmail'] ?? $_SESSION['NoEmail'] ?? '';
$userPhone = $_SESSION['userPhone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <title>Profile Test | MK Scholars</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Universal Navigation -->
            <?php 
            try {
                include("./partials/universalNavigation.php"); 
            } catch (Exception $e) {
                echo "<!-- Navigation error: " . $e->getMessage() . " -->";
            }
            ?>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content">
                <div class="content-container">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="page-title">Profile Test</h1>
                                <p class="page-subtitle">Testing profile page functionality</p>
                            </div>
                        </div>
                    </div>

                    <!-- Simple Test Card -->
                    <div class="glass-panel slide-in">
                        <h5>Debug Information</h5>
                        <p>User ID: <?php echo $userId; ?></p>
                        <p>User Name: <?php echo htmlspecialchars($userName); ?></p>
                        <p>User Email: <?php echo htmlspecialchars($userEmail); ?></p>
                        <p>User Phone: <?php echo htmlspecialchars($userPhone); ?></p>
                        <p>Session Data: <?php echo json_encode($_SESSION); ?></p>
                    </div>

                    <!-- Test Subscriptions -->
                    <div class="glass-panel slide-in">
                        <h5>Test Subscriptions</h5>
                        <button class="btn btn-primary" onclick="testSubscriptions()">Test Load Subscriptions</button>
                        <div id="testResult" class="mt-3"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function testSubscriptions() {
            console.log('Testing subscriptions...');
            $('#testResult').html('<div class="loading"></div> Loading...');
            
            $.ajax({
                url: 'php/list_user_subscriptions.php',
                method: 'GET',
                data: { userId: <?php echo (int)$userId; ?> },
                dataType: 'json',
                success: function(data) {
                    console.log('Subscription data:', data);
                    $('#testResult').html('<pre>' + JSON.stringify(data, null, 2) + '</pre>');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', status, error);
                    $('#testResult').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                }
            });
        }
    </script>
</body>
</html>
