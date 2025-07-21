<?php
session_start();
include("./dbconnections/connection.php");

// Temporarily disable strict session validation for testing
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

$adminId        = intval($_SESSION['adminId'] ?? 1); // Default to 1 for testing
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

// Debug information
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
echo "<strong>Debug Info:</strong><br>";
echo "Admin ID: $adminId<br>";
echo "Current Convo ID: " . ($currentConvoId ?? 'null') . "<br>";
echo "User ID: " . ($UserId ?? 'null') . "<br>";
echo "Conversations found: " . ($selectConvos ? $selectConvos->num_rows : 'query failed') . "<br>";
echo "</div>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .chat-container { height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; }
        .message { margin-bottom: 10px; padding: 8px; border-radius: 5px; }
        .user-message { background: #e3f2fd; }
        .admin-message { background: #f3e5f5; text-align: right; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <h4>Users</h4>
                <div class="list-group">
                    <?php if ($selectConvos && $selectConvos->num_rows > 0): ?>
                        <?php while ($c = $selectConvos->fetch_assoc()): ?>
                            <a href="?username=<?= urlencode($c['NoUsername']) ?>&userId=<?= $c['NoUserId'] ?>" 
                               class="list-group-item list-group-item-action">
                                <?= htmlspecialchars($c['NoUsername']) ?>
                                <?php if ($c['unreadCount'] > 0): ?>
                                    <span class="badge bg-danger"><?= $c['unreadCount'] ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info">No conversations found</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="col-md-9">
                <?php if ($currentConvoId): ?>
                    <h4>Chat with <?= htmlspecialchars($_GET['username'] ?? 'User') ?></h4>
                    <div id="chat-container" class="chat-container">
                        <!-- Messages will be loaded here -->
                    </div>
                    <form id="messageForm" class="mt-3">
                        <div class="input-group">
                            <input type="hidden" name="ConvId" value="<?= $currentConvoId ?>">
                            <input type="hidden" name="AdminId" value="<?= $adminId ?>">
                            <input type="hidden" name="UserId" value="<?= $UserId ?>">
                            <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">Select a user to start chatting</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            const convId = <?= json_encode($currentConvoId) ?>;
            const adminId = <?= json_encode($adminId) ?>;
            const userId = <?= json_encode($UserId) ?>;
            
            function loadMessages() {
                if (!convId) return;
                
                $.get('<?php echo $baseUrl; ?>/admin/php/fetch_messages.php', {
                    ConvId: convId
                }, function(messages) {
                    const chatContainer = $('#chat-container');
                    chatContainer.empty();
                    
                    messages.forEach(function(msg) {
                        const isAdmin = msg.AdminId != 0;
                        const messageClass = isAdmin ? 'admin-message' : 'user-message';
                        const sender = isAdmin ? 'Admin' : 'User';
                        
                        let content = msg.MessageContent;
                        if (msg.MessageContent && msg.MessageContent.startsWith('./uploads/')) {
                            const fileName = msg.MessageContent.split('/').pop();
                            content = `<strong>File:</strong> ${fileName} <a href="${msg.MessageContent}" download>Download</a>`;
                        }
                        
                        chatContainer.append(`
                            <div class="message ${messageClass}">
                                <strong>${sender}:</strong> ${content}
                                <br><small>${msg.SentTime}</small>
                            </div>
                        `);
                    });
                    
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);
                }).fail(function() {
                    console.log('Failed to load messages');
                });
            }
            
            // Load messages initially
            loadMessages();
            
            // Auto-refresh every 3 seconds
            setInterval(loadMessages, 3000);
            
            // Handle message submission
            $('#messageForm').submit(function(e) {
                e.preventDefault();
                const message = $(this).find('input[name="message"]').val().trim();
                if (!message) return;
                
                $.post('<?php echo $baseUrl; ?>/admin/php/submit_message.php', {
                    ConvId: convId,
                    AdminId: adminId,
                    UserId: userId,
                    message: message
                }, function() {
                    $(this).find('input[name="message"]').val('');
                    loadMessages();
                }.bind(this));
            });
        });
    </script>
</body>
</html> 