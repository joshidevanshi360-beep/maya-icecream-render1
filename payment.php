<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Payment';

// Must be logged in and have checkout data
if (!isLoggedIn() || !isset($_SESSION['checkout'])) {
    setAlert('Please complete the checkout process first.', 'error');
    redirect('cart.php');
}

$checkout = $_SESSION['checkout'];
$total = $checkout['total'];
$user_id = $_SESSION['user_id'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = sanitize($_POST['payment_method'] ?? '');

    if (empty($payment_method)) {
        $error = 'Please select a payment method.';
    } else {
        // Fetch cart items for the order
        $stmt = mysqli_prepare($conn, "
            SELECT c.product_id, c.quantity, p.name, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $cart_items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $cart_items[] = $row;
        }

        if (empty($cart_items)) {
            setAlert('Your cart is empty.', 'error');
            redirect('cart.php');
        }

        // Create the order
        $stmt = mysqli_prepare($conn, "
            INSERT INTO orders (user_id, customer_name, email, mobile, address, city, pincode, total_amount, payment_method, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
        ");
        $stmt->bind_param("issssssss",
            $user_id,
            $checkout['customer_name'],
            $checkout['email'],
            $checkout['mobile'],
            $checkout['address'],
            $checkout['city'],
            $checkout['pincode'],
            $total,
            $payment_method
        );

        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($conn);

            // Insert order items
            foreach ($cart_items as $item) {
                $item_stmt = mysqli_prepare($conn, "
                    INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $item_stmt->bind_param("iisss",
                    $order_id,
                    $item['product_id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                );
                mysqli_stmt_execute($item_stmt);
            }

            // Simulate payment (always succeeds for this project)
            $pay_stmt = mysqli_prepare($conn, "
                INSERT INTO payments (order_id, payment_method, amount, payment_status)
                VALUES (?, ?, ?, 'Success')
            ");
            $pay_stmt->bind_param("iss", $order_id, $payment_method, $total);
            mysqli_stmt_execute($pay_stmt);

            // Clear the cart
            $clear_stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
            $clear_stmt->bind_param("i", $user_id);
            $clear_stmt->execute();

            // Clear checkout session
            unset($_SESSION['checkout']);

            // Redirect to success page
            $_SESSION['order_id'] = $order_id;
            redirect('order_success.php');
        } else {
            $error = 'Failed to place order. Please try again.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="checkout-container">
    <h1>Payment</h1>

    <?php if ($error): ?>
        <div class="form-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="form-card" style="box-shadow:var(--shadow-sm); padding:32px;">
            <h3 style="margin-bottom:24px; color:var(--neutral-800);">Select Payment Method</h3>
            <p style="color:var(--neutral-500); margin-bottom:20px; font-size:0.9rem;">
                This is a simulated payment for the college project. No real payment will be processed.
            </p>
            <form method="POST" action="">
                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Cash on Delivery" required>
                        <span class="payment-icon">💵</span>
                        <div class="payment-label">
                            <h4>Cash on Delivery</h4>
                            <p>Pay with cash when your order arrives</p>
                        </div>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="UPI" required>
                        <span class="payment-icon">📱</span>
                        <div class="payment-label">
                            <h4>UPI Payment</h4>
                            <p>Pay using Google Pay, PhonePe, Paytm, etc.</p>
                        </div>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Credit/Debit Card" required>
                        <span class="payment-icon">💳</span>
                        <div class="payment-label">
                            <h4>Credit / Debit Card</h4>
                            <p>Pay securely with Visa, Mastercard, or RuPay</p>
                        </div>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">Place Order & Pay</button>
            </form>
        </div>

        <div class="order-summary-box">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Customer:</span>
                <span style="font-weight:600;"><?php echo sanitize($checkout['customer_name']); ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery to:</span>
                <span style="font-weight:600;"><?php echo sanitize($checkout['city']); ?> - <?php echo sanitize($checkout['pincode']); ?></span>
            </div>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span><?php echo formatPrice($total); ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery:</span>
                <span>FREE</span>
            </div>
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span style="color:var(--primary);"><?php echo formatPrice($total); ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
