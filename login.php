<?php
require_once 'config/database.php';
require_once 'app/models/user.php';
session_start();

if (isset($_POST['submit'])) {
    // Lấy thông tin từ form đăng nhập
    $loginInput = trim($_POST['username']);
    $password = trim($_POST['password']);

    $user = new User();
    // Gọi phương thức login từ lớp User để kiểm tra thông tin
    if ($user->login($loginInput, $password)) {
        // Kiểm tra role và chuyển hướng
        if ($_SESSION['role'] === 'admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        // Lưu thông báo lỗi vào session và quay lại trang đăng nhập
        $_SESSION['message'] = $user->errorMessage;
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Juice Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF6B35;      /* Vibrant Orange */
            --secondary-color: #4ECB71;    /* Fresh Green */
            --background-color: #FFF8F0;   /* Soft Peach Background */
            --text-color: #2C5F2D;         /* Dark Green */
        }

        body {
            background-color: var(--background-color);
            font-family: 'Quicksand', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .login-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(44, 95, 45, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
            left: 30%;
            border: 2px solid var(--secondary-color);
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(135deg, 
                rgba(78, 203, 113, 0.1) 0%, 
                rgba(255, 107, 53, 0.1) 100%);
            z-index: -1;
            border-radius: 20px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: var(--text-color);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 30px;
            border-color: var(--secondary-color);
            padding: 12px 20px;
            background-color: #f9f9f9;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }

        .btn-login {
            background-color: var(--primary-color);
            border: none;
            border-radius: 30px;
            padding: 12px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(78, 203, 113, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-text {
            text-align: center;
            margin-top: 20px;
            color: var(--text-color);
        }

        .form-text a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .decorative-fruit {
            position: absolute;
            opacity: 0.1;
            z-index: -2;
        }

        .fruit-1 {
            top: -50px;
            left: -50px;
            width: 200px;
            transform: rotate(-15deg);
        }

        .fruit-2 {
            bottom: -50px;
            right: -50px;
            width: 250px;
            transform: rotate(15deg);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>Fresh Juice Login</h2>
                <p>Login to explore our delicious fruit juices!</p>
            </div>
            
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Email Address</label>
                    <input type="username" class="form-control" id="username" name="username" required placeholder="Enter your email">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">
                        Remember me
                    </label>
                </div>
                
                <button type="submit" name="submit" class="btn btn-login w-100">Sign In</button>
                
                <div class="form-text">
                    Don't have an account? <a href="register.php" class="text-decoration-none">Sign Up</a>
                </div>
            </form>
                <!-- Hiển thị thông báo nếu có -->
                <?php
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-danger mt-3">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>