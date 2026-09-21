<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Checkout';

// Must be logged in to checkout
if (!isLoggedIn()) {
    setAlert('Please login to checkout.', 'error');
    redirect('login.php');
}

// Fetch cart items
$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "
    SELECT c.product_id, c.quantity, p.name, p.price, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$total = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}

// Redirect if cart is empty
if (empty($cart_items)) {
    setAlert('Your cart is empty.', 'error');
    redirect('cart.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = sanitize($_POST['customer_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $mobile = sanitize($_POST['mobile'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $pincode = sanitize($_POST['pincode'] ?? '');

    if (empty($customer_name) || empty($email) || empty($mobile) || empty($address) || empty($city) || empty($pincode)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $error = 'Mobile number must be 10 digits.';
    } elseif (!preg_match('/^[0-9]{6}$/', $pincode)) {
        $error = 'Pincode must be 6 digits.';
    } else {
        // Save checkout info in session and proceed to payment
        $_SESSION['checkout'] = [
            'customer_name' => $customer_name,
            'email' => $email,
            'mobile' => $mobile,
            'address' => $address,
            'city' => $city,
            'pincode' => $pincode,
            'total' => $total,
        ];
        redirect('payment.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="checkout-container">
    <h1>Checkout</h1>

    <?php if ($error): ?>
        <div class="form-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="form-card" style="box-shadow:var(--shadow-sm); padding:32px;">
            <h3 style="margin-bottom:24px; color:var(--neutral-800);">Delivery Details</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="customer_name">Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" required value="<?php echo sanitize($_SESSION['user_name']); ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required value="<?php echo sanitize($_SESSION['user_email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="mobile">Mobile Number</label>
                        <input type="tel" id="mobile" name="mobile" required maxlength="10" pattern="[0-9]{10}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="pincode">Pincode</label>
                        <input type="text" id="pincode" name="pincode" required maxlength="6" pattern="[0-9]{6}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Proceed to Payment</button>
            </form>
        </div>

        <div class="order-summary-box">
            <h3>Order Summary</h3>
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <span><?php echo sanitize($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                    <span style="font-weight:600;"><?php echo formatPrice($item['subtotal']); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row" style="margin-top:16px;">
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
