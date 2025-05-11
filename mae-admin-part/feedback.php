<?php
require_once 'config/database.php';
$conn = getConnection();

// Fetch services for the filter dropdown
try {
    $stmt = $conn->query("SELECT id, name FROM services ORDER BY name");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $services = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Management - ACCESS Admin Dashboard</title>
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
            <a href="index.php" class="nav-link">
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
            <a href="feedback.php" class="nav-link active">
                <i class="bi bi-chat-square-text"></i> <span>Feedback</span>
            </a>
        </nav>
        <div class="user-container">
          <img src="ACCESS.jpg" alt="Admin User" class="user-image">
          <h6 class="mb-0 text-white">Admin User</h6>
          <small class="text-white-50">Administrator</small>
          <a href="logout.php" class="btn btn-outline-light btn-sm w-100 mt-3">
            <i class="bi bi-box-arrow-right me-1"></i> Log Out
          </a>
        </div>
    </aside>

    <!-- Main content area -->
    <main class="main-content">
        <header class="page-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Feedback Management</h1>
            <div class="dropdown">
          <button class="btn btn-light d-flex align-items-center shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="ACCESS.jpg" alt="Admin" class="rounded-circle me-2" width="32" height="32">
            <span>Administrator</span>
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

        <div class="container-fluid px-0">
            <!-- Feedback Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label for="eventFilter" class="form-label">Filter by Service</label>
                            <select class="form-select" id="eventFilter">
                                <option value="">All Services</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="dateFilter" class="form-label">Filter by Date</label>
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary mt-4" onclick="filterFeedback()">
                                <i class="bi bi-search me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            

            <!-- Feedback List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="feedbackTable">
                            <thead>
                                <tr>
                                    <th>Requesters</th>
                                    <th>Service</th>
                                    <th>Rating</th>
                                    <th>Feedback</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Feedback data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Feedback Detail Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Feedback Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6>Member</h6>
                        <p id="feedbackMember"></p>
                    </div>
                    <div class="mb-3">
                        <h6>Service</h6>
                        <p id="feedbackEvent"></p>
                    </div>
                    <div class="mb-3">
                        <h6>Rating</h6>
                        <div id="feedbackRating" class="text-warning">
                            <!-- Stars will be displayed here -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Feedback</h6>
                        <p id="feedbackContent"></p>
                    </div>
                    <div class="mb-3">
                        <h6>Date</h6>
                        <p id="feedbackDate"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="deleteFeedback()">Delete Feedback</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/feedback.js"></script>
</body>
</html> 