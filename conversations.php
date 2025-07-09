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
                            <!-- Tabs for Messages and Files -->
                            <ul class="nav nav-tabs mb-3" id="conversationTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="true">Messages</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab" aria-controls="files" aria-selected="false">Files</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="conversationTabsContent">
                                <div class="tab-pane fade show active" id="messages" role="tabpanel" aria-labelledby="messages-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
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
                        <!-- Static File Gallery below chat -->
                        <div class="mt-5">
                            <h5 class="mb-3">Shared Files</h5>
                            <!-- Upload Button/Form -->
                            <form action="php/upload_file.php" method="post" enctype="multipart/form-data" class="mb-4 d-flex flex-wrap align-items-center gap-2">
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION['userName'] ?? 'user'); ?>">
                                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($_SESSION['userId']); ?>">
                                <input type="hidden" name="convid" value="<?php echo htmlspecialchars($convoId ?? ''); ?>">
                                <input type="file" name="file" class="form-control" style="max-width:300px;" required>
                                <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Upload New Document</button>
                            </form>
                            <?php if (isset($_GET['upload_success'])): ?>
                                <div class="alert alert-success">File uploaded successfully!</div>
                            <?php endif; ?>
                            <?php if (isset($_GET['upload_error'])): ?>
                                <div class="alert alert-danger">Upload failed: <?php echo htmlspecialchars($_GET['upload_error']); ?></div>
                            <?php endif; ?>
                            <div class="container-fluid py-2">
                                <div class="row" id="static-files-list"></div>
                            </div>
                        </div>
                        <!-- Preview Modal -->
                        <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body" id="previewModalBody" style="min-height:400px;display:flex;align-items:center;justify-content:center;"></div>
                            </div>
                          </div>
                        </div>
                    <!-- Files Modal -->
                    <div class="modal fade" id="filesModal" tabindex="-1" aria-labelledby="filesModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="filesModalLabel">Files Sent in Conversation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div id="modal-files-list" class="d-flex flex-wrap gap-3 justify-content-start"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- End Files Modal -->
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
                    $('input[name="message"]').val('');
                    $('#file-input').val('');
                    // Real-time update will handle the new message
                },
                error: function() {
                    alert('Error sending message');
                }
            });
        });

        // Real-time chat using Server-Sent Events
        let lastMessageId = 0;
        let eventSource = null;

        function startRealTimeChat() {
            const convId = $('input[name="ConvId"]').val();
            if (!convId) return;

            // Close existing connection if any
            if (eventSource) {
                eventSource.close();
            }

            // Start SSE connection
            eventSource = new EventSource(`./php/chat_stream.php?convId=${convId}&lastMessageId=${lastMessageId}`);
            
            eventSource.onmessage = function(event) {
                try {
                    const messages = JSON.parse(event.data);
                    if (Array.isArray(messages)) {
                        messages.forEach(msg => {
                            addMessageToChat(msg);
                            lastMessageId = Math.max(lastMessageId, msg.MessageId);
                        });
                    }
                } catch (e) {
                    console.log('SSE data received:', event.data);
                }
            };

            eventSource.onerror = function(event) {
                console.log('SSE error, reconnecting...');
                setTimeout(startRealTimeChat, 5000);
            };
        }

        function addMessageToChat(message) {
            const chatContainer = $('#chat-container');
            const userId = $('input[name="UserId"]').val();
            const messageDate = new Date(message.SentDate).toDateString();
            const messageTime = new Date('2000-01-01 ' + message.SentTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            // Check if we need to add a date separator
            const lastDateSeparator = chatContainer.find('.date-separator').last();
            if (lastDateSeparator.length === 0 || lastDateSeparator.text() !== messageDate) {
                chatContainer.append(`<div class="date-separator text-center my-3">${messageDate}</div>`);
            }

            let content;
            if (message.MessageContent.startsWith('./uploads/')) {
                const ext = message.MessageContent.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                    content = `<img src="${message.MessageContent}" style="max-width:200px;" alt="File">`;
                } else {
                    content = `<a href="${message.MessageContent}" target="_blank">📎 Download File</a>`;
                }
            } else {
                content = message.MessageContent;
            }

            const bubbleClass = message.UserId == userId ? 'sent' : 'received';
            const bubble = `
                <div class="chat-bubble ${bubbleClass} mb-3">
                    <p class="message-content">${content}</p>
                    <span class="time">${messageTime}</span>
                </div>
            `;
            chatContainer.append(bubble);
            
            // Scroll to bottom
            chatContainer.scrollTop(chatContainer[0].scrollHeight);
        }

        // Initialize real-time chat when page loads
        $(document).ready(function() {
            startRealTimeChat();
        });

        // Clean up on page unload
        $(window).on('beforeunload', function() {
            if (eventSource) {
                eventSource.close();
            }
        });

        // Files Tab: Load files when tab is shown
        document.getElementById('files-tab').addEventListener('shown.bs.tab', function (e) {
            loadFiles();
        });
        function loadFiles() {
            const convId = $('input[name="ConvId"]').val();
            $.get('./php/fetch_files.php', { ConvId: convId }, function(data) {
                const files = JSON.parse(data);
                const filesList = $('#files-list');
                filesList.html('');
                if (files.length === 0) {
                    filesList.html('<div class="text-center text-muted">No files sent in this conversation.</div>');
                    return;
                }
                files.forEach(file => {
                    const ext = file.MessageContent.split('.').pop().toLowerCase();
                    let content;
                    if (["jpg","jpeg","png","gif","webp","bmp","avif"].includes(ext)) {
                        content = `<img src="${file.MessageContent}" style="max-width:120px;max-height:120px;margin:5px;" alt="File">`;
                    } else {
                        content = `<a href="${file.MessageContent}" target="_blank">📎 Download File</a>`;
                    }
                    filesList.append(`<div class="mb-2">${content} <span class="text-muted" style="font-size:0.9em;">${file.SentDate} ${file.SentTime}</span></div>`);
                });
            });
        }

        // Remove Files Tab JS if present
        // Add Modal File Viewer JS
        // $('#filesModal').on('show.bs.modal', function () {
        //     loadFilesModal();
        // });
        // function loadFilesModal() {
        //     const convId = $('input[name="ConvId"]').val();
        //     $.get('./php/fetch_files.php', { ConvId: convId }, function(data) {
        //         const files = JSON.parse(data);
        //         const filesList = $('#modal-files-list');
        //         filesList.html('');
        //         if (files.length === 0) {
        //             filesList.html('<div class="text-center text-muted w-100">No files sent in this conversation.</div>');
        //             return;
        //         }
        //         files.forEach(file => {
        //             const ext = file.MessageContent.split('.').pop().toLowerCase();
        //             let content;
        //             if (["jpg","jpeg","png","gif","webp","bmp","avif"].includes(ext)) {
        //                 content = `<img src="${file.MessageContent}" style="max-width:120px;max-height:120px;" alt="File">`;
        //             } else {
        //                 content = `<a href="${file.MessageContent}" target="_blank">📎 Download File</a>`;
        //             }
        //             filesList.append(`<div class="mb-2">${content}<br><span class="text-muted" style="font-size:0.9em;">${file.SentDate} ${file.SentTime}</span></div>`);
        //         });
        //     });
        // }
    </script>

    <script>
        // Scroll to the bottom of the chat container
        const chatContainer = document.getElementById('chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>

    <script>
        // Utility: Map file extensions to icon, color, and label
        const docTypeMap = {
            pdf:  { icon: 'fa-file-pdf',   color: '#e74c3c', label: 'PDF' },
            doc:  { icon: 'fa-file-word',  color: '#2980b9', label: 'DOC' },
            docx: { icon: 'fa-file-word',  color: '#2980b9', label: 'DOCX' },
            xls:  { icon: 'fa-file-excel', color: '#27ae60', label: 'XLS' },
            xlsx: { icon: 'fa-file-excel', color: '#27ae60', label: 'XLSX' },
            ppt:  { icon: 'fa-file-powerpoint', color: '#e67e22', label: 'PPT' },
            pptx: { icon: 'fa-file-powerpoint', color: '#e67e22', label: 'PPTX' },
            txt:  { icon: 'fa-file-alt',   color: '#7f8c8d', label: 'TXT' },
            zip:  { icon: 'fa-file-archive', color: '#8e44ad', label: 'ZIP' },
            rar:  { icon: 'fa-file-archive', color: '#8e44ad', label: 'RAR' },
            csv:  { icon: 'fa-file-csv',   color: '#16a085', label: 'CSV' },
            default: { icon: 'fa-file', color: '#34495e', label: 'FILE' }
        };

        // Assume these are set from PHP session or JS global
        const currentUser = {
            username: 'demoUser', // Replace with actual username from session
            userid: 123           // Replace with actual user id from session
        };

        // Remove staticFiles array. Fetch files from backend instead.
        let uploadedFiles = [];
        // Optionally set conversation id if available
        const conversationId = window.currentConversationId || null; // Set this from your session/page if needed

        function fetchAndRenderFiles() {
            let url = `./php/list_user_files.php?userid=${currentUser.userid}`;
            if (conversationId) url += `&convid=${conversationId}`;
            $.get(url, function(files) {
                uploadedFiles = files;
                renderStaticFiles();
            });
        }

        function renderStaticFiles() {
            const filesList = document.getElementById('static-files-list');
            filesList.innerHTML = '';
            if (!uploadedFiles || uploadedFiles.length === 0) {
                filesList.innerHTML = '<div class="text-center text-muted w-100">No files in gallery.</div>';
                return;
            }
            uploadedFiles.forEach((file, idx) => {
                const type = getFileType(file.url);
                const col = document.createElement('div');
                col.className = 'col-12 col-sm-6 col-md-4 col-lg-3 file-card';
                let content = '';
                if (type === 'image') {
                    content = `
                    <div class="file-image-card shadow-sm p-2 mb-2 bg-white rounded-4 d-flex flex-column align-items-center position-relative">
                        <img src="${file.url}" alt="${file.name}" class="file-thumb mb-2" style="cursor:pointer;" onclick="previewFile('${file.url}','image')">
                        <div class="file-link text-center fw-semibold">${file.name}</div>
                        <div class="text-muted small">${formatFileSize(file.size)}</div>
                        <div class="d-flex gap-2 mt-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="previewFile('${file.url}','image')"><i class="fas fa-eye"></i></button>
                            <a href="${file.url}" download class="btn btn-outline-success btn-sm"><i class="fas fa-download"></i></a>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteFile('${file.url}', ${file.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>`;
                } else {
                    const docInfo = getDocTypeInfo(file.url);
                    content = `
                    <div class="file-doc-card shadow-sm p-3 mb-2 rounded-4 d-flex flex-column align-items-center position-relative" style="background:${docInfo.color}1A; border:2px solid ${docInfo.color};">
                        <div class="display-3 mb-2" style="color:${docInfo.color}"><i class="fas ${docInfo.icon}"></i></div>
                        <div class="badge mb-2" style="background:${docInfo.color};color:#fff;font-size:0.9em;">${docInfo.label}</div>
                        <div class="file-link text-center fw-semibold" style="color:${docInfo.color}">${file.name}</div>
                        <div class="text-muted small">${formatFileSize(file.size)}</div>
                        <div class="d-flex gap-2 mt-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="previewFile('${file.url}','doc','${docInfo.label}')"><i class="fas fa-eye"></i></button>
                            <a href="${file.url}" download class="btn btn-outline-success btn-sm"><i class="fas fa-download"></i></a>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteFile('${file.url}', ${file.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>`;
                }
                col.innerHTML = content;
                filesList.appendChild(col);
            });
        }

        // Preview logic
        window.previewFile = function(url, type, label) {
            const ext = url.split('.').pop().toLowerCase();
            if (type === 'image' || ext === 'pdf') {
                window.open(url, '_blank');
            } else if (["doc","docx","ppt","pptx","xls","xlsx"].includes(ext)) {
                const gviewUrl = `https://docs.google.com/gview?url=${encodeURIComponent(window.location.origin + '/' + url)}&embedded=true`;
                window.open(gviewUrl, '_blank');
            } else {
                window.open(url, '_blank');
            }
        }
        // Delete logic
        window.deleteFile = function(url, docId) {
            if (!confirm('Are you sure you want to delete this file?')) return;
            $.post('./php/delete_file.php', { file: url }, function(resp) {
                // Remove from uploadedFiles array and re-render
                uploadedFiles = uploadedFiles.filter(f => f.id !== docId);
                renderStaticFiles();
            });
        }
        // Upload logic: after upload, refresh file list
        // This section is now handled by the PHP form submission and the refreshFileList function
        // $('#uploadForm').on('submit', function(e) {
        //     e.preventDefault();
        //     const formData = new FormData(this);
        //     formData.append('username', currentUser.username);
        //     formData.append('userid', currentUser.userid);
        //     if (conversationId) formData.append('convid', conversationId);
        //     $.ajax({
        //         url: './php/upload_file.php',
        //         type: 'POST',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(resp) {
        //             $('#fileInput').val('');
        //             fetchAndRenderFiles();
        //         },
        //         error: function() {
        //             alert('Upload failed.');
        //         }
        //     });
        // });

        // On page load, fetch files
        $(document).ready(function() {
            refreshFileList();
            // Refresh file list every 30 seconds
            setInterval(refreshFileList, 30000);
        });
        // Add styles for file gallery
        const style = document.createElement('style');
        style.innerHTML = `
        .file-thumb {
            max-width: 160px;
            max-height: 160px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid #f8f9fa;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
            background: #fff;
            transition: box-shadow 0.2s;
        }
        .file-thumb:hover {
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        }
        .file-card {
            margin-bottom: 32px;
        }
        .file-image-card {
            border: 2px solid #e3e3e3;
            background: #f9f9f9;
            transition: box-shadow 0.2s, border 0.2s;
        }
        .file-image-card:hover {
            border: 2px solid #007bff;
            box-shadow: 0 4px 24px rgba(0,123,255,0.10);
        }
        .file-doc-card {
            min-height: 180px;
            width: 100%;
            background: #f8f9fa;
            border-width: 2px;
            border-style: solid;
            transition: box-shadow 0.2s, border 0.2s;
        }
        .file-doc-card:hover {
            box-shadow: 0 4px 24px rgba(0,0,0,0.13);
            filter: brightness(1.04);
        }
        .file-link {
            word-break: break-all;
        }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>