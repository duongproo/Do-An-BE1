
<?php
session_start();

// Hủy session
session_unset();
session_destroy();

// Đặt thông báo đăng xuất thành công vào session
$_SESSION['message'] = 'Đăng xuất thành công!';

// Chuyển hướng người dùng về trang đăng nhập
header("Location: login.php");
exit();
?>
