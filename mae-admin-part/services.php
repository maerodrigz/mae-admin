<?php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE service_requests SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $request_id]);
        echo json_encode(['success' => true]);
        exit();
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

// Fetch service requests
try {
    $stmt = $pdo->query("SELECT * FROM service_requests ORDER BY request_date DESC");
    $serviceRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $serviceRequests = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Services - ACCESS Admin Dashboard</title>
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
      <a href="services.php" class="nav-link active">
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
      <h1 class="h3 mb-0">Services Management</h1>
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

    <!-- Main content -->
    <div class="container-fluid px-0">
      <!-- Welcome banner -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="bg-primary text-white rounded-3 p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h2 class="fw-bold">Service Requests</h2>
                <p class="mb-0">Review and manage service requests from members.</p>
              </div>
              <i class="bi bi-tools display-4"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Service requests table -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Service Name</th>
                  <th>Category</th>
                  <th>Requester</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="serviceRequests">
                <!-- Service requests will be loaded dynamically -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- View Service Details Modal -->
  <div class="modal fade" id="viewServiceModal" tabindex="-1" aria-labelledby="viewServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewServiceModalLabel">Service Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="serviceDetailsContent">
          <!-- Service details will be loaded here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Fetch service requests from PHP
      const serviceRequests = <?php echo json_encode($serviceRequests); ?>;
      
      // Populate service requests table
      const allRequestsTable = document.getElementById('serviceRequests');
      serviceRequests.forEach(request => {
        const row = document.createElement('tr');
        let statusBadge = '';
        
        switch(request.status) {
          case 'Pending':
            statusBadge = '<span class="badge bg-warning">Pending</span>';
            break;
          case 'Approved':
            statusBadge = '<span class="badge bg-success">Approved</span>';
            break;
          case 'Declined':
            statusBadge = '<span class="badge bg-danger">Declined</span>';
            break;
        }
        
        row.innerHTML = `
          <td>${request.id}</td>
          <td>${request.service_name}</td>
          <td>${request.category}</td>
          <td>${request.requester_name}</td>
          <td>${new Date(request.request_date).toLocaleDateString()}</td>
          <td>${statusBadge}</td>
          <td>
            <button class="btn btn-sm btn-outline-primary me-1" onclick="viewServiceDetails('${request.id}')">
              <i class="bi bi-eye"></i>
            </button>
            ${request.status === 'Pending' ? `
              <button class="btn btn-sm btn-success me-1" onclick="updateRequestStatus(this, '${request.id}', 'Approved')">
                <i class="bi bi-check-lg"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="updateRequestStatus(this, '${request.id}', 'Declined')">
                <i class="bi bi-x-lg"></i>
              </button>
            ` : ''}
          </td>
        `;
        
        allRequestsTable.appendChild(row);
      });
    });

    function updateRequestStatus(button, id, status) {
      const formData = new FormData();
      formData.append('action', 'update_status');
      formData.append('request_id', id);
      formData.append('status', status);
      
      fetch('services.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const row = button.closest('tr');
          const statusCell = row.querySelector('td:nth-child(6)');
          
          let statusBadge = '';
          switch(status) {
            case 'Approved':
              statusBadge = '<span class="badge bg-success">Approved</span>';
              break;
            case 'Declined':
              statusBadge = '<span class="badge bg-danger">Declined</span>';
              break;
          }
          
          statusCell.innerHTML = statusBadge;
          
          // Remove action buttons
          const actionsCell = row.querySelector('td:last-child');
          actionsCell.innerHTML = `
            <button class="btn btn-sm btn-outline-primary" onclick="viewServiceDetails('${id}')">
              <i class="bi bi-eye"></i>
            </button>
          `;
          
          alert(`Service request ${id} has been ${status.toLowerCase()}.`);
        } else {
          alert('Error updating status: ' + data.error);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status.');
      });
    }

    function viewServiceDetails(id) {
      fetch(`get_service_details.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          const detailsContent = document.getElementById('serviceDetailsContent');
          detailsContent.innerHTML = `
            <div class="mb-3">
              <h6 class="text-muted">Service ID</h6>
              <p>${data.id}</p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Service Name</h6>
              <p>${data.service_name}</p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Category</h6>
              <p>${data.category}</p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Requester</h6>
              <p>${data.requester_name} (ID: ${data.requester_id})</p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Request Date</h6>
              <p>${new Date(data.request_date).toLocaleDateString()}</p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Status</h6>
              <p>${data.status}</p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Description</h6>
              <p>${data.description || 'No description provided'}</p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Notes</h6>
              <p>${data.notes || 'No notes available'}</p>
            </div>
          `;
          
          const modal = new bootstrap.Modal(document.getElementById('viewServiceModal'));
          modal.show();
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while fetching service details.');
        });
    }
  </script>
</body>
</html>