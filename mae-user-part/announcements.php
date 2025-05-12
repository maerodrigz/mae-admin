<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = ""; // or your MySQL password
$dbname = "access_db"; // change to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements - ACCESS-USTP Oroquieta</title>
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
                    <li class="nav-item"><a class="nav-link active" href="announcements.php"><i class="fas fa-bullhorn"></i> Announcement</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="members.php"><i class="fas fa-users"></i> Members</a></li>
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
    <div class="card shadow-lg rounded-4 animate__animated animate__fadeIn">
      <div class="card-header bg-gradient-primary text-white fw-bold d-flex align-items-center" style="background: linear-gradient(90deg, #1976d2 60%, #63a4ff 100%); border-radius: 1rem 1rem 0 0; min-height: 70px;">
        <i class="fas fa-bullhorn me-3 fa-lg"></i>
        <div>
          <div style="font-size: 1.25rem; font-weight: 700; letter-spacing: 0.5px;">Announcements</div>
          <div style="font-size: 0.98rem; font-weight: 400; opacity: 0.85;">Stay updated with the latest news and notices</div>
        </div>
      </div>
      <div class="card-body" style="padding-top: 2rem;">
        <h4 class="mb-2 fw-semibold" style="color: #1976d2;">Latest Announcements</h4>
        <div class="mb-4" style="font-size: 1rem; color: #555;">All important updates and information are listed below. Please check regularly for new announcements.</div>
        <hr class="mb-4" style="border-top: 1.5px solid #e3f2fd;">
        <?php
        if ($result->num_rows > 0) {
            echo '<div class="table-responsive"><table class="table announcements-table table-bordered table-hover animate__animated animate__fadeIn">';
            echo '<thead><tr>
                    <th><i class="fas fa-heading"></i> Title</th>
                    <th><i class="fas fa-tags"></i> Category</th>
                    <th><i class="fas fa-align-left"></i> Content</th>
                    <th><i class="fas fa-calendar-day"></i> Start Date</th>
                    <th><i class="fas fa-calendar-check"></i> End Date</th>
                    <th><i class="fas fa-exclamation-circle"></i> Priority</th>
                    <th><i class="fas fa-toggle-on"></i> Status</th>
                  </tr></thead><tbody>';
            while($row = $result->fetch_assoc()) {
                echo '<tr>
                        <td>'.htmlspecialchars($row["title"]).'</td>
                        <td>'.htmlspecialchars($row["category"]).'</td>
                        <td>'.htmlspecialchars($row["content"]).'</td>
                        <td>'.$row["start_date"].'</td>
                        <td>'.$row["end_date"].'</td>
                        <td>'.$row["priority"].'</td>
                        <td>'.$row["status"].'</td>
                      </tr>';
            }
            echo '</tbody></table></div>';
        } else {
            echo '<div class="alert alert-info">No announcements yet. Stay tuned!</div>';
        }
        $conn->close();
        ?>
      </div>
    </div>
  </div>
   <!-- Footer -->
   <div class="footer text-center">
        &copy; 2025 BSIT2A. All rights reserved.<br>
    </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 
</body>
</html> 