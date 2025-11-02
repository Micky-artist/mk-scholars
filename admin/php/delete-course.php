<?php
session_start();
include("../dbconnections/connection.php");
include("../php/validateAdminSession.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if admin has DeleteApplication permission
if (!hasPermission('DeleteApplication')) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete courses.']);
    exit;
}

$courseId = isset($_POST['courseId']) ? (int)$_POST['courseId'] : 0;

if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
    exit;
}

// Start transaction
mysqli_autocommit($conn, false);

try {
    // Delete discussion board messages first
    $deleteDiscussionsQuery = "DELETE FROM DiscussionBoard WHERE courseId = ?";
    $deleteDiscussionsStmt = $conn->prepare($deleteDiscussionsQuery);
    $deleteDiscussionsStmt->bind_param("i", $courseId);
    $deleteDiscussionsStmt->execute();
    $deleteDiscussionsStmt->close();
    
    // Delete course files
    $deleteFilesQuery = "DELETE FROM CourseFiles WHERE courseId = ?";
    $deleteFilesStmt = $conn->prepare($deleteFilesQuery);
    $deleteFilesStmt->bind_param("i", $courseId);
    $deleteFilesStmt->execute();
    $deleteFilesStmt->close();
    
    // Delete course lessons
    $deleteLessonsQuery = "DELETE FROM CourseLessons WHERE courseId = ?";
    $deleteLessonsStmt = $conn->prepare($deleteLessonsQuery);
    $deleteLessonsStmt->bind_param("i", $courseId);
    $deleteLessonsStmt->execute();
    $deleteLessonsStmt->close();
    
    // Delete course tags
    $deleteTagsQuery = "DELETE FROM CourseTags WHERE courseId = ?";
    $deleteTagsStmt = $conn->prepare($deleteTagsQuery);
    $deleteTagsStmt->bind_param("i", $courseId);
    $deleteTagsStmt->execute();
    $deleteTagsStmt->close();
    
    // Delete course enrollments
    $deleteEnrollmentsQuery = "DELETE FROM CourseEnrollments WHERE courseId = ?";
    $deleteEnrollmentsStmt = $conn->prepare($deleteEnrollmentsQuery);
    $deleteEnrollmentsStmt->bind_param("i", $courseId);
    $deleteEnrollmentsStmt->execute();
    $deleteEnrollmentsStmt->close();
    
    // Delete course pricing
    $deletePricingQuery = "DELETE FROM CoursePricing WHERE courseId = ?";
    $deletePricingStmt = $conn->prepare($deletePricingQuery);
    $deletePricingStmt->bind_param("i", $courseId);
    $deletePricingStmt->execute();
    $deletePricingStmt->close();
    
    // Delete subscription records related to this course
    $deleteSubscriptionsQuery = "DELETE FROM subscription WHERE Item = ?";
    $deleteSubscriptionsStmt = $conn->prepare($deleteSubscriptionsQuery);
    $deleteSubscriptionsStmt->bind_param("i", $courseId);
    $deleteSubscriptionsStmt->execute();
    $deleteSubscriptionsStmt->close();
    
    // Delete the course
    $deleteCourseQuery = "DELETE FROM Courses WHERE courseId = ?";
    $deleteCourseStmt = $conn->prepare($deleteCourseQuery);
    $deleteCourseStmt->bind_param("i", $courseId);
    $deleteCourseStmt->execute();
    $deleteCourseStmt->close();
    
    // Commit transaction
    mysqli_commit($conn);
    
    echo json_encode(['success' => true, 'message' => 'Course and all related data deleted successfully']);
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Error deleting course: ' . $e->getMessage()]);
}

// Re-enable autocommit
mysqli_autocommit($conn, true);
?>
