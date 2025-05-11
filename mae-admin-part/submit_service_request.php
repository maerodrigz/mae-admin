<?php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = $_POST['service_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $requester_id = $_SESSION['user_id'];
    $requester_name = $_SESSION['user_name'] ?? 'Unknown';

    if (empty($service_name) || empty($category)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO service_requests (service_name, category, requester_id, requester_name, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$service_name, $category, $requester_id, $requester_name, $description]);
            $message = 'Service request submitted successfully!';
        } catch(PDOException $e) {
            $error = 'Error submitting request: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Service Request - ACCESS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Submit Service Request</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="service_name" class="form-label">Service Name *</label>
                                <input type="text" class="form-control" id="service_name" name="service_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select a category</option>
                                    <option value="Housing Assistance">Housing Assistance</option>
                                    <option value="Financial Aid">Financial Aid</option>
                                    <option value="Education Support">Education Support</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Legal Services">Legal Services</option>
                                    <option value="Employment">Employment</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                                <a href="index.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 