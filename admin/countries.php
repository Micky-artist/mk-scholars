<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

if (!hasPermission('ManageCountries')) {
  header("Location: ./index");
  exit;
}

// Handle Edit Country
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editCountry'])) {
    // Validate session exists to prevent unauthorized access
    if (!isset($_SESSION['adminId']) || empty($_SESSION['adminId'])) {
        echo "<script>alert('Unauthorized access.'); window.location.href = 'login.php';</script>";
        exit;
    }
    
    $adminId = (int)$_SESSION['adminId']; // Cast to integer for safety
    
    // Validate and sanitize countryId (use prepared statements)
    if (!isset($_POST['countryId']) || !is_numeric($_POST['countryId'])) {
        echo "<script>alert('Invalid country ID.'); window.location.href = window.location.href;</script>";
        exit;
    }
    $countryId = (int)$_POST['countryId']; // Cast to integer
    
    // Validate new country name
    if (!isset($_POST['newName']) || empty(trim($_POST['newName']))) {
        echo "<script>alert('Country name cannot be empty.'); window.location.href = window.location.href;</script>";
        exit;
    }
    
    // Use prepared statements instead of mysqli_real_escape_string
    // Fetch the current country name
    $currentNameStmt = $conn->prepare("SELECT CountryName FROM countries WHERE countryId = ?");
    $currentNameStmt->bind_param("i", $countryId);
    $currentNameStmt->execute();
    $currentNameResult = $currentNameStmt->get_result();
    
    if ($currentNameResult->num_rows === 0) {
        echo "<script>alert('Country not found.'); window.location.href = window.location.href;</script>";
        exit;
    }
    
    $currentNameData = $currentNameResult->fetch_assoc();
    $currentName = $currentNameData['CountryName'];
    $currentNameStmt->close();
    
    // Update the country name using prepared statement
    $updateStmt = $conn->prepare("UPDATE countries SET CountryName = ? WHERE countryId = ?");
    $updateStmt->bind_param("si", $_POST['newName'], $countryId);
    $updateResult = $updateStmt->execute();
    
    if ($updateResult) {
        // Log the change using prepared statement
        $logMessage = "Country name changed from '" . $currentName . "' to '" . $_POST['newName'] . "' by Admin ID: " . $adminId;
        $logTime = date("H:i:s");
        $logDate = date("Y-m-d");
        
        $logStmt = $conn->prepare("INSERT INTO Logs (userId, logMessage, logDate, logTime, logStatus) VALUES (?, ?, ?, ?, 0)");
        $logStmt->bind_param("isss", $adminId, $logMessage, $logDate, $logTime);
        $logStmt->execute();
        $logStmt->close();
        
        echo "<script>alert('Country updated successfully!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Failed to update country: " . htmlspecialchars($conn->error) . "');</script>";
    }
    
    $updateStmt->close();
}

?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include("./partials/head.php"); ?>

<body>
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
    data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <?php include("./partials/header.php"); ?>
    <?php include("./partials/navbar.php"); ?>
    <div class="page-wrapper">
      <div class="page-breadcrumb">
        <div class="row">
          <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">Countries</h4>
            <div class="ms-auto text-end">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="./index">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Countries</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Countries</h4>
              </div>
              <div class="card scrollable">
                <?php
                $selectCountries = mysqli_query($conn, "SELECT * FROM countries WHERE CountryStatus = 1 ORDER BY CountryName DESC");
                if (mysqli_num_rows($selectCountries) > 0) {
                  while ($getCountries = mysqli_fetch_assoc($selectCountries)) {
                ?>
                    <div class="d-flex align-items-center mb-2" id="country-<?= $getCountries['countryId'] ?>">
                      <span class="btn btn-primary me-2" style="margin: 5px 20px;">
                        <?= $getCountries['CountryName'] ?>
                      </span>
                      <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $getCountries['countryId'] ?>" data-name="<?= $getCountries['CountryName'] ?>">
                        <i class="fas fa-edit"></i> Edit
                      </button>
                    </div>
                <?php
                  }
                } else {
                  echo '<div class="alert alert-info">No countries found.</div>';
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include("./partials/footer.php"); ?>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Country</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm" method="POST">
            <input type="hidden" name="countryId" id="editCountryId">
            <div class="mb-3">
              <label for="newName" class="form-label">Country Name</label>
              <input type="text" class="form-control" id="newName" name="newName" required>
            </div>
            <button type="submit" name="editCountry" class="btn btn-primary">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
  <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
  <script src="./dist/js/waves.js"></script>
  <script src="./dist/js/sidebarmenu.js"></script>
  <script src="./dist/js/custom.min.js"></script>
  <!-- Font Awesome -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

  <script>
    // Handle Edit Button Click
    document.querySelectorAll('.edit-btn').forEach(button => {
      button.addEventListener('click', function () {
        const countryId = this.getAttribute('data-id');
        const countryName = this.getAttribute('data-name');

        document.getElementById('editCountryId').value = countryId;
        document.getElementById('newName').value = countryName;

        // Show the modal
        new bootstrap.Modal(document.getElementById('editModal')).show();
      });
    });
  </script>
</body>
</html>