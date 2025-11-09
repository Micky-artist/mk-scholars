<?php
session_start();
require_once './dbconnections/connection.php';
require_once './php/validateAdminSession.php';

// Ensure admin is logged in
if (!isset($_SESSION['adminId'])) {
    header('Location: login');
    exit;
}

// Optimize: Cache courses data to reduce database queries
$courses = [];
$coursesCacheKey = 'courses_list_' . date('Y-m-d-H'); // Cache for 1 hour

// Try to get from cache first (if you have a caching system)
// For now, we'll use a simple approach with minimal database queries

// Get courses with optimized query - only select necessary fields
$coursesQuery = "SELECT courseId, courseName, courseShortDescription, courseDisplayStatus 
                 FROM Courses 
                 WHERE courseDisplayStatus = 1 
                 ORDER BY courseName ASC 
                 LIMIT 50"; // Limit to prevent too many options

$coursesResult = $conn->query($coursesQuery);
if ($coursesResult) {
    $courses = $coursesResult->fetch_all(MYSQLI_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grant_subscription'])) {
    // Debug: Log all POST data
    error_log("Subscription POST data: " . print_r($_POST, true));
    
    try {
        // Check database connection first
        if (!$conn) {
            throw new Exception('Database connection failed.');
        }
        
        // Collect and validate inputs
        $userId            = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $subscriptionType  = filter_input(INPUT_POST, 'subscription_type', FILTER_SANITIZE_STRING);
        $duration          = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
        
        // Debug: Log input values
        error_log("Parsed inputs - UserID: $userId, Type: $subscriptionType, Duration: $duration");
        
        // Validate inputs
        if (!$userId || $userId <= 0) {
            throw new Exception('Invalid user ID: ' . $userId);
        }
        
        if (!$subscriptionType || empty(trim($subscriptionType))) {
            throw new Exception('Please select a subscription type. Received: ' . $subscriptionType);
        }
        
        if (!$duration || $duration <= 0) {
            throw new Exception('Duration must be a positive number. Received: ' . $duration);
        }
        
        // Validate subscription type against available courses
        $validCourseIds = array_column($courses, 'courseId');
        
        error_log("Valid course IDs: " . print_r($validCourseIds, true));
        error_log("Selected type: " . $subscriptionType);

        if (!in_array($subscriptionType, $validCourseIds)) {
            throw new Exception('Invalid course selected: ' . $subscriptionType . '. Please select a valid course.');
        }

        // Check if user exists
        $userCheck = $conn->prepare("SELECT NoUserId, NoUsername FROM normUsers WHERE NoUserId = ?");
        if (!$userCheck) {
            throw new Exception('User check prepare failed: ' . $conn->error);
        }
        
        $userCheck->bind_param('i', $userId);
        if (!$userCheck->execute()) {
            throw new Exception('User check execute failed: ' . $userCheck->error);
        }
        
        $userResult = $userCheck->get_result();
        
        if ($userResult->num_rows === 0) {
            throw new Exception('User not found with ID: ' . $userId);
        }
        
        $userData = $userResult->fetch_assoc();
        $userCheck->close();
        
        error_log("User found: " . print_r($userData, true));

        // Check for existing active subscription
        $existingCheck = $conn->prepare("SELECT SubId FROM subscription WHERE UserId = ? AND Item = ? AND SubscriptionStatus = 1 AND expirationDate > CURDATE()");
        if (!$existingCheck) {
            throw new Exception('Existing check prepare failed: ' . $conn->error);
        }
        
        $existingCheck->bind_param('is', $userId, $subscriptionType);
        if (!$existingCheck->execute()) {
            throw new Exception('Existing check execute failed: ' . $existingCheck->error);
        }
        
        $existingResult = $existingCheck->get_result();
        
        if ($existingResult->num_rows > 0) {
            throw new Exception('User already has an active subscription for this item.');
        }
        $existingCheck->close();

        // Generate subscription code
        try {
            $rand = bin2hex(random_bytes(4));
        } catch (Exception $e) {
            $rand = substr(md5(uniqid('', true)), 0, 8);
        }
        $subscriptionCode = 'ADM_' . strtoupper($rand) . '_' . date('Ymd');
        $subscriptionDate = date('Y-m-d');
        $expirationDate   = date('Y-m-d', strtotime("+{$duration} days"));
        
        error_log("Generated subscription code: $subscriptionCode");
        error_log("Dates - Start: $subscriptionDate, End: $expirationDate");

        // Insert into DB - using correct column names from database
        $stmt = $conn->prepare("
            INSERT INTO subscription 
              (SubscriptionStatus, Item, UserId, adminId, SubscriptionCode, subscriptionDate, expirationDate)
            VALUES 
              (1, ?, ?, ?, ?, ?, ?)
        ");
        
        if (!$stmt) {
            throw new Exception('Database prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param(
            'siisss',
            $subscriptionType,
            $userId,
            $_SESSION['adminId'],
            $subscriptionCode,
            $subscriptionDate,
            $expirationDate
        );

        if ($stmt->execute()) {
            $insertId = $conn->insert_id;
            $stmt->close();
            error_log("Subscription inserted successfully with ID: $insertId");
            $_SESSION['success'] = "Subscription granted successfully to {$userData['NoUsername']} for {$subscriptionType} (expires {$expirationDate}).";
            header('Location: add_subscription');
            exit;
        } else {
            throw new Exception('Database insert failed: ' . $stmt->error);
        }
        
    } catch (Exception $e) {
        error_log("Subscription error: " . $e->getMessage());
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header('Location: add_subscription');
        exit;
    }
}

// Handle search with optimized query
$searchQuery = '';
$users       = [];
if (!empty($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    if (strlen($searchQuery) >= 2) { // Only search if at least 2 characters
        $esc = $conn->real_escape_string($searchQuery);
        $sql = "
          SELECT NoUserId, NoUsername, NoEmail, NoPhone, NoCreationDate
            FROM normUsers
           WHERE NoUsername LIKE '%{$esc}%'
              OR NoEmail    LIKE '%{$esc}%'
              OR NoPhone    LIKE '%{$esc}%'
           ORDER BY NoUsername ASC
           LIMIT 15
        ";
        if ($res = $conn->query($sql)) {
            $users = $res->fetch_all(MYSQLI_ASSOC);
        }
    }
}

// Grab and clear flash messages
$error   = $_SESSION['error']   ?? null; unset($_SESSION['error']);
$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grant New Subscription</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Preload critical resources -->
  <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
  <style>
    :root {
      --primary-color:   #4361ee;
      --secondary-color: #3f37c9;
      --success-color:   #4cc9f0;
      --light-bg:        #f5f7fb;
    }
    
    /* Performance optimizations */
    * {
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-bg);
      margin: 0;
      padding: 0;
    }
    
    .card {
      border: none;
      border-radius: .75rem;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      will-change: transform;
    }
    
    .card-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-radius: .75rem .75rem 0 0 !important;
    }
    
    .search-box {
      position: relative;
    }
    
    .search-box i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #adb5bd;
      pointer-events: none;
    }
    
    .search-box input {
      padding-left: 40px;
      border-radius: 50px;
      transition: border-color 0.2s ease;
    }
    
    .search-box input:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }
    
    .user-card {
      border: 1px solid rgba(0,0,0,0.08);
      border-radius: .5rem;
      transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
      cursor: pointer;
      will-change: transform;
    }
    
    .user-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border-color: var(--primary-color);
    }
    
    .user-card.selected {
      background-color: rgba(67,97,238,0.05);
      border-color: var(--primary-color);
    }
    
    .subscription-option {
      border: 1px solid #dee2e6;
      border-radius: .5rem;
      padding: 1rem;
      margin-bottom: 1rem;
      transition: border-color 0.2s ease, background-color 0.2s ease;
      cursor: pointer;
      will-change: transform;
    }
    
    .subscription-option:hover {
      border-color: var(--primary-color);
      transform: translateY(-1px);
    }
    
    .subscription-option.selected {
      background-color: rgba(67,97,238,0.05);
      border-color: var(--primary-color);
    }
    
    .course-item {
      display: block;
    }
    
    .course-item.hidden {
      display: none;
    }
    
    #courseList {
      border: 1px solid #dee2e6;
      border-radius: .5rem;
      padding: 0.5rem;
      background: #fff;
    }
    
    .btn-grant {
      background-color: var(--success-color);
      color: white;
      border-radius: 50px;
      padding: .5rem 1.5rem;
      font-weight: 500;
      transition: background-color 0.2s ease, transform 0.2s ease;
    }
    
    .btn-grant:hover {
      background-color: #3ab4d8;
      color: white;
      transform: translateY(-1px);
    }
    
    .btn-grant:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }
    
    /* Loading states */
    .loading {
      opacity: 0.6;
      pointer-events: none;
    }
    
    .spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid var(--primary-color);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    /* Responsive optimizations */
    @media (max-width: 768px) {
      .subscription-option {
        margin-bottom: 0.5rem;
      }
      
      .user-card {
        padding: 0.75rem;
      }
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Grant New Subscription</h4>
        <a href="subscriptions" class="btn btn-sm btn-light">
          <i class="fas fa-arrow-left me-1"></i>Back to Subscriptions
        </a>
      </div>
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success:</strong> <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>
        
        <!-- Debug Information -->
        <?php if (isset($_POST['grant_subscription'])): ?>
          <div class="alert alert-info">
            <h6><i class="fas fa-bug me-2"></i>Debug Information</h6>
            <small>
              <strong>POST Data:</strong> <?= htmlspecialchars(print_r($_POST, true)) ?><br>
              <strong>User ID:</strong> <?= isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : 'Not set' ?><br>
              <strong>Subscription Type:</strong> <?= isset($_POST['subscription_type']) ? htmlspecialchars($_POST['subscription_type']) : 'Not set' ?><br>
              <strong>Duration:</strong> <?= isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : 'Not set' ?><br>
              <strong>Admin ID:</strong> <?= isset($_SESSION['adminId']) ? $_SESSION['adminId'] : 'Not set' ?>
            </small>
          </div>
        <?php endif; ?>

        <div class="row">
          <!-- Search Column -->
          <div class="col-md-6">
            <h5 class="mb-3"><i class="fas fa-search me-2"></i>Search User</h5>
            <form method="GET" class="mb-4" id="searchForm">
              <div class="search-box mb-3">
                <i class="fas fa-search"></i>
                <input
                  type="text"
                  name="search"
                  id="searchInput"
                  class="form-control"
                  placeholder="Username, email or phone (min 2 characters)"
                  value="<?= htmlspecialchars($searchQuery, ENT_QUOTES) ?>"
                  autocomplete="off"
                >
                <div id="searchSpinner" class="spinner" style="display: none; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></div>
              </div>
              <button type="submit" class="btn btn-primary" id="searchBtn">
                <i class="fas fa-search me-1"></i>Search
              </button>
            </form>

            <?php if ($searchQuery !== ''): ?>
              <h6 class="mb-3">Results (<?= count($users) ?>)</h6>
              <div class="user-list">
                <?php if ($users): ?>
                  <?php foreach ($users as $user): ?>
                    <div
                      class="user-card p-3 mb-2"
                      onclick="selectUser(<?= (int)$user['NoUserId'] ?>, '<?= addslashes(htmlspecialchars($user['NoUsername'], ENT_QUOTES)) ?>')"
                      id="user-<?= (int)$user['NoUserId'] ?>"
                    >
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1"><?= htmlspecialchars($user['NoUsername']) ?></h6>
                          <small class="text-muted"><?= htmlspecialchars($user['NoEmail']) ?></small>
                        </div>
                        <small class="text-muted">
                          Joined <?= date('M Y', strtotime($user['NoCreationDate'])) ?>
                        </small>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="alert alert-info">No users found.</div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Subscription Column -->
          <div class="col-md-6">
            <h5 class="mb-3"><i class="fas fa-gift me-2"></i>Subscription Details</h5>
            <form method="POST" id="subscriptionForm">
              <input type="hidden" name="grant_subscription" value="1">
              <input type="hidden" name="user_id" id="userIdInput">
              <input type="hidden" name="subscription_type" id="subscriptionTypeInput">

              <div class="mb-3">
                <label class="form-label">Selected User</label>
                <div class="form-control" id="selectedUserDisplay">None</div>
              </div>

              <div class="mb-3">
                <label class="form-label">Select Course</label>
                <div class="search-box mb-3">
                  <i class="fas fa-search"></i>
                  <input
                    type="text"
                    id="courseSearchInput"
                    class="form-control"
                    placeholder="Type to search courses..."
                    autocomplete="off"
                  >
                </div>
                <div id="courseList" class="row g-2" style="max-height: 400px; overflow-y: auto;">
                  <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                      <div class="col-12 course-item" data-course-id="<?= $course['courseId'] ?>" data-course-name="<?= htmlspecialchars(strtolower($course['courseName'])) ?>">
                        <div class="subscription-option" onclick="selectSubscription('<?= $course['courseId'] ?>')" id="option-<?= $course['courseId'] ?>">
                          <h6><?= htmlspecialchars($course['courseName']) ?></h6>
                          <small class="text-muted"><?= htmlspecialchars($course['courseShortDescription'] ?? 'Course access') ?></small>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="col-12">
                      <div class="alert alert-info">No courses available.</div>
                    </div>
                  <?php endif; ?>
                </div>
                <div id="noCourseResults" class="alert alert-warning mt-2" style="display: none;">
                  <i class="fas fa-info-circle me-2"></i>No courses found matching your search.
                </div>
              </div>

              <div class="mb-3">
                <label for="duration" class="form-label">Duration (days)</label>
                <input
                  type="number"
                  class="form-control"
                  id="duration"
                  name="duration"
                  min="1"
                  value="30"
                  required
                >
              </div>

              <div class="d-grid">
                <button
                  type="submit"
                  name="grant_subscription"
                  class="btn btn-grant"
                  id="grantButton"
                  disabled
                >
                  <i class="fas fa-check-circle me-1"></i>Grant Subscription
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Performance optimizations
    'use strict';
    
    let selectedUserId = null;
    let selectedSubscriptionType = null;
    let searchTimeout = null;
    
    // Cache DOM elements for better performance
    const elements = {
      searchInput: document.getElementById('searchInput'),
      searchBtn: document.getElementById('searchBtn'),
      searchSpinner: document.getElementById('searchSpinner'),
      searchForm: document.getElementById('searchForm'),
      userIdInput: document.getElementById('userIdInput'),
      subscriptionTypeInput: document.getElementById('subscriptionTypeInput'),
      selectedUserDisplay: document.getElementById('selectedUserDisplay'),
      grantButton: document.getElementById('grantButton'),
      duration: document.getElementById('duration'),
      courseSearchInput: document.getElementById('courseSearchInput'),
      courseList: document.getElementById('courseList'),
      noCourseResults: document.getElementById('noCourseResults')
    };

    // Debounced search function
    function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    // Optimized user selection
    function selectUser(userId, username) {
      console.log('selectUser called:', userId, username);
      // Use requestAnimationFrame for smooth animations
      requestAnimationFrame(() => {
        document.querySelectorAll('.user-card').forEach(c => c.classList.remove('selected'));
        const userCard = document.getElementById(`user-${userId}`);
        if (userCard) {
          userCard.classList.add('selected');
        }
        
        selectedUserId = userId;
        elements.userIdInput.value = userId;
        elements.selectedUserDisplay.textContent = username;
        console.log('User selected - ID:', userId, 'Input value:', elements.userIdInput.value);
        checkFormCompletion();
      });
    }

    // Optimized subscription selection
    function selectSubscription(type) {
      console.log('selectSubscription called:', type);
      requestAnimationFrame(() => {
        document.querySelectorAll('.subscription-option').forEach(o => o.classList.remove('selected'));
        const option = document.getElementById(`option-${type}`);
        if (option) {
          option.classList.add('selected');
        }
        
        selectedSubscriptionType = type;
        elements.subscriptionTypeInput.value = type;
        console.log('Subscription selected - Type:', type, 'Input value:', elements.subscriptionTypeInput.value);
        
        // Set default duration
        elements.duration.value = 30;
        checkFormCompletion();
      });
    }
    
    // Course search functionality
    function filterCourses() {
      const searchTerm = elements.courseSearchInput.value.trim().toLowerCase();
      const courseItems = document.querySelectorAll('.course-item');
      let visibleCount = 0;
      
      courseItems.forEach(item => {
        const courseName = item.getAttribute('data-course-name') || '';
        if (courseName.includes(searchTerm)) {
          item.classList.remove('hidden');
          visibleCount++;
        } else {
          item.classList.add('hidden');
        }
      });
      
      // Show/hide "no results" message
      if (visibleCount === 0 && searchTerm.length > 0) {
        elements.noCourseResults.style.display = 'block';
      } else {
        elements.noCourseResults.style.display = 'none';
      }
    }

    // Optimized form completion check
    function checkFormCompletion() {
      const isComplete = !!(selectedUserId && selectedSubscriptionType);
      console.log('Form completion check - UserID:', selectedUserId, 'Type:', selectedSubscriptionType, 'Complete:', isComplete);
      elements.grantButton.disabled = !isComplete;
      
      if (isComplete) {
        elements.grantButton.classList.remove('btn-secondary');
        elements.grantButton.classList.add('btn-grant');
      } else {
        elements.grantButton.classList.remove('btn-grant');
        elements.grantButton.classList.add('btn-secondary');
      }
    }

    // Search functionality with debouncing
    function performSearch() {
      const query = elements.searchInput.value.trim();
      
      if (query.length < 2) {
        elements.searchBtn.disabled = true;
        return;
      }
      
      elements.searchBtn.disabled = false;
      elements.searchSpinner.style.display = 'block';
      elements.searchForm.submit();
    }

    // Debounced search
    const debouncedSearch = debounce(performSearch, 300);

    // Event listeners with performance optimizations
    document.addEventListener('DOMContentLoaded', function() {
      // Search input event listener
      if (elements.searchInput) {
        elements.searchInput.addEventListener('input', function() {
          const query = this.value.trim();
          elements.searchBtn.disabled = query.length < 2;
          
          if (query.length >= 2) {
            debouncedSearch();
          }
        });
      }

      // Form submission optimization
      if (elements.searchForm) {
        elements.searchForm.addEventListener('submit', function(e) {
          const query = elements.searchInput.value.trim();
          if (query.length < 2) {
            e.preventDefault();
            return false;
          }
          
          elements.searchBtn.disabled = true;
          elements.searchSpinner.style.display = 'block';
        });
      }

      // Course search input event listener
      if (elements.courseSearchInput) {
        elements.courseSearchInput.addEventListener('input', debounce(filterCourses, 200));
        elements.courseSearchInput.addEventListener('keyup', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
          }
        });
      }
      
      // Initial form state
      checkFormCompletion();
      
      // Add loading states to form submission
      const subscriptionForm = document.getElementById('subscriptionForm');
      if (subscriptionForm) {
        subscriptionForm.addEventListener('submit', function(e) {
          console.log('Form submission started');
          console.log('Form data:', {
            user_id: elements.userIdInput.value,
            subscription_type: elements.subscriptionTypeInput.value,
            duration: elements.duration.value,
            grant_subscription: '1'
          });
          
          // Validate form data before submission
          if (!elements.userIdInput.value) {
            e.preventDefault();
            alert('Please select a user');
            return false;
          }
          
          if (!elements.subscriptionTypeInput.value) {
            e.preventDefault();
            alert('Please select a subscription type');
            return false;
          }
          
          elements.grantButton.disabled = true;
          elements.grantButton.innerHTML = '<span class="spinner me-2"></span>Processing...';
        });
      }
    });

    // Optimize scroll performance
    let ticking = false;
    function updateScroll() {
      // Add any scroll-based optimizations here
      ticking = false;
    }

    window.addEventListener('scroll', function() {
      if (!ticking) {
        requestAnimationFrame(updateScroll);
        ticking = true;
      }
    });

    // Preload critical resources
    function preloadResources() {
      // Preload any additional resources if needed
    }

    // Initialize performance optimizations
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', preloadResources);
    } else {
      preloadResources();
    }
  </script>
</body>
</html>
