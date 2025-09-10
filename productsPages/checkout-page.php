<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../include/header.php');
include('../include/connection.php');

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

function calculateTotal()
{
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        $product = getProductDetails($id);
        if ($product) {
            $subtotal = $product['price'] * $item['quantity'];
            $total += $subtotal;
        }
    }
    return $total;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="checkout-page.css">
    <script src="validateForm.js"></script>
    <script>
        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        function deleteCookie(name) {
            document.cookie = name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        }

        function saveFormData() {
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                address: document.getElementById('address').value,
                cardNumber: document.getElementById('cardNumber').value,
                expirationDate: document.getElementById('expirationDate').value,
                cvv: document.getElementById('cvv').value,
                billingAddress: document.getElementById('billingAddress').value
            };
            
            setCookie('checkoutFormData', JSON.stringify(formData), 30);
        }

        function loadFormData() {
            const savedData = getCookie('checkoutFormData');
            if (savedData) {
                try {
                    const formData = JSON.parse(savedData);
                    
                    document.getElementById('name').value = formData.name || '';
                    document.getElementById('email').value = formData.email || '';
                    document.getElementById('address').value = formData.address || '';
                    document.getElementById('cardNumber').value = formData.cardNumber || '';
                    document.getElementById('expirationDate').value = formData.expirationDate || '';
                    document.getElementById('cvv').value = formData.cvv || '';
                    document.getElementById('billingAddress').value = formData.billingAddress || '';
                } catch (e) {
                }
            }
        }

        function clearSavedData() {
            deleteCookie('checkoutFormData');
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('address').value = '';
            document.getElementById('cardNumber').value = '';
            document.getElementById('expirationDate').value = '';
            document.getElementById('cvv').value = '';
            document.getElementById('billingAddress').value = '';
            
            showNotification('Saved data cleared successfully!', 'success');
        }


        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = message;
            
            const bgColor = type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${bgColor};
                color: white;
                padding: 12px 20px;
                border-radius: 5px;
                z-index: 1000;
                font-size: 14px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                animation: slideIn 0.3s ease-out;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        document.addEventListener('DOMContentLoaded', function() {
            loadFormData();
            
            let saveTimeout;
            const formFields = ['name', 'email', 'address', 'cardNumber', 'expirationDate', 'cvv', 'billingAddress'];
            
            formFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', function() {
                        clearTimeout(saveTimeout);
                        saveTimeout = setTimeout(saveFormData, 1000); // Save after 1 second of no typing
                    });
                }
            });
        });
    </script>
    <?php include("../include/fonts.html"); ?>
</head>

<body>
<div class="container-between-header-footer">
    <!-- Cart Summary -->
    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <!-- Display cart items and total here -->
        <?php
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $id => $item) {
                $product = getProductDetails($id);
                if ($product) {
                    echo "<div class='cart-item'> \n";
                    echo "<span>" . $product['name'] . "</span> \n";
                    echo "<span>Quantity: " . $item['quantity'] . "</span> \n ";
                    echo "<span>Price: " . $product['price'] * $item['quantity'] . "SAR </span> \n";
                    echo "</div>";
                }
            }
            echo "<div class='total'>Total Amount:" . calculateTotal() . "SAR </div>";
        } else {
            echo "<p > No items in the cart.</p>";
        }
        ?>
    </div>

    <!-- Checkout Form -->
    <div class="checkout-form">
        <h2>Enter Your Information</h2>
        <form id="checkoutForm" action="process-order.php" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" required></textarea>
            </div>
            <!-- Payment Information -->
            <h2>Payment Information</h2>
            <div class="form-group">
                <label for="cardNumber">Card Number:</label>
                <input type="text" id="cardNumber" name="cardNumber" required>
            </div>
            <div class="form-group">
                <label for="expirationDate">Expiration Date:</label>
                <input type="text" id="expirationDate" name="expirationDate" placeholder="MM/YY" required>
            </div>
            <div class="form-group">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" required>
            </div>
            <div class="form-group">
                <label for="billingAddress">Billing Address:</label>
                <textarea id="billingAddress" name="billingAddress" required></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-secondary">Buy</button>
                <button type="button" class="btn-primary" onclick="clearSavedData()">Clear Saved Data</button>
            </div>
        </form>
    </div>
</div>

    <?php include('../include/footer.php'); ?>

</body>
</html>