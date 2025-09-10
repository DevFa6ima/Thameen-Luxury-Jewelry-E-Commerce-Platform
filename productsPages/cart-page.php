<?php
session_start();

include('../include/header.php');
include('../include/connection.php');

if (!function_exists('getProductDetails')) {
    function getProductDetails($id)
    {
        global $conn;
        $sql = "SELECT * FROM products WHERE id = $id";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            return null;
        }
    }
}

function addToCart($id, $name, $price, $quantity)
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    $product = getProductDetails($id);

    if ($product) {
        if ($quantity > $product['stock']) {
            echo "The quantity exceeds the available stock for this product.";
            return; // Exit the function if quantity exceeds stock
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$id] = array(
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            );
        }
    } else {
        echo "Product not found or details could not be fetched.";
    }
}

function removeFromCart($id)
{
    if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$id])) {

        // Remove the item from the cart
        unset($_SESSION['cart'][$id]);
        header("Location: cart-page.php");
    }
}

// Function to calculate total
function calculateTotal()
{
    if (isset($_SESSION['cart'])) {
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $item) {
            // Fetch the product details from the database
            $product = getProductDetails($id);
            if ($product) {
                // Calculate subtotal using the fetched price and quantity from the session
                $subtotal = $product['price'] * $item['quantity'];
                $total += $subtotal;
            }
        }
        return $total;
    } else {
        return 0;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['quantity'])) {
    $id = $_POST['id']; // Product ID
    $quantity = $_POST['quantity']; // Quantity input by the user
    // Update the quantity in the cart with the new value
    $_SESSION['cart'][$id]['quantity'] = $quantity;
}

// Check if the remove form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_id'])) {
    // Remove the item from the cart
    removeFromCart($_POST['remove_id']);
}

// Check if the empty cart button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['empty_cart'])) {
    // Clear the session cart array
    $_SESSION['cart'] = array();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Page</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="cart-style.css">
    <?php include("../include/fonts.html"); ?>
    <script>
        // Function to validate quantity input
        function validateQuantity() {
            var quantityInput = document.getElementById('quantity');
            var quantity = parseInt(quantityInput.value);
            var maxStock = parseInt(quantityInput.getAttribute('data-max-stock'));

            if (isNaN(quantity) || quantity <= 0 || quantity > maxStock) {
                alert('Please enter a valid quantity within the available stock.');
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <h1>Your Cart</h1>
    <?php else: ?>
        <h1>No items in the cart.</h1>
    <?php endif; ?>
    <div class="cart-items">
        <?php
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            $counter = 0;
            foreach ($_SESSION['cart'] as $id => $item) {
                $product = getProductDetails($id);
                if ($product) {
                    if ($counter % 2 == 0) {
                        echo "<div class='row'>"; // Start a new row
                    }
        ?>
                    <div class="product-details">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($product['picture']); ?>" alt="<?php echo $product['name']; ?>">
                        <h2><?php echo $product['name']; ?></h2>
                        <div class="product-info">
                            <p>Price: <?php echo $product['price']; ?> SAR</p>
                            <!-- Form to modify quantity -->
                             <p>Quantity:
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="quantity-update-form" onsubmit="return validateQuantity();" onsubmit="this.addEventListener('submit', function() { setTimeout(function() { document.dispatchEvent(new CustomEvent('cartUpdated')); }, 100); });">
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="quantity" id="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $product['stock']; ?>" data-max-stock="<?php echo $product['stock']; ?>" onchange="this.form.submit(); setTimeout(function() { document.dispatchEvent(new CustomEvent('cartUpdated')); }, 100);">
                            </form>
                            </p>
                            <p>Total Price: <?php echo $item['quantity'] * $product['price']; ?> SAR</p>

                            <!-- Form to remove item from cart -->
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="setTimeout(function() { document.dispatchEvent(new CustomEvent('cartUpdated')); }, 100);">
                                <input type="hidden" name="remove_id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn-primary" onclick="return confirmDelete('<?= $product['name']; ?>');">Remove</button>
                            </form>
                        </div>
                    </div>
        <?php
                    $counter++; // Increment the counter
                    if ($counter % 2 == 0) {
                        echo "</div>"; // End the row after two products
                    }
                }
            }
            if ($counter % 2 != 0) {
                echo "</div>";
            }
        }
        ?>
    </div>

    <!-- Total section - only show when cart has items -->
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
    <div class="total-section">
        <h2>Grand Total: <?php echo calculateTotal(); ?> SAR</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="setTimeout(function() { document.dispatchEvent(new CustomEvent('cartUpdated')); }, 100);">
            <button type="submit" name="empty_cart" class="btn-primary"  onclick="return confirmEmpty();">Empty Cart</button>
        </form>
        <a href="checkout-page.php" class="btn-primary">Proceed to Checkout</a>
 
    </div>
    <?php endif; ?>
    <!-- JavaScript function to show a confirmation dialog before deleting a product -->
   <script type="text/javascript">
        function confirmDelete(productName) {
            return confirm("Are you sure you want to delete " + productName + "?");
        }
    </script> 
       <script type="text/javascript">
        function confirmEmpty() {
            return confirm("Are you sure you want to empty your cart ?");
        }
    </script> 
</body>

</html>

<?php
include('../include/footer.php');

// Flush output buffer and send the output to the browser
ob_end_flush();
?>
