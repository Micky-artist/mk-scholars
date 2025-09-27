<?php
include("./php/accessRestriction.php");
?>
<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Favicon icon -->
    <!-- <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="./assets/images/favicon.png"
    /> -->
    <!-- Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="shortcut icon" href="./assets/images/logoRound.png" type="image/x-icon">
    <link href="./assets/libs/flot/css/float-chart.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="./dist/css/style.min.css" rel="stylesheet" />
    <style>
      /* Navigation Group Styling */
      .nav-small-cap {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        padding: 1rem 1.5rem 0.5rem 1.5rem;
        margin-top: 1rem;
        border-top: 1px solid #e9ecef;
        letter-spacing: 0.5px;
      }

      .nav-small-cap:first-child {
        border-top: none;
        margin-top: 0;
        padding-top: 0.5rem;
      }

      .nav-small-cap i {
        margin-right: 0.5rem;
        font-size: 0.875rem;
      }

      /* Enhanced sidebar item styling */
      .sidebar-item {
        margin-bottom: 0.25rem;
      }

      .sidebar-link {
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        margin: 0 0.5rem;
        transition: all 0.3s ease;
        position: relative;
      }

      .sidebar-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
      }

      .sidebar-link i {
        margin-right: 0.75rem;
        width: 20px;
        text-align: center;
        font-size: 1.1rem;
      }

      .sidebar-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
      }

      /* Group spacing */
      .nav-small-cap + .sidebar-item {
        margin-top: 0.5rem;
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
        .nav-small-cap {
          padding: 0.75rem 1rem 0.25rem 1rem;
          font-size: 0.7rem;
        }
        
        .sidebar-link {
          padding: 0.5rem 1rem;
          margin: 0 0.25rem;
        }
      }
    </style>
    <title>MK Scholars</title>
  </head>

