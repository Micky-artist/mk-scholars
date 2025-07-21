<?php
// session_start();
// include("./dbconnections/connection.php");
// include("./php/validateAdminSession.php");

// Function to detect if site is online and get base URL
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pathInfo = pathinfo($scriptName);
    $basePath = dirname($pathInfo['dirname']);
    
    // If we're in admin folder, go up one level to root
    if (strpos($basePath, '/admin') !== false) {
        $basePath = dirname($basePath);
    }
    
    return $protocol . '://' . $host . $basePath;
}

// Get the base URL for file operations
$baseUrl = getBaseUrl();

$adminId        = intval($_SESSION['adminId'] ?? 0);
$currentConvoId = null;
$UserId         = null;

// 1) Open convo & mark read
if (!empty($_GET['userId']) && is_numeric($_GET['userId'])) {
    $UserId = intval($_GET['userId']);
    $stmt = $conn->prepare("SELECT ConvId FROM Conversation WHERE UserId = ? LIMIT 1");
    $stmt->bind_param('i', $UserId);
    $stmt->execute();
    $stmt->bind_result($cId);
    if ($stmt->fetch()) $currentConvoId = $cId;
    $stmt->close();
    if ($currentConvoId) {
        $upd = $conn->prepare("UPDATE Message SET MessageStatus = 1 WHERE ConvId = ?");
        $upd->bind_param('i', $currentConvoId);
        $upd->execute();
        $upd->close();
    }
}

// 2) Sidebar query
$convSql = "SELECT c.ConvId,u.NoUsername,u.NoUserId,lm.lastMessageId,
         COALESCE(uc.unreadCount,0) AS unreadCount
    FROM Conversation c
    JOIN normUsers u ON c.UserId=u.NoUserId
    JOIN (SELECT ConvId,MAX(MessageId) AS lastMessageId FROM Message GROUP BY ConvId) lm
      ON c.ConvId=lm.ConvId
    LEFT JOIN (SELECT ConvId,COUNT(*) AS unreadCount FROM Message WHERE MessageStatus=0 GROUP BY ConvId) uc
      ON c.ConvId=uc.ConvId
   ORDER BY lm.lastMessageId DESC
";
$selectConvos = $conn->query($convSql);
?>
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.92);
        --glass-border: rgba(255, 255, 255, 0.15);
        --primary-color: #3B82F6;
        --text-primary: #1F2937;
        --bg-primary: #F3F4F6;
        --hover-effect: rgba(59, 130, 246, 0.08);
    }

    [data-theme="dark"] {
        --glass-bg: rgba(17, 24, 39, 0.95);
        --glass-border: rgba(255, 255, 255, 0.08);
        --primary-color: #60A5FA;
        --text-primary: #F9FAFB;
        --bg-primary: #0F172A;
        --hover-effect: rgb(185, 185, 185);
    }

    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        min-height: 100vh;
        transition: .3s;
    }

    .glass-panel {
        /* overflow-y: scroll;
        overflow-x: scroll; */
        overflow: hidden;
        background: var(--glass-bg);
        /* backdrop-filter: blur(12px) saturate(180%); */
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        /* box-shadow: 0 8px 32px rgba(0, 0, 0, .1); */
    }

    .usersChat {
        overflow-y: scroll;
        overflow-x: scroll;
        height: 100%;
    }

    .user-card-link {
        display: block;
        text-decoration: none;
        color: inherit;
        transition: .2s;
        border: 1px solid #e1d9d1;
        /* border: 1px solid ; */
        /* background: #e1d9d1; */
        /* background: #e1d9d1; */
    }

    .user-card-link:hover {
        background: var(--hover-effect);
        transform: translateY(-2px);
    }

    .user-card-link.active {
        background: var(--hover-effect);
    }

    .main-container {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem;
        height: calc(100vh - 3rem);
    }

    .chat-container {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1rem;
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        background: var(--glass-bg);
        display: flex;
        flex-direction: column;
        gap: .5rem;
        height: 50vh;
    }

    .chat-bubble {
        max-width: 70%;
        padding: .75rem 1rem;
        border-radius: 1rem;
        margin-bottom: .75rem;
        word-wrap: break-word;
    }

    .chat-bubble.sent {
        background: var(--primary-color);
        color: #fff;
        margin-left: auto;
    }

    .chat-bubble.received {
        background: rgba(59, 130, 246, .1);
        border: 1px solid var(--glass-border);
        margin-right: auto;
    }

    .chat-bubble .time {
        font-size: .7rem;
        opacity: .7;
        display: block;
        text-align: right;
    }

    .date-separator {
        font-size: .9rem;
        text-align: center;
        margin: 1rem 0;
        opacity: .7;
    }

    .input-group .form-control {
        border-radius: 2rem 0 0 2rem;
        border: 1px solid var(--glass-border);
    }

    .input-group .btn {
        border-radius: 0 2rem 2rem 0;
        background: var(--primary-color);
        color: #fff;
        border: none;
    }

    /* Files Panel */
    .files-panel {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100vh;
        background: var(--glass-bg);
        border-left: 1px solid var(--glass-border);
        transition: .3s;
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
        border-bottom: 1px solid var(--glass-border);
    }

    .files-panel-content {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    .file-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: .2s;
    }

    .file-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .file-image-card {
        text-align: center;
    }

    .file-thumb {
        max-width: 100%;
        max-height: 150px;
        border-radius: 0.5rem;
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

    /* Notifications */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 350px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideInRight 0.3s ease;
    }

    .notification-content {
        display: flex;
        align-items: center;
        padding: 1rem;
        gap: 0.75rem;
    }

    .notification-success {
        border-left: 4px solid #28a745;
    }

    .notification-error {
        border-left: 4px solid #dc3545;
    }

    .notification-info {
        border-left: 4px solid var(--primary-color);
    }

    .notification-close {
        background: none;
        border: none;
        color: var(--text-primary);
        cursor: pointer;
        padding: 0.25rem;
        margin-left: auto;
    }

    .notification-close:hover {
        opacity: 0.7;
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
        transition: transform 0.2s ease;
        border: 1px solid var(--glass-border);
    }

    .message-file-image img:hover {
        transform: scale(1.05);
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
        border: 1px solid var(--glass-border);
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

    /* Image Preview Modal */
    #imagePreviewModal .modal-content {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(12px);
    }

    #imagePreviewModal .modal-header {
        border-bottom: 1px solid var(--glass-border);
    }

    #imagePreviewModal .modal-footer {
        border-top: 1px solid var(--glass-border);
    }

    #preview-image {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Animation for notifications */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Real-time indicators */
    .user-card-link.new-message {
        animation: pulse 2s infinite;
        border-color: var(--primary-color);
    }

    .user-card-link.new-message .badge {
        animation: bounce 1s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-5px); }
        60% { transform: translateY(-3px); }
    }

    /* Connection Status */
    .connection-status {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        z-index: 1000;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .connection-status i {
        margin-right: 0.5rem;
        animation: pulse 2s infinite;
    }

    .connection-status.offline i {
        animation: none;
    }

    /* Conversation Card Styling */
    .conversation-card {
        width: 100%;
    }

    .conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.1rem;
    }

    .conversation-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.1rem;
        flex-shrink: 0;
    }

    .username {
        flex: 1;
        font-weight: 500;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.9rem;
    }

    .conversation-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.25rem;
        flex-shrink: 0;
    }

    .conversation-meta .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
        display: inline-block !important;
        min-width: 18px;
        text-align: center;
        animation: badgePulse 0.5s ease;
        font-weight: 600;
        background-color: #dc3545 !important;
        border: 1px solid #dc3545;
    }

    @keyframes badgePulse {
        0% { transform: scale(0.8); opacity: 0.7; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }

    .last-message-time {
        font-size: 0.65rem;
        color: var(--text-secondary);
        white-space: nowrap;
    }

    /* Enhanced Real-time Indicators */
    .user-card-link.new-message .last-message-time {
        color: var(--primary-color);
        font-weight: 500;
    }

    /* Ensure proper spacing and alignment */
    .user-card-link {
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .user-card-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .conversation-card {
        min-height: 35px;
    }

    .user-card-link {
        padding: 0.5rem !important;
        margin-bottom: 0.25rem !important;
    }

    @media(max-width:992px) {
        .main-container {
            flex-direction: column;
            height: auto;
        }

        .files-panel {
            width: 100%;
            right: -100%;
        }

        .conversation-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .conversation-meta {
            align-items: flex-start;
            flex-direction: row;
            gap: 0.5rem;
        }

        .username {
            font-size: 0.9rem;
        }
    }
</style>

<body data-theme="light">
    <div class="main-container">
        <!-- Sidebar -->
        <div class="glass-panel p-3" style="width:300px;">
            <h5 class="mb-3">Users</h5>
            <div class="usersChat">
                <?php if ($selectConvos->num_rows): ?>
                    <?php while ($c = $selectConvos->fetch_assoc()):
                        $active = ($c['ConvId'] == $currentConvoId) ? ' active' : ''; ?>
                        <a href="?username=<?= urlencode($c['NoUsername']) ?>&userId=<?= $c['NoUserId'] ?>"
                            class="user-card-link p-3 mb-2 glass-panel<?= $active ?>"
                            data-convid="<?= $c['ConvId'] ?>">
                            <div class="conversation-card">
                                <div class="conversation-header">
                                    <div class="username">
                                        <i class="fas fa-user-circle text-primary me-2"></i><?= $c['NoUsername'] ?>
                                    </div>
                                    <div class="conversation-meta">
                                        <?php if ($c['unreadCount'] > 0): ?>
                                            <span class="badge bg-danger"><?= $c['unreadCount'] ?></span>
                                        <?php endif; ?>
                                        <div class="last-message-time small text-muted">
                                            <?= $c['lastMessageTime'] ?? '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-muted">No conversations</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Panel -->
        <div class="d-flex flex-column flex-grow-1">
            <?php if ($currentConvoId): ?>
                <div class="glass-panel p-3 d-flex flex-column flex-grow-1">
                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                            <h5 class="mb-0"><?= htmlspecialchars($_GET['username']) ?></h5>
                        </div>
                        <div class="chat-actions">
                            <button class="btn btn-outline-secondary btn-sm me-2" id="sound-toggle" title="Toggle notification sound">
                                <i class="fas fa-volume-up" id="sound-icon"></i>
                            </button>
                            <button class="btn btn-outline-primary" id="files-toggle">
                                <i class="fas fa-folder me-2"></i>User Files
                            </button>
                            <button class="btn btn-outline-warning btn-sm ms-2" onclick="testBadgeCreation()" title="Test badge creation">
                                <i class="fas fa-bell"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Messages -->
                    <div id="chat-container" class="chat-container"></div>
                    <!-- Send Form -->
                    <form id="messageForm" class="input-group mt-3">
                        <input type="hidden" name="ConvId" value="<?= $currentConvoId ?>">
                        <input type="hidden" name="AdminId" value="<?= $adminId ?>">
                        <input type="hidden" name="UserId" value="<?= $UserId ?>">
                        <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                        <button type="submit" class="btn"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>

                <!-- Files Panel -->
                <div class="files-panel" id="files-panel">
                    <div class="files-panel-header">
                        <h6><i class="fas fa-folder me-2"></i>User Files</h6>
                        <button class="btn btn-link btn-sm" id="close-files">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="files-panel-content">
                        <div class="files-list" id="user-files-list">
                            <!-- User files will be loaded here -->
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="glass-panel p-3 flex-grow-1 d-flex align-items-center justify-content-center">
                    <span class="text-muted">Select a user</span>
                </div>
            <?php endif; ?>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            const convId = <?= json_encode($currentConvoId) ?>,
                adminId = <?= json_encode($adminId) ?>,
                userId = <?= json_encode($UserId) ?>,
                $c = $('#chat-container');

            let lastMessageId = 0;
            let isInitialLoad = true;

            // --- New: Fetch both messages and user files, merge, and display ---
            function fetchChatData() {
                if (!convId) return;
                // Fetch messages
                $.get('<?php echo $baseUrl; ?>/admin/php/fetch_messages.php', { ConvId: convId }, function(messages) {
                    // Fetch user files
                    $.get('<?php echo $baseUrl; ?>/php/list_user_files.php', { userid: userId, convid: convId }, function(files) {
                        // Merge messages and files by date/time
                        let items = [];
                        // Add messages
                        messages.forEach(m => {
                            items.push({
                                type: 'message',
                                data: m,
                                date: m.SentDate,
                                time: m.SentTime,
                                sortKey: new Date(m.SentDate + 'T' + m.SentTime).getTime() || 0
                            });
                        });
                        // Add files (only those not already in messages)
                        const messageFileUrls = new Set(messages.filter(m => m.MessageContent && m.MessageContent.startsWith('./uploads/')).map(m => m.MessageContent));
                        files.forEach(f => {
                            // Only add if not already in messages
                            if (!messageFileUrls.has(f.url)) {
                                items.push({
                                    type: 'file',
                                    data: f,
                                    date: f.uploadDate,
                                    time: f.uploadTime,
                                    sortKey: new Date(f.uploadDate + 'T' + (f.uploadTime || '00:00:00')).getTime() || 0
                                });
                            }
                        });
                        // Sort by date/time ascending
                        items.sort((a, b) => a.sortKey - b.sortKey);
                        // Render
                        $c.empty();
                        let lastDate = '';
                        items.forEach(item => {
                            const d = new Date(item.date).toDateString();
                            if (d !== lastDate) {
                                lastDate = d;
                                $c.append(`<div class="date-separator">${d}</div>`);
                            }
                            if (item.type === 'message') {
                                appendMessage(item.data);
                            } else if (item.type === 'file') {
                                appendFileMessage(item.data);
                            }
                        });
                        // Auto-scroll to bottom
                        $c.scrollTop($c.prop('scrollHeight'));
                    }, 'json');
                }, 'json');
            }

            // --- Existing: appendMessage ---
            function appendMessage(m) {
                const cls = m.AdminId != 0 ? 'sent' : 'received';
                let messageContent = m.MessageContent;
                // Detect file messages
                if (m.MessageContent && m.MessageContent.startsWith('./uploads/')) {
                    const fileName = m.MessageContent.split('/').pop();
                    const fileExt = fileName.split('.').pop().toLowerCase();
                    const fileUrl = m.MessageContent + '?v=' + Date.now();
                    if (["jpg","jpeg","png","gif","webp"].includes(fileExt)) {
                        messageContent = `
                            <div class="message-file-image">
                                <img src="${fileUrl}" alt="${fileName}" style="max-width:200px;max-height:200px;border-radius:8px;cursor:pointer;" onclick="previewImage('${fileUrl}', '${fileName}')">
                                <div class="file-info">
                                    <span class="file-name">${fileName}</span>
                                    <a href="${fileUrl}" download class="file-download"><i class="fas fa-download"></i></a>
                                </div>
                            </div>`;
                    } else {
                        messageContent = `
                            <div class="message-file-document">
                                <div class="file-icon"><i class="fas fa-file"></i></div>
                                <div class="file-details">
                                    <span class="file-name">${fileName}</span>
                                    <span class="file-type">${fileExt.toUpperCase()} File</span>
                                </div>
                                <a href="${fileUrl}" download class="file-download"><i class="fas fa-download"></i></a>
                            </div>`;
                    }
                }
                $c.append(`
                    <div class="chat-bubble ${cls}">
                      <div class="message-content">${messageContent}</div>
                      <span class="time">${m.SentTime}</span>
                    </div>
                `);
            }

            // --- New: appendFileMessage for files not in messages ---
            function appendFileMessage(f) {
                // Only show files uploaded by the user (not admin)
                const cls = 'received';
                const fileName = f.name || f.originalName;
                const fileExt = (f.type || '').toLowerCase();
                const fileUrl = f.url + '?v=' + Date.now();
                let messageContent = '';
                if (["jpg","jpeg","png","gif","webp"].includes(fileExt)) {
                    messageContent = `
                        <div class="message-file-image">
                            <img src="${fileUrl}" alt="${fileName}" style="max-width:200px;max-height:200px;border-radius:8px;cursor:pointer;" onclick="previewImage('${fileUrl}', '${fileName}')">
                            <div class="file-info">
                                <span class="file-name">${fileName}</span>
                                <a href="${fileUrl}" download class="file-download"><i class="fas fa-download"></i></a>
                            </div>
                        </div>`;
                } else {
                    messageContent = `
                        <div class="message-file-document">
                            <div class="file-icon"><i class="fas fa-file"></i></div>
                            <div class="file-details">
                                <span class="file-name">${fileName}</span>
                                <span class="file-type">${fileExt.toUpperCase()} File</span>
                            </div>
                            <a href="${fileUrl}" download class="file-download"><i class="fas fa-download"></i></a>
                        </div>`;
                }
                $c.append(`
                    <div class="chat-bubble ${cls}">
                      <div class="message-content">${messageContent}</div>
                      <span class="time">${f.uploadTime || ''}</span>
                    </div>
                `);
            }

            // Replace fetchMessages with fetchChatData
            fetchChatData();
            setInterval(fetchChatData, 3000);

            // Files Panel Toggle
            const filesToggle = document.getElementById('files-toggle');
            const filesPanel = document.getElementById('files-panel');
            const closeFiles = document.getElementById('close-files');

            function openFilesPanel() {
                filesPanel.classList.add('active');
                loadUserFiles();
            }

            function closeFilesPanel() {
                filesPanel.classList.remove('active');
            }

            if (filesToggle) {
                filesToggle.addEventListener('click', openFilesPanel);
            }

            if (closeFiles) {
                closeFiles.addEventListener('click', closeFilesPanel);
            }

            // Close files panel when clicking outside
            document.addEventListener('click', (e) => {
                if (!filesPanel.contains(e.target) && !filesToggle.contains(e.target)) {
                    closeFilesPanel();
                }
            });

            // Load user files
            function loadUserFiles() {
                if (!userId) return;
                
                $.get('<?php echo $baseUrl; ?>/php/list_user_files.php', {
                    userid: userId,
                    convid: convId
                }, function(files) {
                    renderUserFiles(files);
                });
            }

            function renderUserFiles(files) {
                const filesList = document.getElementById('user-files-list');
                filesList.innerHTML = '';

                if (!files || files.length === 0) {
                    filesList.innerHTML = '<div class="text-center text-muted">No files shared by this user.</div>';
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
                    const fileUrl = file.fullUrl || '<?php echo $baseUrl; ?>/' + file.url;
                    card.innerHTML = `
                        <div class="file-image-card">
                            <img src="${fileUrl}" alt="${file.name}" class="file-thumb" onclick="previewImage('${fileUrl}', '${file.name}')">
                            <div class="file-info-details">
                                <div class="fw-semibold">${file.name}</div>
                                <div class="text-muted small">${formatFileSize(file.size)} • Uploaded ${file.uploadDate}</div>
                            </div>
                            <div class="file-actions">
                                <button class="btn btn-outline-primary btn-sm" onclick="previewImage('${fileUrl}', '${file.name}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="${fileUrl}" download class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteUserFile('${file.url}', ${file.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>`;
                } else {
                    const docInfo = getDocTypeInfo(file.url);
                    const fileUrl = file.fullUrl || '<?php echo $baseUrl; ?>/' + file.url;
                    card.innerHTML = `
                        <div class="file-doc-card">
                            <div class="file-doc-icon">
                                <i class="fas ${docInfo.icon}" style="color: ${docInfo.color}"></i>
                            </div>
                            <div class="file-info-details">
                                <div class="fw-semibold">${file.name}</div>
                                <div class="text-muted small">${formatFileSize(file.size)} • ${docInfo.label}</div>
                                <div class="text-muted small">Uploaded ${file.uploadDate}</div>
                            </div>
                            <div class="file-actions">
                                <button class="btn btn-outline-primary btn-sm" onclick="previewFile('${fileUrl}', 'doc')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="${fileUrl}" download class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteUserFile('${file.url}', ${file.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
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

            // Real-time conversation updates
            let lastMessageIds = {};
            let realTimeInterval;
            let soundEnabled = true;

            function startRealTimeUpdates() {
                // Initial load of all conversation IDs
                initializeConversationTracking();
                
                // Start real-time updates
                realTimeInterval = setInterval(checkNewMessages, 1500);
                
                // Add connection status indicator
                addConnectionStatusIndicator();
            }

            function initializeConversationTracking() {
                const userCards = document.querySelectorAll('.user-card-link');
                userCards.forEach(card => {
                    const convId = card.getAttribute('data-convid');
                    if (convId && !lastMessageIds[convId]) {
                        lastMessageIds[convId] = 0;
                    }
                    
                    // Initialize badges for existing unread messages
                    const existingBadge = card.querySelector('.badge');
                    if (existingBadge && existingBadge.textContent > 0) {
                        // Ensure the badge is properly positioned
                        const metaSection = card.querySelector('.conversation-meta');
                        if (metaSection && !metaSection.contains(existingBadge)) {
                            metaSection.insertBefore(existingBadge, metaSection.firstChild);
                        }
                    }
                });
            }

            function addConnectionStatusIndicator() {
                const statusIndicator = document.createElement('div');
                statusIndicator.id = 'connection-status';
                statusIndicator.className = 'connection-status';
                statusIndicator.innerHTML = '<i class="fas fa-circle text-success"></i> Live';
                document.body.appendChild(statusIndicator);
            }

            function checkNewMessages() {
                // Get all conversation IDs from the sidebar
                const userCards = document.querySelectorAll('.user-card-link');
                userCards.forEach(card => {
                    const convId = card.getAttribute('data-convid');
                    
                    if (convId && !lastMessageIds[convId]) {
                        // Initialize last message ID for this conversation
                        lastMessageIds[convId] = 0; // Start from 0 to get all recent messages
                    }
                });

                // Check for new messages in all conversations
                Object.keys(lastMessageIds).forEach(convId => {
                    checkNewMessagesForConv(convId);
                });
            }

            function checkNewMessagesForConv(convId) {
                $.get('./php/check_new_messages.php', {
                    ConvId: convId,
                    lastMessageId: lastMessageIds[convId] || 0
                }, function(data) {
                    updateConnectionStatus(true);
                    
                    if (data.newMessages && data.newMessages.length > 0) {
                        // Update last message ID
                        lastMessageIds[convId] = Math.max(...data.newMessages.map(m => m.MessageId));
                        
                        // Update unread count in real-time
                        if (data.unreadCount !== undefined) {
                            updateUnreadCountRealTime(convId, data.unreadCount);
                        }
                        
                        // Show notification if not in current conversation
                        if (convId != currentConvoId) {
                            const userCard = document.querySelector(`[data-convid="${convId}"]`);
                            if (userCard) {
                                userCard.classList.add('new-message');
                                
                                // Show notification
                                const username = userCard.querySelector('.username').textContent.trim();
                                showNotification(`New message from ${username}`, 'info');
                                
                                // Play notification sound (optional)
                                playNotificationSound();
                                
                                // Debug log
                                console.log(`Updated badge for conversation ${convId} with ${data.unreadCount} unread messages`);
                            }
                        } else {
                            // If in current conversation, just append new messages
                            appendNewMessages(data.newMessages);
                        }
                    }
                }).fail(function() {
                    updateConnectionStatus(false);
                });
            }

            function updateConnectionStatus(isOnline) {
                const statusIndicator = document.getElementById('connection-status');
                if (statusIndicator) {
                    if (isOnline) {
                        statusIndicator.innerHTML = '<i class="fas fa-circle text-success"></i> Live';
                        statusIndicator.className = 'connection-status';
                    } else {
                        statusIndicator.innerHTML = '<i class="fas fa-circle text-danger"></i> Offline';
                        statusIndicator.className = 'connection-status offline';
                    }
                }
            }

            function updateUnreadCount(convId, newCount) {
                const userCard = document.querySelector(`[data-convid="${convId}"]`);
                if (userCard) {
                    const metaSection = userCard.querySelector('.conversation-meta');
                    let badge = userCard.querySelector('.badge');
                    
                    if (!badge) {
                        // Create new badge
                        badge = document.createElement('span');
                        badge.className = 'badge bg-danger';
                        badge.textContent = newCount;
                        // Insert at the beginning of meta section
                        metaSection.insertBefore(badge, metaSection.firstChild);
                    } else {
                        // Update existing badge
                        const currentCount = parseInt(badge.textContent) || 0;
                        badge.textContent = currentCount + newCount;
                    }
                    
                    // Ensure badge is visible
                    badge.style.display = 'inline-block';
                }
            }

            function updateUnreadCountRealTime(convId, newCount) {
                const userCard = document.querySelector(`[data-convid="${convId}"]`);
                if (userCard) {
                    const metaSection = userCard.querySelector('.conversation-meta');
                    let badge = userCard.querySelector('.badge');
                    
                    if (!badge) {
                        // Create new badge
                        badge = document.createElement('span');
                        badge.className = 'badge bg-danger';
                        metaSection.insertBefore(badge, metaSection.firstChild);
                    } else {
                        // Update existing badge
                        badge.textContent = newCount;
                    }
                }
            }

            function appendNewMessages(newMessages) {
                newMessages.forEach(message => {
                    appendMessage(message);
                    lastMessageId = Math.max(lastMessageId, message.MessageId || 0);
                });
                
                // Scroll to bottom only if user is at bottom
                const chatContainer = $c[0];
                const isAtBottom = chatContainer.scrollHeight - chatContainer.clientHeight <= chatContainer.scrollTop + 1;
                if (isAtBottom) {
                    $c.scrollTop($c.prop('scrollHeight'));
                }
            }

            function formatMessageTime(timeString) {
                const time = new Date('2000-01-01 ' + timeString);
                return time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            function playNotificationSound() {
                if (!soundEnabled) return;
                
                // Create a simple notification sound
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
                audio.volume = 0.3;
                audio.play().catch(e => console.log('Audio play failed:', e));
            }

            // Sound toggle functionality
            const soundToggle = document.getElementById('sound-toggle');
            const soundIcon = document.getElementById('sound-icon');
            
            if (soundToggle && soundIcon) {
                soundToggle.addEventListener('click', function() {
                    soundEnabled = !soundEnabled;
                    soundIcon.className = soundEnabled ? 'fas fa-volume-up' : 'fas fa-volume-mute';
                    soundToggle.title = soundEnabled ? 'Mute notification sound' : 'Unmute notification sound';
                    
                    showNotification(
                        soundEnabled ? 'Notification sound enabled' : 'Notification sound disabled', 
                        'info'
                    );
                });
            }

            // Start real-time updates
            startRealTimeUpdates();

            // Optimize real-time updates based on page visibility
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Page is hidden, slow down updates
                    if (realTimeInterval) {
                        clearInterval(realTimeInterval);
                        realTimeInterval = setInterval(checkNewMessages, 5000);
                    }
                } else {
                    // Page is visible, resume normal updates
                    if (realTimeInterval) {
                        clearInterval(realTimeInterval);
                        realTimeInterval = setInterval(checkNewMessages, 1500);
                    }
                }
            });

            // Handle window focus/blur for better notification experience
            window.addEventListener('focus', function() {
                // Clear all new message indicators when window gains focus
                document.querySelectorAll('.user-card-link.new-message').forEach(card => {
                    card.classList.remove('new-message');
                });
            });

            // Add keyboard shortcut to toggle sound
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'm') {
                    e.preventDefault();
                    if (soundToggle) {
                        soundToggle.click();
                    }
                }
            });

            // Clear new message indicator when conversation is clicked
            document.addEventListener('click', function(e) {
                if (e.target.closest('.user-card-link')) {
                    const card = e.target.closest('.user-card-link');
                    const convId = card.getAttribute('data-convid');
                    
                    // Clear new message indicator
                    card.classList.remove('new-message');
                    
                    // Mark messages as read and clear unread count when entering conversation
                    if (convId == currentConvoId) {
                        markMessagesAsRead(convId);
                        clearUnreadCount(convId);
                        // Force scroll to bottom when conversation is opened
                        setTimeout(() => {
                            const chatContainer = document.getElementById('chat-container');
                            if (chatContainer) {
                                chatContainer.scrollTop = chatContainer.scrollHeight;
                            }
                        }, 100);
                    }
                }
            });

            function markMessagesAsRead(convId) {
                $.post('<?php echo $baseUrl; ?>/admin/php/mark_messages_read.php', {
                    ConvId: convId
                }, function(response) {
                    if (response.success) {
                        console.log('Messages marked as read for conversation:', convId);
                    } else {
                        console.error('Failed to mark messages as read:', response.error);
                    }
                }).fail(function() {
                    console.error('Error marking messages as read');
                });
            }

            function clearUnreadCount(convId) {
                const userCard = document.querySelector(`[data-convid="${convId}"]`);
                if (userCard) {
                    const badge = userCard.querySelector('.badge');
                    if (badge) {
                        badge.remove();
                    }
                    // Also remove new-message class
                    userCard.classList.remove('new-message');
                }
            }

            // Debug function to test badge creation
            function testBadgeCreation() {
                const userCards = document.querySelectorAll('.user-card-link');
                userCards.forEach((card, index) => {
                    const convId = card.getAttribute('data-convid');
                    if (convId) {
                        setTimeout(() => {
                            updateUnreadCount(convId, 1);
                        }, index * 1000);
                    }
                });
            }

            $('#messageForm').submit(function(e) {
                e.preventDefault();
                const msg = this.message.value.trim();
                if (!msg) return;
                $.post('<?php echo $baseUrl; ?>/admin/php/submit_message.php', {
                    ConvId: convId,
                    AdminId: adminId,
                    UserId: userId,
                    message: msg
                }, () => {
                    this.message.value = '';
                    fetchChatData(); // Use fetchChatData to update the main chat stream
                });
            });
        });

        // Global functions for file preview and management
        function previewImage(url, fileName = '') {
            // Set modal content
            document.getElementById('preview-image').src = url;
            document.getElementById('download-image').href = url;
            document.getElementById('download-image').download = fileName || 'image';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        }

        function previewFile(url, type) {
            const ext = url.split('.').pop().toLowerCase();
            if (type === 'image' || ext === 'pdf') {
                window.open(url, '_blank');
            } else if (["doc","docx","ppt","pptx","xls","xlsx"].includes(ext)) {
                const gviewUrl = `https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true`;
                window.open(gviewUrl, '_blank');
            } else {
                window.open(url, '_blank');
            }
        }

        function deleteUserFile(url, fileId) {
            if (!confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
                return;
            }

            $.post('<?php echo $baseUrl; ?>/php/delete_file.php', {
                file: url,
                docId: fileId,
                adminDelete: true
            }, function(response) {
                if (response.success) {
                    // Remove the file card from the UI
                    const fileCards = document.querySelectorAll('.file-card');
                    fileCards.forEach(card => {
                        const deleteBtn = card.querySelector(`[onclick*="${fileId}"]`);
                        if (deleteBtn) {
                            card.remove();
                        }
                    });
                    
                    // Show success message
                    showNotification('File deleted successfully', 'success');
                    
                    // Reload files if panel is open
                    if (filesPanel && filesPanel.classList.contains('active')) {
                        loadUserFiles();
                    }
                } else {
                    showNotification('Failed to delete file', 'error');
                }
            }).fail(function() {
                showNotification('Error deleting file', 'error');
            });
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                    <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
    </script>
</body>