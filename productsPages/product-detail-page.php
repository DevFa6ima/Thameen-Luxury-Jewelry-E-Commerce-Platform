<?php
session_start();
// Check if a session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../include/header.php');
include('../include/connection.php');

// Function to add a product to the cart
function addToCart($id, $quantity)
{
    // Check if the cart session variable is not set, initialize it as an empty array
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Fetch product details by ID
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $GLOBALS['conn']->query($sql);
    $product = $result->fetch_assoc();

    // Check if the product exists and if details are fetched successfully
    if ($product) {
        // Check if the quantity exceeds the available stock
        if ($quantity > $product['stock']) {
            return "The quantity exceeds the available stock for this product.";
        }

        // Check if the product is already in the cart
        if (isset($_SESSION['cart'][$id])) {
            // Update the quantity in the cart with the new value
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            // Add the new product to the cart
            $_SESSION['cart'][$id] = array(
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            );
        }
        return "Item added to cart successfully!";
    } else {
        return "Product not found or details could not be fetched.";
    }
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['quantity'])) {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $message = addToCart($id, $quantity);
    
    // If item was successfully added to cart, redirect to products page
    if (empty($message) || strpos($message, 'successfully') !== false) {
        header("Location: products-page.php");
        exit();
    }
}

// Check if the product ID is set in the URL
if (isset($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch the details of the product with the given ID
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result === false) {
        echo "Error executing query: " . $conn->error;
    } else {
        // Check if the product exists
        if ($result->num_rows > 0) {
            // Fetch the product details
            $product = $result->fetch_assoc();
?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Product Detail - <?php echo $product['name']; ?></title>
                <link rel="stylesheet" href="../css/global.css">
                <link rel="stylesheet" href="product-detail-styles.css">
                <?php include("../include/fonts.html"); ?>
            </head>

            <body>
                <!-- Product Details -->
                <div class="product-detail-container">
                    <div class="product-detail-info">
                        <div class="product-image">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['picture']); ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <h2><?php echo $product['name']; ?></h2>
                        <div class="product-details">
                            <p>Description: <span style="font-weight: lighter"><?php echo $product['description']; ?></span></p>
                            <p>Price: <span style="font-weight: lighter"><?php echo $product['price']; ?> SAR</span></p>
                            
                            <!-- Display success/error message -->
                            <?php if (!empty($message)): ?>
                                <div class="cart-message" style="padding: 10px; margin: 10px 0; border-radius: 5px; <?php echo (strpos($message, 'successfully') !== false) ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'; ?>">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Add to Cart button -->
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return validateQuantity(); setTimeout(function() { document.dispatchEvent(new CustomEvent('cartUpdated')); }, 100);" >
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <div class="quantity-input">
                                    <lable>Quantity: <input type="number" id="quantity" name="quantity" min="1" value="1" step="1" required></lablel>
                                </div>
                                <div class="add-to-cart-input">
                                    <button type="submit" class="btn-secondary">Add to Cart</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    function validateQuantity() {
                        var quantityInput = document.getElementById('quantity');
                        var quantity = parseInt(quantityInput.value);
                        var maxStock = <?php echo $product['stock']; ?>;
                        if (isNaN(quantity) || quantity <= 0 || quantity > maxStock) {
                            alert('Only ' + maxStock + ' items are available. Please update your quantity.');
                            return false;
                        }
                        return true;
                    }
                </script>
            </body>

            </html>
<?php
        } else {
            echo "Product not found.";
        }
    }
} else {
    echo "Product ID not provided.";
}

// Close the database connection
$conn->close();

include('../include/footer.php');
?>
