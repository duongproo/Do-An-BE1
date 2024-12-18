<?php   
require_once 'Database.php';
    class Coupon extends Database{
        public function validateCoupon($couponCode) {
            $stmt = self::$connection->prepare("SELECT * FROM coupons WHERE code = ? AND active = 1 AND expiry_date > NOW()");
            $stmt->bind_param("s", $couponCode);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->num_rows > 0; // Trả về true nếu có kết quả
        }   
    }
?>