<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Chat</title>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Chat container */
        .chat-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            height: 500px;
            overflow-y: auto;
        }

        /* Chat bubbles */
        .chat-bubble {
            max-width: 70%;
            padding: 8px 12px;
            border-radius: 10px;
            margin-bottom: 10px;
            position: relative;
            font-size: 0.9em;
        }

        .chat-bubble.sent {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }

        .chat-bubble.received {
            background-color: #e1e1e1;
            color: black;
            margin-right: auto;
        }

        .chat-bubble .time {
            display: block;
            font-size: 0.8em;
            text-align: right;
            margin-top: 5px;
            color: rgba(255, 255, 255, 0.7);
        }

        .chat-bubble.received .time {
            color: rgba(0, 0, 0, 0.7);
        }

        /* Date separator */
        .date-separator {
            font-size: 0.8em;
            color: #777;
            text-align: center;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <!-- Chat Container -->
    <div class="chat-container" id="chatContainer">
        <!-- Messages will be dynamically inserted here -->
    </div>

    <script>
        // Function to fetch messages
        function fetchMessages() {
            $.ajax({
                url: './fetch_messages.php',
                type: 'GET',
                data: {
                    convoId: <?php echo $convoId; ?>, // Pass the conversation ID
                    userId: <?php echo $UserId; ?> // Pass the user ID
                },
                success: function(response) {
                    // Parse the JSON response
                    const messages = JSON.parse(response);
                    let chatContainer = $('#chatContainer');
                    chatContainer.empty(); // Clear the chat container

                    let currentDate = null;

                    // Loop through messages and display them
                    messages.forEach(message => {
                        const messageDate = new Date(message.SentDate).toLocaleDateString();
                        const messageTime = new Date(`1970-01-01T${message.SentTime}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                        // Display date separator if the date changes
                        if (currentDate !== messageDate) {
                            currentDate = messageDate;
                            chatContainer.append(`<div class="date-separator">${currentDate}</div>`);
                        }

                        // Determine if the message is sent or received
                        if (message.UserId == <?php echo $UserId; ?>) {
                            // Sent message
                            chatContainer.append(`
                                <div class="chat-bubble sent">
                                    <p>${message['Message Content']}</p>
                                    <span class="time">${messageTime}</span>
                                </div>
                            `);
                        } else {
                            // Received message
                            chatContainer.append(`
                                <div class="chat-bubble received">
                                    <p>${message['Message Content']}</p>
                                    <span class="time">${messageTime}</span>
                                </div>
                            `);
                        }
                    });

                    // Scroll to the bottom of the chat container
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching messages:', error);
                }
            });
        }

        // Fetch messages every 2 seconds
        setInterval(fetchMessages, 2000);

        // Initial fetch
        fetchMessages();
    </script>
</body>
</html>