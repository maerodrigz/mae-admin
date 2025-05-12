<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // your password
$dbname = "access_db"; // change to your DB name
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
  <title>Gallery - ACCESS-USTP Oroquieta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .container {
      flex: 1;
    }
    .footer {
      margin-top: auto;
      padding: 1rem 0;
      background-color: #f8f9fa;
      border-top: 1px solid #dee2e6;
    }
    .gallery-img {
      cursor: pointer;
      transition: transform 0.2s;
    }
    .gallery-img:hover {
      transform: scale(1.02);
    }
    .modal-img {
      max-width: 100%;
      max-height: 80vh;
      object-fit: contain;
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
                    <li class="nav-item"><a class="nav-link " href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php"><i class="fas fa-calendar-alt"></i> Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcements.php"><i class="fas fa-bullhorn"></i> Announcement</a></li>
                    <li class="nav-item"><a class="nav-link active" href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
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
        <i class="fas fa-images me-2"></i>Gallery
      </div>
      <div class="card-body">
        <h4 class="mb-3">Event Albums</h4>
        <div class="row g-3">
          <?php
            $base_url = "http://localhost/mae-admin-part/uploads/gallery/";
            
            // Get unique events
            $events_sql = "SELECT DISTINCT event FROM gallery ORDER BY event";
            $events_result = $conn->query($events_sql);
            
            if ($events_result->num_rows > 0) {
              while($event_row = $events_result->fetch_assoc()) {
                $event = $event_row["event"];
                
                // Get first image for this event
                $cover_sql = "SELECT * FROM gallery WHERE event = ? ORDER BY upload_date DESC LIMIT 1";
                $stmt = $conn->prepare($cover_sql);
                $stmt->bind_param("s", $event);
                $stmt->execute();
                $cover_result = $stmt->get_result();
                $cover_row = $cover_result->fetch_assoc();
                
                if ($cover_row) {
                  $image_filename = basename($cover_row["path"]);
                  $total_images_sql = "SELECT COUNT(*) as count FROM gallery WHERE event = ?";
                  $count_stmt = $conn->prepare($total_images_sql);
                  $count_stmt->bind_param("s", $event);
                  $count_stmt->execute();
                  $count_result = $count_stmt->get_result();
                  $count_row = $count_result->fetch_assoc();
                  
                  echo '<div class="col-6 col-md-4 col-lg-3">';
                  echo '  <div class="card h-100 shadow-sm">';
                  echo '    <img src="' . $base_url . $image_filename . '" class="card-img-top gallery-img" alt="' . htmlspecialchars($event) . '" style="height:180px;object-fit:cover;" onclick="openEventAlbum(\'' . htmlspecialchars($event) . '\')">';
                  echo '    <div class="card-body p-2">';
                  echo '      <h6 class="card-title mb-1">' . htmlspecialchars($event) . '</h6>';
                  echo '      <small class="text-muted">' . $count_row['count'] . ' photos</small>';
                  echo '    </div>';
                  echo '  </div>';
                  echo '</div>';
                }
              }
            } else {
              echo '<div class="col-12"><p class="text-center">No events found.</p></div>';
            }
          ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Event Album Modal -->
  <div class="modal fade" id="eventAlbumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="eventAlbumTitle"></h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3" id="eventAlbumContent">
            <!-- Event images will be loaded here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Image View Modal -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="imageModalTitle"></h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img src="" class="modal-img" id="modalImage" alt="Gallery Image">
          <p class="mt-2 text-muted" id="modalEvent"></p>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function openImageModal(src, title, event) {
      document.getElementById('modalImage').src = src;
      document.getElementById('imageModalTitle').textContent = title;
      document.getElementById('modalEvent').textContent = event;
      new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    function openEventAlbum(event) {
      document.getElementById('eventAlbumTitle').textContent = event;
      document.getElementById('eventAlbumContent').innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
      
      new bootstrap.Modal(document.getElementById('eventAlbumModal')).show();
      
      // Fetch event images
      fetch('get_event_images.php?event=' + encodeURIComponent(event))
        .then(response => response.json())
        .then(data => {
          const content = document.getElementById('eventAlbumContent');
          content.innerHTML = '';
          
          data.forEach(image => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';
            col.innerHTML = `
              <div class="card h-100 shadow-sm">
                <img src="${image.path}" class="card-img-top gallery-img" alt="${image.title}" 
                     style="height:180px;object-fit:cover;" 
                     onclick="openImageModal('${image.path}', '${image.title}', '${image.event}')">
                <div class="card-body p-2">
                  <h6 class="card-title mb-1">${image.title}</h6>
                </div>
              </div>
            `;
            content.appendChild(col);
          });
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('eventAlbumContent').innerHTML = '<div class="col-12"><p class="text-center text-danger">Error loading images. Please try again.</p></div>';
        });
    }
  </script>
  <!-- Footer -->
  <div class="footer text-center">
        &copy; 2025 BSIT2A. All rights reserved.<br>
    </div>
</body>
</html>
<?php $conn->close(); ?> 