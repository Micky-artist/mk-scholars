
<?php
// Assumes session_start() and your DB connection ($conn) are already in place
$adminId = isset($_SESSION['adminId']) ? intval($_SESSION['adminId']) : 0;
$currentConvoId = null;

// 1) If a conversation is opened, fetch its ConvId and mark all its messages as read
if (!empty($_GET['userId']) && is_numeric($_GET['userId'])) {
    $UserId = intval($_GET['userId']);

    // Fetch the conversation
    $stmt = $conn->prepare("
      SELECT ConvId
        FROM Conversation
       WHERE UserId = ?
       LIMIT 1
    ");
    $stmt->bind_param('i', $UserId);
    $stmt->execute();
    $stmt->bind_result($fetchedConvoId);
    if ($stmt->fetch()) {
        $currentConvoId = $fetchedConvoId;
    }
    $stmt->close();

    // Mark its messages as read
    if ($currentConvoId) {
        $upd = $conn->prepare("
          UPDATE Message
             SET MessageStatus = 1
           WHERE ConvId = ?
        ");
        $upd->bind_param('i', $currentConvoId);
        $upd->execute();
        $upd->close();
    }
}

// 2) Fetch all conversations with latest message ID and unread count
$convSql = "
  SELECT
    c.ConvId,
    u.NoUsername,
    u.NoUserId,
    lm.lastMessageId,
    COALESCE(uc.unreadCount, 0) AS unreadCount
  FROM Conversation AS c
  JOIN normUsers AS u
    ON c.UserId = u.NoUserId
  JOIN (
    SELECT ConvId, MAX(MessageId) AS lastMessageId
      FROM Message
     GROUP BY ConvId
  ) AS lm
    ON c.ConvId = lm.ConvId
  LEFT JOIN (
    SELECT ConvId, COUNT(*) AS unreadCount
      FROM Message
     WHERE MessageStatus = 0
     GROUP BY ConvId
  ) AS uc
    ON c.ConvId = uc.ConvId
  ORDER BY lm.lastMessageId DESC
";
$selectConvos = $conn->query($convSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat Interface</title>
  <!-- Bootstrap CSS (adjust as needed) -->

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
        --hover-effect: rgba(96, 165, 250, 0.08);
    }
    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        min-height: 100vh;
        transition: all 0.3s;
    }
    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(12px) saturate(180%);
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .user-card-link {
        transition: all 0.2s;
        cursor: pointer;
        border: 1px solid transparent;
    }
    .user-card-link:hover {
        background: var(--hover-effect);
        transform: translateY(-2px);
    }
    .user-card-link.active {
        border: 2px solid var(--primary-color);
        box-shadow: 0 0 8px var(--primary-color);
    }
    .theme-toggle {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: grid;
        place-items: center;
        z-index: 9999;
    }
    .chat-container {
        overflow-y: auto;
        scroll-behavior: smooth;
        padding: 1rem;
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        background: var(--glass-bg);
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: .5rem;
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
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid var(--glass-border);
        margin-right: auto;
    }
    .chat-bubble .message-content { font-size: .9rem; margin-bottom: .25rem; }
    .chat-bubble .time { font-size: .7rem; opacity: .7; text-align: right; }
    .date-separator {
        font-size: .9rem;
        color: var(--text-primary);
        text-align: center;
        margin: 1rem 0;
        opacity: .7;
    }
    .file-input { display: none; }
    .file-label {
        cursor: pointer;
        background-color: var(--hover-effect);
        padding: .5rem;
        border-radius: .5rem;
        transition: background-color .3s;
    }
    .file-label:hover { background-color: rgba(59, 130, 246, .2); }
    .attachment-icon { font-size: 1.2rem; color: var(--primary-color); }
    .main-container {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem;
        height: calc(100vh - 3rem);
    }
    @media (max-width: 992px) {
        .main-container { flex-direction: column; height: auto; }
    }
  </style>
</head>
<body data-theme="light">
  <!-- Theme Toggle -->
  <button class="btn btn-primary theme-toggle glass-panel">
    <i class="fas fa-moon"></i>
  </button>

  <div class="main-container">
    <!-- Sidebar -->
    <div class="glass-panel p-3" style="width:300px;">
      <h4 class="mb-3 fw-semibold">Users</h4>
      <div class="input-group glass-panel mb-3">
        <input type="text" class="form-control bg-transparent" placeholder="Search..." data-bs-toggle="modal" data-bs-target="#searchModal">
        <button class="btn btn-transparent" data-bs-toggle="modal" data-bs-target="#searchModal">
          <i class="fas fa-search"></i>
        </button>
      </div>
      <div id="user-list" style="height: calc(100vh - 200px); overflow-y:auto;">
        <?php if ($selectConvos && $selectConvos->num_rows > 0): ?>
          <?php while ($c = $selectConvos->fetch_assoc()): ?>
            <?php $active = ($c['ConvId'] === $currentConvoId) ? ' active' : ''; ?>
            <a
              href="?username=<?= urlencode($c['NoUsername']) ?>&userId=<?= $c['NoUserId'] ?>"
              class="user-card-link d-flex justify-content-between align-items-center glass-panel p-3 mb-2<?= $active ?>"
              data-convid="<?= $c['ConvId'] ?>"
            >
              <div class="d-flex align-items-center">
                <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                <h6 class="mb-0"><?= htmlspecialchars($c['NoUsername']) ?></h6>
              </div>
              <?php if ((int)$c['unreadCount'] > 0): ?>
                <span class="badge bg-danger"><?= $c['unreadCount'] ?></span>
              <?php endif; ?>
            </a>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-muted">No conversations yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Main Panel -->
    <div class="flex-grow-1 d-flex flex-column">
      <?php if ($currentConvoId): ?>
        <div class="glass-panel p-3 d-flex flex-column flex-grow-1">
          <!-- Header -->
          <div class="d-flex align-items-center mb-4">
            <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
            <div>
              <h5 class="mb-0 fw-semibold"><?= htmlspecialchars($_GET['username']) ?></h5>
              <small class="text-muted">Last active: 2h ago</small>
            </div>
          </div>
          <!-- Chat -->
          <div class="chat-container" id="chat-container">
            <?php
            $msgStmt = $conn->prepare("
              SELECT AdminId, MessageContent, SentDate, SentTime
                FROM Message
               WHERE ConvId = ?
            ORDER BY SentDate, SentTime
            ");
            $msgStmt->bind_param('i', $currentConvoId);
            $msgStmt->execute();
            $res = $msgStmt->get_result();
            $lastDate = null;
            while ($m = $res->fetch_assoc()):
              $d = date("Y-m-d", strtotime($m['SentDate']));
              if ($d !== $lastDate):
                echo '<div class="date-separator">'. date("F j, Y", strtotime($d)) .'</div>';
                $lastDate = $d;
              endif;
              $cls = ($m['AdminId'] == $adminId) ? 'sent' : 'received';
            ?>
              <div class="chat-bubble <?= $cls ?>">
                <p class="message-content"><?= htmlspecialchars($m['MessageContent']) ?></p>
                <span class="time"><?= date("h:i A", strtotime($m['SentTime'])) ?></span>
              </div>
            <?php endwhile; $msgStmt->close(); ?>
          </div>
          <!-- Send Form -->
          <form action="./php/submit_message.php" method="post" enctype="multipart/form-data" class="input-group mt-4">
            <input type="file" name="file" id="file-input" class="file-input">
            <label for="file-input" class="file-label"><i class="fas fa-paperclip attachment-icon"></i></label>
            <input type="hidden" name="UserId" value="<?= $UserId ?>">
            <input type="hidden" name="AdminId" value="<?= $adminId ?>">
            <input type="hidden" name="ConvId" value="<?= $currentConvoId ?>">
            <input type="text" name="message" class="form-control bg-transparent" placeholder="Type message…" required>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
          </form>
        </div>
      <?php else: ?>
        <div class="glass-panel p-3 flex-grow-1 d-flex align-items-center justify-content-center">
          <h5 class="text-muted">Please select a conversation</h5>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Bootstrap & jQuery -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Theme toggle
    const btn = document.querySelector('.theme-toggle');
    const body = document.body;
    const stored = localStorage.getItem('theme') || 'light';
    body.dataset.theme = stored;
    btn.innerHTML = stored === 'light' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
    btn.onclick = () => {
      const t = body.dataset.theme === 'light' ? 'dark' : 'light';
      body.dataset.theme = t;
      localStorage.setItem('theme', t);
      btn.innerHTML = t === 'light' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
    };

    // Auto-scroll chat
    const container = document.getElementById('chat-container');
    if (container) container.scrollTop = container.scrollHeight;
  </script>
</body>
</html>