<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'access_db';
$user = 'root'; // Change if your MySQL user is different
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query to check credentials
    $stmt = $conn->prepare('SELECT * FROM student WHERE student_id = ? AND password = ?');
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['student_id'] = $row['student_id'];
        $_SESSION['fullname'] = $row['fullname'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid Student ID or Password.';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - ACCESS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            --secondary-gradient: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.18);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        }

        body {
            min-height: 100vh;
            background: var(--primary-gradient);
            font-family: 'Inter', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 1rem;
            overflow-x: hidden;
        }

        .pattern {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://www.toptal.com/designers/subtlepatterns/patterns/symphony.png');
            opacity: 0.08;
            z-index: 0;
            animation: patternMove 20s linear infinite;
        }

        @keyframes patternMove {
            0% { background-position: 0 0; }
            100% { background-position: 100px 100px; }
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2.5rem 2rem 2rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.18);
            position: relative;
            z-index: 1;
            margin: 1rem;
            backdrop-filter: blur(12px);
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--secondary-gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .login-container:hover::before {
            transform: scaleX(1);
        }

        .login-container::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .login-container:hover::after {
            transform: translateX(100%);
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(31, 38, 135, 0.25);
        }

        /* Add decorative elements */
        .login-container .corner {
            position: absolute;
            width: 50px;
            height: 50px;
            border: 2px solid rgba(37, 117, 252, 0.2);
            transition: all 0.3s ease;
        }

        .login-container .corner-tl {
            top: 10px;
            left: 10px;
            border-right: none;
            border-bottom: none;
        }

        .login-container .corner-tr {
            top: 10px;
            right: 10px;
            border-left: none;
            border-bottom: none;
        }

        .login-container .corner-bl {
            bottom: 10px;
            left: 10px;
            border-right: none;
            border-top: none;
        }

        .login-container .corner-br {
            bottom: 10px;
            right: 10px;
            border-left: none;
            border-top: none;
        }

        .login-container:hover .corner {
            width: 60px;
            height: 60px;
            border-color: rgba(37, 117, 252, 0.4);
        }

        /* Add subtle pattern overlay */
        .login-container .pattern-overlay {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232575fc' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
            pointer-events: none;
        }

        /* Enhance form elements */
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(37, 117, 252, 0.2);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: #2575fc;
            box-shadow: 0 0 0 3px rgba(37, 117, 252, 0.15);
        }

        .input-group-text {
            background: rgba(37, 117, 252, 0.1);
            border: 1px solid rgba(37, 117, 252, 0.2);
            color: #2575fc;
        }

        .btn-primary {
            background: var(--secondary-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .btn-primary.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-primary.loading .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 117, 252, 0.3);
        }

        /* Add animation for form elements */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .logo-circle {
            background: var(--secondary-gradient);
            border-radius: 50%;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            box-shadow: 0 4px 16px rgba(80, 80, 200, 0.12);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .logo-circle::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .logo-circle:hover {
            transform: scale(1.05);
        }

        .logo-container img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            transition: transform 0.3s ease;
        }

        .logo-circle:hover img {
            transform: scale(1.1);
        }

        .login-container h4 {
            font-weight: 600;
            color: #2575fc;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-label {
            font-weight: 500;
            color: #444;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .input-group {
            position: relative;
            transition: transform 0.3s ease;
        }

        .input-group:focus-within {
            transform: translateY(-2px);
        }

        .show-password {
            cursor: pointer;
            color: #888;
            margin-left: -35px;
            z-index: 2;
            transition: color 0.3s ease;
        }

        .show-password:hover {
            color: #2575fc;
        }

        .alert {
            font-size: 0.97rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Floating animation for background elements */
        .floating {
            position: absolute;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
            }
            .login-container {
                padding: 1.5rem 1.25rem 1.25rem 1.25rem;
                margin: 0.5rem;
            }
            .logo-circle {
                width: 80px;
                height: 80px;
            }
            .logo-container img {
                width: 50px;
                height: 50px;
            }
            .login-container h4 {
                font-size: 1.3rem;
            }
            .form-control, .btn-primary {
                height: 42px;
            }
        }

        @media (max-width: 360px) {
            .login-container {
                padding: 1.25rem 1rem 1rem 1rem;
            }
            .logo-circle {
                width: 70px;
                height: 70px;
            }
            .logo-container img {
                width: 45px;
                height: 45px;
            }
            .login-container h4 {
                font-size: 1.2rem;
            }
            .form-label {
                font-size: 0.9rem;
            }
            .form-control, .btn-primary {
                height: 40px;
                font-size: 0.9rem;
            }
        }

        @media (min-width: 1200px) {
            .login-container {
                max-width: 450px;
            }
            .logo-circle {
                width: 100px;
                height: 100px;
            }
            .logo-container img {
                width: 65px;
                height: 65px;
            }
        }

        @media (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 0.5rem;
            }
            .login-container {
                padding: 1.25rem;
                margin: 0.5rem;
            }
            .logo-circle {
                width: 70px;
                height: 70px;
                margin-bottom: 0.5rem;
            }
            .logo-container {
                margin-bottom: 1rem;
            }
        }

        .footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(37, 117, 252, 0.1);
            color: #666;
            font-size: 0.9rem;
            line-height: 1.6;
            position: relative;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: var(--secondary-gradient);
            border-radius: 2px;
        }

        .footer a {
            color: #2575fc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #6a11cb;
        }
    </style>
</head>
<body>
    <div class="pattern"></div>
    <div class="floating"></div>
    <div class="floating"></div>
    <div class="floating"></div>
    <div class="login-container">
        <div class="pattern-overlay"></div>
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>
        <div class="logo-container">
            <div class="logo-circle">
                <img src="ACCESS.jpg" alt="ACCESS Organization Logo">
            </div>
            <h4>ACCESS </h4>
            <p class="text-muted mb-3">Please login to continue</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
            </div>
            
            <div class="form-group mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="input-group-text show-password" onclick="togglePassword()"><i class="bi bi-eye" id="toggleIcon"></i></span>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary w-100" id="loginButton">
                    <span class="spinner" style="display: none;"></span>
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </div>
        </form>
        <div class="footer text-center">
            <p>&copy; 2025 BSIT2A. All rights reserved.</p>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        // Add loading animation to login button
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            const spinner = button.querySelector('.spinner');
            const icon = button.querySelector('.bi');
            
            button.classList.add('loading');
            spinner.style.display = 'inline-block';
            icon.style.display = 'none';
        });
    </script>
</body>
</html> 