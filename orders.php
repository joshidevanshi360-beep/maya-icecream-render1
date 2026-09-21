<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$message = '';
$message_type = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['status'] ?? '');

    $valid_statuses = ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];
    if (in_array($status, $valid_statuses)) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Order status updated to "' . $status . '".';
            $message_type = 'success';
        } else {
            $message = 'Failed to update order status.';
            $message_type = 'error';
        }
    }
}

// Fetch all orders with items
$orders_result = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC");

$statuses = ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-header">
        <h1>🍨 Maya Ice Cream - Admin Panel</h1>
        <div>
            <span style="color:var(--neutral-400);">Welcome, <?php echo sanitize($_SESSION['admin_username']); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <nav class="admin-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php" class="active">Orders</a>
        <a href="../index.php" target="_blank">View Website</a>
    </nav>

    <div class="admin-container">
        <h2>Customer Orders</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($orders_result) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                <?php
                // Fetch order items
                $items_stmt = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
                $items_stmt->bind_param("i", $order['id']);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                $items = [];
                while ($item = mysqli_fetch_assoc($items_result)) {
                    $items[] = $item;
                }
                ?>
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

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; color:var(--neutral-600); font-size:0.9rem;">
                        <div>
                            <strong>Customer:</strong> <?php echo sanitize($order['customer_name']); ?><br>
                            <strong>Email:</strong> <?php echo sanitize($order['email']); ?><br>
                            <strong>Mobile:</strong> <?php echo sanitize($order['mobile']); ?>
                        </div>
                        <div>
                            <strong>Address:</strong> <?php echo sanitize($order['address']); ?><br>
                            <strong>City:</strong> <?php echo sanitize($order['city']); ?> - <?php echo sanitize($order['pincode']); ?><br>
                            <strong>Payment:</strong> <?php echo sanitize($order['payment_method']); ?>
                        </div>
                    </div>

                    <div class="order-card-items">
                        <?php foreach ($items as $item): ?>
                            <?php echo sanitize($item['product_name']); ?> × <?php echo $item['quantity']; ?>
                            (<?php echo formatPrice($item['price']); ?>)
                            <br>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-card-footer">
                        <form method="POST" action="" style="display:flex; gap:8px; align-items:center;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" style="padding:8px 12px; border-radius:var(--radius-sm); border:1px solid var(--neutral-300); font-family:'Poppins',sans-serif;">
                                <?php foreach ($statuses as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo ($order['status'] === $s) ? 'selected' : ''; ?>>
                                        <?php echo $s; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-accent btn-sm">Update Status</button>
                        </form>
                        <span class="order-total"><?php echo formatPrice($order['total_amount']); ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <h2>No Orders Yet</h2>
                <p>No customer orders have been placed yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
