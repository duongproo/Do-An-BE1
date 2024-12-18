<?php
require_once 'config/database.php';
require_once 'app/models/user.php'; 

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPass']);

    // Kiểm tra mật khẩu khớp
    if ($password !== $confirmPassword) {
        echo "<script>alert('Mật khẩu không khớp. Vui lòng thử lại.');</script>";
    } else {
        $user = new User();
        $result = $user->register($username, $email, $phone, $password);

        if ($result === "Đăng ký thành công!") {
            // Sử dụng json_encode để thoát chuỗi đúng cách
            echo "<script>alert(" . json_encode($result) . "); window.location.href = 'login.php';</script>";
            // header("Location: index.php");
        } else {
            // Sử dụng json_encode để thoát chuỗi đúng cách
            echo "<script>alert(" . json_encode($result) . ");</script>";
        }
        
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Juice Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/register.css">
</head>


<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Create Your Account</h2>
        </div>

        <form action="" method="POST">
            <div class="form-row mb-3">
                <div class="flex-grow-1">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Choose a username">
                </div>
                <div class="flex-grow-1">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                </div>
            </div>

            <div class="form-row mb-3">
                <div class="flex-grow-1">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required placeholder="Enter your phone number">
                </div>
                <div class="flex-grow-1">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Create a strong password">
                </div>
            </div>

            <div class="mb-3">
                <label for="confirmPass" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPass" name="confirmPass" required placeholder="Repeat your password">
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                <label class="form-check-label" for="termsCheck">
                    I agree to the Terms and Conditions
                </label>
            </div>

            <button type="submit" name="submit" class="btn btn-register w-100">Sign Up</button>

            <div class="form-text">
                Already have an account? <a href="login.php" class="text-decoration-none">Sign In</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>