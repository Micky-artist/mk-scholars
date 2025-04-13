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
    <title>Conversation</title>
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
                    <h3 class="mb-0">Conversation</h3>
                    <div class="glass-panel px-3 py-2 notification-btn" style="cursor: pointer;">
                        <i class="fas fa-bell text-muted"></i>
                    </div>
                </div>

                <div class="row g-4">



                    <div class="col-lg-8">
                        <div class="glass-panel p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0" style="font-size: 13px;">Chat With Us (Refresh to check new messages) <span style="background-color: green; padding: 5px; color: white; border-radius: 5px; cursor: pointer;" onclick="window.location.href='conversations'">Refresh</span></h5>
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

                            ?>
                                <div class="chat-container" style="height: 400px; overflow-y: auto;" id="chat-container">
                                    <div id="typing-indicator" style="font-size: 12px; margin-top: 5px; display: none;">
                                        <em>Typing...</em>
                                    </div>


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
                                <form id="messageForm" class="input-group mt-4" enctype="multipart/form-data">
                                    <div class="file-input-container">
                                        <input type="file" name="file" id="file-input" class="file-input">
                                        <label for="file-input" class="file-label">
                                            <i class="fas fa-paperclip attachment-icon"></i>
                                        </label>
                                    </div>
                                    <input type="hidden" name="UserId" value="<?php echo htmlspecialchars($UserId); ?>">
                                    <input type="hidden" name="AdminId" value="0">
                                    <input type="hidden" name="ConvId" value="<?php echo htmlspecialchars($convoId); ?>">
                                    <input type="text" class="form-control bg-transparent" name="message" placeholder="Type message...">
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
        // Typing Indicator
        let typingTimer;
        $('input[name="message"]').on('input', function() {
            $('#typing-indicator').show();
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                $('#typing-indicator').hide();
            }, 1000);
        });

        // Send Message + File
        $('#messageForm').on('submit', function(event) {
            event.preventDefault();
            const form = new FormData(this);

            $.ajax({
                url: './php/submit_message.php',
                type: 'POST',
                data: form,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#statusMessage').html('<div class="alert alert-success">Message sent!</div>');
                    $('input[name="message"]').val('');
                    $('#file-input').val('');
                    loadMessages(); // Refresh messages instantly
                },
                error: function() {
                    $('#statusMessage').html('<div class="alert alert-danger">Error sending message</div>');
                }
            });
        });

        // Load Messages (poll every 3 sec)
        function loadMessages() {
            const convId = $('input[name="ConvId"]').val();
            const userId = $('input[name="UserId"]').val();
            $.get('./php/fetch_messages.php', {
                ConvId: convId
            }, function(data) {
                const messages = JSON.parse(data);
                const chatContainer = $('#chat-container');
                const isAtBottom = chatContainer[0].scrollTop + chatContainer[0].clientHeight >= chatContainer[0].scrollHeight - 100;

                chatContainer.html('');
                let currentDate = '';
                messages.forEach(msg => {
                    const messageDate = new Date(msg.SentDate).toDateString();
                    if (currentDate !== messageDate) {
                        currentDate = messageDate;
                        chatContainer.append(`<div class="date-separator text-center my-3">${messageDate}</div>`);
                    }

                    let content;
                    if (msg.MessageContent.startsWith('./uploads/')) {
                        const ext = msg.MessageContent.split('.').pop().toLowerCase();
                        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                            content = `<img src="${msg.MessageContent}" style="max-width:200px;" alt="File">`;
                        } else {
                            content = `<a href="${msg.MessageContent}" target="_blank">📎 Download File</a>`;
                        }
                    } else {
                        content = msg.MessageContent;
                    }

                    const bubbleClass = msg.UserId == userId ? 'sent' : 'received';
                    const bubble = `
                    <div class="chat-bubble ${bubbleClass} mb-3">
                        <p class="message-content">${content}</p>
                        <span class="time">${msg.SentTime}</span>
                    </div>
                `;
                    chatContainer.append(bubble);
                });

                if (isAtBottom) {
                    chatContainer[0].scrollTop = chatContainer[0].scrollHeight;
                }
            });
        }

        setInterval(loadMessages, 3000);
        loadMessages();
    </script>

    <script>
        // Scroll to the bottom of the chat container
        const chatContainer = document.getElementById('chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>
</body>

</html>