<?php
session_start();
require_once 'config/database.php';

// Check if it's an AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['productId'];
    $newQuantity = $_POST['quantity'];
    
    // Validate input
    if (isset($_SESSION['cart'][$productId])) {
        // Update quantity in session
        $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
        
        // Optional: Update database or perform additional logic
        
        // Send success response
        echo json_encode(['status' => 'success']);
    } else {
        // Product not in cart
        echo json_encode(['status' => 'error', 'message' => 'Product not found in cart']);
    }
    exit;
}
?>