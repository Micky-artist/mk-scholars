<header class="topbar" data-navbarbg="skin5">
      <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin5">
          <a class="navbar-brand" href="index">
            <b class="logo-icon ps-2">
              <!-- <img src="./assets/images/logo.png" alt="homepage" class="light-logo" style="max-width: 50px;" /> -->
              <h1>MK Scholars</h1>
            </b>
            <span class="logo-text ms-2">
             </span>
          </a>
          <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
        </div>
        <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
          <ul class="navbar-nav float-start me-auto">
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-none d-md-block">Upload New <i class="fa fa-angle-down"></i></span>
                <span class="d-block d-md-none"><i class="fa fa-plus"></i></span>
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="./upload-scholarship">Upload Scholarship</a></li>
                <li><a class="dropdown-item" href="./upload-country">Upload Country</a></li>
                <!-- <li><a class="dropdown-item" href="./upload-post">Post Project</a></li> -->
              </ul>
            </li>
          </ul>
          <ul class="navbar-nav float-end">
            <li class="nav-item dropdown">
              <a class="
                    nav-link
                    dropdown-toggle
                    text-muted
                    waves-effect waves-dark
                    pro-pic
                  " href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="./assets/images/user_icon.png" alt="user" class="rounded-circle" width="31" />
              </a>
              <ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
                <a href="./profile" class="dropdown-item" href="javascript:void(0)"><i class="mdi mdi-account me-1 ms-1"></i><?php echo $_SESSION['AdminName']; ?> </a>
               <div class="dropdown-divider"></div>
                <!-- <a class="dropdown-item" href="posts"><i class="mdi mdi-settings me-1 ms-1"></i> Pending Approvals</a> -->
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="./php/logout.php"><i class="fa fa-power-off me-1 ms-1"></i> Logout</a>
                <div class="dropdown-divider"></div>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>