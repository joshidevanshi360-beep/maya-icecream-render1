<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'My Orders';

if (!isLoggedIn()) {
    setAlert('Please login to view your orders.', 'error');
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT o.*, p.payment_status
    FROM orders o
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="orders-container">
    <h1>My Orders</h1>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <h2>No Orders Yet</h2>
            <p>You haven't placed any orders yet. Start shopping for your favorite ice cream!</p>
            <br>
            <a href="menu.php" class="btn btn-primary btn-lg">Browse Menu</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <span class="order-id">Order #<?php echo $order['id']; ?></span>
                        <span class="order-date" style="margin-left:16px;">
                            <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?>
                        </span>
                    </div>
                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                        <?php echo sanitize($order['status']); ?>
                    </span>
                </div>

                <?php
                $items_stmt = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
                $items_stmt->bind_param("i", $order['id']);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                $item_names = [];
                while ($item = mysqli_fetch_assoc($items_result)) {
                    $item_names[] = sanitize($item['product_name']) . ' × ' . $item['quantity'];
                }
                ?>
                <div class="order-card-items">
                    <?php echo implode(', ', $item_names); ?>
                </div>

                <div class="order-card-footer">
                    <div>
                        <small style="color:var(--neutral-400);">Payment:</small>
                        <span style="font-weight:600; margin-left:8px;"><?php echo sanitize($order['payment_method']); ?></span>
                    </div>
                    <span class="order-total"><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

