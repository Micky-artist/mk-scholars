<aside class="left-sidebar" data-sidebarbg="skin5">
  <!-- Sidebar scroll-->
  <div class="scroll-sidebar">
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav">
      <ul id="sidebarnav" class="pt-4">

        <?php
        // Get current page name for active state
        $currentPage = basename($_SERVER['PHP_SELF'], '.php');
        $currentDir = basename(dirname($_SERVER['PHP_SELF']));
        
        // Debug information (remove in production)
        // echo "<!-- Debug: Current Page: $currentPage, Current Dir: $currentDir -->";
        
        // Function to check if link is active
        function isActive($pageName, $currentPage, $currentDir = '') {
            if ($currentDir && $currentDir !== 'admin') {
                return false;
            }
            return $pageName === $currentPage;
        }
        ?>

        <!-- Dashboard -->
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('index', $currentPage) ? 'active' : ''; ?>" href="index" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a>
        </li>

        <!-- Communication Section -->
        <li class="nav-small-cap">
          <i class="mdi mdi-dots-horizontal"></i>
          <span class="hide-menu">Communication</span>
        </li>

        <?php if (hasPermission('ChatGround')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('chat-ground', $currentPage) ? 'active' : ''; ?>" href="chat-ground" aria-expanded="false"><i class="mdi mdi-message-text"></i><span class="hide-menu">Chat Ground</span></a>
        </li>
        <?php endif; ?>

        <!-- Main Content Section -->
        <li class="nav-small-cap">
          <i class="mdi mdi-dots-horizontal"></i>
          <span class="hide-menu">Main Content</span>
        </li>

        <?php if (hasPermission('PublishApplication')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('upload-scholarship', $currentPage) ? 'active' : ''; ?>" href="upload-scholarship" aria-expanded="false"><i class="bi bi-upload"></i><span class="hide-menu">Upload Scholarship</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('ManageYoutubeVideo')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('upload-youtube-video', $currentPage) ? 'active' : ''; ?>" href="upload-youtube-video" aria-expanded="false"><i class="mdi mdi-youtube"></i><span class="hide-menu">Upload Video</span></a>
        </li>
        <?php endif; ?>

        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('course-management', $currentPage) ? 'active' : ''; ?>" href="course-management" aria-expanded="false"><i class="mdi mdi-book-open"></i><span class="hide-menu">Course Management</span></a>
        </li>

        <!-- Applications Section -->
        <li class="nav-small-cap">
          <i class="mdi mdi-dots-horizontal"></i>
          <span class="hide-menu">Applications</span>
        </li>

        <?php if (hasPermission('ViewApplications')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('applications', $currentPage) ? 'active' : ''; ?>" href="applications" aria-expanded="false"><i class="mdi mdi-view-list"></i><span class="hide-menu">All Applications</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('ApplicationSupportRequest')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('application-requests', $currentPage) ? 'active' : ''; ?>" href="application-requests" aria-expanded="false"><i class="mdi mdi-account-check"></i><span class="hide-menu">Support Requests</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('CourseApplication')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('course-applications', $currentPage) ? 'active' : ''; ?>" href="course-applications" aria-expanded="false"><i class="mdi mdi-school"></i><span class="hide-menu">Course Applications</span></a>
        </li>
        <?php endif; ?>

        <!-- User Management Section -->
        <li class="nav-small-cap">
          <i class="mdi mdi-dots-horizontal"></i>
          <span class="hide-menu">User Management</span>
        </li>

        <?php if (hasPermission('ViewUsers')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('users', $currentPage) ? 'active' : ''; ?>" href="users" aria-expanded="false"><i class="mdi mdi-account-group"></i><span class="hide-menu">All Users</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('AddAdmin')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('register', $currentPage) ? 'active' : ''; ?>" href="register" aria-expanded="false"><i class="mdi mdi-account-plus"></i><span class="hide-menu">Register User</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('ManageUserLogs')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('user-logs', $currentPage) ? 'active' : ''; ?>" href="user-logs" aria-expanded="false"><i class="mdi mdi-account-clock"></i><span class="hide-menu">User Logs</span></a>
        </li>
        <?php endif; ?>

        <!-- System Management Section -->
        <li class="nav-small-cap">
          <i class="mdi mdi-dots-horizontal"></i>
          <span class="hide-menu">System Management</span>
        </li>

        <?php if (hasPermission('ManageCountries')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('countries', $currentPage) ? 'active' : ''; ?>" href="countries" aria-expanded="false"><i class="mdi mdi-earth"></i><span class="hide-menu">Countries</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('ViewTags')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('tags', $currentPage) ? 'active' : ''; ?>" href="tags" aria-expanded="false"><i class="mdi mdi-tag-multiple"></i><span class="hide-menu">Tags</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('ManageRights')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('manage-access', $currentPage) ? 'active' : ''; ?>" href="manage-access" aria-expanded="false"><i class="mdi mdi-account-key"></i><span class="hide-menu">Manage Access</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('ManageRights')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('subscriptions', $currentPage) ? 'active' : ''; ?>" href="subscriptions" aria-expanded="false"><i class="mdi mdi-credit-card"></i><span class="hide-menu">Subscriptions</span></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('ManageRights')): ?>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('coupons', $currentPage) ? 'active' : ''; ?>" href="coupons" aria-expanded="false"><i class="mdi mdi-ticket-percent"></i><span class="hide-menu">Coupons</span></a>
        </li>
        <?php endif; ?>

        <!-- Account Section -->
        <li class="nav-small-cap">
          <i class="mdi mdi-dots-horizontal"></i>
          <span class="hide-menu">Account</span>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link <?php echo isActive('admin-profile', $currentPage) ? 'active' : ''; ?>" href="admin-profile" aria-expanded="false"><i class="mdi mdi-account-circle"></i><span class="hide-menu">Profile</span></a>
        </li>

      </ul>
    </nav>
    <!-- End Sidebar navigation -->
  </div>
  <!-- End Sidebar scroll-->
</aside>