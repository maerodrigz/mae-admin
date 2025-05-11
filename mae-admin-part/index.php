<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ACCESS Admin Dashboard - USTP Oroquieta</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="styles.css">
  
</head>
<body>
  <!-- Sidebar navigation -->
  <aside class="sidebar">
    <div class="logo-container">
      <img src="ACCESS.jpg" alt="ACCESS Organization Logo" class="logo">
      <h5 class="mt-3 text-white">ACCESS</h5>
      <p class="text-muted small">Admin Panel</p>
    </div>
    
    <nav class="nav flex-column mb-auto">
      <a href="index.php" class="nav-link active">
        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
      </a>
      <a href="members.php" class="nav-link">
        <i class="bi bi-people"></i> <span>Members</span>
      </a>
     <a href="services.php" class="nav-link ">
        <i class="bi bi-tools"></i> <span>Services</span>
      </a>
      
      <a href="announcements.php" class="nav-link">
        <i class="bi bi-megaphone"></i> <span>Announcements</span>
      </a>
      <a href="gallery.php" class="nav-link">
        <i class="bi bi-images"></i> <span>Gallery</span>
      </a>
      <a href="reports.php" class="nav-link">
        <i class="bi bi-file-earmark-bar-graph"></i> <span>Articles</span>
      </a>
      <a href="feedback.php" class="nav-link">
        <i class="bi bi-chat-square-text"></i> <span>Feedback</span>
      </a>
    </nav>
    
    <div class="user-container">
      <img src="ACCESS.jpg" alt="Admin User" class="user-image">
      <h6 class="mb-0 text-white">Administrator</h6>
      <small class="text-white-50">ADMIN USER</small>
      <a href="logout.php" class="btn btn-outline-light btn-sm w-100 mt-3">
        <i class="bi bi-box-arrow-right me-1"></i> Log Out
      </a>
    </div>
  </aside>

  <!-- Main content area -->
  <main class="main-content">
    <!-- Page header -->
    <header class="page-header d-flex justify-content-between align-items-center">
      <h1 class="h3 mb-0">Admin Dashboard</h1>
     
        <div class="dropdown">
          <button class="btn btn-light d-flex align-items-center shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="ACCESS.jpg" alt="Admin" class="rounded-circle me-2" width="32" height="32">
            <span></span>
            <i class="bi bi-chevron-down ms-2"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Log Out</a></li>
          </ul>
        </div>
      </div>
    </header>
    
    <!-- Dashboard content -->
    <div class="container-fluid px-0">
      <!-- Welcome banner -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="bg-primary text-white rounded-3 p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h2 class="fw-bold">Welcome back,ADMIN!</h2>
                <p class="mb-0">Here's what's happening with ACCESS organization today.</p>
              </div>
              <i class="bi bi-stars display-4"></i>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Announcements Section -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Announcements</h5>
              <a href="announcements.php" class="btn btn-primary btn-sm">
                <i class="bi bi-list-ul me-1"></i> View All
              </a>
            </div>
            <div class="card-body">
              <?php
              require_once 'config.php';
              
              // Fetch all announcements with user information
              $stmt = $pdo->prepare("SELECT a.*, u.username as posted_by_name 
                                   FROM announcements a 
                                   LEFT JOIN users u ON a.posted_by = u.id 
                                   ORDER BY a.created_at DESC");
              $stmt->execute();
              $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              if (count($announcements) > 0):
                foreach ($announcements as $announcement):
              ?>
                <div class="announcement-item mb-3 pb-3 border-bottom">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0"><?php echo htmlspecialchars($announcement['title']); ?></h6>
                        <span class="badge bg-<?php echo $announcement['category'] === 'Event' ? 'success' : 'primary'; ?>">
                          <?php echo htmlspecialchars($announcement['category']); ?>
                        </span>
                      </div>
                      <p class="text-muted small mb-2">
                        <i class="bi bi-calendar-event me-1"></i>
                        <?php echo date('M d, Y', strtotime($announcement['start_date'])); ?> - 
                        <?php echo date('M d, Y', strtotime($announcement['end_date'])); ?>
                      </p>
                      <p class="mb-2"><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                      <div class="d-flex align-items-center">
                        <span class="badge bg-<?php 
                          echo match($announcement['priority']) {
                            'High' => 'danger',
                            'Urgent' => 'warning',
                            'Normal' => 'info',
                            default => 'secondary'
                          };
                        ?> me-2">
                          <?php echo htmlspecialchars($announcement['priority']); ?>
                        </span>
                        <span class="badge bg-<?php 
                          echo match($announcement['status']) {
                            'Active' => 'success',
                            'Pending' => 'warning',
                            'Draft' => 'secondary',
                            default => 'primary'
                          };
                        ?> me-2">
                          <?php echo htmlspecialchars($announcement['status']); ?>
                        </span>
                        <small class="text-muted">
                          <i class="bi bi-person me-1"></i>
                          Posted by <?php echo htmlspecialchars($announcement['posted_by_name']); ?>
                          <i class="bi bi-clock ms-2 me-1"></i>
                          <?php echo date('M d, Y h:i A', strtotime($announcement['created_at'])); ?>
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              <?php 
                endforeach;
              else:
              ?>
                <div class="alert alert-info mb-0">
                  <i class="bi bi-info-circle me-2"></i> No announcements have been created yet.
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row g-4 mb-4">
      
</main>

<!-- Bootstrap JS Bundle (with Popper for dropdowns/tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom script (optional, for tooltips or interactivity) -->
<script>
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
  new bootstrap.Tooltip(el);
});
</script>
</body>
</html>
