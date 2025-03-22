
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
            transition: all 0.3s;
        }

        .user-card {
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .user-card:hover {
            background: var(--hover-effect);
            transform: translateY(-2px);
        }

        .chat-container {
            height: 400px; /* Smaller chat container */
            overflow-y: auto;
            scroll-behavior: smooth;
            padding: 1rem;
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            background: var(--glass-bg);
        }

        .chat-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            margin-bottom: 0.75rem;
            position: relative;
            word-wrap: break-word;
        }

        .chat-bubble.sent {
            background: var(--primary-color);
            color: white;
            margin-left: auto;
        }

        .chat-bubble.received {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid var(--glass-border);
            margin-right: auto;
        }

        .chat-bubble .message-content {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .chat-bubble .time {
            font-size: 0.7rem;
            opacity: 0.7;
            text-align: right;
        }

        .date-separator {
            font-size: 0.9rem;
            color: var(--text-primary);
            text-align: center;
            margin: 1rem 0;
            opacity: 0.7;
        }

        .file-input {
            display: none;
        }

        .file-label {
            display: inline-block;
            cursor: pointer;
            background-color: var(--hover-effect);
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .file-label:hover {
            background-color: rgba(59, 130, 246, 0.2);
        }

        .attachment-icon {
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .main-container {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem;
            height: calc(100vh - 3rem);
        }

        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
                height: auto;
            }
        }

        /* Search Modal */
        .search-modal .modal-content {
            background: var(--glass-bg);
            backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
        }

        .search-modal .modal-header {
            border-bottom: 1px solid var(--glass-border);
        }

        .search-modal .modal-footer {
            border-top: 1px solid var(--glass-border);
        }

        .search-modal .list-group-item {
            background: transparent;
            border: 1px solid var(--glass-border);
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .search-modal .list-group-item:hover {
            background: var(--hover-effect);
            transform: translateY(-2px);
        }
    </style>
<!-- </head> -->

<body data-theme="light">
    <!-- Theme Toggle Button -->
    <button class="btn btn-primary theme-toggle glass-panel">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Search Modal -->
    <div class="modal fade search-modal" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="searchInput" class="form-control bg-transparent" placeholder="Search by name or email...">
                    <div class="list-group mt-3" id="searchResults">
                        <!-- Search results will be dynamically populated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- User List -->
        <div class="glass-panel p-3" style="width: 300px;">
            <div class="d-flex flex-column h-100">
                <h4 class="mb-3 fw-semibold">Users</h4>
                <div class="input-group glass-panel mb-3">
                    <input type="text" class="form-control bg-transparent" placeholder="Search..." data-bs-toggle="modal" data-bs-target="#searchModal">
                    <button class="btn btn-transparent" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="user-list" style="height: calc(100vh - 200px); overflow-y: auto;">
                    <?php
                    $selectConvos = mysqli_query($conn, "SELECT c.*, u.NoUsername, u.NoUserId FROM Conversation c JOIN normUsers u ON c.UserId=u.NoUserId");
                    if ($selectConvos->num_rows > 0) {
                        while ($convos = mysqli_fetch_assoc($selectConvos)) {
                    ?>
                            <a href="?username=<?php echo $convos['NoUsername'] ?>&userId=<?php echo $convos['NoUserId'] ?>" style="text-decoration: none;">
                                <div class="user-card glass-panel p-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo $convos['NoUsername'] ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <?php
        if (isset($_GET['username']) && $_GET['username'] != null && isset($_GET['userId']) && $_GET['userId'] != null) {
            $UserId = $_GET['userId'];
            $CheckConvo = mysqli_query($conn, "SELECT * FROM Conversation WHERE UserId = '$UserId' LIMIT 1");
            if ($CheckConvo->num_rows == 1) {
                $convoData = mysqli_fetch_assoc($CheckConvo);
                $convoId = $convoData['ConvId'];
                $ConvStatus = $convoData['ConvStatus'];
                $adminId = $_SESSION['adminId'];
                
        ?>
                <div class="glass-panel p-3 flex-grow-1">
                    <div class="d-flex flex-column h-100">
                        <!-- Header -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3">
                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-semibold"><?php echo $_GET['username']; ?></h5>
                                <small class="text-muted">Last active: 2h ago</small>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <ul class="nav nav-pills mb-4">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="pill" href="#chat">Chat</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#applications">Applications</a>
                            </li> -->
                        </ul>

                        <!-- Content -->
                        <div class="tab-content flex-grow-1">
                            <!-- Chat -->
                            <div class="tab-pane fade show active h-100" id="chat">
                                <div class="chat-container" id="chat-container">
                                    <?php
                                    $selectMessages = mysqli_query($conn, "SELECT * FROM Message WHERE ConvId = $convoId ORDER BY SentDate, SentTime");
                                    if ($selectMessages->num_rows > 0) {
                                        $currentDate = null;
                                        while ($messages = mysqli_fetch_assoc($selectMessages)) {
                                            $messageDate = date("Y-m-d", strtotime($messages['SentDate']));
                                            $messageTime = date("h:i A", strtotime($messages['SentTime']));

                                            if ($currentDate !== $messageDate) {
                                                $currentDate = $messageDate;
                                                echo '<div class="date-separator">' . date("F j, Y", strtotime($currentDate)) . '</div>';
                                            }

                                            if ($messages['AdminId'] == $adminId) {
                                                echo '<div class="chat-bubble sent">';
                                                echo '<p class="message-content">' . htmlspecialchars($messages['MessageContent']) . '</p>';
                                                echo '<span class="time">' . $messageTime . '</span>';
                                                echo '</div>';
                                            } else {
                                                echo '<div class="chat-bubble received">';
                                                echo '<p class="message-content">' . htmlspecialchars($messages['MessageContent']) . '</p>';
                                                echo '<span class="time">' . $messageTime . '</span>';
                                                echo '</div>';
                                            }
                                        }
                                    } else {
                                        echo '<div class="text-center">No messages found.</div>';
                                    }
                                    ?>
                                </div>

                                <!-- Message Form -->
                                <div id="statusMessage"></div>
                                <form id="messageForm" action="" class="input-group mt-4">
                                    <div class="file-input-container">
                                        <input type="file" name="file" id="file-input" class="file-input">
                                        <label for="file-input" class="file-label">
                                            <i class="fas fa-paperclip attachment-icon"></i>
                                        </label>
                                    </div>
                                    <input type="hidden" name="UserId" value="0">
                                    <input type="hidden" name="AdminId" value="<?php echo htmlspecialchars($adminId); ?>">
                                    <input type="hidden" name="ConvId" value="<?php echo htmlspecialchars($convoId); ?>">
                                    <input type="text" class="form-control bg-transparent" name="message" placeholder="Type message..." required>
                                    <button type="submit" name="send" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Send
                                    </button>
                                </form>
                            </div>

                            <!-- Applications -->
                            <div class="tab-pane fade h-100" id="applications">
                                <div class="row g-3 h-100" style="overflow-y: auto;">
                                    <!-- App Cards -->
                                    <div class="col-12">
                                        <div class="application-card glass-panel p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Website Development</h6>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-link" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu glass-panel">
                                                        <li><a class="dropdown-item">Edit</a></li>
                                                        <li><a class="dropdown-item">Delete</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="progress-glass">
                                                <div class="progress-bar" style="width: 75%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <small class="text-muted">Status: In Progress</small>
                                                <small class="text-muted">75% Complete</small>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Add more apps -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="glass-panel p-3 flex-grow-1">
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3">
                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-semibold">User not found (Please select a user)</h5>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Theme Toggle
        const themeToggle = document.querySelector('.theme-toggle');
        const body = document.body;

        const savedTheme = localStorage.getItem('theme') || 'light';
        body.setAttribute('data-theme', savedTheme);
        updateToggleIcon();

        function updateTheme() {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleIcon();
        }

        function updateToggleIcon() {
            const currentTheme = body.getAttribute('data-theme');
            themeToggle.innerHTML = currentTheme === 'light' ?
                '<i class="fas fa-moon"></i>' :
                '<i class="fas fa-sun"></i>';
        }

        themeToggle.addEventListener('click', updateTheme);

        // AJAX for sending messages
        $(document).ready(function() {
            // Search functionality
            $('#searchInput').on('input', function() {
                const query = $(this).val();
                if (query.length > 2) {
                    $.ajax({
                        url: './php/search_users.php',
                        type: 'GET',
                        data: { query: query },
                        success: function(response) {
                            $('#searchResults').html(response);
                        }
                    });
                } else {
                    $('#searchResults').html('');
                }
            });

        });
    </script>
        <script>
        // Scroll to the bottom of the chat container
        const chatContainer = document.getElementById('chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>
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
