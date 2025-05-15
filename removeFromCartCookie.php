<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    if (isset($_COOKIE['cart_items'])) {
        $cart_items = json_decode($_COOKIE['cart_items'], true);
        
        if (isset($cart_items[$product_id])) {
            unset($cart_items[$product_id]);
            setcookie('cart_items', json_encode($cart_items), time() + (86400 * 30), "/");
            $_SESSION['success_message'] = "Article retiré du panier";
        }
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>