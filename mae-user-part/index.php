<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCESS USTP Council - User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .announcement-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .announcement-card:hover {
            transform: translateY(-5px);
        }
        .event-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .urgency-badge {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
        }
        .announcement-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .event-time {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .modal-content {
            border-radius: 15px;
        }
        .modal-header {
            border-radius: 15px 15px 0 0;
        }
        
    </style>
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
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php"><i class="fas fa-calendar-alt"></i> Services</a></li>
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

    <div class="container">
        <div class="welcome-banner mb-4 position-relative" style="background: url('aces.jpg') center/cover no-repeat; border-radius: 1.5rem; box-shadow: 0 4px 24px rgba(25, 118, 210, 0.15); overflow: hidden; min-height: 220px;">
            <div style="position: absolute; inset: 0; background: rgba(25, 118, 210, 0.65); z-index: 1;"></div>
            <div class="position-relative text-center text-white py-5" style="z-index: 2;">
                <i class="fas fa-laptop-code mb-3" style="font-size: 2.5rem;"></i>
                <h2 class="fw-bold mb-2">Welcome to ACCESS-USTP Oroquieta</h2>
                <p class="mb-0">Active Certified Computer-Enhance Students Society.</p>
            </div>
        </div>
        <!-- About Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="fas fa-info-circle me-2"></i>ABOUT ACCESS-USTP OROQUIETA
            </div>
            <div class="card-body">
                <p>
                    The <strong>Active Certified Computer-Enhance Students Society (ACCESS-USTP Oroquieta)</strong> is a recognized student organization at the University of Science and Technology of Southern Philippines (USTP) Oroquieta, dedicated to promoting the values and mission of ACCESS within the university community.
                </p>
                <p>
                    Our organization is committed to fostering a culture of excellence, leadership, and service in the field of computer studies. We offer services that focus on photograph editing, including workshops, tutorials, and hands-on training in digital photo enhancement and graphic design. We also organize seminars, competitions, and various outreach programs. Through these activities, we aim to empower students and staff to become active contributors to the advancement of technology and the well-being of the community.
                </p>
                <p>
                    Guided by the core values of ACCESS—Leadership, Innovation, Service, and Unity—we strive to make a positive impact both on campus and beyond.
                </p>
                <p class="mb-0">
                    <strong>Join us</strong> in making a difference!
                </p>
            </div>
        </div>
       
       
    <!-- Footer -->
    <div class="footer text-center">
        &copy; 2025 BSIT2A. All rights reserved.<br>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
