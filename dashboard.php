<?php
session_start();
include('./dbconnection/connection.php');
include('./php/validateSession.php');
// include('./php/sendMessage.php');


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK Dashboard</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body data-theme="light">
    <!-- Theme Toggle Button -->
    <button style="color: orange;" class="btn btn-secondary theme-toggle glass-panel">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Notification Box -->
    <div class="glass-panel notification-box p-3">
        <h5>Notifications</h5>
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bell text-warning me-2"></i>
                    <div>
                        <small>New message received</small>
                        <div class="text-muted">2 minutes ago</div>
                    </div>
                </div>
            </a>
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center">
                    <i class="fas fa-tasks text-success me-2"></i>
                    <div>
                        <small>Task completed</small>
                        <div class="text-muted">1 hour ago</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include("./partials/dashboardNavigation.php"); ?>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h3 class="mb-0">Dashboard</h3>
                    <div class="glass-panel px-3 py-2 notification-btn" style="cursor: pointer;">
                        <i class="fas fa-bell text-muted"></i>
                    </div>
                </div>



                <div class="row mt-4 g-4">
                    <div class="col-lg-4">
                        <div class="glass-panel p-4 h-100">
                            <h5 class="mb-4">Application Requests</h5>
                            <?php
                            $UserId = $_SESSION['userId'];
                            $Status =  0;
                            $Percentage = '';
                            $SelectApplicationRequest = mysqli_query($conn, "SELECT a.*, s.* FROM ApplicationRequests a JOIN scholarships s ON s.scholarshipId = a.ApplicationId WHERE UserId=$UserId ORDER BY RequestId DESC");
                            if ($SelectApplicationRequest->num_rows > 0) {
                                while ($ApplicationRequests = mysqli_fetch_assoc($SelectApplicationRequest)) {

                                    switch ($ApplicationRequests['Status']) {
                                        case 0:
                                            $Status =  'Submited';
                                            $Percentage = 10;
                                            break;
                                        case 1:
                                            $Status = 'seen';
                                            $Percentage = 40;
                                            break;
                                        case 2:
                                        case 3:
                                            $Status = 'In-Progress';
                                            $Percentage = 70;
                                            break;
                                        case 4:
                                            $Status = 'Completed';
                                            $Percentage = 100;
                                            break;
                                        default:
                                            $Status = "There was some Issue";
                                            $Percentage = 10;
                                            break;
                                    }

                            ?>

                                    <div class="app-card p-3 mb-3 rounded-3">
                                        <div class="d-flex align-items-center">
                                            <div class="neumorphic-icon me-3">
                                                <i class="fas fa-list text-info"></i>
                                            </div>
                                            <div class="w-100">
                                                <h6 class="mb-0"><?php echo $ApplicationRequests['scholarshipTitle']; ?></h6>
                                                <small class="text-muted">Application Date: <?php echo $ApplicationRequests['RequestDate']; ?></small>
                                                <div class="progress mt-1 mb-1">
                                                    <div class="progress-bar bg-success" style="width: <?php echo $Percentage; ?>%; background:green !important;"></div>
                                                </div>

                                                <div style="font-size: 14px !important;">
                                                    Status: <?php echo $Status; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>

                            <!-- <div class="app-card p-3 mb-3 rounded-3">
                                <div class="d-flex align-items-center">
                                    <div class="neumorphic-icon me-3">
                                        <i class="fas fa-cloud-upload-alt text-danger"></i>
                                    </div>
                                    <div class="w-100">
                                        <h6 class="mb-0">Cloud Storage</h6>
                                        <small class="text-muted">Syncing files</small>
                                        <div class="progress-glass mt-2">
                                            <div class="progress-bar bg-info" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="glass-panel p-4">
                            <h5><i class="fas fa-chart-line me-2"></i>Performance</h5>
                            <canvas id="performanceChart" style="height: 200px;"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-panel p-4">
                            <h5><i class="fas fa-tasks me-2"></i>Active Projects</h5>
                            <div class="progress-glass mt-3">
                                <div class="progress-bar bg-warning" style="width: 30%"></div>
                            </div>
                            <small class="text-muted">Project Alpha - 30% complete</small>
                        </div>
                    </div> -->

                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Theme Toggle
        const themeToggle = document.querySelector('.theme-toggle');
        const body = document.body;
        const savedTheme = localStorage.getItem('theme') || 'light';
        body.setAttribute('data-theme', savedTheme);
        updateToggleIcon();

        themeToggle.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleIcon();
        });

        function updateToggleIcon() {
            const currentTheme = body.getAttribute('data-theme');
            themeToggle.innerHTML = currentTheme === 'light' ?
                '<i class="fas fa-moon"></i>' :
                '<i class="fas fa-sun"></i>';
        }

        // Notifications
        const notificationBtn = document.querySelector('.notification-btn');
        const notificationBox = document.querySelector('.notification-box');

        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationBox.style.display = notificationBox.style.display === 'block' ? 'none' : 'block';
        });

        // Close notifications when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target)) {
                notificationBox.style.display = 'none';
            }
        });

        // Mobile Sidebar Toggle
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const mainContent = document.querySelector('.main-content');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#messageForm').on('submit', function(event) {
                event.preventDefault();

                var formData = {
                    UserId: $('input[name="UserId"]').val(),
                    AdminId: $('input[name="AdminId"]').val(),
                    ConvId: $('input[name="ConvId"]').val(),
                    message: $('input[name="message"]').val()
                };

                if (formData.message.trim() === '') {
                    alert('Please enter a message');
                    return;
                }

                $.ajax({
                    url: './php/submit_message.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#statusMessage').html('<div class="alert alert-success">Message sent successfully!</div>');
                        $('input[name="message"]').val('');
                    },
                    error: function(xhr, status, error) {
                        $('#statusMessage').html('<div class="alert alert-danger">Failed to send message</div>');
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
    <script>
        // Scroll to the bottom of the chat container
        const chatContainer = document.getElementById('chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>
</body>

</html>