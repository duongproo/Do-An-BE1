<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/Order.php';
require_once 'app/models/Product.php';

// Kiểm tra xem order_id có tồn tại không
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$orderId = $_GET['order_id'];
$orderModel = new Order();
$productModel = new Product();

// Lấy thông tin đơn hàng
$orderDetails = $orderModel->getOrderById($orderId);

// Lấy chi tiết các sản phẩm trong đơn hàng
$orderItems = $orderModel->getOrderItems($orderId);

// Kiểm tra quyền truy cập
if (!$orderDetails || $orderDetails['user_id'] != $_SESSION['user_id']) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác Nhận Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white text-center">
                        <h2>Đơn Hàng Đã Được Xác Nhận</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Mã Đơn Hàng:</strong> #<?php echo $orderId; ?>
                        </div>

                        <h3 class="mb-4">Chi Tiết Đơn Hàng</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sản Phẩm</th>
                                    <th>Số Lượng</th>
                                    <th>Giá</th>
                                    <th>Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalOrder = 0;
                                foreach ($orderItems as $item): 
                                    $productDetails = $productModel->getProductById($item['product_id']);
                                    $itemTotal = $item['quantity'] * $item['price'];
                                    $totalOrder += $itemTotal;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($productDetails['name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                                        <td><?php echo number_format($itemTotal, 0, ',', '.'); ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>Tổng Cộng:</strong></td>
                                    <td><strong><?php echo number_format($totalOrder, 0, ',', '.'); ?> đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="row">
                            <div class="col-md-6">
                                <h4>Thông Tin Giao Hàng</h4>
                                <p>
                                    <strong>Địa Chỉ:</strong> <?php echo htmlspecialchars($orderDetails['address']); ?><br>
                                    <strong>Phương Thức Vận Chuyển:</strong> <?php echo htmlspecialchars($orderDetails['shipping_method']); ?><br>
                                    <strong>Ghi Chú:</strong> <?php echo htmlspecialchars($orderDetails['notes'] ?? 'Không có'); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h4>Phương Thức Thanh Toán</h4>
                                <p>
                                    <strong><?php echo htmlspecialchars($orderDetails['payment_method']); ?></strong>
                                </p>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary me-2">Tiếp Tục Mua Hàng</a>
                            <a href="order_history.php" class="btn btn-secondary">Xem Lịch Sử Mua Hàng</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>