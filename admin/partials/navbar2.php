<aside class="left-sidebar" data-sidebarbg="skin5">
  <!-- Sidebar scroll-->
  <div class="scroll-sidebar">
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav">
      <ul id="sidebarnav" class="pt-4">

        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="index" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="countries" aria-expanded="false"><i class="mdi mdi-earth"></i><span class="hide-menu">Countries</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="upload-scholarship" aria-expanded="false"><i class="mdi mdi-file"></i><span class="hide-menu">Upload Scholarship</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="scholarships" aria-expanded="false"><i class="mdi mdi-view-list"></i><span class="hide-menu">Applications</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
<li class="sidebar-item">
              <a class="sidebar-link waves-effect waves-dark sidebar-link" href="application-requests" aria-expanded="false"><i class="mdi mdi-account-check"></i><span class="hide-menu">Application Support Requests</span></a>
            </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="course-applications" aria-expanded="false"><i class="mdi mdi-account-settings"></i><span class="hide-menu">Course Applications</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="chat-ground" aria-expanded="false"><i class="mdi mdi-message"></i><span class="hide-menu">Chat Ground</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="users" aria-expanded="false"><i class="mdi mdi-account"></i><span class="hide-menu">Users</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="tags" aria-expanded="false"><i class="mdi mdi-tag"></i><span class="hide-menu">Tags</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="upload-youtube-video" aria-expanded="false"><i class="mdi mdi-link"></i><span class="hide-menu">Upload Youtube Video</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="user-logs" aria-expanded="false"><i class="mdi mdi-account-switch"></i><span class="hide-menu">User Logs</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="register" aria-expanded="false"><i class="mdi mdi-account-plus"></i><span class="hide-menu">Register User</span></a>
          </li>
        <?php } ?>
        <?php
        if (hasPermission('ManageCountries')) {
        ?>
          <li class="sidebar-item">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="manage-access" aria-expanded="false"><i class="mdi mdi-account-key"></i><span class="hide-menu">Manage Access</span></a>
          </li>
        <?php } ?>
      </ul>
    </nav>
    <!-- End Sidebar navigation -->
  </div>
  <!-- End Sidebar scroll-->
</aside>