<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $title = $_POST['title'];
                $category = $_POST['category'];
                $content = $_POST['content'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $priority = $_POST['priority'];
                $status = $_POST['status'];
                
                try {
                    $pdo->beginTransaction();
                    
                    // Insert announcement
                    $stmt = $pdo->prepare("INSERT INTO announcements (title, category, content, start_date, end_date, priority, status, posted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $category, $content, $start_date, $end_date, $priority, $status, $_SESSION['user_id']]);
                    $announcement_id = $pdo->lastInsertId();
                    
                    // Handle file uploads
                    if (!empty($_FILES['attachments']['name'][0])) {
                        $upload_dir = 'uploads/announcements/';
                        
                        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                            $file_name = $_FILES['attachments']['name'][$key];
                            $file_type = $_FILES['attachments']['type'][$key];
                            $file_size = $_FILES['attachments']['size'][$key];
                            
                            // Generate unique filename
                            $unique_filename = uniqid() . '_' . $file_name;
                            $file_path = $upload_dir . $unique_filename;
                            
                            if (move_uploaded_file($tmp_name, $file_path)) {
                                $stmt = $pdo->prepare("INSERT INTO announcement_attachments (announcement_id, file_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?)");
                                $stmt->execute([$announcement_id, $file_name, $file_path, $file_type, $file_size]);
                            }
                        }
                    }
                    
                    $pdo->commit();
                    header("Location: announcements.php?success=1");
                    exit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    header("Location: announcements.php?error=" . urlencode($e->getMessage()));
                    exit();
                }
                break;
                
            case 'edit':
                if (isset($_POST['id'])) {
                    $id = $_POST['id'];
                    $title = $_POST['title'];
                    $category = $_POST['category'];
                    $content = $_POST['content'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];
                    $priority = $_POST['priority'];
                    $status = $_POST['status'];
                    
                    try {
                        $pdo->beginTransaction();
                        
                        // Update announcement
                        $stmt = $pdo->prepare("UPDATE announcements SET title = ?, category = ?, content = ?, start_date = ?, end_date = ?, priority = ?, status = ? WHERE id = ?");
                        $stmt->execute([$title, $category, $content, $start_date, $end_date, $priority, $status, $id]);
                        
                        // Handle new file uploads
                        if (!empty($_FILES['attachments']['name'][0])) {
                            $upload_dir = 'uploads/announcements/';
                            
                            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                                $file_name = $_FILES['attachments']['name'][$key];
                                $file_type = $_FILES['attachments']['type'][$key];
                                $file_size = $_FILES['attachments']['size'][$key];
                                
                                // Generate unique filename
                                $unique_filename = uniqid() . '_' . $file_name;
                                $file_path = $upload_dir . $unique_filename;
                                
                                if (move_uploaded_file($tmp_name, $file_path)) {
                                    $stmt = $pdo->prepare("INSERT INTO announcement_attachments (announcement_id, file_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?)");
                                    $stmt->execute([$id, $file_name, $file_path, $file_type, $file_size]);
                                }
                            }
                        }
                        
                        $pdo->commit();
                        header("Location: announcements.php?success=3");
                        exit();
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        header("Location: announcements.php?error=" . urlencode($e->getMessage()));
                        exit();
                    }
                }
                break;
                
            case 'delete':
                if (isset($_POST['id'])) {
                    try {
                        $pdo->beginTransaction();
                        
                        // Delete attachments first (files will be deleted by ON DELETE CASCADE)
                        $stmt = $pdo->prepare("DELETE FROM announcement_attachments WHERE announcement_id = ?");
                        $stmt->execute([$_POST['id']]);
                        
                        // Delete announcement
                        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        
                        $pdo->commit();
                        header("Location: announcements.php?success=2");
                        exit();
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        header("Location: announcements.php?error=" . urlencode($e->getMessage()));
                        exit();
                    }
                }
                break;
                
            case 'delete_attachment':
                if (isset($_POST['id'])) {
                    try {
                        // Get file path before deleting
                        $stmt = $pdo->prepare("SELECT file_path FROM announcement_attachments WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        $file_path = $stmt->fetchColumn();
                        
                        // Delete from database
                        $stmt = $pdo->prepare("DELETE FROM announcement_attachments WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        
                        // Delete file
                        if ($file_path && file_exists($file_path)) {
                            unlink($file_path);
                        }
                        
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true]);
                        exit();
                    } catch (Exception $e) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                        exit();
                    }
                }
                break;
        }
    }
}

// Get announcement details for editing
if (isset($_GET['action']) && $_GET['action'] === 'get_announcement' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT a.*, GROUP_CONCAT(at.id, ':', at.file_name, ':', at.file_path) as attachments 
                          FROM announcements a 
                          LEFT JOIN announcement_attachments at ON a.id = at.announcement_id 
                          WHERE a.id = ? 
                          GROUP BY a.id");
    $stmt->execute([$_GET['id']]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($announcement) {
        header('Content-Type: application/json');
        echo json_encode($announcement);
        exit();
    }
    http_response_code(404);
    exit();
}

// Get search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';

// Build query
$query = "SELECT a.*, u.username as posted_by_name 
          FROM announcements a 
          LEFT JOIN users u ON a.posted_by = u.id 
          WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (a.title LIKE ? OR a.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category && $category !== 'All Categories') {
    $query .= " AND a.category = ?";
    $params[] = $category;
}

if ($status && $status !== 'All Status') {
    $query .= " AND a.status = ?";
    $params[] = $status;
}

if ($month) {
    $query .= " AND DATE_FORMAT(a.start_date, '%Y-%m') = ?";
    $params[] = $month;
}

$query .= " ORDER BY a.start_date DESC";

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Announcements - ACCESS Admin Dashboard</title>
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
     
      <a href="announcements.php" class="nav-link active">
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
      <h6 class="mb-0 text-white">Admin User</h6>
      <small class="text-white-50">Administrator</small>
      <a href="logout.php" class="btn btn-outline-light btn-sm w-100 mt-3">
        <i class="bi bi-box-arrow-right me-1"></i> Log Out
      </a>
    </div>
  </aside>

  <!-- Main content area -->
  <main class="main-content">
    <!-- Page header -->
    <header class="page-header d-flex justify-content-between align-items-center">
      <h1 class="h3 mb-0">Announcements</h1>
      
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
    
    <!-- Announcements content -->
    <div class="container-fluid px-0">
      <!-- Actions bar -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Announcements List</h5>
              <div class="d-flex">
                <form class="input-group me-3" style="max-width: 300px;" method="GET">
                  <input type="text" class="form-control" name="search" placeholder="Search announcements..." value="<?php echo htmlspecialchars($search); ?>">
                  <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                  </button>
                </form>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                  <i class="bi bi-plus-circle me-1"></i> Create Announcement
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Success Messages -->
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php if ($_GET['success'] == 1): ?>
            Announcement created successfully!
          <?php elseif ($_GET['success'] == 2): ?>
            Announcement deleted successfully!
          <?php elseif ($_GET['success'] == 3): ?>
            Announcement updated successfully!
          <?php endif; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      
      <!-- Filters and stats -->
      <div class="row mb-4">
        <div class="col-lg-8">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Announcement Filters</h5>
              <form method="GET" class="row g-3">
                <div class="col-md-4">
                  <select class="form-select" name="category">
                    <option value="">All Categories</option>
                    <option value="Event" <?php echo $category === 'Event' ? 'selected' : ''; ?>>Event</option>
                    <option value="Academic" <?php echo $category === 'Academic' ? 'selected' : ''; ?>>Academic</option>
                    <option value="Club Notice" <?php echo $category === 'Club Notice' ? 'selected' : ''; ?>>Club Notice</option>
                    <option value="Competition" <?php echo $category === 'Competition' ? 'selected' : ''; ?>>Competition</option>
                    <option value="Other" <?php echo $category === 'Other' ? 'selected' : ''; ?>>Other</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="Active" <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Closed" <?php echo $status === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                    <option value="Draft" <?php echo $status === 'Draft' ? 'selected' : ''; ?>>Draft</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <input type="month" class="form-control" name="month" value="<?php echo htmlspecialchars($month); ?>">
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">Apply Filters</button>
                  <a href="announcements.php" class="btn btn-secondary">Clear Filters</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Announcements table -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="selectAll">
                          <label class="form-check-label" for="selectAll"></label>
                        </div>
                      </th>
                      <th>Title</th>
                      <th>Category</th>
                      <th>Posted Date</th>
                      <th>End Date</th>
                      <th>Posted By</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                    <tr>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="<?php echo $announcement['id']; ?>">
                        </div>
                      </td>
                      <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                      <td><?php echo htmlspecialchars($announcement['category']); ?></td>
                      <td><?php echo date('M d, Y', strtotime($announcement['start_date'])); ?></td>
                      <td><?php echo date('M d, Y', strtotime($announcement['end_date'])); ?></td>
                      <td><?php echo htmlspecialchars($announcement['posted_by_name']); ?></td>
                      <td>
                        <span class="badge bg-<?php 
                          echo match($announcement['status']) {
                            'Active' => 'success',
                            'Pending' => 'warning',
                            'Closed' => 'secondary',
                            'Draft' => 'info',
                            default => 'primary'
                          };
                        ?>">
                          <?php echo htmlspecialchars($announcement['status']); ?>
                        </span>
                      </td>
                      <td>
                        <div class="btn-group">
                          <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewAnnouncement(<?php echo $announcement['id']; ?>)">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editAnnouncement(<?php echo $announcement['id']; ?>)">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              
              <!-- Pagination -->
              <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                  <span class="text-muted">Showing 1 to 5 of 16 entries</span>
                </div>
                <nav>
                  <ul class="pagination mb-0">
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                      <a class="page-link" href="#">Next</a>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Announcement Preview -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title mb-4">Announcement Preview</h5>
              <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i> Select an announcement from the list above to preview its content here.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Add Announcement Modal -->
  <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAnnouncementModalLabel">Create New Announcement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" id="announcementForm" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <div class="row mb-3">
              <div class="col-md-8">
                <label class="form-label">Announcement Title</label>
                <input type="text" class="form-control" name="title" placeholder="Enter announcement title" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Category</label>
                <select class="form-select" name="category" required>
                  <option value="">Select category</option>
                  <option value="Event">Event</option>
                  <option value="Academic">Academic</option>
                  <option value="Club Notice">Club Notice</option>
                  <option value="Competition">Competition</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Content</label>
              <textarea class="form-control" name="content" rows="6" placeholder="Compose announcement content..." required></textarea>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" required>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Priority Level</label>
                <select class="form-select" name="priority" required>
                  <option value="Low">Low</option>
                  <option value="Normal" selected>Normal</option>
                  <option value="High">High</option>
                  <option value="Urgent">Urgent</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" required>
                  <option value="Active" selected>Active</option>
                  <option value="Pending">Pending</option>
                  <option value="Draft">Draft</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Attachments</label>
              <input type="file" class="form-control" name="attachments[]" multiple>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" form="announcementForm" class="btn btn-primary">Post Announcement</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Announcement Modal -->
  <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" id="editAnnouncementForm" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="row mb-3">
              <div class="col-md-8">
                <label class="form-label">Announcement Title</label>
                <input type="text" class="form-control" name="title" id="edit_title" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Category</label>
                <select class="form-select" name="category" id="edit_category" required>
                  <option value="">Select category</option>
                  <option value="Event">Event</option>
                  <option value="Academic">Academic</option>
                  <option value="Club Notice">Club Notice</option>
                  <option value="Competition">Competition</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Content</label>
              <textarea class="form-control" name="content" id="edit_content" rows="6" required></textarea>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" id="edit_end_date" required>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Priority Level</label>
                <select class="form-select" name="priority" id="edit_priority" required>
                  <option value="Low">Low</option>
                  <option value="Normal">Normal</option>
                  <option value="High">High</option>
                  <option value="Urgent">Urgent</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" id="edit_status" required>
                  <option value="Active">Active</option>
                  <option value="Pending">Pending</option>
                  <option value="Draft">Draft</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Current Attachments</label>
              <div id="currentAttachments" class="mb-2"></div>
              <label class="form-label">Add New Attachments</label>
              <input type="file" class="form-control" name="attachments[]" multiple>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" form="editAnnouncementForm" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle (with Popper for dropdowns/tooltips) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom script -->
  <script>
    // View announcement details
    function viewAnnouncement(id) {
      // TODO: Implement view functionality
      alert('View announcement ' + id);
    }

    // Edit announcement
    function editAnnouncement(id) {
      fetch(`edit_announcement.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('edit_id').value = data.id;
          document.getElementById('edit_title').value = data.title;
          document.getElementById('edit_category').value = data.category;
          document.getElementById('edit_content').value = data.content;
          document.getElementById('edit_start_date').value = data.start_date;
          document.getElementById('edit_end_date').value = data.end_date;
          document.getElementById('edit_priority').value = data.priority;
          document.getElementById('edit_status').value = data.status;
          
          // Display current attachments
          const attachmentsDiv = document.getElementById('currentAttachments');
          attachmentsDiv.innerHTML = '';
          
          if (data.attachments) {
            const attachments = data.attachments.split(',');
            attachments.forEach(attachment => {
              const [id, fileName] = attachment.split(':');
              const attachmentElement = document.createElement('div');
              attachmentElement.className = 'd-flex align-items-center mb-2';
              attachmentElement.innerHTML = `
                <i class="bi bi-paperclip me-2"></i>
                <span class="me-2">${fileName}</span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAttachment(${id})">
                  <i class="bi bi-trash"></i>
                </button>
              `;
              attachmentsDiv.appendChild(attachmentElement);
            });
          }
          
          new bootstrap.Modal(document.getElementById('editAnnouncementModal')).show();
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error loading announcement details');
        });
    }

    // Delete attachment
    function deleteAttachment(id) {
      if (confirm('Are you sure you want to delete this attachment?')) {
        fetch('edit_announcement.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `action=delete_attachment&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Remove the attachment element from the DOM
            event.target.closest('.d-flex').remove();
          } else {
            alert('Error deleting attachment');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error deleting attachment');
        });
      }
    }

    // Handle select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('tbody .form-check-input');
      checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // Form validation
    document.getElementById('announcementForm').addEventListener('submit', function(e) {
      const startDate = new Date(this.start_date.value);
      const endDate = new Date(this.end_date.value);
      
      if (endDate < startDate) {
        e.preventDefault();
        alert('End date cannot be earlier than start date');
      }
    });

    // Edit form validation
    document.getElementById('editAnnouncementForm').addEventListener('submit', function(e) {
      const startDate = new Date(this.start_date.value);
      const endDate = new Date(this.end_date.value);
      
      if (endDate < startDate) {
        e.preventDefault();
        alert('End date cannot be earlier than start date');
      }
    });
  </script>
</body>
</html>