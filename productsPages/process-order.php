<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../include/connection.php'); 
include("../include/fonts.html"); 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

    $existingPurchases = array();
    if (isset($_COOKIE['past_purchases'])) {
        $existingPurchases = unserialize($_COOKIE['past_purchases']);
    }

    $orderNumber = 1;
    if (!empty($existingPurchases)) {
        $lastOrder = end($existingPurchases);
        if (isset($lastOrder['order_number'])) {
            $orderNumber = $lastOrder['order_number'] + 1;
        }
    }

    $orderDate = date('Y-m-d');
    $orderTime = date('H:i:s');

    $pastPurchases = array();

    foreach ($cart as $id => $item) {
        $quantity = $item['quantity'];
        
        $sql = "SELECT * FROM products WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        $product = mysqli_fetch_assoc($result);

        $pastPurchases[] = array(
            'product_name' => $product['name'],
            'id' => $id,
            'quantity' => $quantity,
            'price' => $product['price'],
            'order_number' => $orderNumber,
            'order_date' => $orderDate,
            'order_time' => $orderTime
        );

        $newStock = $product['stock'] - $quantity;
        
        $updateSql = "UPDATE products SET stock = $newStock WHERE id = $id";
        mysqli_query($conn, $updateSql);
    }

    $allPurchases = array_merge($existingPurchases, $pastPurchases);

    $pastPurchasesSerialized = serialize($allPurchases);

    setcookie('past_purchases', $pastPurchasesSerialized, time() + (86400 * 30), '/'); // Cookie valid for 30 days

    unset($_SESSION['cart']);
} else {
    header("Location: checkout-page.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Thameen</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="process-order.css">
</head>
<body>
<?php include('../include/header.php'); ?>
    <div class="containerp">
<?php
    echo "<h1>Thank You, $name!</h1>";
    echo "<br>";

    echo "<div class='order-details'>";
    echo "<h2>Order Details</h2>";
    echo "<p>Name: $name</p>";
    echo "<p>Email: $email</p>";
    echo "<p>Address: $address</p>";

    echo "<h2>Product Details</h2>";
    echo "<ul>";
    foreach ($pastPurchases as $purchase) {
        echo "<li>{$purchase['quantity']} x {$purchase['product_name']} - {$purchase['price']} SAR</li>";
    }
    echo "</ul>";

    echo "</div>";
?>
    </div>
<?php include('../include/footer.php'); ?>
</body>
</html>
