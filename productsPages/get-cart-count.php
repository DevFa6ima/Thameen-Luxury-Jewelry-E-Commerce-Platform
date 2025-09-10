<?php
session_start();

// Include necessary files
include('../include/connection.php');

// Define the getProductDetails function only if it is not already defined
if (!function_exists('getProductDetails')) {
    function getProductDetails($id) {
        global $conn;
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

// Function to get the total number of items in the cart
function getCartItemCount()
{
    if (isset($_SESSION['cart'])) {
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $item) {
            // Fetch the product details from the database
            $product = getProductDetails($id) ? getProductDetails($id) : null;
            if ($product) {
                // Calculate total quantity from the session
                $total += $item['quantity'];
            }
        }
        return $total;
    } else {
        return 0;
    }
}

// Set content type to JSON
header('Content-Type: application/json');

// Return the cart count as JSON
echo json_encode(['count' => getCartItemCount()]);
?>
