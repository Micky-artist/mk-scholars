<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

// Get course ID from URL
$courseId = isset($_GET['course']) ? intval($_GET['course']) : 0;

if ($courseId <= 0) {
    header("Location: course-management.php");
    exit;
}

// Get course details
$courseQuery = "SELECT * FROM Courses WHERE courseId = ?";
$courseStmt = $conn->prepare($courseQuery);
$courseStmt->bind_param("i", $courseId);
$courseStmt->execute();
$courseResult = $courseStmt->get_result();

if ($courseResult->num_rows === 0) {
    header("Location: course-management.php");
    exit;
}

$course = $courseResult->fetch_assoc();
$courseStmt->close();

// Handle reply submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $submissionId = intval($_POST['submission_id'] ?? 0);
    $reply = trim($_POST['reply'] ?? '');
    $status = $_POST['status'] ?? 'replied';
    $adminId = $_SESSION['adminId'];
    
    if ($submissionId > 0 && !empty($reply)) {
        $updateQuery = "UPDATE Submissions 
                       SET facilitatorReply = ?, 
                           repliedBy = ?, 
                           repliedDate = NOW(), 
                           submissionStatus = ?,
                           updatedDate = NOW()
                       WHERE submissionId = ? AND courseId = ?";
        $updateStmt = $conn->prepare($updateQuery);
        
        if ($updateStmt) {
            $updateStmt->bind_param("sisii", $reply, $adminId, $status, $submissionId, $courseId);
            
            if ($updateStmt->execute()) {
                $success = true;
            } else {
                $error = 'Failed to save reply. Please try again.';
            }
            $updateStmt->close();
        } else {
            $error = 'Database error. Please try again.';
        }
    } else {
        $error = 'Please provide a reply.';
    }
}

// Handle status update only (without reply)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $submissionId = intval($_POST['submission_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    
    if ($submissionId > 0) {
        $updateQuery = "UPDATE Submissions 
                       SET submissionStatus = ?,
                           updatedDate = NOW()
                       WHERE submissionId = ? AND courseId = ?";
        $updateStmt = $conn->prepare($updateQuery);
        
        if ($updateStmt) {
            $updateStmt->bind_param("sii", $status, $submissionId, $courseId);
            $updateStmt->execute();
            $updateStmt->close();
        }
    }
}

// Get filter status
$filterStatus = $_GET['status'] ?? 'all';

// Build query
$query = "SELECT s.*, u.NoUsername, u.NoEmail 
          FROM Submissions s 
          LEFT JOIN normUsers u ON s.userId = u.NoUserId 
          WHERE s.courseId = ?";

if ($filterStatus !== 'all') {
    $query .= " AND s.submissionStatus = ?";
}

$query .= " ORDER BY s.createdDate DESC";

$stmt = $conn->prepare($query);
if ($filterStatus !== 'all') {
    $stmt->bind_param("is", $courseId, $filterStatus);
} else {
    $stmt->bind_param("i", $courseId);
}
$stmt->execute();
$result = $stmt->get_result();
$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
}
$stmt->close();

// Get counts for filter badges
$countQuery = "SELECT submissionStatus, COUNT(*) as count FROM Submissions WHERE courseId = ? GROUP BY submissionStatus";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("i", $courseId);
$countStmt->execute();
$countResult = $countStmt->get_result();
$counts = ['pending' => 0, 'replied' => 0, 'closed' => 0];
while ($row = $countResult->fetch_assoc()) {
    $counts[$row['submissionStatus']] = $row['count'];
}
$countStmt->close();
$totalCount = array_sum($counts);
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php
include("./partials/head.php");
?>

<body>
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
    data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <?php
    include("./partials/header.php");
    ?>
    <?php
    include("./partials/navbar.php");
    ?>
    <div class="page-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div>
                    <h4 class="card-title mb-1">
                      <i class="mdi mdi-file-upload me-2"></i>
                      Submissions - <?php echo htmlspecialchars($course['courseName']); ?>
                    </h4>
                    <a href="course-management.php" class="text-muted small">
                      <i class="mdi mdi-arrow-left me-1"></i>Back to Course Management
                    </a>
                  </div>
                </div>

                <?php if ($success): ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-2"></i>
                    Reply saved successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
                <?php endif; ?>

                <?php if ($error): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
                <?php endif; ?>

                <!-- Filter Tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link <?php echo $filterStatus === 'all' ? 'active' : ''; ?>" 
                       href="?course=<?php echo $courseId; ?>&status=all">
                      All <span class="badge bg-secondary"><?php echo $totalCount; ?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php echo $filterStatus === 'pending' ? 'active' : ''; ?>" 
                       href="?course=<?php echo $courseId; ?>&status=pending">
                      Pending <span class="badge bg-warning"><?php echo $counts['pending']; ?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php echo $filterStatus === 'replied' ? 'active' : ''; ?>" 
                       href="?course=<?php echo $courseId; ?>&status=replied">
                      Replied <span class="badge bg-success"><?php echo $counts['replied']; ?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php echo $filterStatus === 'closed' ? 'active' : ''; ?>" 
                       href="?course=<?php echo $courseId; ?>&status=closed">
                      Closed <span class="badge bg-secondary"><?php echo $counts['closed']; ?></span>
                    </a>
                  </li>
                </ul>

                <!-- Submissions List -->
                <div class="comment-widgets scrollable">
                  <?php if (empty($submissions)): ?>
                    <div class="text-center py-5">
                      <i class="mdi mdi-inbox-outline" style="font-size: 48px; color: #ccc;"></i>
                      <p class="text-muted mt-3">No submissions found for this course.</p>
                    </div>
                  <?php else: ?>
                    <?php foreach ($submissions as $submission): ?>
                      <div class="card mb-3">
                        <div class="card-body">
                          <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                              <h5 class="card-title mb-1">
                                <?php echo htmlspecialchars($submission['submissionTitle']); ?>
                              </h5>
                              <p class="text-muted small mb-0">
                                <i class="mdi mdi-account me-1"></i>
                                <?php echo htmlspecialchars($submission['NoUsername'] ?? 'Unknown User'); ?>
                                (<?php echo htmlspecialchars($submission['NoEmail'] ?? 'N/A'); ?>)
                                <span class="mx-2">•</span>
                                <i class="mdi mdi-clock-outline me-1"></i>
                                <?php echo date('F j, Y g:i A', strtotime($submission['createdDate'])); ?>
                              </p>
                            </div>
                            <div>
                              <span class="badge 
                                <?php 
                                echo $submission['submissionStatus'] === 'pending' ? 'bg-warning' : 
                                    ($submission['submissionStatus'] === 'replied' ? 'bg-success' : 'bg-secondary'); 
                                ?>">
                                <?php echo ucfirst($submission['submissionStatus']); ?>
                              </span>
                            </div>
                          </div>

                          <?php if (!empty($submission['submissionContent'])): ?>
                            <div class="mb-3">
                              <h6 class="text-muted small mb-2">Content:</h6>
                              <p class="mb-0"><?php echo nl2br(htmlspecialchars($submission['submissionContent'])); ?></p>
                            </div>
                          <?php endif; ?>

                          <?php if (!empty($submission['filePath'])): ?>
                            <div class="mb-3">
                              <h6 class="text-muted small mb-2">Attached File:</h6>
                              <a href="../<?php echo htmlspecialchars($submission['filePath']); ?>" 
                                 class="btn btn-sm btn-outline-primary" 
                                 target="_blank" 
                                 download>
                                <i class="mdi mdi-download me-1"></i>
                                <?php echo htmlspecialchars($submission['fileName']); ?>
                                <small>(<?php echo number_format($submission['fileSize'] / 1024, 2); ?> KB)</small>
                              </a>
                            </div>
                          <?php endif; ?>

                          <?php if (!empty($submission['facilitatorReply'])): ?>
                            <div class="alert alert-info mb-3">
                              <h6 class="mb-2">
                                <i class="mdi mdi-reply me-1"></i>
                                Your Reply:
                              </h6>
                              <p class="mb-0"><?php echo nl2br(htmlspecialchars($submission['facilitatorReply'])); ?></p>
                              <?php if ($submission['repliedDate']): ?>
                                <small class="text-muted">
                                  Replied: <?php echo date('F j, Y g:i A', strtotime($submission['repliedDate'])); ?>
                                </small>
                              <?php endif; ?>
                            </div>
                          <?php endif; ?>

                          <!-- Reply Form -->
                          <div class="border-top pt-3">
                            <form method="POST" class="mb-2">
                              <input type="hidden" name="submission_id" value="<?php echo $submission['submissionId']; ?>">
                              
                              <div class="mb-3">
                                <label for="reply_<?php echo $submission['submissionId']; ?>" class="form-label">
                                  <?php echo empty($submission['facilitatorReply']) ? 'Reply to Student:' : 'Update Reply:'; ?>
                                </label>
                                <textarea class="form-control" 
                                          id="reply_<?php echo $submission['submissionId']; ?>" 
                                          name="reply" 
                                          rows="4" 
                                          required><?php echo htmlspecialchars($submission['facilitatorReply'] ?? ''); ?></textarea>
                              </div>

                              <div class="row">
                                <div class="col-md-6 mb-2">
                                  <select class="form-select" name="status" required>
                                    <option value="pending" <?php echo $submission['submissionStatus'] === 'pending' ? 'selected' : ''; ?>>
                                      Pending
                                    </option>
                                    <option value="replied" <?php echo $submission['submissionStatus'] === 'replied' ? 'selected' : ''; ?>>
                                      Replied
                                    </option>
                                    <option value="closed" <?php echo $submission['submissionStatus'] === 'closed' ? 'selected' : ''; ?>>
                                      Closed
                                    </option>
                                  </select>
                                </div>
                                <div class="col-md-6">
                                  <button type="submit" name="submit_reply" class="btn btn-primary w-100">
                                    <i class="mdi mdi-send me-1"></i>
                                    <?php echo empty($submission['facilitatorReply']) ? 'Send Reply' : 'Update Reply'; ?>
                                  </button>
                                </div>
                              </div>
                            </form>

                            <!-- Quick Status Update -->
                            <?php if (!empty($submission['facilitatorReply'])): ?>
                              <form method="POST" class="d-inline">
                                <input type="hidden" name="submission_id" value="<?php echo $submission['submissionId']; ?>">
                                <input type="hidden" name="status" value="<?php echo $submission['submissionStatus'] === 'closed' ? 'replied' : 'closed'; ?>">
                                <button type="submit" name="update_status" class="btn btn-sm btn-outline-secondary">
                                  <i class="mdi mdi-<?php echo $submission['submissionStatus'] === 'closed' ? 'lock-open' : 'lock'; ?> me-1"></i>
                                  <?php echo $submission['submissionStatus'] === 'closed' ? 'Reopen' : 'Close'; ?>
                                </button>
                              </form>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
      include("./partials/footer.php");
      ?>
    </div>
  </div>

  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
  <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
  <script src="./dist/js/waves.js"></script>
  <script src="./dist/js/sidebarmenu.js"></script>
  <script src="./dist/js/custom.min.js"></script>
</body>

</html>

