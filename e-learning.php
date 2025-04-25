<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>E-Learning Platform Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <link rel="shortcut icon" href="https://mkscholars.com/images/logo/logoRound.png" type="image/x-icon">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background: #f4f4f4;
      color: #333;
    }
    .card {
      border: none;
      border-radius: 0.75rem;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar Navigation -->
      <div class="col-md-3 col-lg-2 bg-white border-end vh-100 overflow-auto p-0">
        <?php include './partials/dashboardNavigation.php'; ?>
      </div>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 p-4 vh-100 overflow-auto">
        <h2 class="mb-4">Courses</h2>
        <div class="row g-4">
          <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
              <img src="https://mkscholars.com/images/courses/ucat.jpg" class="card-img-top" alt="UCAT">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">UCAT</h5>
                <p class="card-text flex-grow-1">Comprehensive online coaching for students preparing for the University Clinical Aptitude Test (UCAT). Includes expert guidance, practice tests, and strategies to improve scores. Morning and evening classes are available.</p>
                <a href="ucat-course" class="btn btn-primary mt-3">View Course</a>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
              <img src="https://via.placeholder.com/400x200" class="card-img-top" alt="morocco-admissions">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"></h5>
                <p class="card-text flex-grow-1">Welcome to the Morocco Admissions Preparation Course. Here you’ll find targeted interview practice, hospitality & tourism knowledge checks, and logical reasoning quizzes designed to help you excel in your application and assessments.</p>
                <a href="morocco-admissions" class="btn btn-primary mt-3">View Course</a>
              </div>
            </div>
          </div>
          <!-- <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
              <img src="https://via.placeholder.com/400x200" class="card-img-top" alt="JavaScript Course">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">JavaScript Essentials</h5>
                <p class="card-text flex-grow-1">Dive into JavaScript fundamentals and add interactivity to your websites.</p>
                <a href="#" class="btn btn-primary mt-3">View Course</a>
              </div>
            </div>
          </div> -->
          <!-- <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
              <img src="https://via.placeholder.com/400x200" class="card-img-top" alt="Responsive Design Course">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Responsive Web Design</h5>
                <p class="card-text flex-grow-1">Ensure your websites look great on any device with responsive techniques.</p>
                <a href="#" class="btn btn-primary mt-3">View Course</a>
              </div>
            </div>
          </div> -->
        </div>
      </main>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
