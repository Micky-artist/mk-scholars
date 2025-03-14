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
    <style>
        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.3);
            --neumorphic-shadow: 5px 5px 10px #d1d5db, -5px -5px 10px #ffffff;
        }

        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(31, 41, 55, 0.9);
            --glass-border: rgba(255, 255, 255, 0.1);
            --neumorphic-shadow: 5px 5px 10px #0a0c10, -5px -5px 10px #283447;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-right: 1px solid var(--glass-border);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            width: 250px;
        }

        .chat-bubble {
            max-width: 75%;
            padding: 15px 20px;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .received {
            background: rgb(181, 181, 181);
            border: 1px solid var(--glass-border);
        }

        .sent {
            background: rgba(59, 130, 246, 0.9);
            color: white;
            margin-left: auto;
        }

        .app-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            transition: all 0.3s;
        }

        .neumorphic-icon {
            width: 40px;
            height: 40px;
            background: var(--glass-bg);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--neumorphic-shadow);
        }

        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-box {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1050;
        }

        .progress-glass {
            background: rgba(255, 255, 255, 0.1);
            height: 8px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .notification-box {
                width: 90%;
                right: 5%;
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
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
            <nav class="col-md-3 col-lg-2 sidebar p-4">
                <div class="d-flex flex-column h-100">
                    <div class="text-center mb-5">
                        <div class="neumorphic-icon mx-auto mb-3">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <h5 class="mb-1"><?php echo $_SESSION['username']; ?></h5>
                        <small class="text-muted"><?php echo $_SESSION['NoEmail']; ?></small>
                    </div>

                    <div class="glass-panel p-2 mb-1">
                        <a class="nav-link d-flex align-items-center" href="./dashboard">
                            <i class="fas fa-comment-alt me-3 text-primary"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
<!--                     
                    <div class="glass-panel p-2 mb-1">
                        <a class="nav-link d-flex align-items-center" href="#">
                            <i class="fas fa-comment-alt me-3 text-primary"></i>
                            <span>Chat</span>
                        </a>
                    </div>
                    <div class="glass-panel p-2 mb-1">
                        <a class="nav-link d-flex align-items-center" href="#">
                            <i class="fas fa-comment-alt me-3 text-primary"></i>
                            <span>Chat</span>
                        </a>
                    </div> -->
                    
                    


                    <a class="p-3 mt-auto" href="">
                    <button class=" glass-panel p-3 mt-auto" style="color: red; font-weight: bold;">Logout</button>
                    </a>

                </div>
            </nav>

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

                <div class="row g-4">



                    <div class="col-lg-8">
                        <div class="glass-panel p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Chat With Us (Refresh to check new messages)</h5>
                                <!-- <span class="badge bg-primary rounded-pill">3 New</span> -->
                            </div>
                            <?php
                            $UserId = $_SESSION['userId'];
                            $CheckConvo = mysqli_query($conn, "SELECT * FROM Conversation WHERE UserId = '$UserId' LIMIT 1");
                            if ($CheckConvo->num_rows == 1) {
                                $convoData = mysqli_fetch_assoc($CheckConvo);
                                $convoId = $convoData['ConvId'];
                                $UserId = $convoData['UserId'];
                                $ConvStatus = $convoData['ConvStatus'];

                                // if ($convoData['ConvStatus'] == 1) {
                                //     $DeleteConvo = mysqli_query($conn, "DELETE FROM Conversation WHERE ConvId = $convoId");
                                //     if ($DeleteConvo) {
                                //         echo ('
                                //         <script>window.location.href = "./dashboard"</script>
                                //         ');
                                //     }
                                // }

                            ?>
                                <div class="chat-container" style="height: 400px; overflow-y: auto;" id="chat-container">
                                    <?php
                                    // Fetch messages for the current conversation
                                    $selectMessages = mysqli_query($conn, "SELECT * FROM Message WHERE ConvId = $convoId ORDER BY SentDate, SentTime");

                                    if ($selectMessages->num_rows > 0) {
                                        $currentDate = null; // Variable to track the current date for separating messages by day

                                        while ($messages = mysqli_fetch_assoc($selectMessages)) {
                                            $messageDate = date("Y-m-d", strtotime($messages['SentDate']));
                                            $messageTime = date("h:i A", strtotime($messages['SentTime']));

                                            // Display the date separator if the date changes
                                            if ($currentDate !== $messageDate) {
                                                $currentDate = $messageDate;
                                                echo '<div class="date-separator text-center my-3">' . date("F j, Y", strtotime($currentDate)) . '</div>';
                                            }

                                            // Check if the sender is the user or admin
                                            if ($messages['UserId'] == $UserId) {
                                                // User's message (sent)
                                                echo '<div class="chat-bubble sent mb-3">';
                                                echo '<p class="message-content">' . htmlspecialchars($messages['MessageContent']) . '</p>';
                                                echo '<span class="time">' . $messageTime . '</span>';
                                                echo '</div>';
                                            } else {
                                                // Admin's message (received)
                                                echo '<div class="chat-bubble received mb-3">';
                                                echo '<p class="message-content">' . htmlspecialchars($messages['MessageContent']) . '</p>';
                                                echo '<span class="time">' . $messageTime . '</span>';
                                                echo '</div>';
                                            }
                                        }
                                    } else {
                                        echo '<div class="text-center">No messages found.</div>';
                                    }

                                    $conn->close();
                                    ?>
                                </div>
                                <style>
                                    .chat-container {
                                        scroll-behavior: smooth;
                                        /* Smooth scrolling */
                                    }

                                    .chat-bubble {
                                        max-width: 70%;
                                        padding: 10px;
                                        border-radius: 10px;
                                        position: relative;
                                        word-wrap: break-word;
                                        /* Ensure long messages wrap */
                                    }

                                    .chat-bubble.sent {
                                        background-color: #007bff;
                                        color: white;
                                        margin-left: auto;
                                    }

                                    .chat-bubble.received {
                                        background-color: #f1f1f1;
                                        color: black;
                                        margin-right: auto;
                                    }

                                    .chat-bubble .time {
                                        display: block;
                                        font-size: 0.8em;
                                        text-align: right;
                                        margin-top: 5px;
                                    }

                                    .date-separator {
                                        font-size: 0.9em;
                                        color: #777;
                                        background-color: #f9f9f9;
                                        padding: 5px;
                                        border-radius: 5px;
                                        display: inline-block;
                                    }

                                    .message-content {
                                        margin: 0;
                                    }

                                    .file-input {
                                        display: none;
                                    }

                                    .file-label {
                                        display: inline-block;
                                        cursor: pointer;
                                        background-color: #f1f1f1;
                                        padding: 10px;
                                        border-radius: 5px;
                                        transition: background-color 0.3s ease;
                                    }

                                    .file-label:hover {
                                        background-color: #ddd;
                                    }

                                    .attachment-icon {
                                        font-size: 20px;
                                        color: #555;
                                    }
                                </style>

                                <!-- <div > -->
                                <div id="statusMessage"></div>
                                <form id="messageForm" class="input-group mt-4">
                                    <div class="file-input-container">
                                        <input type="file" name="file" id="file-input" class="file-input">
                                        <label for="file-input" class="file-label">
                                            <i class="fas fa-paperclip attachment-icon"></i>
                                        </label>
                                    </div>
                                    <input type="hidden" name="UserId" value="<?php echo htmlspecialchars($UserId); ?>">
                                    <input type="hidden" name="AdminId" value="0">
                                    <input type="hidden" name="ConvId" value="<?php echo htmlspecialchars($convoId); ?>">
                                    <input type="text" class="form-control bg-transparent" name="message" placeholder="Type message..." required>
                                    <button type="submit" name="send" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Send
                                    </button>
                                </form>
                                <!-- </div> -->
                            <?php
                            } else {
                            ?>
                                <div>
                                    <form action="" method="post">
                                        <input type="hidden" name="" value="">
                                        <button name="startConvo" class="glass-panel p-3">Start New Conversation</button>
                                    </form>
                                </div>
                            <?php
                                if (isset($_POST['startConvo'])) {
                                    $startTime = date("H:i");
                                    $startDate = date("Y-m-d");
                                    $adminId = 0;
                                    $StartConvo = mysqli_query($conn, "INSERT INTO Conversation(UserId, AdminId, StartDate, StartTime, ConvStatus) VALUES($UserId, $adminId, '$startDate', '$startTime', 0)");
                                    if ($StartConvo) {
                                        echo ('
                                        <script>window.location.href = "./dashboard"</script>
                                        ');
                                    }
                                }
                            }
                            ?>



                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="glass-panel p-4 h-100">
                            <h5 class="mb-4">Application Requests</h5>
                            <div class="app-card p-3 mb-3 rounded-3">
                                <div class="d-flex align-items-center">
                                    <div class="neumorphic-icon me-3">
                                        <i class="fas fa-photo-video text-info"></i>
                                    </div>
                                    <div class="w-100">
                                        <h6 class="mb-0">Media Manager</h6>
                                        <small class="text-muted">Updated 2h ago</small>
                                        <div class="progress-glass mt-2">
                                            <div class="progress-bar bg-success" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="app-card p-3 mb-3 rounded-3">
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
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="row mt-4 g-4">
                    <div class="col-md-6">
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
                    </div>
                </div> -->
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