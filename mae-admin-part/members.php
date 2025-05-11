<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle member deletion
if (isset($_POST['delete_member'])) {
    $member_id = $_POST['member_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$member_id]);
        $_SESSION['success'] = "Member deleted successfully";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error deleting member: " . $e->getMessage();
    }
    header("Location: members.php");
    exit();
}

// Build the query based on filters
$where_conditions = [];
$params = [];

if (!empty($_GET['search'])) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ?)";
    $search = "%" . $_GET['search'] . "%";
    $params[] = $search;
    $params[] = $search;
}

if (!empty($_GET['department'])) {
    $where_conditions[] = "department = ?";
    $params[] = $_GET['department'];
}

if (!empty($_GET['memberType'])) {
    $where_conditions[] = "member_type = ?";
    $params[] = $_GET['memberType'];
}

if (!empty($_GET['status'])) {
    $where_conditions[] = "status = ?";
    $params[] = $_GET['status'];
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Fetch members
try {
    $stmt = $pdo->prepare("SELECT * FROM members $where_clause ORDER BY name ASC");
    $stmt->execute($params);
    $members = $stmt->fetchAll();
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching members: " . $e->getMessage();
    $members = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Members - ACCESS Admin Dashboard</title>
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
      <a href="members.php" class="nav-link active">
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
        <i class="bi bi-file-earmark-bar-graph"></i> <span>Reports</span>
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
      <h1 class="h3 mb-0">Members Management</h1>
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
    </header>
    
    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <!-- Members content -->
    <div class="container-fluid px-0">
      <!-- Actions bar -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Members Directory</h5>
              <div class="d-flex">
                <form class="input-group me-3" style="max-width: 300px;" method="GET">
                  <input type="text" class="form-control" name="search" placeholder="Search members...">
                  <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                  </button>
                </form>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                  <i class="bi bi-person-plus me-1"></i> Add Member
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Filters row -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body">
              <form method="GET" class="row g-3">
                <div class="col-md-3">
                  <label for="department" class="form-label">Department</label>
                  <select class="form-select" id="department" name="department">
                    <option value="">All Departments</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BTLED-IA">BTLED-IA</option>
                    <option value="BTLED-HE">BTLED-HE</option>
                    <option value="BTLED-ICT">BTLED-ICT</option>
                    <option value="BFPT">BFPT</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="memberType" class="form-label">Member Type</label>
                  <select class="form-select" id="memberType" name="memberType">
                    <option value="">All Member Types</option>
                    <option value="Student Member">Student Member</option>
                    <option value="Faculty Member">Faculty Member</option>
                    <option value="Industry Partner">Industry Partner</option>
                    <option value="Alumni">Alumni</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Pending">Pending</option>
                    <option value="Suspended">Suspended</option>
                  </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Apply Filters
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Members table -->
      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th scope="col">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="selectAll">
                        </div>
                      </th>
                      <th scope="col">Name</th>
                      <th scope="col">Department</th>
                      <th scope="col">Member Type</th>
                      <th scope="col">Status</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($members as $member): ?>
                    <tr>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="<?php echo $member['id']; ?>">
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="<?php echo !empty($member['profile_image']) ? $member['profile_image'] : 'ACCESS.jpg'; ?>" 
                               alt="<?php echo htmlspecialchars($member['name']); ?>" 
                               class="rounded-circle me-2" width="40" height="40">
                          <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($member['name']); ?></h6>
                            <small class="text-muted"><?php echo htmlspecialchars($member['email']); ?></small>
                          </div>
                        </div>
                      </td>
                      <td><?php echo htmlspecialchars($member['department']); ?></td>
                      <td><?php echo htmlspecialchars($member['member_type']); ?></td>
                      <td>
                        <span class="badge bg-<?php 
                          echo match($member['status']) {
                            'Active' => 'success',
                            'Inactive' => 'secondary',
                            'Pending' => 'warning',
                            'Suspended' => 'danger',
                            default => 'secondary'
                          };
                        ?>">
                          <?php echo htmlspecialchars($member['status']); ?>
                        </span>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button class="btn btn-outline-secondary" title="View Details" 
                                  onclick="viewMember(<?php echo $member['id']; ?>)">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button class="btn btn-outline-primary" title="Edit Member"
                                  onclick="editMember(<?php echo $member['id']; ?>)">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this member?');">
                            <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                            <button type="submit" name="delete_member" class="btn btn-outline-danger" title="Delete Member">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($members)): ?>
                    <tr>
                      <td colspan="6" class="text-center py-4">
                        <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">No members found</p>
                      </td>
                    </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Add Member Modal -->
  <div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Member</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="add_member.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="department" class="form-label">Department</label>
              <select class="form-select" id="department" name="department" required>
                <option value="">Select Department</option>
                <option value="BSIT">BSIT</option>
                <option value="BTLED-IA">BTLED-IA</option>
                <option value="BTLED-HE">BTLED-HE</option>
                <option value="BTLED-ICT">BTLED-ICT</option>
                <option value="BFPT">BFPT</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="member_type" class="form-label">Member Type</label>
              <select class="form-select" id="member_type" name="member_type" required>
                <option value="">Select Member Type</option>
                <option value="Student Member">Student Member</option>
                <option value="Faculty Member">Faculty Member</option>
                <option value="Industry Partner">Industry Partner</option>
                <option value="Alumni">Alumni</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Pending">Pending</option>
                <option value="Suspended">Suspended</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="profile_image" class="form-label">Profile Image</label>
              <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Member</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Member Modal -->
  <div class="modal fade" id="editMemberModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Member</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="edit_member.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="member_id" id="edit_member_id">
          <div class="modal-body">
            <!-- Same fields as Add Member Modal -->
            <div class="mb-3">
              <label for="edit_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="edit_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="edit_email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="edit_department" class="form-label">Department</label>
              <select class="form-select" id="edit_department" name="department" required>
                <option value="BSIT">BSIT</option>
                <option value="BTLED-IA">BTLED-IA</option>
                <option value="BTLED-HE">BTLED-HE</option>
                <option value="BTLED-ICT">BTLED-ICT</option>
                <option value="BFPT">BFPT</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="edit_member_type" class="form-label">Member Type</label>
              <select class="form-select" id="edit_member_type" name="member_type" required>
                <option value="Student Member">Student Member</option>
                <option value="Faculty Member">Faculty Member</option>
                <option value="Industry Partner">Industry Partner</option>
                <option value="Alumni">Alumni</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="edit_status" class="form-label">Status</label>
              <select class="form-select" id="edit_status" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Pending">Pending</option>
                <option value="Suspended">Suspended</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="edit_profile_image" class="form-label">Profile Image</label>
              <input type="file" class="form-control" id="edit_profile_image" name="profile_image" accept="image/*">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Member Modal -->
  <div class="modal fade" id="viewMemberModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Member Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-4">
            <img id="view_profile_image" src="" alt="Profile" class="rounded-circle" width="100" height="100">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Name</label>
            <p id="view_name" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Email</label>
            <p id="view_email" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Department</label>
            <p id="view_department" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Member Type</label>
            <p id="view_member_type" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <p id="view_status" class="mb-0"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Function to view member details
    function viewMember(id) {
      fetch(`get_member.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('view_profile_image').src = data.profile_image || 'ACCESS.jpg';
          document.getElementById('view_name').textContent = data.name;
          document.getElementById('view_email').textContent = data.email;
          document.getElementById('view_department').textContent = data.department;
          document.getElementById('view_member_type').textContent = data.member_type;
          document.getElementById('view_status').textContent = data.status;
          new bootstrap.Modal(document.getElementById('viewMemberModal')).show();
        })
        .catch(error => console.error('Error:', error));
    }

    // Function to edit member
    function editMember(id) {
      fetch(`get_member.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('edit_member_id').value = data.id;
          document.getElementById('edit_name').value = data.name;
          document.getElementById('edit_email').value = data.email;
          document.getElementById('edit_department').value = data.department;
          document.getElementById('edit_member_type').value = data.member_type;
          document.getElementById('edit_status').value = data.status;
          new bootstrap.Modal(document.getElementById('editMemberModal')).show();
        })
        .catch(error => console.error('Error:', error));
    }

    // Handle select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('tbody .form-check-input');
      checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
  </script>
</body>
</html>