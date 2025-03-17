<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Icyeza Interiors</title>
  <style>
    /* Global Styles */
    :root {
      --primary: #4F46E5; /* Indigo */
      --secondary: #818CF8; /* Lighter Indigo */
      --background: #F8FAFC; /* Light Gray */
      --surface: #FFFFFF; /* White */
      --border: #E2E8F0; /* Light Border */
      --text: #1E293B; /* Dark Gray */
      --success: #22C55E; /* Green */
      --error: #EF4444; /* Red */
    }

    body {
      background: var(--background);
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      color: var(--text);
    }

    /* Container */
    .profile-container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 1rem;
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 2rem;
    }

    /* Sidebar */
    .profile-sidebar {
      background: var(--surface);
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      text-align: center;
    }

    .profile-picture {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 1.5rem;
      border: 4px solid var(--primary);
    }

    .profile-name {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .profile-role {
      color: #64748B; /* Gray */
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
    }

    .profile-stats {
      display: flex;
      justify-content: space-around;
      margin-bottom: 2rem;
    }

    .stat-item {
      text-align: center;
    }

    .stat-number {
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--primary);
    }

    .stat-label {
      font-size: 0.8rem;
      color: #64748B; /* Gray */
    }

    .profile-actions {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .profile-actions button {
      padding: 0.75rem;
      border: none;
      border-radius: 8px;
      background: var(--primary);
      color: white;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .profile-actions button:hover {
      background: #4338CA; /* Darker Indigo */
      transform: translateY(-2px);
    }

    /* Main Content */
    .profile-main {
      background: var(--surface);
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      padding: 2rem;
    }

    .section-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      color: var(--primary);
    }

    .activity-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      border-radius: 8px;
      background: var(--background);
      transition: all 0.3s ease;
    }

    .activity-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .activity-details {
      flex: 1;
    }

    .activity-title {
      font-size: 1rem;
      font-weight: 500;
    }

    .activity-time {
      font-size: 0.8rem;
      color: #64748B; /* Gray */
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .profile-container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="profile-container">
    <!-- Sidebar -->
    <div class="profile-sidebar">
      <img src="https://via.placeholder.com/120" alt="Profile Picture" class="profile-picture">
      <div class="profile-name">John Doe</div>
      <div class="profile-role">Administrator</div>

      <!-- Stats -->
      <div class="profile-stats">
        <div class="stat-item">
          <div class="stat-number">1.2K</div>
          <div class="stat-label">Followers</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">356</div>
          <div class="stat-label">Following</div>
        </div>
      </div>

      <!-- Actions -->
      <div class="profile-actions">
        <button>Edit Profile</button>
        <button>Change Password</button>
        <button>Log Out</button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="profile-main">
      <!-- Recent Activity -->
      <div class="activity-section">
        <div class="section-title">Recent Activity</div>
        <div class="activity-list">
          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-user-plus"></i>
            </div>
            <div class="activity-details">
              <div class="activity-title">New User Added</div>
              <div class="activity-time">2 hours ago</div>
            </div>
          </div>
          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-edit"></i>
            </div>
            <div class="activity-details">
              <div class="activity-title">Profile Updated</div>
              <div class="activity-time">5 hours ago</div>
            </div>
          </div>
          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-comment"></i>
            </div>
            <div class="activity-details">
              <div class="activity-title">New Comment</div>
              <div class="activity-time">1 day ago</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>