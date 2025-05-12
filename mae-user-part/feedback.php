<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

// Database connection (same as members.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "access_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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

// Fetch recent feedback (latest 5)
$recent_feedback = [];
$recent_sql = "SELECT f.requester_name, s.name AS service_name, f.rating, f.feedback_text, f.created_at FROM feedback f JOIN services s ON f.service_id = s.id ORDER BY f.created_at DESC LIMIT 5";
$recent_result = $conn->query($recent_sql);
if ($recent_result && $recent_result->num_rows > 0) {
    while ($row = $recent_result->fetch_assoc()) {
        $recent_feedback[] = $row;
    }
}

// Handle feedback form submission
$feedback_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $requester_name = trim($_POST['requester_name']);
    $service_id = intval($_POST['service_id']);
    $rating = intval($_POST['rating']);
    $feedback_text = trim($_POST['feedback_text']);
    $created_at = date('Y-m-d H:i:s');

    if ($requester_name && $service_id && $rating && $feedback_text) {
        $stmt = $conn->prepare("INSERT INTO feedback (requester_name, service_id, rating, feedback_text, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('siiss', $requester_name, $service_id, $rating, $feedback_text, $created_at);
        if ($stmt->execute()) {
            $feedback_msg = '<div class="alert alert-success mt-3">Thank you for your feedback!</div>';
        } else {
            $feedback_msg = '<div class="alert alert-danger mt-3">Error submitting feedback. Please try again.</div>';
        }
        $stmt->close();
    } else {
        $feedback_msg = '<div class="alert alert-warning mt-3">Please fill in all fields.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback - ACCESS-USTP Oroquieta</title>
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
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php"><i class="fas fa-calendar-alt"></i> Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcements.php"><i class="fas fa-bullhorn"></i> Announcement</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="members.php"><i class="fas fa-users"></i> Members</a></li>
                    <li class="nav-item"><a class="nav-link  active active" href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a></li>
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
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card shadow-lg rounded-4 h-100">
          <div class="card-header bg-primary text-white fw-bold">
            <i class="fas fa-comment-dots me-2"></i>Feedback
          </div>
          <div class="card-body">
            <h4 class="mb-3">We Value Your Feedback</h4>
            <?php echo $feedback_msg; ?>
            <script>
            // Hide feedback message after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
              var alert = document.querySelector('.alert');
              if (alert) {
                setTimeout(function() {
                  alert.style.display = 'none';
                }, 5000);
              }
            });
            </script>
            <form method="post" class="needs-validation" novalidate>
              <div class="mb-3">
                <label for="requester_name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="requester_name" name="requester_name" required>
                <div class="invalid-feedback">Please enter your name.</div>
              </div>
              <div class="mb-3">
                <label for="service_id" class="form-label">Service</label>
                <select class="form-select" id="service_id" name="service_id" required>
                  <option value="">Select a service</option>
                  <?php foreach ($services as $service): ?>
                    <option value="<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a service.</div>
              </div>
              <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select class="form-select" id="rating" name="rating" required>
                  <option value="">Select rating</option>
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
                <div class="invalid-feedback">Please select a rating.</div>
              </div>
              <div class="mb-3">
                <label for="feedback_text" class="form-label">Feedback</label>
                <textarea class="form-control" id="feedback_text" name="feedback_text" rows="4" required></textarea>
                <div class="invalid-feedback">Please enter your feedback.</div>
              </div>
              <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback</button>
            </form>
            <script>
            // Bootstrap validation
            (() => {
              'use strict';
              const forms = document.querySelectorAll('.needs-validation');
              Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                  if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                  }
                  form.classList.add('was-validated');
                }, false);
              });
            })();
            </script>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card shadow-lg rounded-4 h-100">
          <div class="card-header bg-secondary text-white fw-bold">
            <i class="fas fa-clock me-2"></i>Recent Feedback
          </div>
          <div class="card-body p-0">
            <?php if (count($recent_feedback) > 0): ?>
              <ul class="list-group list-group-flush" style="max-height: 480px; overflow-y: auto;">
                <?php foreach ($recent_feedback as $fb): ?>
                  <li class="list-group-item" style="background: #f8f9fa; border-radius: 12px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(25, 118, 210, 0.06);">
                    <div class="d-flex align-items-center mb-2">
                      <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.1rem;">
                        <?php echo strtoupper(substr($fb['requester_name'], 0, 1)); ?>
                      </div>
                      <div class="flex-grow-1">
                        <strong><?php echo htmlspecialchars($fb['requester_name']); ?></strong>
                        <span class="badge bg-info text-dark ms-2" style="font-size: 0.85rem;"> <?php echo htmlspecialchars($fb['service_name']); ?> </span>
                        <span class="ms-2">
                          <?php for ($i = 0; $i < $fb['rating']; $i++) echo '<i class="fas fa-star" style="color: #ffc107;"></i>'; ?>
                        </span>
                      </div>
                    </div>
                    <div class="ps-2" style="font-size: 0.97rem; color: #333;">"<?php echo nl2br(htmlspecialchars($fb['feedback_text'])); ?>"</div>
                    <div class="text-end pe-2 mt-1"><small class="text-muted"><?php echo date('M d, Y H:i', strtotime($fb['created_at'])); ?></small></div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div class="alert alert-info mb-0">No feedback yet.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Footer -->
  <div class="footer text-center">
        &copy; 2025 BSIT2A. All rights reserved.<br>
    </div>
</body>
</html>
<?php $conn->close(); ?> 