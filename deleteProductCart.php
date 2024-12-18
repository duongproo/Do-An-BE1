<?php 
           require_once 'config/database.php';
           require_once 'app/models/Product.php';
           require_once 'app/models/Category.php';
           session_start();
           if(isset($_GET['id']))
           {
            $productId=$_GET['id'];
                if(isset($_SESSION['cart'][$productId]))
                {
                    unset($_SESSION['cart'][$productId]);
                    header("Location: cart.php");
                    exit();
                }
           }
           header("Location: cart.php");
           exit();

?>