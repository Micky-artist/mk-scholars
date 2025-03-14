<?php
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Get form data
$UserId = $_POST['UserId'];
$AdminId = $_POST['AdminId'];
$ConvId = $_POST['ConvId'];
$MessageContent = $_POST['message'];
$SentDate = date("Y-m-d");
$SentTime = date("H:i:s");
$MessageStatus = "Sent"; // Assuming the default status is "Sent"

// Insert into database
$sql = "INSERT INTO Message(UserId, senderId, AdminId, ConvId, MessageContent, SentDate, SentTime, MessageStatus) 
        VALUES ('$UserId', '$UserId', '$AdminId', '$ConvId', '$MessageContent', '$SentDate', '$SentTime', '$MessageStatus')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
// }
?>