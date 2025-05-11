<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery Management - ACCESS Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Lightbox CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
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
            <a href="gallery.php" class="nav-link active">
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
        <header class="page-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Gallery Management</h1>
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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cloud-upload me-2"></i>Upload Images
            </button>
            <!-- Gallery Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label for="eventFilter" class="form-label">Filter by Event</label>
                            <select class="form-select" id="eventFilter">
                                <option value="">All Events</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="dateFilter" class="form-label">Filter by Date</label>
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                        <div class="col-md-4">
                            <label for="typeFilter" class="form-label">Filter by Type</label>
                            <select class="form-select" id="typeFilter">
                                <option value="">All Media</option>
                                <option value="image">Images</option>
                                <option value="video">Videos</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary mt-4" onclick="filterGallery()">
                                <i class="bi bi-search me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="card">
                <div class="card-body">
                    <div class="row g-4" id="galleryGrid">
                        <!-- Gallery items will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm">
                        <div class="mb-3">
                            <label class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="uploadEvent" required placeholder="Enter event name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image Title</label>
                            <input type="text" class="form-control" id="imageTitle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="imageDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Images</label>
                            <input type="file" class="form-control" id="imageFiles" name="images[]" multiple accept="image/*" required>
                            <small class="text-muted">Supported formats: JPG, PNG, GIF</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="uploadImages()">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img src="" alt="" id="previewImage" class="img-fluid">
                    </div>
                    <div class="mb-3">
                        <h6>Title</h6>
                        <p id="previewTitle"></p>
                    </div>
                    <div class="mb-3">
                        <h6>Description</h6>
                        <p id="previewDescription"></p>
                    </div>
                    <div class="mb-3">
                        <h6>Event</h6>
                        <p id="previewEvent"></p>
                    </div>
                    <div class="mb-3">
                        <h6>Upload Date</h6>
                        <p id="previewDate"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="deleteImage()">
                        <i class="bi bi-trash"></i> Delete Image
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lightbox JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/gallery.js"></script>
</body>
</html>