<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Cart';

// Fetch cart items
$cart_items = [];
$total = 0;

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "
        SELECT c.product_id, c.quantity, p.name, p.price, p.image
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.id
    ");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total += $row['subtotal'];
        $cart_items[] = $row;
    }
} else {
    // Guest cart from session
    if (isset($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart'])) {
        foreach ($_SESSION['guest_cart'] as $product_id => $quantity) {
            $stmt = mysqli_prepare($conn, "SELECT id, name, price, image FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($product = mysqli_fetch_assoc($result)) {
                $product['product_id'] = $product['id'];
                $product['quantity'] = $quantity;
                $product['subtotal'] = $product['price'] * $quantity;
                $total += $product['subtotal'];
                $cart_items[] = $product;
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="cart-container">
    <h1>Shopping Cart</h1>

    <?php if (empty($cart_items)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🛒</div>
            <h2>Your Cart is Empty</h2>
            <p>Looks like you haven't added any ice cream yet. Browse our menu and treat yourself!</p>
            <br>
            <a href="menu.php" class="btn btn-primary btn-lg">Browse Menu</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-product">
                                <img src="<?php echo sanitize($item['image']); ?>" alt="<?php echo sanitize($item['name']); ?>">
                                <span class="cart-product-name"><?php echo sanitize($item['name']); ?></span>
                            </div>
                        </td>
                        <td><?php echo formatPrice($item['price']); ?></td>
                        <td>
                            <div class="cart-qty">
                                <form action="cart_action.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="qty-btn">-</button>
                                </form>
                                <span class="qty-value"><?php echo $item['quantity']; ?></span>
                                <form action="cart_action.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="qty-btn">+</button>
                                </form>
                            </div>
                        </td>
                        <td style="font-weight:600; color:var(--primary);"><?php echo formatPrice($item['subtotal']); ?></td>
                        <td>
                            <form action="cart_action.php" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <button type="submit" class="cart-remove" title="Remove">✕</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span><?php echo formatPrice($total); ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery Charge:</span>
                <span>₹0.00 (Free)</span>
            </div>
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span style="color:var(--primary);"><?php echo formatPrice($total); ?></span>
            </div>
            <?php if (!isLoggedIn()): ?>
                <div class="form-error" style="margin-top:16px;">
                    Please <a href="login.php" style="color:var(--error); font-weight:600;">login</a> to proceed to checkout.
                </div>
            <?php else: ?>
                <a href="checkout.php" class="btn btn-primary btn-block" style="margin-top:16px;">Proceed to Checkout</a>
            <?php endif; ?>
            <a href="menu.php" class="btn btn-outline btn-block" style="margin-top:12px;">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
