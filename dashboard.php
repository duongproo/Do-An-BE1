<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/Product.php';
require_once 'app/models/Category.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$productModel = new Product();
$products = $productModel->getNewProductDashboard();
$categoryModel = new Category();
$category = $categoryModel->getAllCatogories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xóa sản phẩm
    if (isset($_POST['delete']) && isset($_POST['product_id'])) {
        $productId = $_POST['product_id'];
        if ($productModel->deleteProduct($productId)) {
            $_SESSION['message'] = 'Xóa sản phẩm thành công!';
        } else {
            $_SESSION['message'] = 'Xóa sản phẩm thất bại!';
        }
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Thêm sản phẩm mới
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $categoryId = $_POST['id'];
        $discount = $_POST['discount'];
        $rating = $_POST['rating'];
        $image = "";

        if ($_FILES['image']['error'] === 0) {
            $uploadDir = 'public/uploads/';
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $image = basename($_FILES['image']['name']);
                } else {
                    echo "UPLOAD THẤT BẠI!";
                    exit();
                }
            } else {
                echo "Chỉ chấp nhận file ảnh!";
                exit();
            }
        }

        $success = $productModel->addProduct($name, $description, $price, $discount, $categoryId, $image, $rating);
        if ($success) {
            $_SESSION['message'] = 'Thêm sản phẩm thành công!';
            header("Location: dashboard.php");
            exit();
        }
    }

    // Sửa sản phẩm
    if (isset($_POST['update_product'])) {
        $productId = $_POST['product_id'];
        $productName = $_POST['product_name'];
        $productPrice = $_POST['product_price'];
        $productDescription = $_POST['product_description'];
        $productDiscount = $_POST['product_discount'];
        $productRating = $_POST['product_rating'];
        $categoryId = $_POST['id'];
        $image = "";

            // Xử lý ảnh mới hoặc giữ ảnh cũ
        if ($_FILES['image']['error'] === 0) {
            // Nếu có ảnh cũ, xóa ảnh cũ trước khi tải ảnh mới lên
            if (isset($product) && !empty($product['image'])) {
                $oldImage = 'public/uploads/' . $product['image'];
                if (file_exists($oldImage)) {
                    unlink($oldImage);  // Xóa ảnh cũ
                }
            }

            // Đường dẫn lưu ảnh mới
            $uploadDir = 'public/uploads/';
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);

            // Kiểm tra và di chuyển ảnh mới vào thư mục
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = basename($_FILES['image']['name']);
            } else {
                echo "UPLOAD THẤT BẠI!";
                exit();
            }
        } else {
            // Sử dụng ảnh cũ nếu không tải lên ảnh mới
            $product = $productModel->getProductById($productId);
            $image = $product['image'];
        }


        $success = $productModel->editProduct($productId, $productName,$productDescription, $productPrice, $productDiscount, $categoryId, $image, $productRating);
        if ($success) {
            $_SESSION['message'] = 'Cập nhật sản phẩm thành công!';
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = 'Cập nhật sản phẩm thất bại!';
        }
    }
    // Sửa category 
    if(isset($_POST['update_category'])){

        $categoryName=$_POST['category_name'];
        $categoryId = $_POST['category_id'];
        $result=$categoryModel->editCategory($categoryId,$categoryName);
        if  ($result)
        {
            $_SESSION['message'] = 'Cập nhật danh mục thành công!';
            header("Location: dashboard.php");
            exit();
        }
        else {
            $_SESSION['message'] = 'Cập nhật danh mục thất bại!';
        }
    }
    // Thêm category 
    if(isset($_POST['add_category'])){
        $categoryName=$_POST['category_name'];
        $result=$categoryModel->addCategory($categoryName);
        if($result)
        {
            $_SESSION['message'] = 'Thêm danh mục thành công!';
            header("Location: dashboard.php");
            exit();
        }
    }
    if (isset($_POST['delete_category']))
     {
        $categoryId=$_POST['category_id'];
        $result=$categoryModel->deleteCategory($categoryId);
        $_SESSION['message'] = 'Xóa danh mục thành công!';
        header("Location: dashboard.php");
        exit();
     }
}

// Số sản phẩm trên mỗi trang
$perPage = 5;

// Trang hiện tại từ request (nếu không có thì mặc định là 1)
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính toán vị trí bắt đầu (offset)
$offset = ($currentPage - 1) * $perPage;

// Lấy danh sách sản phẩm phân trang
$productModel = new Product();
$products = $productModel->getAllProductPaginated($offset, $perPage);

// Tổng số sản phẩm
$totalProducts = $productModel->countAllProducts();

// Tính tổng số trang
$totalPages = ceil($totalProducts / $perPage);

// Chuẩn bị dữ liệu trả về cho view
$data = [
    'products' => $products,
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'totalProducts' => $totalProducts,
    'perPage' => $perPage
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruit Juice Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2196F3;
            --background-color: #f0f4f8;
            --card-background: #ffffff;
            --text-primary: #333;
            --text-secondary: #666;
            --border-color: #e0e0e0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 20px;
            color: white;
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar-logo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.3);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu button {
            width: 100%;
            text-align: left;
            background: transparent;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu button:hover {
            background-color: rgba(255,255,255,0.1);
        }

        /* Main Content Styles */
        .main-content {
            padding: 20px;
            background-color: var(--background-color);
            overflow-y: auto;
        }

        .card {
            background-color: var(--card-background);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .card-header h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary-color);
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background-color: var(--primary-color);
            color: white;
            padding: 12px;
            text-align: left;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
            .alert {
        padding: 10px;
        margin: 10px 0;
        border: 1px solid green;
        background-color: #dff0d8;
        color: #3c763d;
        border-radius: 5px;
        }
        .pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">
                <img src="public/images/heallthyfood.jpg" alt="Fruit Juice Logo">
                <h2>HealthyFruits Dashboard</h2>
            </div>
            
            <ul class="sidebar-menu">
                  <li>
                  <button onclick="window.location.href='index.php'">
                        <i class="fas fa-home"></i> Trang Chủ
                    </button>

                </li>
                <li>
                    <button onclick="showSection('products')">
                        <i class="fas fa-wine-bottle"></i> Quản Lý Sản Phẩm
                    </button>
                </li>
                <li>
                    <button onclick="showSection('categories')">
                        <i class="fas fa-list-alt"></i> Quản Lý Danh Mục
                    </button>
                </li>
                <li>
                    <button onclick="showSection('analytics')">
                        <i class="fas fa-chart-pie"></i> Thống Kê
                    </button>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Sản Phẩm Section -->
            <div id="products-section" class="section">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-plus-circle"></i> Thêm Sản Phẩm Mới</h2>
                    </div>
                    <form id="product-form" class="form-grid" method="post" enctype="multipart/form-data">
                        <input type="text" name="name" class="form-control" placeholder="Tên sản phẩm" required>
                        <input type="text"name="price" class="form-control" placeholder="Giá" required>
                        <input type="number" name ="discount" class="form-control" placeholder="Nhập % Giảm giá" required>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Mô tả" required></textarea>
                        <select class="form-control" required name="id" id="category_id">
                             <?php  foreach($category as $ca) :?>
                                <option value="<?php echo $ca['id']?>">
                                        <?php echo $ca['name']?>
                                </option>
                                <?php  endforeach ?>
                        </select>
                        <input type="file" class="form-control" id="image" name="image" require>
                        <input type="number" class="form-control" id="rating" name="rating" require placeholder="Rating" >
                        <div style="grid-column: span 2;">
                            <button type="submit" class="btn btn-primary" name="add_product">
                                <i class="fas fa-save"></i> Thêm Sản Phẩm
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <?php
                    // Hiển thị thông báo nếu có
                    if (isset($_SESSION['message'])) {
                        echo "<div class='alert alert-success'>{$_SESSION['message']}</div>";
                        // Xóa thông báo sau khi hiển thị
                        unset($_SESSION['message']);
                    }
                    ?>

                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Danh Sách Sản Phẩm</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên Sản Phẩm</th>
                                    <th>Hình Ảnh</th>
                                    <th>Giá</th>
                                    <th>% Giảm Giá</th>
                                    <th>Đánh Giá</th>
                                    <th>Mô tả</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                    foreach($products as $pr):?>
                                 <tr>
                                    <td><?php echo $pr['id']?></td>
                                    <td><?php echo $pr['name'] ?></td>
                                    <?php
                                        // Tên file ảnh
                                        $imageName = $pr['image'];

                                        // Kiểm tra ảnh có tồn tại trong thư mục public/images
                                        $imagePath = 'public/images/' . $imageName;
                                        if (!file_exists($imagePath)) {
                                            // Nếu không tồn tại, kiểm tra trong public/uploads
                                            $imagePath = 'public/uploads/' . $imageName;
                                            if (!file_exists($imagePath)) {
                                                // Nếu không tồn tại trong cả hai thư mục, dùng ảnh mặc định
                                                $imagePath = 'public/images/default.jpg';
                                            }
                                        }
                                        ?>
                                    <td> <img src="<?php echo htmlspecialchars($imagePath); ?>" width="100px" alt="Product Image"></td>
                                    <td><?php echo $pr['price']?></td>
                                    <td><?php echo $pr['discount']?></td>
                                    <td><?php echo $pr['rating']?></td>
                                    <td><?php echo $pr['description'] ?></td>
                                
                                 <td>  <a href="#" 
                                    onclick="openEditModal(
                                         '<?php echo $pr['id']; ?>', 
                                            '<?php echo $pr['name']; ?>', 
                                            '<?php echo $pr['price']; ?>', 
                                            '<?php echo $pr['category_id']; ?>', 
                                            '<?php echo htmlspecialchars($pr['description']); ?>', 
                                            '<?php echo $pr['image']; ?>',
                                             '<?php echo $pr['rating']; ?>'
                                        )" 
                                    class="btn btn-success" 
                                    data-toggle="modal" 
                                    data-target="#editProductModal">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form method="post" style="display:inline;" action="dashboard.php">
                                        <button type="submit" 
                                                name="delete" 
                                                class="btn btn-danger btn-action" 
                                                onclick="return confirm('Bạn có chắc chắn xóa sản phẩm này không')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <input type="hidden" name="product_id" value="<?php echo $pr['id']?>">
                                    </form></td>
                             </tr>
                        <?php endforeach ?>
                            </tbody>
                        </table>
                          <!-- Nút phân trang -->
                          <div class="pagination">
                                    <?php if ($data['currentPage'] > 1): ?>
                                        <a href="?page=<?php echo $data['currentPage'] - 1; ?>">&laquo; Trang trước</a>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>" 
                                        class="<?php echo $i === $data['currentPage'] ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($data['currentPage'] < $data['totalPages']): ?>
                                        <a href="?page=<?php echo $data['currentPage'] + 1; ?>">Trang sau &raquo;</a>
                                    <?php endif; ?>
                             </div>
                    </div>
                </div>
            </div>

            <!-- Danh Mục Section -->
            <div id="categories-section" class="section" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-plus-circle"></i> Thêm Danh Mục Mới</h2>
                    </div>
                    <form id="category-form" class="form-grid" method="post">
                        <input type="text" class="form-control" placeholder="Tên danh mục" required name="category_name">
                        <div style="grid-column: span 2;">
                            <button type="submit" class="btn btn-primary" name="add_category">
                                <i class="fas fa-save"></i> Thêm Danh Mục
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <?php
                    // Hiển thị thông báo nếu có
                    if (isset($_SESSION['message'])) {
                        echo "<div class='alert alert-success'>{$_SESSION['message']}</div>";
                        // Xóa thông báo sau khi hiển thị
                        unset($_SESSION['message']);
                    }
                    ?>

                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Danh Sách Danh Mục</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên Danh Mục</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                               foreach($category as $ca):
                                ?>
                                     <tr>
                                        <td><?php echo $ca['id']?></td>
                                        <td><?php echo $ca['name']?></td>
                                        <td>
                                        <form action="" method="post">
                                           
                                            <a href="#" 
                                            onclick="openEditCategoryModal(
                                                    '<?php echo $ca['id']; ?>', 
                                                    '<?php echo $ca['name']; ?>', 
                                                  
                                                )" 
                                            class="btn btn-success" 
                                            data-toggle="modal" 
                                            data-target="#editCategoryModal">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="submit" name="delete_category" class="btn btn-danger btn-action" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                                <i class="fas fa-trash-alt"></i> 
                                            </button>
                                            <input type="hidden" name="category_id" value="<?php echo $ca['id']; ?>"> 
                                        </form>

                                        </td>
                                    </tr>
                                <?php endforeach?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Thống Kê Section -->
            <div id="analytics-section" class="section" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-chart-bar"></i> Thống Kê Bán Hàng</h2>
                    </div>
                    <div class="form-grid">
                        <div>
                            <h3><i class="fas fa-box"></i> Tổng Sản Phẩm: <span id="total-products">0</span></h3>
                            <h3><i class="fas fa-tags"></i> Tổng Danh Mục: <span id="total-categories">0</span></h3>
                        </div>
                        <div>
                            <h3><i class="fas fa-star"></i> Sản Phẩm Bán Chạy:</h3>
                            <ul id="best-selling-products"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Chỉnh Sửa Sản Phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" method="post" enctype="multipart/form-data" action="dashboard.php"> 
                    <input type="hidden" name="product_id" id="edit-product-id" value="">
                    
                    <div class="form-group">
                        <label>Tên Sản Phẩm</label>
                        <input type="text" class="form-control" id="edit-product-name" name="product_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Giá</label>
                        <input type="number" class="form-control" id="edit-product-price" name="product_price" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Danh Mục</label>
                        <select class="form-control" id="edit-product-category" name="id" id="category_id" required>
                                <?php  foreach($category as $ca) :?>
                        <option value="<?php echo $ca['id']?>">
                                <?php echo $ca['name']?>
                        </option>
                        <?php  endforeach ?>
                        
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rating</label>
                        <input type="number" class="form-control" id="edit-product-rating" name="product_rating" required>
                    </div>
                    <div class="form-group">
                        <label>Hình Ảnh</label>
                        <input type="file" class="form-control" name="image" id="edit-product-image">
                        <img id="edit-image-preview" src="" class="img-fluid mt-2" style="max-height: 200px; display: none;">
                    </div>
                    
                    <div class="form-group">
                        <label>Mô Tả</label>
                        <textarea class="form-control" id="edit-product-description" name="product_description"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" name="update_product">Lưu Thay Đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModal">Chỉnh Sửa Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" method="post" enctype="multipart/form-data" action="dashboard.php"> 
                <input type="hidden" name="category_id" id="edit-category-id" value="">
                    
                    <div class="form-group">
                        <label>Tên Category</label>
                        <input type="text" class="form-control" id="edit-category-name" name="category_name" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" name="update_category">Lưu Thay Đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script>
        function showSection(sectionName) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.style.display = 'none');
            document.getElementById(`${sectionName}-section`).style.display = 'block';
        }
        function openEditModal(productId, name, price, categoryId, description, imageSrc,rating) {
    document.getElementById('edit-product-id').value = productId;
    document.getElementById('edit-product-name').value = name;
    document.getElementById('edit-product-price').value = price;
    document.getElementById('edit-product-category').value = categoryId;
    document.getElementById('edit-product-description').value = description;
    document.getElementById('edit-product-rating').value = rating;
    
    // Hiển thị ảnh preview nếu có
    const imagePreview = document.getElementById('edit-image-preview');
    if (imageSrc) {
        imagePreview.src = 'public/uploads/' + imageSrc;
        imagePreview.style.display = 'block';
    } else {
        imagePreview.style.display = 'none';
    }
    
    // Hiển thị modal
    $('#editProductModal').modal('show');
}
function openEditCategoryModal(categoryId,categoryName) {
    document.getElementById('edit-category-id').value = categoryId;
    document.getElementById('edit-category-name').value = categoryName;
    // Hiển thị modal
    $('#editCategoryModal').modal('show');
}

// Xem trước hình ảnh khi tải lên
document.getElementById('edit-product-image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('edit-image-preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>