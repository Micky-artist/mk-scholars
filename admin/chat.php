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
        overflow: hidden;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
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

    /* Network Status */
    .network-status .badge {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s ease;
        }

    .network-status .badge.bg-success {
        background-color: #28a745 !important;
    }

    .network-status .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    /* Connection Status */
    #connection-indicator.badge {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s ease;
    }

    #connection-indicator.badge.bg-success {
        background-color: #28a745 !important;
    }

    #connection-indicator.badge.bg-secondary {
        background-color: #6c757d !important;
    }

    #connection-indicator.badge.bg-danger {
        background-color: #dc3545 !important;
    }

    @media(max-width:992px) {
        .main-container {
            flex-direction: column;
            height: auto;
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
                            class="user-card-link p-3 mb-2 glass-panel<?= $active ?>">
                            <div class="d-flex justify-content-between">
                                <div><i class="fas fa-user-circle text-primary me-2"></i><?= $c['NoUsername'] ?></div>
                                        <?php if ($c['unreadCount'] > 0): ?>
                                            <span class="badge bg-danger"><?= $c['unreadCount'] ?></span>
                                        <?php endif; ?>
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
                        <div class="d-flex align-items-center gap-2">
                            <span id="connection-indicator" class="badge bg-secondary">
                                <i class="fas fa-circle me-1"></i>Connecting...
                            </span>
                            <span id="network-indicator" class="badge bg-success">
                                <i class="fas fa-wifi me-1"></i>Online
                            </span>
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
                $c = $('#chat-container'),
                baseUrl = '<?php echo $baseUrl; ?>';

            let lastMessageId = 0;
            let processedMessages = new Set(); // Track processed messages to prevent duplicates
            let pollInterval = null;
            let pendingMessages = new Set(); // Track pending messages for immediate display

            // Function to get the appropriate file URL
            function getFileUrl(filePath) {
                if (!filePath) return '';
                
                // Check if it's already a full URL
                if (filePath.startsWith('http://') || filePath.startsWith('https://')) {
                    return filePath;
                }
                
                // Always use the main domain for hosted environment
                const mainDomain = 'https://mkscholars.com';
                
                // Remove leading ./ if present and construct full URL
                const cleanPath = filePath.replace(/^\.\//, '');
                return mainDomain + '/' + cleanPath;
                }

            // Network status detection
            function updateNetworkStatus() {
                const isOnline = navigator.onLine;
                const indicator = document.getElementById('network-indicator');
                
                if (indicator) {
                    if (isOnline) {
                        indicator.className = 'badge bg-success';
                        indicator.innerHTML = '<i class="fas fa-wifi me-1"></i>Online';
                } else {
                        indicator.className = 'badge bg-warning';
                        indicator.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Offline';
                    }
                }
            }

            // Initialize network status
            updateNetworkStatus();

            // Listen for network status changes
            window.addEventListener('online', updateNetworkStatus);
            window.addEventListener('offline', updateNetworkStatus);

            function fetchInitialMessages() {
                if (!convId) return;
                $.get('./php/fetch_messages.php', {
                    ConvId: convId
                }, msgs => {
                        let lastDate = '';
                        $c.empty();
                    processedMessages.clear();
                    
                        msgs.forEach(m => {
                            const d = new Date(m.SentDate).toDateString();
                            if (d !== lastDate) {
                                lastDate = d;
                                $c.append(`<div class="date-separator">${d}</div>`);
                            }
                        
                const cls = m.AdminId != 0 ? 'sent' : 'received';
                let messageContent = m.MessageContent;
                
                        // Handle file messages
                if (m.MessageContent && m.MessageContent.startsWith('./uploads/')) {
                    const fileName = m.MessageContent.split('/').pop();
                    const fileExt = fileName.split('.').pop().toLowerCase();
                            const fileUrl = getFileUrl(m.MessageContent) + '?v=' + Date.now();
                            
                    if (["jpg","jpeg","png","gif","webp"].includes(fileExt)) {
                        messageContent = `
                            <div class="message-file-image">
                                        <img src="${fileUrl}" alt="${fileName}" onclick="previewImage('${fileUrl}', '${fileName}')">
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
                        } else {
                            messageContent = `<p class="message-content">${escapeHtml(m.MessageContent)}</p>`;
                }
                
                $c.append(`
                    <div class="chat-bubble ${cls}">
                                ${messageContent}
                      <span class="time">${m.SentTime}</span>
                    </div>
                `);
                        
                        // Update last message ID and track processed message
                        lastMessageId = Math.max(lastMessageId, m.MessageId || 0);
                        processedMessages.add(m.MessageId);
                    });
                    $c.scrollTop($c.prop('scrollHeight'));
                    
                    // Start polling after initial load
                    startPolling();
                }, 'json');
            }

            function startPolling() {
                // Clear any existing interval
                if (pollInterval) {
                    clearInterval(pollInterval);
                        }
                
                // Update connection indicator
                const indicator = document.getElementById('connection-indicator');
                if (indicator) {
                    indicator.className = 'badge bg-success';
                    indicator.innerHTML = '<i class="fas fa-circle me-1"></i>Connected';
            }

                // Start polling every 3 seconds with a small delay to ensure initial load is complete
                setTimeout(() => {
                    pollInterval = setInterval(() => {
                        fetchNewMessages();
                    }, 3000);
                }, 1000); // 1 second delay
            }

            function fetchNewMessages() {
                if (!convId) return;
                
                $.get('./php/fetch_messages.php', {
                    ConvId: convId,
                    lastMessageId: lastMessageId
                }, msgs => {
                    if (msgs && msgs.length > 0) {
                        let hasNewMessages = false;
                        msgs.forEach(message => {
                            if (message.MessageId > lastMessageId && !processedMessages.has(message.MessageId)) {
                                // Check if this is a pending message we already added optimistically
                                const isPendingMessage = pendingMessages.has(message.MessageContent);
                                
                                if (!isPendingMessage) {
                                    addMessageToChat(message);
                                    hasNewMessages = true;
                        } else {
                                    // Remove from pending since we received the real message
                                    pendingMessages.delete(message.MessageContent);
                                }
                                
                    lastMessageId = Math.max(lastMessageId, message.MessageId || 0);
                                processedMessages.add(message.MessageId);
                            }
                });
                
                        // Only scroll if we added new messages
                        if (hasNewMessages) {
                    $c.scrollTop($c.prop('scrollHeight'));
                }
            }
                }, 'json');
            }

            function addMessageToChat(message) {
                const cls = message.AdminId != 0 ? 'sent' : 'received';
                let messageContent = message.MessageContent;
                
                // Handle file messages
                if (message.MessageContent && message.MessageContent.startsWith('./uploads/')) {
                    const fileName = message.MessageContent.split('/').pop();
                    const fileExt = fileName.split('.').pop().toLowerCase();
                    const fileUrl = getFileUrl(message.MessageContent) + '?v=' + Date.now();
                    
                    if (["jpg","jpeg","png","gif","webp"].includes(fileExt)) {
                        messageContent = `
                            <div class="message-file-image">
                                <img src="${fileUrl}" alt="${fileName}" onclick="previewImage('${fileUrl}', '${fileName}')">
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
                    } else {
                    messageContent = `<p class="message-content">${escapeHtml(message.MessageContent)}</p>`;
            }

                $c.append(`
                    <div class="chat-bubble ${cls}">
                        ${messageContent}
                        <span class="time">${message.SentTime}</span>
                    </div>
                `);
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Load initial messages
            fetchInitialMessages();

            $('#messageForm').submit(function(e) {
                e.preventDefault();
                const msg = this.message.value.trim();
                if (!msg) return;
                
                // Add message immediately for better UX
                const messageData = {
                    MessageId: Date.now(), // Temporary ID
                    UserId: 0,
                    AdminId: adminId,
                    MessageContent: msg,
                    SentDate: new Date().toISOString().split('T')[0],
                    SentTime: new Date().toLocaleTimeString('en-US', {hour12: false, hour: '2-digit', minute: '2-digit'})
                };
                addMessageToChat(messageData);
                $c.scrollTop($c.prop('scrollHeight'));
                
                // Track this pending message
                pendingMessages.add(msg);
                
                $.post('./php/submit_message.php', {
                    ConvId: convId,
                    AdminId: adminId,
                    UserId: userId,
                    message: msg
                }, (response) => {
                    this.message.value = '';
                    // Message will be added via polling, but we already added it optimistically
                }, 'json');
            });

            // Clean up on page unload
            $(window).on('beforeunload', function() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
                // Clear tracking sets
                processedMessages.clear();
                pendingMessages.clear();
            });
        });

        // Global functions for image preview and download
        function previewImage(url, fileName = '') {
            // Set modal content
            document.getElementById('preview-image').src = url;
            document.getElementById('download-image').href = url;
            document.getElementById('download-image').download = fileName || 'image';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        }
    </script>
</body>