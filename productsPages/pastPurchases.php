<?php 
include('../include/connection.php');
include('../include/header.php'); 

// Start the session
$haveOrdered = false;

// Handle clear all past purchases
if(isset($_POST['clear_all_purchases'])) {
    // Delete the past purchases cookie
    setcookie('past_purchases', '', time() - 3600, '/');
    // Redirect to the same page to refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Check if the past purchases cookie is set
if(isset($_COOKIE['past_purchases'])) {
    // Retrieve past purchases from cookie
    $pastPurchasesSerialized = $_COOKIE['past_purchases'];
    $pastPurchases = unserialize($pastPurchasesSerialized);
        $haveOrdered = true;

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THAMEEN- Past Purchases</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="products-style.css">
    <?php include("../include/fonts.html"); ?>
</head>
<body>
    <div class="containerp">
<?php if (!$haveOrdered): ?>
    <h1>No past purchases available.</h1>
<?php else: ?>
        <h1>Past Purchases</h1>
        <br>
        <?php 
        // Group purchases by order number
        $orders = array();
        foreach($pastPurchases as $purchase) {
            $orderNum = isset($purchase['order_number']) ? $purchase['order_number'] : 1;
            if (!isset($orders[$orderNum])) {
                $orders[$orderNum] = array(
                    'order_number' => $orderNum,
                    'order_date' => isset($purchase['order_date']) ? $purchase['order_date'] : 'Unknown Date',
                    'order_time' => isset($purchase['order_time']) ? $purchase['order_time'] : 'Unknown Time',
                    'products' => array()
                );
            }
            $orders[$orderNum]['products'][] = $purchase;
        }
        
        // Sort orders by order number (newest first)
        krsort($orders);
        
        // Display each order
        foreach($orders as $order): 
            $totalPrice = 0;
        ?>
        
        <table class="table-margin">
            <thead>
                <tr>
                    <th colspan="2">Order Information</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-left">
                        <strong>Number: #</strong><?= $order['order_number']; ?><br><br>
                        <strong>Date: </strong><?= $order['order_date']; ?><br><br>
                        <strong>Time: </strong><?= $order['order_time']; ?>
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan="2">Purchased Products</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($order['products'] as $purchase) {
                    // Check if 'id' key exists and is not empty
                    if (!isset($purchase['id']) || empty($purchase['id'])) {
                        // Skip this purchase if no valid ID
                        continue;
                    }
                    
                    $id = intval($purchase['id']); // Ensure it's an integer
                    $selectAllProduct_query = "SELECT picture FROM products WHERE id = $id";
                    $result = mysqli_query($conn, $selectAllProduct_query);
                    
                    // Check if the query was successful
                    if ($result && mysqli_num_rows($result) > 0) {
                        $product = mysqli_fetch_assoc($result);
                        $purchase['picture'] = $product['picture'];
                    } else {
                        $purchase['picture'] = '';
                    }
                ?>
                <tr>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($purchase['picture']); ?>" alt="<?php echo $purchase['product_name']; ?>"></td>
                    <td class="text-left">
                        <strong>Name: </strong><?= isset($purchase['product_name']) ? $purchase['product_name'] : ''; ?><br><br>
                        <strong>Quantity: </strong><?= isset($purchase['quantity']) ? $purchase['quantity'] : ''; ?><br><br>
                        <strong>Price: </strong><?= isset($purchase['price']) ? $purchase['price'] . " SAR" : ''; ?>
                    </td>
                </tr>
                <!-- Update total price -->
                <?php 
                    if (isset($purchase['quantity']) && isset($purchase['price'])) {
                        $totalPrice += $purchase['quantity'] * $purchase['price'];
                    }
                ?>
                <?php } ?>
                <tr>
                    <th><strong>Total Price</strong></th>
                    <td colspan="2" class="total-cell"><?php echo $totalPrice; ?> SAR</td>
                </tr>
            </tbody>
        </table>
        
        <?php endforeach; ?>
        
        <!-- Clear All Past Purchases Button -->
        <div class="center-margin">
            <form method="post" action="" onsubmit="return confirm('Are you sure you want to clear all past purchases? This action cannot be undone.');">
                <button type="submit" name="clear_all_purchases" class="btn-primary">Clear All Past Purchases</button>
            </form>
        </div>
        
<?php endif; ?>
    </div>
<?php include('../include/footer.php'); ?>

</body>

</html>
