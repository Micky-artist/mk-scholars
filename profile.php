<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile & Subscriptions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <!-- Sidebar Navigation Include -->
    <?php include("./partials/dashboardNavigation.php"); ?>

    <main class="main-content p-4">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card glass-panel shadow mb-4">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Your Profile</h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Username:</strong> <span id="displayUsername">Loading...</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Email:</strong> <span id="displayEmail">Loading...</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card glass-panel shadow mb-4">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Edit Profile</h4>
                            <form id="profileForm">
                                <div class="mb-3">
                                    <label for="username" class="form-label">New Username</label>
                                    <input type="text" class="form-control" id="username" name="username">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">New Email</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                                <hr>
                                <h6 class="mt-3">Change Password</h6>
                                <div class="mb-3">
                                    <label for="currentPassword" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" name="currentPassword">
                                </div>
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" name="newPassword">
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm with Current Password to Save Changes</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                            <div id="updateMsg" class="mt-3"></div>
                        </div>
                    </div>

                    <div class="card glass-panel shadow">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Your Subscriptions</h4>
                            <div id="subscriptions">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fetch current user profile
            $.get('./php/get_user_profile.php', function(data) {
                const user = JSON.parse(data);
                $('#displayUsername').text(user.username);
                $('#displayEmail').text(user.email);
                $('#username').val(user.username);
                $('#email').val(user.email);
            });

            // Handle form submission
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.post('./php/update_user_profile.php', formData, function(response) {
                    $('#updateMsg').html('<div class="alert alert-success">' + response + '</div>');
                    setTimeout(() => location.reload(), 1500);
                }).fail(function(xhr) {
                    const error = xhr.responseText || 'Failed to update profile.';
                    $('#updateMsg').html('<div class="alert alert-danger">' + error + '</div>');
                });
            });

            // Load user subscriptions
            $.get('./php/get_user_subscriptions.php', function(data) {
                const subs = JSON.parse(data);
                if (subs.length === 0) {
                    $('#subscriptions').html('<div class="text-muted">You have no active subscriptions.</div>');
                } else {
                    let html = '<ul class="list-group">';
                    subs.forEach(sub => {
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${sub.subscriptionType}</strong><br>
                                <small>${sub.subscriptionDate} - Expires: ${sub.expirationDate}</small>
                            </div>
                            <span class="badge ${sub.SubscriptionStatus === 'active' ? 'bg-success' : 'bg-secondary'}">${sub.SubscriptionStatus}</span>
                        </li>`;
                    });
                    html += '</ul>';
                    $('#subscriptions').html(html);
                }
            });
        });
    </script>
</body>
</html>