<?php
// Include session configuration for persistent sessions
include("./config/session.php");
include("./dbconnection/connection.php");
include("./php/validateSession.php");

// Get course ID from URL
$courseId = isset($_GET['course']) ? intval($_GET['course']) : 0;

if ($courseId <= 0) {
    header("Location: my-discussions.php");
    exit;
}

// Get user ID from session
$userId = $_SESSION['userId'];
$username = $_SESSION['username'] ?? 'User';

// Verify user has access to this course
$accessQuery = "SELECT c.* FROM Courses c 
                LEFT JOIN CourseEnrollments e ON e.courseId = c.courseId AND e.userId = ? AND e.enrollmentStatus = 1
                LEFT JOIN subscription s ON s.Item = c.courseId AND s.UserId = ? AND s.SubscriptionStatus = 1 AND (s.expirationDate IS NULL OR s.expirationDate > NOW())
                WHERE c.courseId = ? AND (e.enrollmentId IS NOT NULL OR s.SubId IS NOT NULL)";
$accessStmt = $conn->prepare($accessQuery);
$accessStmt->bind_param("iii", $userId, $userId, $courseId);
$accessStmt->execute();
$accessResult = $accessStmt->get_result();

if ($accessResult->num_rows === 0) {
    header("Location: my-discussions.php");
    exit;
}

$course = $accessResult->fetch_assoc();
$accessStmt->close();

// Handle form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_submission'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    // Validate input
    if (empty($title)) {
        $error = 'Please provide a title for your submission.';
    } elseif (strlen($title) > 255) {
        $error = 'Title is too long. Maximum 255 characters allowed.';
    } elseif (empty($content) && (!isset($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE)) {
        $error = 'Please provide either text content or upload a document.';
    } else {
        // Handle file upload if provided
        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;
        
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/submissions/';
            
            // Ensure directory exists with proper permissions
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $error = 'Failed to create upload directory. Please contact administrator.';
                    error_log("Failed to create directory: $uploadDir. Error: " . error_get_last()['message']);
                } else {
                    chmod($uploadDir, 0755);
                }
            }
            
            // Check if directory is writable
            if (empty($error) && !is_writable($uploadDir)) {
                $error = 'Upload directory is not writable. Please contact administrator.';
                error_log("Upload directory not writable: $uploadDir");
            }
            
            if (empty($error)) {
                $originalName = basename($_FILES['document']['name']);
                $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                
                // Allowed file types
                $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
                
                if (!in_array($fileExtension, $allowedTypes)) {
                    $error = 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes);
                } elseif ($_FILES['document']['size'] > 10 * 1024 * 1024) { // 10MB limit
                    $error = 'File size exceeds 10MB limit.';
                } elseif ($_FILES['document']['size'] === 0) {
                    $error = 'Uploaded file is empty.';
                } elseif (!is_uploaded_file($_FILES['document']['tmp_name'])) {
                    $error = 'Invalid file upload. Security check failed.';
                } else {
                    // Generate unique filename
                    $uniqueName = $courseId . '_' . $userId . '_' . time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
                    $targetPath = $uploadDir . $uniqueName;
                    
                    // Check if temp file is readable
                    if (!is_readable($_FILES['document']['tmp_name'])) {
                        $error = 'Uploaded file is not readable.';
                        error_log("Temp file not readable: " . $_FILES['document']['tmp_name']);
                    } else {
                        // Attempt to move the file
                        if (move_uploaded_file($_FILES['document']['tmp_name'], $targetPath)) {
                            // Verify file was actually moved
                            if (file_exists($targetPath)) {
                                chmod($targetPath, 0644);
                                $filePath = './uploads/submissions/' . $uniqueName;
                                $fileName = $originalName;
                                $fileType = $fileExtension;
                                $fileSize = $_FILES['document']['size'];
                            } else {
                                $error = 'File upload failed: File was not saved correctly.';
                                error_log("File not found after move: $targetPath");
                            }
                        } else {
                            $lastError = error_get_last();
                            $errorMsg = isset($lastError['message']) ? $lastError['message'] : 'Unknown error';
                            $error = 'Failed to upload file: ' . $errorMsg;
                            error_log("move_uploaded_file failed. Source: " . $_FILES['document']['tmp_name'] . ", Target: $targetPath, Error: $errorMsg");
                        }
                    }
                }
            }
        } elseif (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Handle other upload errors
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File too large (server limit exceeded)',
                UPLOAD_ERR_FORM_SIZE => 'File too large (form limit exceeded)',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            $errorCode = $_FILES['document']['error'];
            $error = isset($uploadErrors[$errorCode]) ? $uploadErrors[$errorCode] : 'Upload error code: ' . $errorCode;
            error_log("File upload error code: $errorCode");
        }
        
        // Insert submission into database
        if (empty($error)) {
            // Handle NULL values for file fields if no file was uploaded
            $filePath = $filePath ?? null;
            $fileName = $fileName ?? null;
            $fileType = $fileType ?? null;
            $fileSize = $fileSize ?? null;
            
            $insertQuery = "INSERT INTO Submissions (courseId, userId, submissionTitle, submissionContent, filePath, fileName, fileType, fileSize, submissionStatus) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $insertStmt = $conn->prepare($insertQuery);
            
            if ($insertStmt) {
                $insertStmt->bind_param("iisssssi", $courseId, $userId, $title, $content, $filePath, $fileName, $fileType, $fileSize);
                
                if ($insertStmt->execute()) {
                    $success = true;
                    // Clear form data
                    $_POST = [];
                } else {
                    $error = 'Failed to save submission: ' . $insertStmt->error;
                    error_log("Submission insert error: " . $insertStmt->error);
                }
                $insertStmt->close();
            } else {
                $error = 'Database error: ' . $conn->error;
                error_log("Submission prepare error: " . $conn->error);
            }
        }
    }
}

// Fetch user's previous submissions for this course
$submissions = [];
$submissionsQuery = "SELECT * FROM Submissions WHERE courseId = ? AND userId = ? ORDER BY createdDate DESC";
$submissionsStmt = $conn->prepare($submissionsQuery);

if ($submissionsStmt) {
    $submissionsStmt->bind_param("ii", $courseId, $userId);
    $submissionsStmt->execute();
    $submissionsResult = $submissionsStmt->get_result();
    while ($row = $submissionsResult->fetch_assoc()) {
        $submissions[] = $row;
    }
    $submissionsStmt->close();
} else {
    // Table might not exist yet
    error_log("Submissions table query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Submission - <?php echo htmlspecialchars($course['courseName']); ?> - MK Scholars</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --primary-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .submission-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .submission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .submission-card.replied {
            border-left-color: var(--success-color);
        }

        .submission-card.closed {
            border-left-color: var(--text-secondary);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-replied {
            background: #d1fae5;
            color: #065f46;
        }

        .status-closed {
            background: #e5e7eb;
            color: #374151;
        }

        .reply-box {
            background: #f9fafb;
            border-left: 3px solid var(--success-color);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .file-download {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .file-download:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="container-fluid">
        <div class="row">
            <?php $_GET['page'] = 'submission'; include('./partials/universalNavigation.php'); ?>
            <main class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h3 class="mb-0">
                            <i class="fas fa-file-upload me-2"></i>
                            Assignment Submission
                        </h3>
                        <p class="text-muted mb-0 small"><?php echo htmlspecialchars($course['courseName']); ?></p>
                    </div>
                    <a href="my-discussions.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="row">
                    <div class="col-12">
                        <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Success!</strong> Your submission has been sent successfully! A facilitator will review and reply soon.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Submission Form -->
                <div class="glass-panel">
                    <h3 class="mb-4">
                        <i class="fas fa-plus-circle me-2"></i>
                        New Submission
                    </h3>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                                   required maxlength="255" placeholder="Enter a brief title for your submission">
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="6" 
                                      placeholder="Enter your assignment, question, or message here..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                            <small class="text-muted">You can provide text content, upload a document, or both.</small>
                        </div>

                        <div class="mb-3">
                            <label for="document" class="form-label">Upload Document (Optional)</label>
                            <input type="file" class="form-control" id="document" name="document" 
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar">
                            <small class="text-muted">Maximum file size: 10MB. Allowed types: PDF, DOC, DOCX, TXT, Images, ZIP, RAR</small>
                        </div>

                        <button type="submit" name="submit_submission" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit
                        </button>
                    </form>
                </div>

                <!-- Previous Submissions -->
                <div class="glass-panel">
                    <h3 class="mb-4">
                        <i class="fas fa-history me-2"></i>
                        My Submissions
                    </h3>
                    
                    <?php if (empty($submissions)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">You haven't made any submissions yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($submissions as $submission): ?>
                            <div class="submission-card <?php echo $submission['submissionStatus']; ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($submission['submissionTitle']); ?></h5>
                                    <span class="status-badge status-<?php echo $submission['submissionStatus']; ?>">
                                        <?php echo ucfirst($submission['submissionStatus']); ?>
                                    </span>
                                </div>
                                
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-clock me-1"></i>
                                    Submitted: <?php echo date('F j, Y g:i A', strtotime($submission['createdDate'])); ?>
                                </p>

                                <?php if (!empty($submission['submissionContent'])): ?>
                                    <div class="mb-3">
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($submission['submissionContent'])); ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($submission['filePath'])): ?>
                                    <div class="mb-3">
                                        <a href="<?php echo htmlspecialchars($submission['filePath']); ?>" 
                                           class="file-download" 
                                           target="_blank" 
                                           download>
                                            <i class="fas fa-download"></i>
                                            Download: <?php echo htmlspecialchars($submission['fileName']); ?>
                                            <small>(<?php echo number_format($submission['fileSize'] / 1024, 2); ?> KB)</small>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($submission['facilitatorReply'])): ?>
                                    <div class="reply-box">
                                        <h6 class="mb-2">
                                            <i class="fas fa-reply me-2"></i>
                                            Facilitator Reply
                                        </h6>
                                        <p class="mb-2"><?php echo nl2br(htmlspecialchars($submission['facilitatorReply'])); ?></p>
                                        <?php if ($submission['repliedDate']): ?>
                                            <small class="text-muted">
                                                Replied: <?php echo date('F j, Y g:i A', strtotime($submission['repliedDate'])); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleBtn = document.querySelector('.sidebar-toggle');
        var sidebar = document.querySelector('.sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });
        }
    });
    </script>
</body>
</html>

