<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - ACCESS Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            font-family: 'Inter', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 1rem;
        }
        .pattern {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://www.toptal.com/designers/subtlepatterns/patterns/symphony.png');
            opacity: 0.08;
            z-index: 0;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2.5rem 2rem 2rem 2rem;
            background: rgba(255,255,255,0.97);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            border: 1px solid #e3e6f0;
            position: relative;
            z-index: 1;
            margin: 1rem;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-circle {
            background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
            border-radius: 50%;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            box-shadow: 0 4px 16px rgba(80, 80, 200, 0.12);
        }
        .logo-container img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
        }
        .login-container h4 {
            font-weight: 600;
            color: #2575fc;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .form-label {
            font-weight: 500;
            color: #444;
            font-size: 0.95rem;
        }
        .input-group-text {
            background: #f0f4fa;
            border: none;
        }
        .form-control {
            height: 45px;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #2575fc;
            box-shadow: 0 0 0 0.2rem rgba(37,117,252,.15);
        }
        .btn-primary {
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            border: none;
            font-weight: 600;
            transition: background 0.2s, transform 0.2s;
            height: 45px;
            font-size: 1rem;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .show-password {
            cursor: pointer;
            color: #888;
            margin-left: -35px;
            z-index: 2;
        }
        .alert {
            font-size: 0.97rem;
            padding: 0.75rem 1rem;
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

        /* For larger screens */
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

        /* For landscape orientation on mobile */
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
    </style>
</head>
<body>
    <div class="pattern"></div>
    <div class="login-container">
        <div class="logo-container">
            <div class="logo-circle">
                <img src="ACCESS.jpg" alt="ACCESS Organization Logo">
            </div>
            <h4>ACCESS Admin</h4>
            <p class="text-muted mb-3">Please login to continue</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
            </div>
            
            <div class="mb-4 position-relative">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="input-group-text show-password" onclick="togglePassword()"><i class="bi bi-eye" id="toggleIcon"></i></span>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </button>
        </form>
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
    </script>
</body>
</html> 