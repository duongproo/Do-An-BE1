<?php   
require_once 'Database.php';
class Order extends Database {
    // Get all orders
    public function getAllOrder() {
        $sql = parent::$connection->prepare('SELECT * FROM orders');
        return parent::select($sql);
    }

    // Create a new order
    // public function createOrder($userId, $totalPrice, $paymentMethod, $notes, $shippingMethod, $orderItems) {
    //     parent::$connection->begin_transaction();

    //     try {
    //         // Insert order
    //         $orderSql = parent::$connection->prepare("
    //             INSERT INTO orders 
    //             (user_id, total_price, payment_method, notes, shipping_method) 
    //             VALUES (?, ?, ?, ?, ?)
    //         ");
    //         $orderSql->bind_param('idsss', 
    //             $userId, 
    //             $totalPrice, 
    //             $paymentMethod, 
    //             $notes, 
    //             $shippingMethod
    //         );
    //         $orderSql->execute();

    //         // Get the new order ID
    //         $orderId = parent::$connection->insert_id;

    //         // Insert order items
    //         $itemSql = parent::$connection->prepare("
    //             INSERT INTO order_items 
    //             (order_id, product_id, quantity, price) 
    //             VALUES (?, ?, ?, ?)
    //         ");

    //         foreach ($orderItems as $item) {
    //             $itemSql->bind_param('iids', 
    //                 $orderId, 
    //                 $item['product_id'], 
    //                 $item['quantity'], 
    //                 $item['price']
    //             );
    //             $itemSql->execute();
    //         }

    //         parent::$connection->commit();
    //         return $orderId;
    //     } catch (Exception $e) {
    //         parent::$connection->rollback();
    //         error_log("Order creation failed: " . $e->getMessage());
    //         return false;
    //     }
    // }
    public function createOrder($userId, $totalPrice, $status, $paymentMethod, $notes, $address, $shippingMethod)
    {
        $sql = parent::$connection->prepare('
        INSERT INTO orders (user_id, total_price,status, payment_method, notes, address, shipping_method)
        VALUES (?, ?, ?, ?, ?, ?, ?)
     ');
        $status = 'pending';
        $sql->bind_param('idsssss', $userId, $totalPrice,$status, $paymentMethod,$notes, $address, $shippingMethod);
        if ($sql->execute()) {
            // Trả về ID của đơn hàng mới được tạo
            return parent::$connection->insert_id;
        } else {
            // Nếu có lỗi, trả về false
            return false;
        }
        return $sql->execute();
    }
    public function addOrderItem($orderId, $productId, $quantity, $price)
    {
        // SQL để thêm một mục vào bảng 'order_items' với order_id, product_id, quantity và price
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        
        $stmt = parent::$connection->prepare($query);
        
        // Bind tham số với kiểu dữ liệu phù hợp
        // 'i' - Integer, 'd' - Double
        $stmt->bind_param('iiid', $orderId, $productId, $quantity, $price);
        
        // Thực thi câu lệnh và trả về kết quả
        return $stmt->execute();
    }
    
    
    // Get order details by ID
    public function getOrderById($orderId) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = parent::$connection->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getOrderItems($orderId) {
        $sql = "SELECT * FROM order_items WHERE order_id = ?";
        $stmt = parent::$connection->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getUserOrders($userId, $limit = 5, $offset = 0) {
        $sql = "SELECT * FROM orders 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        $stmt = parent::$connection->prepare($sql);
        $stmt->bind_param("iii", $userId, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function countUserOrders($userId) {
        $sql = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
        $stmt = parent::$connection->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
}
?>