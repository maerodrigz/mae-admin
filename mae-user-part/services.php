<?php
session_start();
// Database connection (same as members.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "access_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Handle form submission
if (isset($_POST['submit_request'])) {
    $service_name = $_POST['service_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $requester_name = $_SESSION['fullname'];
    $request_date = date('Y-m-d H:i:s');
    $status = 'Pending';
    $sql = "INSERT INTO service_requests (service_name, category, requester_name, request_date, status, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $service_name, $category, $requester_name, $request_date, $status, $description);
    $stmt->execute();
}
// Fetch services for dropdown
$services = [];
$service_sql = "SELECT id, name FROM services";
$service_result = $conn->query($service_sql);
if ($service_result && $service_result->num_rows > 0) {
    while ($row = $service_result->fetch_assoc()) {
        $services[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services - ACCESS-USTP Oroquieta</title>
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
                    <li class="nav-item"><a class="nav-link active" href="services.php"><i class="fas fa-calendar-alt"></i> Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcements.php"><i class="fas fa-bullhorn"></i> Announcement</a></li>
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
    <div class="card shadow-lg rounded-4">
      <div class="card-header bg-primary text-white fw-bold">
        <i class="fas fa-tools me-2"></i> Services Request

      </div>
      <div class="card-body">
        <h4 class="mb-3">Please Add Your Request Here</h4>
        <!-- Service Request Section -->
        <div class="row justify-content-center mb-4">
          <div class="col-lg-8">
            <div class="card border-primary shadow-sm mb-4">
              <div class="card-header bg-primary text-white">
                <i class="fas fa-plus-circle me-2"></i>Submit a New Service Request
              </div>
              <div class="card-body">
                <?php if (isset($_POST['submit_request'])): ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>Your request has been submitted!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php endif; ?>
                <form method="POST" action="">
                  <div class="mb-3">
                    <label for="service_name" class="form-label">Service Name</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-tools"></i></span>
                      <input type="text" class="form-control" id="service_name" name="service_name" placeholder="e.g. Document Processing" required>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-list"></i></span>
                      <select class="form-select" id="category" name="category" required>
                        <option value="">Select a category</option>
                        <?php foreach ($services as $service): ?>
                          <option value="<?= htmlspecialchars($service['name']) ?>"><?= htmlspecialchars($service['name']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe your request..." required></textarea>
                  </div>
                  <div class="d-grid">
                    <button type="submit" name="submit_request" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane me-2"></i>Submit Request</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- End Service Request Section -->
        <hr>
        <?php
        // Fetch current user's requests
        $requester_name = $_SESSION['fullname'];
        $sql = "SELECT * FROM service_requests WHERE requester_name = ? ORDER BY request_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $requester_name);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>
        <h5 class="mt-5">Your Service Requests</h5>
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="card shadow-sm mb-4">
              <div class="card-header bg-secondary text-white">
                <i class="fas fa-history me-2"></i>Your Service Requests
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><i class="fas fa-tools text-primary me-1"></i> <?= htmlspecialchars($row['service_name']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['category']) ?></span></td>
                        <td><i class="fas fa-calendar-alt text-secondary me-1"></i> <?= htmlspecialchars($row['request_date']) ?></td>
                        <td>
                          <?php
                            if ($row['status'] == 'Approved') echo '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Approved</span>';
                            elseif ($row['status'] == 'Declined') echo '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Declined</span>';
                            else echo '<span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Pending</span>';
                          ?>
                        </td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                      </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End User Requests Table -->
        <p class="mt-4">Stay tuned for more updates on our upcoming services and events!</p>
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