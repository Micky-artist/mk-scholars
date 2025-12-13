<?php
session_start();
include("../dbconnections/connection.php");
include("validateAdminSession.php");

// Set proper headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Check if course ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
    exit;
}

$courseId = (int)$_GET['id'];

// Validate course access
if (!hasCourseAccess($courseId)) {
    echo json_encode(['success' => false, 'message' => 'You do not have access to this course.']);
    exit;
}

// Fetch course details with pricing information and currency symbol
$query = "SELECT c.*, cp.amount, cp.currency, cp.pricingDescription, curr.currencySymbol 
          FROM Courses c 
          LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
          LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
          WHERE c.courseId = $courseId";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Course not found']);
    exit;
}

$course = mysqli_fetch_assoc($result);

// Format the course data for display
$courseData = [
    'courseId' => $course['courseId'],
    'courseName' => $course['courseName'],
    'courseShortDescription' => $course['courseShortDescription'],
    'courseLongDescription' => $course['courseLongDescription'],
    'courseStartDate' => $course['courseStartDate'],
    'courseRegEndDate' => $course['courseRegEndDate'],
    'courseEndDate' => $course['courseEndDate'],
    'courseSeats' => $course['courseSeats'],
    'coursePhoto' => $course['coursePhoto'],
    'courseDisplayStatus' => $course['courseDisplayStatus'],
    'coursePaymentCodeName' => $course['coursePaymentCodeName'],
    'courseCreatedDate' => $course['courseCreatedDate'],
    'amount' => $course['amount'],
    'currency' => $course['currency'],
    'currencySymbol' => $course['currencySymbol'],
    'pricingDescription' => $course['pricingDescription']
];

echo json_encode(['success' => true, 'course' => $courseData]);
?>
