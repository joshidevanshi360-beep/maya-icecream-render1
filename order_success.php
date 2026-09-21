<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Order Success';

// Must have order_id in session
if (!isset($_SESSION['order_id'])) {
    redirect('index.php');
}

$order_id = (int)$_SESSION['order_id'];
unset($_SESSION['order_id']);

// Fetch order details
$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if (!($order = mysqli_fetch_assoc($result))) {
    redirect('index.php');
}

// Fetch order items
$items_stmt = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

$items_list = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $items_list[] = $item;
}

// Fetch payment
$pay_stmt = mysqli_prepare($conn, "SELECT * FROM payments WHERE order_id = ?");
$pay_stmt->bind_param("i", $order_id);
$pay_stmt->execute();
$payment = mysqli_fetch_assoc($pay_stmt->get_result());

require_once __DIR__ . '/includes/header.php';
?>

<div class="success-page">
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h1>Order Placed Successfully!</h1>
        <p class="subtitle">Thank you for your order. Your ice cream is on its way!</p>

        <div class="order-details">
            <div class="order-detail-row">
                <span class="order-detail-label">Order ID:</span>
                <span class="order-detail-value">#<?php echo $order['id']; ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Customer Name:</span>
                <span class="order-detail-value"><?php echo sanitize($order['customer_name']); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Products:</span>
                <span class="order-detail-value" style="text-align:right;">
                    <?php foreach ($items_list as $item): ?>
                        <?php echo sanitize($item['product_name']); ?> × <?php echo $item['quantity']; ?><br>
                    <?php endforeach; ?>
                </span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Total Amount:</span>
                <span class="order-detail-value" style="color:var(--primary);"><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Payment Method:</span>
                <span class="order-detail-value"><?php echo sanitize($order['payment_method']); ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Payment Status:</span>
                <span class="order-detail-value" style="color:var(--success);"><?php echo $payment ? sanitize($payment['payment_status']) : 'Success'; ?></span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Order Status:</span>
                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>"><?php echo sanitize($order['status']); ?></span>
            </div>
        </div>

        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="menu.php" class="btn btn-primary">Continue Shopping</a>
            <a href="my_orders.php" class="btn btn-outline">View Orders</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
