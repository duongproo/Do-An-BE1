<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/Order.php';
require_once 'app/models/Product.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$orderModel = new Order();
$productModel = new Product();

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Số đơn hàng trên mỗi trang
$offset = ($page - 1) * $limit;

// Lấy danh sách đơn hàng
$orders = $orderModel->getUserOrders($userId, $limit, $offset);
$totalOrders = $orderModel->countUserOrders($userId);
$totalPages = ceil($totalOrders / $limit);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-card {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .order-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Lịch Sử Mua Hàng</h1>
                <a href="index.php" class="btn btn-primary my-3">Quay lại trang chủ</a>
                <?php if (empty($orders)): ?>
                    <div class="alert alert-info">
                        Bạn chưa có đơn hàng nào.
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="card order-card">
                            <div class="card-header order-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Mã Đơn Hàng:</strong> #<?php echo $order['id']; ?>
                                    <span class="ms-3 badge 
                                        <?php 
                                        switch($order['status']) {
                                            case 'pending': echo 'bg-warning'; break;
                                            case 'completed': echo 'bg-success'; break;
                                            case 'cancelled': echo 'bg-danger'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </div>
                                <div>
                                    <strong>Ngày Đặt:</strong> 
                                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5>Chi Tiết Sản Phẩm</h5>
                                        <table class="table table-sm">
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
                                                $orderItems = $orderModel->getOrderItems($order['id']);
                                                $subTotal = 0;
                                                foreach ($orderItems as $item):
                                                    $product = $productModel->getProductById($item['product_id']);
                                                    $itemTotal = $item['quantity'] * $item['price'];
                                                    $subTotal += $itemTotal;
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                        <td><?php echo $item['quantity']; ?></td>
                                                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                                                        <td><?php echo number_format($itemTotal, 0, ',', '.'); ?> đ</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Thông Tin Đơn Hàng</h5>
                                        <ul class="list-unstyled">
                                            <li><strong>Tổng Tiền:</strong> <?php echo number_format($subTotal, 0, ',', '.'); ?> đ</li>
                                            <li><strong>Phương Thức TT:</strong> <?php echo $order['payment_method']; ?></li>
                                            <li><strong>Vận Chuyển:</strong> <?php echo $order['shipping_method']; ?></li>
                                        </ul>
                                        <a href="order_detail.php?order_id=<?php echo $order['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            Chi Tiết Đơn Hàng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Phân trang -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                        
                    </nav>
                 
                <?php endif; ?>
              
            </div>
        </div>
    </div>
</body>
</html>