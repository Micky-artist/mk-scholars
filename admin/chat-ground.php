<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

if (!hasPermission('ChatGround')) {
  header("Location: ./index");
  exit;
}
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Ground | MK Scholars Admin</title>
    
    <!-- Admin Theme CSS -->
    <link href="./assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./dist/css/style.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --chat-bg: #f8f9fa;
            --message-sent: #007bff;
            --message-received: #e9ecef;
            --border-color: #dee2e6;
            --shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--chat-bg);
        }

        .page-wrapper {
            background: var(--chat-bg);
            padding: 0;
        }

        .chat-container {
            height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .chat-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .status-indicators {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-badge.online {
            background: var(--success-color);
            color: white;
        }

        .status-badge.offline {
            background: var(--secondary-color);
            color: white;
        }

        .status-badge.connecting {
            background: var(--warning-color);
            color: var(--dark-color);
        }

        .chat-main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .users-sidebar {
            width: 300px;
            background: white;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .users-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--light-color);
        }

        .users-header h5 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .search-box {
            position: relative;
        }

        .search-box .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            font-size: 0.8rem;
        }

        .search-box input {
            padding-right: 30px;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .users-list {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .user-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            position: relative;
        }

        .user-item:hover {
            background: var(--light-color);
            transform: translateX(2px);
        }

        .user-item.active {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.15), rgba(0, 123, 255, 0.05));
            border-color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
            transform: translateX(2px);
        }

        .user-item.active .user-avatar {
            background: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        .user-item.active .user-name {
            color: var(--primary-color);
            font-weight: 600;
        }

        .user-item.active .user-status {
            color: var(--primary-color);
            font-weight: 500;
        }

        .active-indicator {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--success-color);
            color: white;
            border-radius: 50%;
            width: 8px;
            height: 8px;
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.3);
        }

        .user-item.active {
            animation: slideInActive 0.3s ease;
        }

        @keyframes slideInActive {
            0% {
                transform: translateX(-10px);
                opacity: 0.8;
            }
            100% {
                transform: translateX(2px);
                opacity: 1;
            }
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.75rem;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 500;
            color: var(--dark-color);
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-status {
            font-size: 0.75rem;
            color: var(--secondary-color);
            margin: 0;
        }

        .unread-badge {
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .new-message-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--success-color);
            color: white;
            border-radius: 50%;
            width: 12px;
            height: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            animation: pulse 1s infinite;
        }

        .chat-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
        }

        .message.sent {
            align-items: flex-end;
        }

        .message.received {
            align-items: flex-start;
        }

        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
        }

        .message.sent .message-bubble {
            background: var(--message-sent);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.received .message-bubble {
            background: var(--message-received);
            color: var(--dark-color);
            border-bottom-left-radius: 4px;
        }

        .message-content {
            margin: 0;
            line-height: 1.4;
        }

        .message-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.25rem;
            font-size: 0.75rem;
            opacity: 0.7;
        }

        .message-time {
            font-size: 0.7rem;
        }

        .admin-name {
            font-weight: 600;
            color: var(--primary-color);
        }

        .message.sent .admin-name {
            color: rgba(255, 255, 255, 0.8);
        }

        .chat-input {
            padding: 1rem 1.5rem;
            background: white;
            border-top: 1px solid var(--border-color);
        }

        .input-group {
            display: flex;
            align-items: center;
            background: var(--light-color);
            border-radius: 25px;
            padding: 0.5rem;
        }

        .input-group input {
            border: none;
            background: transparent;
            flex: 1;
            padding: 0.5rem 1rem;
            outline: none;
        }

        .input-group button {
            background: var(--primary-color);
            border: none;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .input-group button:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        .input-group button:disabled {
            background: var(--secondary-color);
            cursor: not-allowed;
            transform: none;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--secondary-color);
            text-align: center;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h5 {
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            margin: 0;
        }

        /* File message styles */
        .message-file {
            margin: 0.5rem 0;
        }

        .message-file img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .message-file img:hover {
            transform: scale(1.02);
        }

        .file-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }

        .file-name {
            opacity: 0.8;
            word-break: break-word;
        }

        .file-download {
            color: inherit;
            text-decoration: none;
            padding: 0.25rem;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .file-download:hover {
            background: rgba(0,0,0,0.1);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .chat-main {
                flex-direction: column;
            }
            
            .users-sidebar {
                width: 100%;
                height: 200px;
            }
            
            .chat-container {
                height: calc(100vh - 200px);
            }
            
            .message-bubble {
                max-width: 85%;
            }
        }

        /* Scrollbar styling */
        .users-list::-webkit-scrollbar,
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .users-list::-webkit-scrollbar-track,
        .chat-messages::-webkit-scrollbar-track {
            background: var(--light-color);
        }

        .users-list::-webkit-scrollbar-thumb,
        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 3px;
        }

        .users-list::-webkit-scrollbar-thumb:hover,
        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }
    </style>
</head>

<body>
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
    data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        
        <!-- Header -->
        <?php include("./partials/header.php"); ?>
        
        <!-- Sidebar -->
        <?php include("./partials/navbar.php"); ?>
        
        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="container-fluid">
        <div class="row">
                    <div class="col-12">
                        <div class="chat-container">
                            <div class="chat-header">
                                <div class="d-flex align-items-center">
                                    <h4><i class="fas fa-comments me-2"></i>Chat Ground</h4>
                                    <div id="active-user-indicator" class="ms-3" style="display: none;">
                                        <span class="badge bg-success">
                                            <i class="fas fa-user-circle me-1"></i>
                                            <span id="active-username">User</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="status-indicators">
                                    <span id="connection-status" class="status-badge connecting">
                                        <i class="fas fa-circle me-1"></i>Connecting...
                                    </span>
                                    <span id="network-status" class="status-badge online">
                                        <i class="fas fa-wifi me-1"></i>Online
                                    </span>
                                </div>
                            </div>
                            
                            <div class="chat-main">
                                <!-- Users Sidebar -->
                                <div class="users-sidebar">
                                    <div class="users-header">
                                        <h5><i class="fas fa-users me-2"></i>Active Users</h5>
                                        <div class="search-box mt-2">
                                            <input type="text" id="user-search" class="form-control form-control-sm" placeholder="Search users..." autocomplete="off">
                                            <i class="fas fa-search search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="users-list" id="users-list">
                                        <!-- Users will be loaded here -->
                                    </div>
                                </div>
                                
                                <!-- Chat Panel -->
                                <div class="chat-panel">
                                    <div id="chat-messages" class="chat-messages">
                                        <div class="empty-state">
                                            <i class="fas fa-comment-dots"></i>
                                            <h5>Select a user to start chatting</h5>
                                            <p>Choose a user from the sidebar to begin your conversation</p>
                                        </div>
                                    </div>
                                    
                                    <div class="chat-input" id="chat-input" style="display: none;">
                                        <form id="message-form" class="input-group">
                                            <input type="hidden" id="current-conv-id" name="convId">
                                            <input type="hidden" id="current-user-id" name="userId">
                                            <input type="text" id="message-input" name="message" placeholder="Type your message..." autocomplete="off">
                                            <button type="submit" id="send-button">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
      </div>
      </div>
     </div>
 
    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="preview-image" src="" alt="Preview" class="img-fluid" style="max-height: 70vh;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="download-image" href="" download class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
  <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
  <script src="./dist/js/waves.js"></script>
  <script src="./dist/js/sidebarmenu.js"></script>
  <script src="./dist/js/custom.min.js"></script>
    
    <script>
        $(document).ready(function() {
            let currentConvId = null;
            let currentUserId = null;
            let currentUsername = null;
            let lastMessageId = 0;
            let pollInterval = null;
            let globalPollInterval = null;
            let processedMessages = new Set();
            let pendingMessages = new Set();
            let allConversations = [];
            let filteredConversations = [];
            let searchTimeout = null;

            // Load users list
            loadUsers();
            
            // Start global polling for all conversations
            startGlobalPolling();

            // Load users from server
            function loadUsers() {
                $.get('./php/fetch_conversations.php', function(data) {
                    allConversations = data;
                    filteredConversations = [...data];
                    renderUsersList();
                }, 'json').fail(function() {
                    $('#users-list').html('<div class="text-center text-danger p-3">Failed to load users</div>');
                });
            }

            // Render users list based on filtered conversations
            function renderUsersList() {
                const usersList = $('#users-list');
                usersList.empty();
                
                if (filteredConversations.length === 0) {
                    usersList.html('<div class="text-center text-muted p-3">No conversations found</div>');
                    return;
                }
                
                filteredConversations.forEach(user => {
                    const isActive = user.ConvId == currentConvId;
                    const activeClass = isActive ? 'active' : '';
                    const activeIndicator = isActive ? '<div class="active-indicator"></div>' : '';
                    
                    const userItem = $(`
                        <div class="user-item ${activeClass}" data-conv-id="${user.ConvId}" data-user-id="${user.NoUserId}" data-username="${user.NoUsername}">
                            <div class="user-avatar">
                                ${user.NoUsername.charAt(0).toUpperCase()}
                            </div>
                            <div class="user-info">
                                <h6 class="user-name">${user.NoUsername}</h6>
                                <p class="user-status">${isActive ? 'Currently chatting' : 'Last seen recently'}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                ${user.unreadCount > 0 ? `<div class="unread-badge">${user.unreadCount}</div>` : ''}
                                ${activeIndicator}
                            </div>
                        </div>
                    `);
                    
                    userItem.click(function() {
                        selectUser(user.ConvId, user.NoUserId, user.NoUsername);
                    });
                    
                    usersList.append(userItem);
                });
            }

            // Search users functionality
            function setupUserSearch() {
                $('#user-search').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase().trim();
                    
                    // Clear previous timeout
                    if (searchTimeout) {
                        clearTimeout(searchTimeout);
                    }
                    
                    // Debounce search
                    searchTimeout = setTimeout(() => {
                        if (searchTerm === '') {
                            filteredConversations = [...allConversations];
                        } else {
                            filteredConversations = allConversations.filter(user => 
                                user.NoUsername.toLowerCase().includes(searchTerm)
                            );
                        }
                        renderUsersList();
                    }, 300);
                });
            }

            // Initialize search
            setupUserSearch();

            // Clear active state
            function clearActiveState() {
                currentConvId = null;
                currentUserId = null;
                currentUsername = null;
                
                // Hide active user indicator
                $('#active-user-indicator').hide();
                
                // Hide chat input
                $('#chat-input').hide();
                
                // Clear all active states
                $('.user-item').removeClass('active');
                
                // Show empty state
                $('#chat-messages').html(`
                    <div class="empty-state">
                        <i class="fas fa-comment-dots"></i>
                        <h5>Select a user to start chatting</h5>
                        <p>Choose a user from the sidebar to begin your conversation</p>
                    </div>
                `);
            }

            // Select user and load conversation
            function selectUser(convId, userId, username) {
                // Update UI
                $('.user-item').removeClass('active');
                $(`.user-item[data-conv-id="${convId}"]`).addClass('active');
                
                // Clear unread badge
                $(`.user-item[data-conv-id="${convId}"] .unread-badge`).remove();
                
                // Set current conversation
                currentConvId = convId;
                currentUserId = userId;
                currentUsername = username;
                
                // Update form
                $('#current-conv-id').val(convId);
                $('#current-user-id').val(userId);
                
                // Show active user indicator
                $('#active-username').text(username);
                $('#active-user-indicator').show();
                
                // Show chat input
                $('#chat-input').show();
                
                // Load messages
                loadMessages();
                
                // Start polling
                startPolling();
                
                // Re-render users list to update active states
                renderUsersList();
            }

            // Load messages for current conversation
            function loadMessages() {
                if (!currentConvId) return;
                
                $.get('./php/fetch_messages.php', {
                    ConvId: currentConvId
                }, function(messages) {
                    const chatMessages = $('#chat-messages');
                    chatMessages.empty();
                    
                    if (messages.length === 0) {
                        chatMessages.html(`
                            <div class="empty-state">
                                <i class="fas fa-comment-dots"></i>
                                <h5>No messages yet</h5>
                                <p>Start the conversation with ${currentUsername}</p>
                            </div>
                        `);
                        return;
                    }
                    
                    messages.forEach(message => {
                        addMessageToChat(message);
                    });
                    
                    scrollToBottom();
                    processedMessages.clear();
                }, 'json').fail(function() {
                    $('#chat-messages').html('<div class="text-center text-danger p-3">Failed to load messages</div>');
                });
            }

            // Add message to chat display
            function addMessageToChat(message) {
                const isSent = message.AdminId != 0;
                const messageClass = isSent ? 'sent' : 'received';
                
                let messageContent = message.MessageContent;
                
                // Handle file messages
                if (message.MessageContent && message.MessageContent.startsWith('./uploads/')) {
                    const fileName = message.MessageContent.split('/').pop();
                    const fileExt = fileName.split('.').pop().toLowerCase();
                    const fileUrl = getFileUrl(message.MessageContent);
                    
                    if (["jpg","jpeg","png","gif","webp"].includes(fileExt)) {
                        messageContent = `
                            <div class="message-file">
                                <img src="${fileUrl}" alt="${fileName}" onclick="previewImage('${fileUrl}', '${fileName}')">
                                <div class="file-info">
                                    <span class="file-name">${fileName}</span>
                                    <a href="${fileUrl}" download class="file-download"><i class="fas fa-download"></i></a>
                                </div>
                            </div>`;
                    } else {
                        messageContent = `
                            <div class="message-file">
                                <div class="file-info">
                                    <span class="file-name">${fileName}</span>
                                    <a href="${fileUrl}" download class="file-download"><i class="fas fa-download"></i></a>
                                </div>
                            </div>`;
                    }
                } else {
                    messageContent = `<p class="message-content">${escapeHtml(message.MessageContent)}</p>`;
                }
                
                const adminName = message.AdminName || 'Admin';
                const time12h = convertTo12Hour(message.SentTime);
                
                const messageHtml = $(`
                    <div class="message ${messageClass}">
                        <div class="message-bubble">
                            ${messageContent}
                            <div class="message-meta">
                                ${isSent ? `<span class="admin-name">${adminName}</span>` : ''}
                                <span class="message-time">${time12h}</span>
                            </div>
                        </div>
                    </div>
                `);
                
                $('#chat-messages').append(messageHtml);
                lastMessageId = Math.max(lastMessageId, message.MessageId || 0);
                processedMessages.add(message.MessageId);
            }

            // Convert 24-hour time to 12-hour format
            function convertTo12Hour(time24) {
                // Check if already in 12-hour format (contains AM/PM)
                if (time24.includes('AM') || time24.includes('PM')) {
                    return time24;
                }
                
                const [hours, minutes] = time24.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour % 12 || 12;
                return `${hour12}:${minutes} ${ampm}`;
            }

            // Get file URL
            function getFileUrl(filePath) {
                if (!filePath) return '';
                if (filePath.startsWith('http://') || filePath.startsWith('https://')) {
                    return filePath;
                }
                const mainDomain = 'https://mkscholars.com';
                const cleanPath = filePath.replace(/^\.\//, '');
                return mainDomain + '/' + cleanPath;
            }

            // Escape HTML
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Scroll to bottom
            function scrollToBottom() {
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Start global polling for all conversations
            function startGlobalPolling() {
                if (globalPollInterval) {
                    clearInterval(globalPollInterval);
                }
                
                updateConnectionStatus('connected');
                
                // Poll every 2 seconds for all conversations
                globalPollInterval = setInterval(() => {
                    pollAllConversations();
                }, 2000);
            }

            // Poll all conversations for new messages and unread counts
            function pollAllConversations() {
                $.get('./php/fetch_conversations.php', function(data) {
                    // Check for new unread messages in other conversations
                    const previousUnreadCounts = {};
                    allConversations.forEach(conv => {
                        previousUnreadCounts[conv.ConvId] = conv.unreadCount || 0;
                    });
                    
                    // Update all conversations data
                    allConversations = data;
                    
                    // Check for new messages in other conversations
                    data.forEach(conv => {
                        const prevCount = previousUnreadCounts[conv.ConvId] || 0;
                        const currentCount = conv.unreadCount || 0;
                        
                        // If there are new unread messages and it's not the current conversation
                        if (currentCount > prevCount && conv.ConvId !== currentConvId) {
                            showNewMessageNotification(conv.NoUsername, currentCount - prevCount);
                        }
                    });
                    
                    // Update filtered conversations if search is active
                    const searchTerm = $('#user-search').val().toLowerCase().trim();
                    if (searchTerm === '') {
                        filteredConversations = [...data];
                    } else {
                        filteredConversations = data.filter(user => 
                            user.NoUsername.toLowerCase().includes(searchTerm)
                        );
                    }
                    
                    // Re-render users list to update unread counts
                    renderUsersList();
                    
                    // If we're in a conversation, also fetch new messages for that conversation
                    if (currentConvId) {
                        fetchNewMessages();
                    }
                }, 'json').fail(function() {
                    console.error('Failed to poll conversations');
                });
            }

            // Show notification for new messages in other conversations
            function showNewMessageNotification(username, count) {
                // Create notification element
                const notification = $(`
                    <div class="alert alert-info alert-dismissible fade show position-fixed" 
                         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                        <i class="fas fa-comment-dots me-2"></i>
                        <strong>${username}</strong> sent ${count} new message${count > 1 ? 's' : ''}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                // Add to page
                $('body').append(notification);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    notification.alert('close');
                }, 5000);
            }

            // Start polling for new messages (for current conversation)
            function startPolling() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
                
                // Current conversation polling is now handled by global polling
                // This function is kept for compatibility but doesn't start its own interval
            }

            // Fetch new messages
            function fetchNewMessages() {
                if (!currentConvId) return;
                
                $.get('./php/fetch_messages.php', {
                    ConvId: currentConvId,
                    lastMessageId: lastMessageId
                }, function(messages) {
                    if (messages && messages.length > 0) {
                        let hasNewMessages = false;
                        messages.forEach(message => {
                            if (message.MessageId > lastMessageId && !processedMessages.has(message.MessageId)) {
                                const isPendingMessage = pendingMessages.has(message.MessageContent);
                                
                                if (!isPendingMessage) {
                                    addMessageToChat(message);
                                    hasNewMessages = true;
                                } else {
                                    pendingMessages.delete(message.MessageContent);
                                }
                                
                                lastMessageId = Math.max(lastMessageId, message.MessageId || 0);
                                processedMessages.add(message.MessageId);
                            }
                        });
                        
                        if (hasNewMessages) {
                            scrollToBottom();
                        }
                    }
                }, 'json');
            }

            // Update connection status
            function updateConnectionStatus(status) {
                const indicator = $('#connection-status');
                indicator.removeClass('connecting online offline');
                
                switch(status) {
                    case 'connected':
                        indicator.addClass('online').html('<i class="fas fa-circle me-1"></i>Connected');
                        break;
                    case 'connecting':
                        indicator.addClass('connecting').html('<i class="fas fa-circle me-1"></i>Connecting...');
                        break;
                    case 'offline':
                        indicator.addClass('offline').html('<i class="fas fa-circle me-1"></i>Offline');
                        break;
                }
            }

            // Update network status
            function updateNetworkStatus() {
                const isOnline = navigator.onLine;
                const indicator = $('#network-status');
                
                if (isOnline) {
                    indicator.removeClass('offline').addClass('online')
                        .html('<i class="fas fa-wifi me-1"></i>Online');
                } else {
                    indicator.removeClass('online').addClass('offline')
                        .html('<i class="fas fa-exclamation-triangle me-1"></i>Offline');
                }
            }

            // Initialize network status
            updateNetworkStatus();
            window.addEventListener('online', updateNetworkStatus);
            window.addEventListener('offline', updateNetworkStatus);

            // Handle message form submission
            $('#message-form').submit(function(e) {
                e.preventDefault();
                
                const message = $('#message-input').val().trim();
                if (!message || !currentConvId) return;
                
                // Add message immediately for better UX
                const messageData = {
                    MessageId: Date.now(),
                    UserId: 0,
                    AdminId: <?= $_SESSION['adminId'] ?? 0 ?>,
                    AdminName: '<?= $_SESSION['AdminName'] ?? 'Admin' ?>',
                    MessageContent: message,
                    SentDate: new Date().toISOString().split('T')[0],
                    SentTime: new Date().toLocaleTimeString('en-US', {hour12: false, hour: '2-digit', minute: '2-digit'})
                };
                
                addMessageToChat(messageData);
                scrollToBottom();
                pendingMessages.add(message);
                
                // Send to server
                $.post('./php/submit_message.php', {
                    ConvId: currentConvId,
                    AdminId: <?= $_SESSION['adminId'] ?? 0 ?>,
                    UserId: currentUserId,
                    message: message
                }, function(response) {
                    $('#message-input').val('');
                }, 'json').fail(function() {
                    console.error('Failed to send message');
                });
            });

            // Clean up on page unload
            $(window).on('beforeunload', function() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
                if (globalPollInterval) {
                    clearInterval(globalPollInterval);
                }
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                processedMessages.clear();
                pendingMessages.clear();
            });
        });

        // Global function for image preview
        function previewImage(url, fileName = '') {
            document.getElementById('preview-image').src = url;
            document.getElementById('download-image').href = url;
            document.getElementById('download-image').download = fileName || 'image';
            
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        }
    </script>
</body>
</html>