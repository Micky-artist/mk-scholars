<?php
session_start();
include('./dbconnection/connection.php');
include('./php/validateSession.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - MK Scholars</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body data-theme="light">
    <!-- Navigation -->
    <?php include("./partials/dashboardNavigation.php"); ?>

    <!-- Main Chat Container -->
    <div class="chat-app-container">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-content">
                <div class="d-flex align-items-center">
                    <div class="chat-avatar">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="chat-info ms-3">
                        <h5 class="mb-0">Support Chat</h5>
                        <small class="text-muted" id="chat-status">Online</small>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="btn btn-link" id="theme-toggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button class="btn btn-success" id="files-toggle">
                        <i class="fas fa-folder me-2"></i>Shared Files
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Body -->
        <div class="chat-body">
            <div class="chat-messages" id="chat-container">
                <?php
                $UserId = $_SESSION['userId'];
                $CheckConvo = mysqli_query($conn, "SELECT * FROM Conversation WHERE UserId = '$UserId' LIMIT 1");
                if ($CheckConvo->num_rows == 1) {
                    $convoData = mysqli_fetch_assoc($CheckConvo);
                    $convoId = $convoData['ConvId'];
                    $UserId = $convoData['UserId'];
                    $ConvStatus = $convoData['ConvStatus'];

                    // Fetch messages for the current conversation
                    $selectMessages = mysqli_query($conn, "SELECT * FROM Message WHERE ConvId = $convoId ORDER BY SentDate, SentTime");

                    if ($selectMessages->num_rows > 0) {
                        $currentDate = null;
                        while ($messages = mysqli_fetch_assoc($selectMessages)) {
                            $messageDate = date("Y-m-d", strtotime($messages['SentDate']));
                            $messageTime = date("h:i A", strtotime($messages['SentTime']));

                            // Display date separator
                            if ($currentDate !== $messageDate) {
                                $currentDate = $messageDate;
                                echo '<div class="message-date-separator">' . date("F j, Y", strtotime($currentDate)) . '</div>';
                            }

                            // Message bubble
                            $isUser = ($messages['UserId'] == $UserId);
                            $messageClass = $isUser ? 'message-sent' : 'message-received';
                            $avatarClass = $isUser ? 'user-avatar' : 'admin-avatar';
                            $avatarIcon = $isUser ? 'fas fa-user' : 'fas fa-headset';
                            
                            echo '<div class="message-wrapper ' . $messageClass . '">';
                            echo '<div class="message-avatar ' . $avatarClass . '">';
                            echo '<i class="' . $avatarIcon . '"></i>';
                            echo '</div>';
                            echo '<div class="message-bubble">';
                            
                            // Handle file messages
                            if (strpos($messages['MessageContent'], './uploads/') === 0) {
                                $filePath = $messages['MessageContent'];
                                $fileName = basename($filePath);
                                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $cacheBuster = '?v=' . time();
                                $fileUrl = $filePath . $cacheBuster;
                                
                                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    echo '<div class="message-file-image">';
                                    echo '<img src="' . $fileUrl . '" alt="' . $fileName . '" onclick="previewImage(\'' . $fileUrl . '\')">';
                                    echo '<div class="file-info">';
                                    echo '<span class="file-name">' . $fileName . '</span>';
                                    echo '<a href="' . $fileUrl . '" download class="file-download"><i class="fas fa-download"></i></a>';
                                    echo '</div>';
                                    echo '</div>';
                                } else {
                                    echo '<div class="message-file-document">';
                                    echo '<div class="file-icon"><i class="fas fa-file"></i></div>';
                                    echo '<div class="file-details">';
                                    echo '<span class="file-name">' . $fileName . '</span>';
                                    echo '<span class="file-type">' . strtoupper($fileExt) . ' File</span>';
                                    echo '</div>';
                                    echo '<a href="' . $fileUrl . '" download class="file-download"><i class="fas fa-download"></i></a>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="message-text">' . htmlspecialchars($messages['MessageContent']) . '</div>';
                            }
                            
                            echo '<div class="message-time">' . $messageTime . '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="welcome-message">';
                        echo '<div class="welcome-icon"><i class="fas fa-comments"></i></div>';
                        echo '<h4>Welcome to MK Scholars Support!</h4>';
                        echo '<p>Start a conversation with our support team. We\'re here to help you with any questions about scholarships and courses.</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="welcome-message">';
                    echo '<div class="welcome-icon"><i class="fas fa-comments"></i></div>';
                    echo '<h4>Welcome to MK Scholars Support!</h4>';
                    echo '<p>Start a conversation with our support team. We\'re here to help you with any questions about scholarships and courses.</p>';
                    echo '<button class="btn btn-primary start-conversation-btn" onclick="startConversation()">';
                    echo '<i class="fas fa-plus me-2"></i>Start Conversation';
                    echo '</button>';
                    echo '</div>';
                }
                ?>
                
                <!-- Typing Indicator -->
                <div class="typing-indicator" id="typing-indicator" style="display: none;">
                    <div class="message-wrapper message-received">
                        <div class="message-avatar admin-avatar">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="message-bubble typing-bubble">
                            <div class="typing-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="chat-input-container">
            <?php if (isset($convoId)): ?>
            <!-- Quick Access Files Button -->
            <div class="quick-files-access mb-3">
                <button class="btn btn-outline-primary w-100" id="files-toggle-bottom">
                    <i class="fas fa-folder me-2"></i>View & Upload Shared Files
                </button>
            </div>
            
            <form id="messageForm" class="chat-input-form" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="hidden" name="UserId" value="<?php echo htmlspecialchars($UserId); ?>">
                    <input type="hidden" name="AdminId" value="0">
                    <input type="hidden" name="ConvId" value="<?php echo htmlspecialchars($convoId); ?>">
                    <input type="text" class="form-control message-input" name="message" placeholder="Type your message..." autocomplete="off">
                    <button type="submit" class="btn btn-primary send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>

        <!-- Files Panel -->
        <div class="files-panel" id="files-panel">
            <div class="files-panel-header">
                <h6><i class="fas fa-folder me-2"></i>Shared Files</h6>
                <button class="btn btn-link btn-sm" id="close-files">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="files-panel-content">
                <!-- Upload Section -->
                <div class="upload-section">
                    <h6 class="mb-3"><i class="fas fa-upload me-2"></i>Upload New File</h6>
                    <form action="php/upload_file.php" method="post" enctype="multipart/form-data" class="upload-form">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION['userName'] ?? 'user'); ?>">
                        <input type="hidden" name="userid" value="<?php echo htmlspecialchars($_SESSION['userId']); ?>">
                        <input type="hidden" name="convid" value="<?php echo htmlspecialchars($convoId ?? ''); ?>">
                        <div class="upload-area" id="upload-area">
                            <input type="file" name="file" class="upload-input" id="panel-file-input" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
                            <div class="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p><strong>Click here to upload files</strong></p>
                                <small class="text-muted">or drag and drop files here</small>
                            </div>
                        </div>
                        
                        <!-- File Preview -->
                        <div class="file-preview" id="file-preview" style="display: none;">
                            <div class="preview-content">
                                <div class="preview-image" id="preview-image-container"></div>
                                <div class="preview-info">
                                    <h6 id="preview-filename"></h6>
                                    <small id="preview-filesize"></small>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mt-3" id="upload-btn">
                            <i class="fas fa-upload me-2"></i>Upload File
                        </button>
                    </form>
                </div>
                
                <!-- Files List -->
                <div class="files-list" id="static-files-list">
                    <!-- Files will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="preview-image" src="" alt="Preview" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom Styles -->
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
            --border-radius: 12px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        [data-theme="dark"] {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --border-color: #404040;
        }

        [data-theme="light"] {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #333333;
            --text-secondary: #666666;
            --border-color: #e9ecef;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            overflow: hidden;
        }

        /* Chat App Container */
        .chat-app-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-width: 100%;
            margin: 0 auto;
            background: var(--bg-primary);
            position: relative;
        }

        /* Desktop specific styling */
        @media (min-width: 992px) {
            .chat-app-container {
                max-width: 800px;
                margin: 2rem auto;
                height: calc(100vh - 4rem);
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
                border: 1px solid var(--border-color);
            }
        }

        /* Chat Header */
        .chat-header {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .chat-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .chat-info h5 {
            font-weight: 600;
            margin: 0;
        }

        .chat-actions .btn {
            color: var(--text-secondary);
            padding: 0.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-actions .btn:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .chat-actions .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .chat-actions .btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
            color: white;
        }

        .chat-actions .btn-success {
            background: #28a745;
            border-color: #28a745;
            color: white;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
        }

        .chat-actions .btn-success:hover {
            background: #218838;
            border-color: #1e7e34;
            color: white;
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
            transform: translateY(-1px);
        }

        /* Quick Files Access Button */
        .quick-files-access {
            text-align: center;
        }

        .quick-files-access .btn {
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
        }

        .quick-files-access .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
        }

        /* Chat Body */
        .chat-body {
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        .chat-messages {
            height: 100%;
            overflow-y: auto;
            padding: 1rem;
            scroll-behavior: smooth;
        }

        /* Messages */
        .message-wrapper {
            display: flex;
            margin-bottom: 1rem;
            align-items: flex-end;
        }

        .message-sent {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            margin: 0 0.5rem;
            flex-shrink: 0;
        }

        .user-avatar {
            background: var(--primary-color);
            color: white;
        }

        .admin-avatar {
            background: var(--secondary-color);
            color: white;
        }

        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            position: relative;
            word-wrap: break-word;
        }

        .message-sent .message-bubble {
            background: var(--primary-color);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-received .message-bubble {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border-bottom-left-radius: 4px;
        }

        .message-text {
            line-height: 1.4;
            margin-bottom: 0.25rem;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            text-align: right;
        }

        .message-sent .message-time {
            text-align: right;
        }

        .message-received .message-time {
            text-align: left;
        }

        /* Date Separator */
        .message-date-separator {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .message-date-separator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-color);
            z-index: 1;
        }

        .message-date-separator span {
            background: var(--bg-primary);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--text-secondary);
            position: relative;
            z-index: 2;
        }

        /* Welcome Message */
        .welcome-message {
            text-align: center;
            padding: 3rem 1rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .welcome-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .start-conversation-btn {
            margin-top: 1.5rem;
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius);
        }

        /* File Messages */
        .message-file-image {
            margin-bottom: 0.5rem;
        }

        .message-file-image img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .message-file-image img:hover {
            transform: scale(1.05);
        }

        .file-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
        }

        .file-name {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .file-download {
            color: inherit;
            text-decoration: none;
            padding: 0.25rem;
            border-radius: 4px;
            transition: var(--transition);
        }

        .file-download:hover {
            background: rgba(255,255,255,0.1);
        }

        .message-file-document {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .file-icon {
            font-size: 1.5rem;
            opacity: 0.7;
        }

        .file-details {
            flex: 1;
        }

        .file-type {
            font-size: 0.7rem;
            opacity: 0.6;
            display: block;
        }

        /* Typing Indicator */
        .typing-indicator {
            margin-top: 10px;
        }

        .typing-bubble {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
        }

        .typing-dots {
            display: flex;
            gap: 4px;
            padding: 8px 12px;
        }

        .typing-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--text-secondary);
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typing {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Connection Status */
        #chat-status {
            transition: color 0.3s ease;
        }

        #chat-status.text-success {
            color: var(--success-color) !important;
        }

        #chat-status.text-warning {
            color: var(--warning-color) !important;
        }

        #chat-status.text-danger {
            color: var(--danger-color) !important;
        }

        /* Notifications */
        .alert {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        /* Message Animations */
        .message-wrapper {
            animation: slideInMessage 0.3s ease-out;
        }

        @keyframes slideInMessage {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* File Upload Improvements */
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(0, 123, 255, 0.05);
        }

        .upload-area.dragover {
            border-color: var(--success-color);
            background: rgba(40, 167, 69, 0.1);
        }

        .upload-input {
            display: none;
        }

        .file-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }

        .preview-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .preview-image {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-primary);
            border-radius: 8px;
            overflow: hidden;
        }

        .preview-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .preview-image i {
            font-size: 2rem;
            color: var(--text-secondary);
        }

        .preview-info h6 {
            margin: 0;
            font-size: 0.9rem;
        }

        .preview-info small {
            color: var(--text-secondary);
        }

        /* Chat Input */
        .chat-input-container {
            background: var(--bg-primary);
            border-top: 1px solid var(--border-color);
            padding: 1rem;
            position: sticky;
            bottom: 0;
        }

        .chat-input-form .input-group {
            background: var(--bg-secondary);
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .file-input {
            display: none;
        }

        .message-input {
            border: none;
            background: transparent;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
        }

        .message-input:focus {
            box-shadow: none;
            outline: none;
        }

        .message-input::placeholder {
            color: var(--text-secondary);
        }

        .send-btn {
            width: 50px;
            height: 50px;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: var(--primary-color);
        }

        .send-btn:hover {
            background: #0056b3;
        }

        /* Files Panel */
        .files-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: var(--bg-primary);
            border-left: 1px solid var(--border-color);
            transition: var(--transition);
            z-index: 1001;
            display: flex;
            flex-direction: column;
        }

        .files-panel.active {
            right: 0;
        }

        .files-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .files-panel-content {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .upload-section {
            margin-bottom: 2rem;
        }

        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            position: relative;
            transition: var(--transition);
            background: var(--bg-secondary);
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(0, 123, 255, 0.05);
        }

        /* File Preview */
        .file-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }

        .preview-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .preview-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
        }

        .preview-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .preview-image .file-icon {
            font-size: 1.5rem;
            color: var(--text-secondary);
        }

        .preview-info {
            flex: 1;
        }

        .preview-info h6 {
            margin: 0;
            font-size: 0.9rem;
        }

        .preview-info small {
            color: var(--text-secondary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .chat-app-container {
                height: 100vh;
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                border: none;
            }

            .files-panel {
                width: 100%;
                right: -100%;
            }

            .message-bubble {
                max-width: 85%;
            }

            .chat-header {
                padding: 0.75rem;
            }

            .chat-messages {
                padding: 0.75rem;
            }

            .chat-input-container {
                padding: 0.75rem;
            }

            .quick-files-access .btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .message-bubble {
                max-width: 90%;
            }

            .chat-avatar {
                width: 32px;
                height: 32px;
            }

            .message-avatar {
                width: 28px;
                height: 28px;
            }

            .quick-files-access .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }
        }

        /* File Gallery Styles */
        .file-card {
            background: var(--bg-secondary);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .file-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .file-image-card {
            text-align: center;
        }

        .file-thumb {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .file-doc-card {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .file-doc-icon {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .file-info-details {
            flex: 1;
        }

        .file-actions {
            display: flex;
            gap: 0.5rem;
        }

        .file-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>

    <!-- JavaScript -->
    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        const savedTheme = localStorage.getItem('theme') || 'light';
        body.setAttribute('data-theme', savedTheme);
        updateThemeIcon();

        themeToggle.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon();
        });

        function updateThemeIcon() {
            const currentTheme = body.getAttribute('data-theme');
            themeToggle.innerHTML = currentTheme === 'light' ? 
                '<i class="fas fa-moon"></i>' : 
                '<i class="fas fa-sun"></i>';
        }

        // Files Panel Toggle
        const filesToggle = document.getElementById('files-toggle');
        const filesToggleBottom = document.getElementById('files-toggle-bottom');
        const filesPanel = document.getElementById('files-panel');
        const closeFiles = document.getElementById('close-files');

        function openFilesPanel() {
            filesPanel.classList.add('active');
        }

        function closeFilesPanel() {
            filesPanel.classList.remove('active');
        }

        // Header button
        if (filesToggle) {
            filesToggle.addEventListener('click', openFilesPanel);
        }

        // Bottom button
        if (filesToggleBottom) {
            filesToggleBottom.addEventListener('click', openFilesPanel);
        }

        // Close button
        if (closeFiles) {
            closeFiles.addEventListener('click', closeFilesPanel);
        }

        // Close files panel when clicking outside
        document.addEventListener('click', (e) => {
            if (!filesPanel.contains(e.target) && 
                !filesToggle.contains(e.target) && 
                !filesToggleBottom.contains(e.target)) {
                closeFilesPanel();
            }
        });

        // Mobile Sidebar Toggle
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        // File Preview Functionality
        const panelFileInput = document.getElementById('panel-file-input');
        const filePreview = document.getElementById('file-preview');
        const previewImageContainer = document.getElementById('preview-image-container');
        const previewFilename = document.getElementById('preview-filename');
        const previewFilesize = document.getElementById('preview-filesize');
        const uploadBtn = document.getElementById('upload-btn');
        const uploadArea = document.getElementById('upload-area');

        if (panelFileInput) {
            panelFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    showFilePreview(file);
                } else {
                    hideFilePreview();
                }
            });
        }

        // Drag and drop functionality
        if (uploadArea) {
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    panelFileInput.files = files;
                    showFilePreview(file);
                }
            });

            uploadArea.addEventListener('click', function() {
                panelFileInput.click();
            });
        }

        function showFilePreview(file) {
            const fileName = file.name;
            const fileSize = formatFileSize(file.size);
            const fileType = file.type;
            
            previewFilename.textContent = fileName;
            previewFilesize.textContent = fileSize;
            
            // Clear previous preview
            previewImageContainer.innerHTML = '';
            
            if (fileType.startsWith('image/')) {
                // Show image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = fileName;
                    previewImageContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                // Show file icon
                const icon = document.createElement('i');
                icon.className = 'fas ' + getFileIcon(fileName);
                previewImageContainer.appendChild(icon);
            }
            
            filePreview.style.display = 'block';
            uploadBtn.disabled = false;
        }

        function hideFilePreview() {
            filePreview.style.display = 'none';
            uploadBtn.disabled = true;
        }

        function getFileIcon(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            const iconMap = {
                'pdf': 'fa-file-pdf',
                'doc': 'fa-file-word',
                'docx': 'fa-file-word',
                'xls': 'fa-file-excel',
                'xlsx': 'fa-file-excel',
                'ppt': 'fa-file-powerpoint',
                'pptx': 'fa-file-powerpoint',
                'txt': 'fa-file-alt',
                'zip': 'fa-file-archive',
                'rar': 'fa-file-archive',
                'csv': 'fa-file-csv'
            };
            return iconMap[ext] || 'fa-file';
        }

        // Message Form
        $(document).ready(function() {
            // Initialize file list
            refreshFileList();
            
            // Auto-refresh every 30 seconds
            setInterval(refreshFileList, 30000);

            // Handle upload form submission
            $('.upload-form').on('submit', function(e) {
                e.preventDefault();
                const form = new FormData(this);
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Reset form and preview
                        $('.upload-form')[0].reset();
                        hideFilePreview();
                        refreshFileList();
                        
                        // Show success message
                        showNotification('File uploaded successfully!', 'success');
                    },
                    error: function() {
                        showNotification('Upload failed. Please try again.', 'error');
                    }
                });
            });

            // Message form submission with real-time updates
            $('#messageForm').on('submit', function(e) {
                e.preventDefault();
                const messageInput = $('input[name="message"]');
                const message = messageInput.val().trim();

                if (!message) {
                    return;
                }

                // Show typing indicator for user
                showUserTyping();

                $.ajax({
                    url: './php/submit_message.php',
                    type: 'POST',
                    data: {
                        ConvId: $('input[name="ConvId"]').val(),
                        UserId: $('input[name="UserId"]').val(),
                        AdminId: $('input[name="AdminId"]').val(),
                        message: message
                    },
                    success: function(response) {
                        messageInput.val('');
                        hideUserTyping();
                        
                        // Add message to chat immediately for better UX
                        const messageData = {
                            MessageId: Date.now(), // Temporary ID
                            UserId: $('input[name="UserId"]').val(),
                            AdminId: 0,
                            MessageContent: message,
                            SentDate: new Date().toISOString().split('T')[0],
                            SentTime: new Date().toLocaleTimeString('en-US', {hour12: false, hour: '2-digit', minute: '2-digit'})
                        };
                        addMessageToChat(messageData);
                        
                        // Track this pending message to prevent duplicates
                        pendingMessages.add(message);
                    },
                    error: function() {
                        hideUserTyping();
                        showNotification('Error sending message', 'error');
                    }
                });
            });

            // Typing indicator
            let typingTimer;
            let isTyping = false;
            
            $('input[name="message"]').on('input', function() {
                if (!isTyping) {
                    isTyping = true;
                    sendTypingStatus(true);
                }
                
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    isTyping = false;
                    sendTypingStatus(false);
                }, 1000);
            });

            // Start real-time chat
            startRealTimeChat();
        });

        // Real-time chat functionality
        let lastMessageId = 0;
        let eventSource = null;
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 5;
        let processedMessages = new Set(); // Track processed messages to prevent duplicates
        let pendingMessages = new Set(); // Track pending messages that were sent optimistically

        function startRealTimeChat() {
            const convId = <?php echo isset($convoId) ? $convoId : 'null'; ?>;
            if (!convId) return;

            // Close existing connection
            if (eventSource) {
                eventSource.close();
            }

            const url = `./php/chat_stream.php?convId=${convId}&lastMessageId=${lastMessageId}`;
            eventSource = new EventSource(url);

            eventSource.onopen = function() {
                console.log('Real-time connection established');
                reconnectAttempts = 0;
                updateConnectionStatus(true);
            };

            eventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    
                    if (data.error) {
                        console.error('SSE Error:', data.error);
                        return;
                    }

                    if (data.type === 'typing') {
                        handleTypingIndicator(data);
                        return;
                    }

                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(message => {
                            // Only add if it's a new message and not already processed
                            if (message.MessageId > lastMessageId && !processedMessages.has(message.MessageId)) {
                                // Check if this is a pending message we already added optimistically
                                const isPendingMessage = pendingMessages.has(message.MessageContent);
                                
                                if (!isPendingMessage) {
                                    addMessageToChat(message);
                                } else {
                                    // Remove from pending since we received the real message
                                    pendingMessages.delete(message.MessageContent);
                                }
                                
                                lastMessageId = Math.max(lastMessageId, message.MessageId || 0);
                                processedMessages.add(message.MessageId);
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error parsing SSE data:', e);
                }
            };

            eventSource.onerror = function() {
                console.log('Real-time connection error, attempting to reconnect...');
                updateConnectionStatus(false);
                eventSource.close();
                
                if (reconnectAttempts < maxReconnectAttempts) {
                    reconnectAttempts++;
                    setTimeout(() => {
                        startRealTimeChat();
                    }, 5000 * reconnectAttempts); // Exponential backoff
                } else {
                    console.error('Max reconnection attempts reached');
                    showNotification('Connection lost. Please refresh the page.', 'error');
                }
            };
        }

        function sendTypingStatus(isTyping) {
            const convId = <?php echo isset($convoId) ? $convoId : 'null'; ?>;
            if (!convId) return;

            $.post('./php/typing_status.php', {
                convId: convId,
                userId: <?php echo isset($_SESSION['userId']) ? $_SESSION['userId'] : 'null'; ?>,
                isTyping: isTyping
            });
        }

        function handleTypingIndicator(data) {
            if (data.isTyping && data.userId != <?php echo isset($_SESSION['userId']) ? $_SESSION['userId'] : 'null'; ?>) {
                $('#typing-indicator').show();
            } else {
                $('#typing-indicator').hide();
            }
        }

        function showUserTyping() {
            // Add a temporary message showing "Sending..."
            const tempMessage = `
                <div class="message-wrapper message-sent" id="temp-message">
                    <div class="message-avatar user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="message-bubble">
                        <div class="message-text">Sending...</div>
                        <div class="message-time">${new Date().toLocaleTimeString('en-US', {hour12: false, hour: '2-digit', minute: '2-digit'})}</div>
                    </div>
                </div>`;
            $('#chat-container').append(tempMessage);
            scrollToBottom();
        }

        function hideUserTyping() {
            $('#temp-message').remove();
        }

        function updateConnectionStatus(isOnline) {
            const statusElement = $('#chat-status');
            if (isOnline) {
                statusElement.text('Online').removeClass('text-danger').addClass('text-success');
            } else {
                statusElement.text('Connecting...').removeClass('text-success').addClass('text-warning');
            }
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        function addMessageToChat(message) {
            const chatContainer = $('#chat-container');
            const userId = <?php echo isset($UserId) ? $UserId : 'null'; ?>;
            const messageDate = new Date(message.SentDate).toDateString();
            const messageTime = new Date('2000-01-01 ' + message.SentTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            // Check if we need to add a date separator
            const lastDateSeparator = chatContainer.find('.message-date-separator').last();
            if (lastDateSeparator.length === 0 || lastDateSeparator.text() !== messageDate) {
                chatContainer.append(`<div class="message-date-separator"><span>${messageDate}</span></div>`);
            }

            const isUser = message.UserId == userId;
            const messageClass = isUser ? 'message-sent' : 'message-received';
            const avatarClass = isUser ? 'user-avatar' : 'admin-avatar';
            const avatarIcon = isUser ? 'fas fa-user' : 'fas fa-headset';

            let content = '';
            if (message.MessageContent && message.MessageContent.startsWith('./uploads/')) {
                const ext = message.MessageContent.split('.').pop().toLowerCase();
                const cacheBuster = '?v=' + new Date().getTime();
                const fileUrl = message.MessageContent + cacheBuster;
                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    content = `
                        <div class="message-file-image">
                            <img src="${fileUrl}" alt="File" onclick="previewImage('${fileUrl}')">
                            <div class="file-info">
                                <span class="file-name">${basename(message.MessageContent)}</span>
                                <a href="${fileUrl}" download class="file-download"><i class="fas fa-download"></i></a>
                            </div>
                        </div>`;
                } else {
                    content = `
                        <div class="message-file-document">
                            <div class="file-icon"><i class="fas fa-file"></i></div>
                            <div class="file-details">
                                <span class="file-name">${basename(message.MessageContent)}</span>
                                <span class="file-type">${ext.toUpperCase()} File</span>
                            </div>
                            <a href="${fileUrl}" download class="file-download"><i class="fas fa-download"></i></a>
                        </div>`;
                }
            } else {
                content = `<div class="message-text">${escapeHtml(message.MessageContent)}</div>`;
            }

            const messageHtml = `
                <div class="message-wrapper ${messageClass}">
                    <div class="message-avatar ${avatarClass}">
                        <i class="${avatarIcon}"></i>
                    </div>
                    <div class="message-bubble">
                        ${content}
                        <div class="message-time">${messageTime}</div>
                    </div>
                </div>`;

            chatContainer.append(messageHtml);
            scrollToBottom();
        }

        function scrollToBottom() {
            const chatContainer = document.getElementById('chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function basename(path) {
            return path.split('/').pop();
        }

        // File management functions
        function refreshFileList() {
            const userId = <?php echo isset($_SESSION['userId']) ? $_SESSION['userId'] : 'null'; ?>;
            const convId = <?php echo isset($convoId) ? $convoId : 'null'; ?>;
            
            if (!userId) return;

            let url = `./php/list_user_files.php?userid=${userId}`;
            if (convId) url += `&convid=${convId}`;

            $.get(url, function(files) {
                renderFiles(files);
            });
        }

        function renderFiles(files) {
            const filesList = document.getElementById('static-files-list');
            filesList.innerHTML = '';

            if (!files || files.length === 0) {
                filesList.innerHTML = '<div class="text-center text-muted">No files shared yet.</div>';
                return;
            }

            files.forEach(file => {
                const fileCard = createFileCard(file);
                filesList.appendChild(fileCard);
            });
        }

        function createFileCard(file) {
            const card = document.createElement('div');
            card.className = 'file-card';

            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(file.type);
            
            if (isImage) {
                const cacheBuster = '?v=' + new Date().getTime(); // Add cache-buster
                const fileUrl = file.url + cacheBuster;
                card.innerHTML = `
                    <div class="file-image-card">
                        <img src="${fileUrl}" alt="${file.name}" class="file-thumb" onclick="previewImage('${fileUrl}')">
                        <div class="file-info-details">
                            <div class="fw-semibold">${file.name}</div>
                            <div class="text-muted small">${formatFileSize(file.size)} • ${file.uploadedByMe ? 'Sent by you' : 'Received'}</div>
                            <div class="text-muted small">${file.uploadDate}</div>
                        </div>
                        <div class="file-actions">
                            <button class="btn btn-outline-primary btn-sm" onclick="previewImage('${fileUrl}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="${fileUrl}" download class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download"></i>
                            </a>
                            ${file.source === 'documents_table' ? `
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteFile('${file.url}', ${file.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>`;
            } else {
                const docInfo = getDocTypeInfo(file.url);
                const cacheBuster = '?v=' + new Date().getTime(); // Add cache-buster
                const fileUrl = file.url + cacheBuster;
                card.innerHTML = `
                    <div class="file-doc-card">
                        <div class="file-doc-icon">
                            <i class="fas ${docInfo.icon}" style="color: ${docInfo.color}"></i>
                        </div>
                        <div class="file-info-details">
                            <div class="fw-semibold">${file.name}</div>
                            <div class="text-muted small">${formatFileSize(file.size)} • ${docInfo.label}</div>
                            <div class="text-muted small">${file.uploadedByMe ? 'Sent by you' : 'Received'} • ${file.uploadDate}</div>
                        </div>
                        <div class="file-actions">
                            <button class="btn btn-outline-primary btn-sm" onclick="previewFile('${fileUrl}', 'doc')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="${fileUrl}" download class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download"></i>
                            </a>
                            ${file.source === 'documents_table' ? `
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteFile('${file.url}', ${file.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>`;
            }

            return card;
        }

        // Utility functions
        const docTypeMap = {
            pdf: { icon: 'fa-file-pdf', color: '#e74c3c', label: 'PDF' },
            doc: { icon: 'fa-file-word', color: '#2980b9', label: 'DOC' },
            docx: { icon: 'fa-file-word', color: '#2980b9', label: 'DOCX' },
            xls: { icon: 'fa-file-excel', color: '#27ae60', label: 'XLS' },
            xlsx: { icon: 'fa-file-excel', color: '#27ae60', label: 'XLSX' },
            ppt: { icon: 'fa-file-powerpoint', color: '#e67e22', label: 'PPT' },
            pptx: { icon: 'fa-file-powerpoint', color: '#e67e22', label: 'PPTX' },
            txt: { icon: 'fa-file-alt', color: '#7f8c8d', label: 'TXT' },
            zip: { icon: 'fa-file-archive', color: '#8e44ad', label: 'ZIP' },
            rar: { icon: 'fa-file-archive', color: '#8e44ad', label: 'RAR' },
            csv: { icon: 'fa-file-csv', color: '#16a085', label: 'CSV' },
            default: { icon: 'fa-file', color: '#34495e', label: 'FILE' }
        };

        function getDocTypeInfo(url) {
            const ext = url.split('.').pop().toLowerCase();
            return docTypeMap[ext] || docTypeMap.default;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function previewImage(url) {
            document.getElementById('preview-image').src = url;
            new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
        }

        function previewFile(url, type) {
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

        function deleteFile(url, docId) {
            if (!confirm('Are you sure you want to delete this file?')) return;
            
            $.post('./php/delete_file.php', { file: url, docId: docId }, function(resp) {
                refreshFileList();
            });
        }

        function startConversation() {
            $.post('', { startConvo: true }, function() {
                location.reload();
            });
        }

        // Clean up on page unload
        $(window).on('beforeunload', function() {
            if (eventSource) {
                eventSource.close();
            }
            // Clear tracking sets
            processedMessages.clear();
            pendingMessages.clear();
        });

        // Auto-scroll to bottom on load
        $(document).ready(function() {
            const chatContainer = document.getElementById('chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>
</body>
</html>