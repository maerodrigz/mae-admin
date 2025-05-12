<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

// Database connection (update credentials as needed)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "access_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Members - ACCESS-USTP Oroquieta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Top Navigation (always visible, replaces sidebar) -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-3">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-laptop-code me-2"></i>ACCESS <span class="d-none d-md-inline ms-2">USTP</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link " href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php"><i class="fas fa-calendar-alt"></i> Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcements.php"><i class="fas fa-bullhorn"></i> Announcement</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
                    <li class="nav-item"><a class="nav-link active" href="members.php"><i class="fas fa-users"></i> Members</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i> Profile</a></li>
                    <li class="nav-item"><a class="nav-link " href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
     <!-- Profile Modal -->
     <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="profileModalLabel"><i class="fas fa-user-circle me-2"></i>User Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                      
                        <h4 class="mb-1"><?php echo htmlspecialchars($_SESSION["fullname"]); ?></h4>
                        <p class="text-muted mb-0">Student ID: <?php echo htmlspecialchars($_SESSION["student_id"]); ?></p>
                    </div>
                    <div class="profile-info">
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Full Name:</div>
                            <div class="col-8"><?php echo htmlspecialchars($_SESSION["fullname"]); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Student ID:</div>
                            <div class="col-8"><?php echo htmlspecialchars($_SESSION["student_id"]); ?></div>
                        </div>
                        <div class="row">
                            <div class="col-4 fw-bold">Member Since:</div>
                            <div class="col-8"><?php echo date('F Y'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
  <div class="container py-5">
    <div class="card shadow-lg rounded-4">
      <div class="card-header bg-primary text-white fw-bold">
        <i class="fas fa-users me-2"></i>Members
      </div>
      <div class="card-body">
        <h4 class="mb-3">Our Members</h4>
        <?php
        $sql = "SELECT * FROM members";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-borderless align-middle">';
            echo '<thead class="table-light">';
            echo '<tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Member Type</th>
                    <th>Status</th>
                  </tr>';
            echo '</thead><tbody>';
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                // Name, profile image, and email
                echo '<td class="d-flex align-items-center">';
                echo '<img src="http://localhost/mae-admin-part/' . htmlspecialchars($row["profile_image"]) . '" alt="Profile" width="48" height="48" class="rounded-circle me-3 profile-img-thumb" style="cursor:pointer" data-img="http://localhost/mae-admin/mae-admin-part/' . htmlspecialchars($row["profile_image"]) . '" data-bs-toggle="modal" data-bs-target="#profileImageModal">';
                echo '<div>';
                echo '<div class="fw-bold">' . htmlspecialchars($row["name"]) . '</div>';
                echo '<div class="text-muted small">' . htmlspecialchars($row["email"]) . '</div>';
                echo '</div>';
                echo '</td>';
                // Department
                echo '<td>' . htmlspecialchars($row["department"]) . '</td>';
                // Member Type
                echo '<td>' . htmlspecialchars($row["member_type"]) . '</td>';
                // Status badge
                $status = htmlspecialchars($row["status"]);
                $badgeClass = ($status === 'Active') ? 'bg-success' : 'bg-secondary';
                echo '<td><span class="badge ' . $badgeClass . '">' . $status . '</span></td>';
                echo '</tr>';
            }
            echo '</tbody></table></div>';
        } else {
            echo '<div class="alert alert-warning">No members found.</div>';
        }
        ?>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Profile Image Modal -->
<div class="modal fade" id="profileImageModal" tabindex="-1" aria-labelledby="profileImageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <img id="modalProfileImage" src="" alt="Profile" class="img-fluid rounded-4">
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalImage = document.getElementById('modalProfileImage');
  document.querySelectorAll('.profile-img-thumb').forEach(function(img) {
    img.addEventListener('click', function() {
      modalImage.src = this.getAttribute('data-img');
    });
  });
});
</script>
<!-- Footer -->
<div class="footer text-center">
        &copy; 2025 BSIT2A. All rights reserved.<br>
    </div>    
</body>
</html>
<?php $conn->close(); ?> 