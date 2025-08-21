<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    include_once "../../conn_db.php"; 

    if(isset($_POST['cart-btn'])){
        if(!isset($_SESSION['id'])){
            header("Location: ../../Login/login_page.php");
            exit();
        }else{
            $productID = $_POST['product_id'];
            $sql_getProduct = "select * from products where id = $productID";
            $product = queryDatabase($sql_getProduct)[0];
            $sql_addToCart = "INSERT INTO `cart`(`product_id`, `product_name`, `product_image`, `product_price`, `member_id`, `quantity`) 
                            VALUES ($productID,'{$product['name']}','{$product['image']}','{$product['price']}','{$_SESSION['id']}',1)";
            if(executeQuery($sql_addToCart)){
                echo "<script>alert('Thêm sản phẩm thành công vào giỏ hàng');</script>";
                echo "<script>window.location.href = '../detail/product_detail.php?id=$productID';</script>";
                exit();
            } else {
                echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại');</script>";
            }
        }
    }

    function getCartItems($memberID){
        $sql = "SELECT * FROM `cart` WHERE `member_id` = '{$memberID}'";
        return queryDatabase($sql);
    }

    function getTotalCart($memberID){
        $total = 0;
        $products = getCartItems($memberID);
        foreach($products as $product){
            $total += $product['product_price'] * $product['quantity'];
        }
        return $total;
    }

    function updateCartItemQuantity($memberID, $productID, $newQuantity){
        $sql = "UPDATE `cart` SET `quantity` = '{$newQuantity}' WHERE `product_id` = '{$productID}' AND `member_id` = '{$memberID}'";
        return executeQuery($sql);
    }

    function removeCartItem($memberID, $productID){
        $sql = "DELETE FROM `cart` WHERE `product_id` = '{$productID}' AND `member_id` = '{$memberID}'";
        return executeQuery($sql);
    }

    function clearCart($memberID){
        $sql = "DELETE FROM `cart` WHERE `member_id` = '{$memberID}'";
        return executeQuery($sql);
    }

    function getCartItemQuantity($memberID, $productID){
        $sql = "SELECT `quantity` FROM `cart` WHERE `member_id` = '{$memberID}' AND `product_id` = '{$productID}'";
        return queryDatabase($sql)[0]['quantity'];
    }

    function increaseCartItemQuantity($memberID, $productID) {
        $currentQuantity = getCartItemQuantity($memberID, $productID); // Lấy số lượng hiện tại
        updateCartItemQuantity($memberID, $productID, $currentQuantity + 1);
    }
    
    function decreaseCartItemQuantity($memberID, $productID) {
        $currentQuantity = getCartItemQuantity($memberID, $productID);
        if ($currentQuantity > 1) { 
            updateCartItemQuantity($memberID, $productID, $currentQuantity - 1);
        }
    }
?>