<?php
header('Content-Type: application/json');
include('../dbconnection/connection.php');
if (!isset($_GET['userid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing userid']);
    exit;
}
$userid = intval($_GET['userid']);
$convid = isset($_GET['convid']) ? intval($_GET['convid']) : null;

// Get all documents for the user - both uploaded by them and shared in their conversations
if ($convid !== null) {
    // If specific conversation, get documents from that conversation
    $sql = "SELECT DocumentId, UserId, ConvId, DocumentName, OriginalFileName, FilePath, FileType, FileSize, UploadDate, UploadTime FROM Documents WHERE ConvId = ?";
    $params = [$convid];
    $types = 'i';
} else {
    // Get all documents related to the user
    $sql = "SELECT DISTINCT d.DocumentId, d.UserId, d.ConvId, d.DocumentName, d.OriginalFileName, d.FilePath, d.FileType, d.FileSize, d.UploadDate, d.UploadTime 
            FROM Documents d 
            WHERE d.UserId = ? 
            OR d.ConvId IN (SELECT ConvId FROM Conversation WHERE UserId = ?)";
    $params = [$userid, $userid];
    $types = 'ii';
}

$sql .= " ORDER BY DocumentId DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = [
        'id' => $row['DocumentId'],
        'url' => $row['FilePath'],
        'name' => $row['DocumentName'],
        'originalName' => $row['OriginalFileName'],
        'size' => $row['FileSize'],
        'type' => $row['FileType'],
        'uploadDate' => $row['UploadDate'],
        'uploadTime' => $row['UploadTime'],
        'convid' => $row['ConvId'],
        'uploadedByMe' => ($row['UserId'] == $userid) // Flag to show if user uploaded this file
    ];
}
$stmt->close();
$conn->close();
echo json_encode($files); 