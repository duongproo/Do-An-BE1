<?php 
     require_once 'config/database.php';
     require_once 'app/models/Product.php';
     require_once 'app/models/Category.php';
     session_start();
     if(isset($_GET['id']))
     {
        $productId=$_GET['id'];
        $productModel=new Product();
        $product=$productModel->getProductById($productId);
        if(!isset($_SESSION['cart'][$productId]))
        {
            $_SESSION['cart'][$productId]=[
                'name'=>$product['name'],
                'price'=>$product['price'],
                'description'=>$product['description'],
                'image'=>$product['image'],
                'quantity'=>1,                

            ];
        }
        else {
            $_SESSION['cart'][$productId]['quantity']++;
        }
     }
     header("Location: index.php");
     exit();

?>
