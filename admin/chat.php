<?php
// session_start();
// include("./dbconnections/connection.php");
// include("./php/validateAdminSession.php");

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
        --hover-effect:rgb(185, 185, 185);
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
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                        <h5 class="mb-0"><?= htmlspecialchars($_GET['username']) ?></h5>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            const convId = <?= json_encode($currentConvoId) ?>,
                adminId = <?= json_encode($adminId) ?>,
                userId = <?= json_encode($UserId) ?>,
                $c = $('#chat-container');

            function fetchMessages() {
                if (!convId) return;
                $.get('./php/fetch_messages.php', {
                    ConvId: convId
                }, msgs => {
                    let lastDate = '';
                    $c.empty();
                    msgs.forEach(m => {
                        const d = new Date(m.SentDate).toDateString();
                        if (d !== lastDate) {
                            lastDate = d;
                            $c.append(`<div class="date-separator">${d}</div>`);
                        }
                        const cls = m.AdminId == adminId ? 'sent' : 'received';
                        $c.append(`
            <div class="chat-bubble ${cls}">
              <p class="message-content">${m.MessageContent}</p>
              <span class="time">${m.SentTime}</span>
            </div>
          `);
                    });
                    $c.scrollTop($c.prop('scrollHeight'));
                }, 'json');
            }

            fetchMessages();
            setInterval(fetchMessages, 3000);

            $('#messageForm').submit(function(e) {
                e.preventDefault();
                const msg = this.message.value.trim();
                if (!msg) return;
                $.post('./php/submit_message.php', {
                    ConvId: convId,
                    AdminId: adminId,
                    UserId: userId,
                    message: msg
                }, () => {
                    this.message.value = '';
                    fetchMessages();
                });
            });
        });
    </script>
</body>