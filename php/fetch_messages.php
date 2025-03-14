<?php

include("../dbconnection/connection.php");

// Get the conversation ID from the AJAX request
if (isset($_GET['convoId'])) {
    $convoId = intval($_GET['convoId']); // Sanitize input

    // Fetch messages for the current conversation
    $selectMessages = mysqli_query($conn, "SELECT * FROM Message WHERE ConvId = $convoId ORDER BY SentDate, SentTime");

    if ($selectMessages && $selectMessages->num_rows > 0) {
        $currentDate = null; // Track the current date for date separators

        while ($messages = mysqli_fetch_assoc($selectMessages)) {
            $messageDate = date("Y-m-d", strtotime($messages['SentDate']));
            $messageTime = date("H:i", strtotime($messages['SentTime']));

            // Display the date separator if the date changes
            if ($currentDate !== $messageDate) {
                $currentDate = $messageDate;
                echo '<div class="date-separator text-center my-3">' . date("F j, Y", strtotime($currentDate)) . '</div>';
            }

            // Check if the sender is the user or admin
            if ($messages['UserId'] == 1) { // Replace 1 with the logged-in user's ID
                // User's message (sent)
                echo '<div class="chat-bubble sent mb-3">';
                echo '<p class="message-content">' . htmlspecialchars($messages['MessageContent']) . '</p>';
                echo '<span class="time">' . $messageTime . '</span>';
                echo '</div>';
            } else {
                // Admin's message (received)
                echo '<div class="chat-bubble received mb-3">';
                echo '<p class="message-content">' . htmlspecialchars($messages['MessageContent']) . '</p>';
                echo '<span class="time">' . $messageTime . '</span>';
                echo '</div>';
            }
        }
    } else {
        // No messages found
        echo '<div class="text-center">No messages found.</div>';
    }
} else {
    // No conversation ID provided
    echo '<div class="text-center">Invalid request.</div>';
}

// Close the database connection
$conn->close();
?>