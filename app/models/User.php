<?php
require_once 'Database.php';
class User extends Database
{
    public $errorMessage;
    public function login($loginInput, $password)
    {
        // Input validation
        if (empty($loginInput) || empty($password)) {
            $this->errorMessage = "Vui lòng điền đầy đủ thông tin.";
            return false;
        }

        // Check database connection
        if (!self::$connection) {
            $this->errorMessage = "Lỗi kết nối cơ sở dữ liệu.";
            return false;
        }

        // Prepare select statement to check username or email
        $stmt = self::$connection->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        if (!$stmt) {
            $this->errorMessage = "Lỗi truy vấn cơ sở dữ liệu.";
            return false;
        }
        $stmt->bind_param("ss", $loginInput, $loginInput);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $this->errorMessage = "Tên đăng nhập hoặc email chưa được đăng ký.";
            return false;
        }

        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
             $_SESSION['email'] = $user['email'];
             $_SESSION['phone'] = $user['phone'];
             $_SESSION['role'] = $user['role'];
            return true;
        } else {
            $this->errorMessage = "Mật khẩu không chính xác.";
            return false;
        }
    }


    public function register($username, $email, $phone, $password)
    {
        // Input validation
        if (empty($username) || empty($email) || empty($phone) || empty($password)) {
            return "Vui lòng điền đầy đủ thông tin.";
        }

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Địa chỉ email không hợp lệ.";
        }

        // Check database connection
        if (!self::$connection) {
            return "Lỗi kết nối cơ sở dữ liệu.";
        }

        // Check if email already exists
        $stmtCheck = self::$connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
            return "Email đã được sử dụng.";
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare insert statement
        $sql = self::$connection->prepare('INSERT INTO users (username, email, password, phone, role) VALUES (?, ?, ?, ?, ?)');
        if (!$sql) {
            return "Lỗi chuẩn bị truy vấn: " . self::$connection->error;
        }
        $defaultRole = 'customer';

        // Bind parameters and execute
        $sql->bind_param("sssss", $username, $email, $hashedPassword, $phone, $defaultRole);

        if ($sql->execute()) {
            return "Đăng ký thành công!";
        } else {
            return "Lỗi khi thêm người dùng mới: " . $sql->error;
        }
    }

    public function logout()
    {
        // Khởi động session nếu chưa được khởi động
        session_start();

        // Xóa tất cả thông tin trong session
        session_unset();

        // Hủy session
        session_destroy();

        // Chuyển hướng người dùng về trang đăng nhập hoặc trang chủ
        header("Location: login.php");
        exit();
    }
    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = parent::$connection->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
$user = new User();
