<?php
session_start();
require_once 'config/database.php'; 
require_once 'app/models/Database.php';
require_once 'app/models/Coupon.php';
// Khởi tạo đối tượng Coupon
$coupon = new Coupon();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voucher_code = trim($_POST['voucher_code']);  // Lấy mã voucher người dùng nhập

    // Kiểm tra voucher có hợp lệ hay không
    if ($coupon->validateCoupon($voucher_code)) {
        // Truy vấn để lấy thông tin voucher
        $stmt = Coupon::$connection->prepare("SELECT * FROM coupons WHERE code = ? AND active = 1 AND expiry_date > NOW()");
        $stmt->bind_param("s", $voucher_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $voucher = $result->fetch_assoc();
        
            $discount_type = $voucher['discount_type'];
            $discount_amount = $voucher['discount_amount'];
            $total_price = $_SESSION['cart_total'] ?? 1000;
        
            if ($discount_type === 'percentage') {
                $discounted_price = $total_price - ($total_price * $discount_amount / 100);
            } else if ($discount_type === 'fixed') {
                $discounted_price = max(0, $total_price - $discount_amount);
            }
        
            // Lưu giá gốc và giá giảm vào session
            $_SESSION['original_price'] = $total_price;
            $_SESSION['discounted_price'] = $discounted_price;
            $_SESSION['applied_voucher'] = $voucher_code;
        
            $_SESSION['voucher_success'] = "Áp dụng voucher thành công! Giá gốc: " . number_format($total_price, 2) . " $. Giá sau giảm: " . number_format($discounted_price, 2) . " $.";
        }
    } else {
        // Nếu voucher không hợp lệ hoặc hết hạn
        $_SESSION['voucher_error'] = "Voucher không hợp lệ hoặc đã hết hạn.";
    }

    header('Location: cart.php');
    exit();
}
?>
