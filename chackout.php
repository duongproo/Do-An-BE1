<?php
require_once 'config/database.php';
require_once 'app/models/Product.php';
require_once 'app/models/Category.php';
require_once 'app/models/Order.php';

session_start();
$totalQuantity = 0;
$totalPrice = 0;

$selectedProducts = [];
if (isset($_POST['selected_cart'])) {
    foreach ($_POST['selected_cart'] as $productJson) {
        $product = json_decode($productJson, true);
        $selectedProducts[] = $product;
       
    }
}
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}
$categoryModel=new Category();
$category=$categoryModel->getAllCatogories();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $selectedProducts = json_decode($_POST['selected_cart'], true);
    // Lấy thông tin từ form
    $userId = $_SESSION['user_id'];  // Giả sử bạn lưu ID người dùng trong session
    $totalPrice = $_POST['total_price'] ?? 0; 
    // Lấy giá trị tổng tiền
    $paymentMethod = $_POST['payment_method'] ?? '';
    $shippingMethod = $_POST['shipping_method'] ?? '';
    $address =  $_POST['address'];  
    $notes = $_POST['text'];  

     // Kiểm tra điều kiện hợp lệ
     if (empty($userId) || empty($selectedProducts)) {
         echo "Error: Missing required information!";
         exit();
     }
    // Gọi hàm createOrder để tạo đơn hàng mới
    $status = 'Pending';
    $order = new Order(); 
    $orderId = $order->createOrder($userId, $totalPrice,$status, $paymentMethod, $notes, $address, $shippingMethod);
    // var_dump($orderId);
    if ($orderId) {
        // Tiến hành xử lý order items (chi tiết sản phẩm)
        foreach ($selectedProducts as $productDetail) {
            // Thêm từng sản phẩm vào bảng order_items
            $order->addOrderItem($orderId, $productDetail['productId'], $productDetail['quantity'], $productDetail['price']);
        }
            // Chuyển hướng sau khi xử lý xong
            header("Location: order_confirmation.php?order_id=" . $orderId);
            exit();
        // Redirect hoặc thông báo thành công
        echo "Order placed successfully!";
    } else {
        echo "There was an error placing the order.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Fruitables - Vegetable Website Template</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="public/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="public/css/style.css" rel="stylesheet">
    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar start -->
        <div class="container-fluid fixed-top">
            <div class="container topbar bg-primary d-none d-lg-block">
                <div class="d-flex justify-content-between">
                    <div class="top-info ps-2">
                        <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">123 Street, New York</a></small>
                        <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">Email@Example.com</a></small>
                    </div>
                    <div class="top-link pe-2">
                        <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                        <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small>/</a>
                        <a href="#" class="text-white"><small class="text-white ms-2">Sales and Refunds</small></a>
                    </div>
                </div>
            </div>
            <div class="container px-0">
                <nav class="navbar navbar-light bg-white navbar-expand-xl">
                    <a href="index.php" class="navbar-brand"><h1 class="text-primary display-6">Fruitables</h1></a>
                    <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars text-primary"></span>
                    </button>
                    <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                        <div class="navbar-nav mx-auto">
                            <a href="index.php" class="nav-item nav-link">Home</a>
                            <a href="shop.php" class="nav-item nav-link">Shop</a>
                            <a href="shop-detail.php" class="nav-item nav-link">Shop Detail</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Pages</a>
                                <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                    <a href="cart.php" class="dropdown-item">Cart</a>
                                    <a href="chackout.php" class="dropdown-item active">Chackout</a>
                                    <a href="testimonial.php" class="dropdown-item">Testimonial</a>
                                    <a href="404.php" class="dropdown-item">404 Page</a>
                                </div>
                            </div>
                            <a href="contact.php" class="nav-item nav-link">Contact</a>
                        </div>
                        <div class="d-flex m-3 me-0">
                            <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button>
                            <a href="cart.php" class="position-relative me-4 my-auto">
                                <i class="fa fa-shopping-bag fa-2x"></i>
                                <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">3</span>
                            </a>
                            <a href="#" class="my-auto">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Navbar End -->


        <!-- Modal Search Start -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center">
                        <div class="input-group w-75 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Search End -->


        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Checkout</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item active text-white">Checkout</li>
            </ol>
        </div>
        <!-- Single Page Header End -->


        <!-- Checkout Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <h1 class="mb-4">Billing details</h1>
                <form action="chackout.php" method="POST">
                    <div class="row g-5">
                        <div class="col-md-12 col-lg-6 col-xl-7">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">User Name<sup>*</sup></label>
                                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">Gmail<sup>*</sup></label>
                                        <input type="text" class="form-control" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Address <sup>*</sup></label>
                                <input type="text" class="form-control" name="address" placeholder="House Number Street Name">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Phone<sup>*</sup></label>
                                <input type="tel" class="form-control" value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" readonly>
                            </div>
                            <div class="form-item">
                            <textarea name="text" class="form-control my-3" spellcheck="false" cols="30" rows="11" placeholder="Order Notes (Optional)"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-5">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Products</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($selectedProducts)): ?>
                                            <?php 
                                            $totalPrice = 0;
                                            foreach ($selectedProducts as $productDetail): 
                                            ?>
                                                <tr>
                                                    <th scope="row">
                                                        <div class="d-flex align-items-center mt-2">
                                                            <img src="public/images/<?= $productDetail['image'] ?>" class="img-fluid rounded-circle" style="width: 90px; height: 90px;" alt="">
                                                        </div>
                                                    </th>
                                                    <td class="py-5"><?= $productDetail['name'] ?></td>
                                                    <td class="py-5"><?= $productDetail['price'] ?> $</td>
                                                    <td class="py-5"><?= $productDetail['quantity'] ?></td>
                                                    <td class="py-5"><?= $productDetail['price'] * $productDetail['quantity'] ?> $</td>
                                                </tr>
                                                <?php 
                                                    $totalPrice += $productDetail['price'] * $productDetail['quantity'];
                                                ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-5">No products selected for checkout</td>
                                            </tr>
                                        <?php endif; ?>

                                        <!-- Total row remains the same -->
                                        <tr>
                                            <th scope="row"></th>
                                            <td class="py-5">
                                                <p class="mb-0 text-dark text-uppercase py-3">TOTAL</p>
                                            </td>
                                            <td class="py-5"></td>
                                            <td class="py-5"></td>
                                            <td class="py-5">
                                                <div class="py-3 border-bottom border-top">
                                                    <p class="mb-0 text-dark"><?= ($totalPrice) ?> $</p>
                                                    <input type="hidden" class="mb-0 text-dark" name="total_price" value="<?= $totalPrice ?>">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mb-0 text-dark">Phương thức thanh toán</p>
                                <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                    <div class="col-12">
                                        <div class="form-check text-start my-3">
                                            <input type="radio" class="form-check-input bg-primary border-0" id="Transfer-1" name="payment_method" value="credit_card">
                                            <label class="form-check-label" for="Transfer-1">Direct Bank Transfer</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                    <div class="col-12">
                                        <div class="form-check text-start my-3">
                                            <input type="radio" class="form-check-input bg-primary border-0" id="Payments-1" name="payment_method" value="cod">
                                            <label class="form-check-label" for="Payments-1">Thanh toán khi nhận hàng</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                    <div class="col-12">
                                        <div class="form-check text-start my-3">
                                            <input type="radio" class="form-check-input bg-primary border-0" id="Paypal-1" name="payment_method" value="vnpay">
                                            <label class="form-check-label" for="Paypal-1">Paypal</label>
                                        </div>
                                    </div>
                                </div>

                            <tr>
                                <th scope="row">
                                 </th>
                                 <td class="py-5">
                                 <p class="mb-0 text-dark py-4">Shipping</p>
                                        <td colspan="3" class="py-5">
                                            <div class="form-check text-start">
                                                <input type="radio" class="form-check-input bg-primary border-0" id="Shipping-1" name="shipping_method" value="Free Shipping">
                                                <label class="form-check-label" for="Shipping-1">Free Shipping</label>
                                            </div>
                                            <div class="form-check text-start">
                                                <input type="radio" class="form-check-input bg-primary border-0" id="Shipping-2" name="shipping_method" value="Standard shipping">
                                                <label class="form-check-label" for="Shipping-2">Standard shipping</label>
                                            </div>
                                            <div class="form-check text-start">
                                                <input type="radio" class="form-check-input bg-primary border-0" id="Shipping-3" name="shipping_method" value="Express shipping">
                                                <label class="form-check-label" for="Shipping-3">Express shipping</label>
                                            </div>
                                        </td>

                            </tr>
                            <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                            <input type="hidden" name="selected_cart" value='<?= json_encode($selectedProducts) ?>'>
                                <button type="submit" class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary" name="place_order">Place Order</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="mt-5">
                    <form action="apply_voucher.php" method="POST">
                    <label for="voucher_code">Nhập mã giảm giá:</label>
                      <input type="hidden" name="selected_cart" value='<?= json_encode($selectedProducts) ?>'>
                        <input type="text" name="voucher_code" id="voucher_code" class="border-0 border-bottom rounded me-5 py-3 mb-4" placeholder="Coupon Code" style="margin: 0 !important;">
                        <button type="submit" class="btn border-secondary rounded-pill px-4 py-3 text-primary">Apply Coupon</button>
                    </form>
                    <?php if (isset($_SESSION['voucher_success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['voucher_success']; unset($_SESSION['voucher_success']); ?>
                    </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['voucher_error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['voucher_error']; unset($_SESSION['voucher_error']); ?>
                            </div>
                        <?php endif; ?>

                </div>                               
            </div>
        </div>
        <!-- Checkout Page End -->

    
        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
            <div class="container py-5">
                <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <a href="#">
                                <h1 class="text-primary mb-0">Fruitables</h1>
                                <p class="text-secondary mb-0">Fresh products</p>
                            </a>
                        </div>
                        <div class="col-lg-6">
                            <div class="position-relative mx-auto">
                                <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="Your Email">
                                <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Subscribe Now</button>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="d-flex justify-content-end pt-3">
                                <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                                <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Why People Like us!</h4>
                            <p class="mb-4">typesetting, remaining essentially unchanged. It was 
                                popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.</p>
                            <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Shop Info</h4>
                            <a class="btn-link" href="">About Us</a>
                            <a class="btn-link" href="">Contact Us</a>
                            <a class="btn-link" href="">Privacy Policy</a>
                            <a class="btn-link" href="">Terms & Condition</a>
                            <a class="btn-link" href="">Return Policy</a>
                            <a class="btn-link" href="">FAQs & Help</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Account</h4>
                            <a class="btn-link" href="">My Account</a>
                            <a class="btn-link" href="">Shop details</a>
                            <a class="btn-link" href="">Shopping Cart</a>
                            <a class="btn-link" href="">Wishlist</a>
                            <a class="btn-link" href="">Order History</a>
                            <a class="btn-link" href="">International Orders</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Contact</h4>
                            <p>Address: 1429 Netus Rd, NY 48247</p>
                            <p>Email: Example@gmail.com</p>
                            <p>Phone: +0123 4567 8910</p>
                            <p>Payment Accepted</p>
                            <img src="public/images/payment.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>Your Site Name</a>, All right reserved.</span>
                    </div>
                    <div class="col-md-6 my-auto text-center text-md-end text-white">
                        <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                        <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                        <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                        Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a> Distributed By <a class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->



        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Template Javascript -->
    <script src="public/js/main.js"></script>
    <script src="public/js/checkout.js"></script>
    </body>

</html>