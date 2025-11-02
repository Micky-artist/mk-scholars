<?php
session_start();
include("../dbconnections/connection.php");
include("./validateAdminSession.php");

// Enhanced function to log actions with detailed information
function logScholarshipAction($conn, $action, $id, $title, $userId) {
    $stmt = $conn->prepare("INSERT INTO Logs (userId, logMessage, logDate, logTime, logStatus) VALUES (?, ?, ?, ?, 0)");
    $logDate = date("Y-m-d");
    $logTime = date("H:i:s");
    $logMessage = "$action: \"$title\".";
    $stmt->bind_param("isss", $userId, $logMessage, $logDate, $logTime);
    $stmt->execute();
    $stmt->close();
}

// Function to get scholarship title by ID
function getScholarshipTitle($conn, $id) {
    $stmt = $conn->prepare("SELECT scholarshipTitle FROM scholarships WHERE scholarshipId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $scholarshipData = $result->fetch_assoc();
        $title = $scholarshipData['scholarshipTitle'];
    } else {
        $title = "Unknown Scholarship";
    }
    
    $stmt->close();
    return $title;
}

// Function to get post title by ID
function getPostTitle($conn, $id) {
    $stmt = $conn->prepare("SELECT projectTitle FROM posts WHERE postId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $postData = $result->fetch_assoc();
        $title = $postData['projectTitle'];
    } else {
        $title = "Unknown Post";
    }
    
    $stmt->close();
    return $title;
}

// Verify admin is logged in
if (!isset($_SESSION['adminId']) || empty($_SESSION['adminId'])) {
    header("Location: ../login.php");
    exit;
}

$adminId = (int)$_SESSION['adminId']; // Cast to integer for safety

// Validate required parameters
if (isset($_GET['i']) && !empty($_GET['i']) && is_numeric($_GET['i']) && 
    isset($_GET['n']) && !empty($_GET['n']) && 
    isset($_GET['a']) && !empty($_GET['a'])) {
    
    $id = (int)$_GET['i']; // Cast to integer for safety
    $action = $_GET['a'];
    
    // Handle different actions using prepared statements
    switch ($action) {
        case "publishScholarship":
            // Check if admin has PublishApplication permission
            if (!hasPermission('PublishApplication')) {
                echo '<script>alert("You do not have permission to publish applications."); window.location.href="../applications";</script>';
                exit;
            }
            
            // Get the scholarship title first
            $scholarshipTitle = getScholarshipTitle($conn, $id);
            
            $stmt = $conn->prepare("UPDATE scholarships SET scholarshipStatus = 1 WHERE scholarshipId = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                logScholarshipAction($conn, "Published Scholarship", $id, $scholarshipTitle, $adminId);
                $stmt->close();
                header("Location: ../applications");
                exit;
            }
            $stmt->close();
            break;
            
        case "unPublishScholarship":
            // Check if admin has PublishApplication permission
            if (!hasPermission('PublishApplication')) {
                echo '<script>alert("You do not have permission to unpublish applications."); window.location.href="../applications";</script>';
                exit;
            }
            
            // Get the scholarship title first
            $scholarshipTitle = getScholarshipTitle($conn, $id);
            
            $stmt = $conn->prepare("UPDATE scholarships SET scholarshipStatus = 0 WHERE scholarshipId = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                logScholarshipAction($conn, "Unpublished Scholarship", $id, $scholarshipTitle, $adminId);
                $stmt->close();
                header("Location: ../applications");
                exit;
            }
            $stmt->close();
            break;
            
        case "publishPost":
            // Get the post title first
            $postTitle = getPostTitle($conn, $id);
            
            $stmt = $conn->prepare("UPDATE posts SET projStatus = 1 WHERE postId = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                logScholarshipAction($conn, "Published Post", $id, $postTitle, $adminId);
                $stmt->close();
                header("Location: ../scholarships");
                exit;
            }
            $stmt->close();
            break;
            
        case "unPublishPost":
            // Get the post title first
            $postTitle = getPostTitle($conn, $id);
            
            $stmt = $conn->prepare("UPDATE posts SET projStatus = 0 WHERE postId = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                logScholarshipAction($conn, "Unpublished Post", $id, $postTitle, $adminId);
                $stmt->close();
                header("Location: ../scholarships");
                exit;
            }
            $stmt->close();
            break;
            
        case "deleteScholarship":
            // Check if admin has DeleteApplication permission
            if (!hasPermission('DeleteApplication')) {
                echo '<script>alert("You do not have permission to delete applications."); window.location.href="../applications";</script>';
                exit;
            }
            
            // First retrieve the scholarship information safely
            $stmt = $conn->prepare("SELECT scholarshipId, scholarshipTitle, scholarshipImage FROM scholarships WHERE scholarshipId = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $scholarshipData = $result->fetch_assoc();
                $scholarshipTitle = $scholarshipData['scholarshipTitle'];
                $imageName = $scholarshipData['scholarshipImage'];
                $filename = "../uploads/posts/" . $imageName;
                $stmt->close();
                
                // Delete file if it exists
                if (!empty($imageName) && file_exists($filename)) {
                    // For security, validate that filename doesn't contain directory traversal
                    $basePath = realpath("../uploads/posts/");
                    $filePath = realpath($filename);
                    
                    // Only delete if the file is in the expected directory
                    if ($filePath && strpos($filePath, $basePath) === 0) {
                        unlink($filePath);
                    }
                }
                
                // Delete the scholarship record
                $stmt = $conn->prepare("DELETE FROM scholarships WHERE scholarshipId = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    logScholarshipAction($conn, "Deleted Scholarship", $id, $scholarshipTitle, $adminId);
                    $stmt->close();
                    echo '<script>alert("Scholarship \"' . htmlspecialchars($scholarshipTitle) . '\" has been successfully deleted"); window.location.href="../applications";</script>';
                    exit;
                }
                $stmt->close();
            } else {
                header("Location: ../404");
                exit;
            }
            break;
            
        case "deletePost":
            // First retrieve the post information safely
            $stmt = $conn->prepare("SELECT postId, projectTitle, projectImg1 FROM posts WHERE postId = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $postData = $result->fetch_assoc();
                $postTitle = $postData['projectTitle'];
                $imageName = $postData['projectImg1'];
                $filename = "../uploads/posts/" . $imageName;
                $stmt->close();
                
                // Delete file if it exists
                if (!empty($imageName) && file_exists($filename)) {
                    // For security, validate that filename doesn't contain directory traversal
                    $basePath = realpath("../uploads/posts/");
                    $filePath = realpath($filename);
                    
                    // Only delete if the file is in the expected directory
                    if ($filePath && strpos($filePath, $basePath) === 0) {
                        unlink($filePath);
                    }
                }
                
                // Delete the post record
                $stmt = $conn->prepare("DELETE FROM posts WHERE postId = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    logScholarshipAction($conn, "Deleted Post", $id, $postTitle, $adminId);
                    $stmt->close();
                    echo '<script>alert("Post \"' . htmlspecialchars($postTitle) . '\" has been successfully deleted"); history.back();</script>';
                    exit;
                }
                $stmt->close();
            } else {
                header("Location: ../404");
                exit;
            }
            break;
            
        default:
            // Invalid action
            header("Location: ../404");
            exit;
    }
} else {
    // Missing or invalid parameters
    header("Location: ../404");
    exit;
}

// If execution reaches here, something went wrong
header("Location: ../error.php?message=Operation failed");
exit;
?>