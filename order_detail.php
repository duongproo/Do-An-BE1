<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/Order.php';
require_once 'app/models/Product.php';
require_once 'app/models/User.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra tham số order_id
if (!isset($_GET['order_id'])) {
    header("Location: order_history.php");
    exit();
}

$userId = $_SESSION['user_id'];
$orderId = $_GET['order_id'];

$orderModel = new Order();
$productModel = new Product();
$userModel = new User();

// Lấy thông tin đơn hàng
$orderDetails = $orderModel->getOrderById($orderId);

// Kiểm tra quyền truy cập
if (!$orderDetails || $orderDetails['user_id'] != $userId) {
    header("Location: order_history.php");
    exit();
}

// Lấy chi tiết người dùng
$userDetails = $userModel->getUserById($userId);

// Lấy các mục trong đơn hàng
$orderItems = $orderModel->getOrderItems($orderId);

// Tính tổng tiền
$subTotal = 0;
$shippingCost = 0; // Bạn có thể thêm logic tính phí ship
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Đơn Hàng #<?php echo $orderId; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-detail-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .status-badge {
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Chi Tiết Đơn Hàng</h1>
                    <div>
                        <span class="badge 
                            <?php 
                            switch($orderDetails['status']) {
                                case 'pending': echo 'bg-warning'; break;
                                case 'completed': echo 'bg-success'; break;
                                case 'cancelled': echo 'bg-danger'; break;
                                default: echo 'bg-secondary';
                            }
                            ?> status-badge">
                            <?php echo $orderDetails['status']; ?>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="order-detail-section">
                            <h4>Sản Phẩm Đã Mua</h4>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Sản Phẩm</th>
                                        <th>Số Lượng</th>
                                        <th>Đơn Giá</th>
                                        <th>Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): 
                                        $product = $productModel->getProductById($item['product_id']);
                                        $itemTotal = $item['quantity'] * $item['price'];
                                        $subTotal += $itemTotal;
                                    ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($product['name']); ?>
                                                <small class="d-block text-muted">
                                                    <?php echo htmlspecialchars($product['category_name'] ?? 'Không rõ'); ?>
                                                </small>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                                            <td><?php echo number_format($itemTotal, 0, ',', '.'); ?> đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="order-detail-section">
                            <h4>Thông Tin Vận Chuyển</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Phương Thức Vận Chuyển:</strong>
                                    <?php echo htmlspecialchars($orderDetails['shipping_method']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Địa Chỉ Giao Hàng:</strong>
                                    <?php echo htmlspecialchars($orderDetails['address']); ?>
                                </div>
                            </div>
                            <?php if (!empty($orderDetails['notes'])): ?>
                                <div class="mt-3">
                                    <strong>Ghi Chú:</strong>
                                    <?php echo htmlspecialchars($orderDetails['notes']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="order-detail-section">
                            <h4>Thông Tin Thanh Toán</h4>
                            <table class="table">
                                <tr>
                                    <td>Tạm Tính:</td>
                                    <td class="text-end">
                                        <?php echo number_format($subTotal, 0, ',', '.'); ?> đ
                                    </td>
                                </tr>
                                <tr>
                                    <td>Phí Vận Chuyển:</td>
                                    <td class="text-end">
                                        <?php echo number_format($shippingCost, 0, ',', '.'); ?> đ
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tổng Cộng:</strong></td>
                                    <td class="text-end text-primary">
                                        <strong>
                                            <?php echo number_format($subTotal + $shippingCost, 0, ',', '.'); ?> đ
                                        </strong>
                                    </td>
                                </tr>
                            </table>

                            <div class="payment-info mt-3">
                                <h5>Thanh Toán</h5>
                                <p>
                                    <strong>Phương Thức:</strong> 
                                    <?php echo htmlspecialchars($orderDetails['payment_method']); ?>
                                </p>
                                <p>
                                    <strong>Ngày Đặt Hàng:</strong> 
                                    <?php echo date('d/m/Y H:i:s', strtotime($orderDetails['created_at'])); ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($orderDetails['status'] == 'Pending'): ?>
                            <div class="mt-3">
                                <form action="cancel_order.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                                    <button type="submit" class="btn btn-danger w-100" 
                                            onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng?');">
                                        Hủy Đơn Hàng
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="order_history.php" class="btn btn-secondary">Quay Lại Danh Sách Đơn Hàng</a>
                    <?php if ($orderDetails['status'] == 'Completed'): ?>
                        <a href="reorder.php?order_id=<?php echo $orderId; ?>" class="btn btn-primary">
                            Đặt Lại Đơn Hàng
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>