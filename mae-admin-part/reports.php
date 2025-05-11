<?php
require_once 'config.php';

// Fetch all articles
try {
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $articles = [];
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Articles - ACCESS Admin ARTICLES</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .article-content {
            min-height: 300px;
            resize: vertical;
        }
    </style>
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
            <a href="services.php" class="nav-link">
                <i class="bi bi-tools"></i> <span>Services</span>
            </a>
           
            <a href="announcements.php" class="nav-link">
                <i class="bi bi-megaphone"></i> <span>Announcements</span>
            </a>
            <a href="gallery.php" class="nav-link">
                <i class="bi bi-images"></i> <span>Gallery</span>
            </a>
            <a href="reports.php" class="nav-link active">
                <i class="bi bi-file-earmark-text"></i> <span>Articles</span>
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
            <h1 class="h3 mb-0">Article Management</h1>
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

        <div class="container-fluid px-0">
            <!-- Action Buttons -->
            <div class="mb-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#articleModal">
                    <i class="bi bi-plus-circle me-2"></i>Create New Article
                </button>
            </div>

            <!-- Articles List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Date Published</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($articles)): ?>
                                    <?php foreach ($articles as $article): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($article['title']); ?></td>
                                            <td><?php echo htmlspecialchars($article['category']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($article['created_at'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $article['status'] === 'published' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($article['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="viewArticle(<?php echo $article['id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-primary" onclick="editArticle(<?php echo $article['id']; ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteArticle(<?php echo $article['id']; ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No articles found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Create/Edit Article Modal -->
    <div class="modal fade" id="articleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create New Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="articleForm" method="POST" action="article_operations.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="articleId">
                        <div class="mb-3">
                            <label for="articleTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="articleTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="articleCategory" class="form-label">Service Category</label>
                            <input type="text" class="form-control" id="articleCategory" name="category" placeholder="Enter service category" required>
                        </div>
                        <div class="mb-3">
                            <label for="articleContent" class="form-label">Content</label>
                            <textarea id="articleContent" name="content" class="form-control article-content" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="articleImage" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="articleImage" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="articleStatus" class="form-label">Status</label>
                            <select class="form-select" id="articleStatus" name="status" required>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="articleForm" class="btn btn-primary">Save Article</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Article Modal -->
    <div class="modal fade" id="viewArticleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewArticleTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="viewArticleContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Article Management Functions
        function viewArticle(id) {
            fetch(`get_article.php?id=${id}`)
                .then(response => response.json())
                .then(article => {
                    document.getElementById('viewArticleTitle').textContent = article.title;
                    let content = article.content;
                    if (article.image_path) {
                        content = `<img src="${article.image_path}" class="img-fluid mb-3" alt="${article.title}"><br>${content}`;
                    }
                    document.getElementById('viewArticleContent').innerHTML = content;
                    new bootstrap.Modal(document.getElementById('viewArticleModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading article');
                });
        }

        function editArticle(id) {
            fetch(`get_article.php?id=${id}`)
                .then(response => response.json())
                .then(article => {
                    document.getElementById('modalTitle').textContent = 'Edit Article';
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('articleId').value = article.id;
                    document.getElementById('articleTitle').value = article.title;
                    document.getElementById('articleCategory').value = article.category;
                    document.getElementById('articleContent').value = article.content;
                    document.getElementById('articleStatus').value = article.status;
                    new bootstrap.Modal(document.getElementById('articleModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading article');
                });
        }

        function deleteArticle(id) {
            if (confirm('Are you sure you want to delete this article?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'article_operations.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Reset form when creating new article
        document.querySelector('[data-bs-target="#articleModal"]').addEventListener('click', function() {
            document.getElementById('modalTitle').textContent = 'Create New Article';
            document.getElementById('formAction').value = 'create';
            document.getElementById('articleForm').reset();
            document.getElementById('articleId').value = '';
        });

        // Show success/error messages
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            alert('Operation completed successfully');
        } else if (urlParams.has('error')) {
            alert('Error: ' + urlParams.get('error'));
        }
    </script>
</body>
</html>
