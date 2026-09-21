<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Fetch stats
$product_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$order_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status = 'Pending'"))['count'];

// Fetch recent orders
$recent_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
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
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="../index.php" target="_blank">View Website</a>
    </nav>

    <div class="admin-container">
        <h2>Dashboard Overview</h2>

        <div class="admin-stat-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-icon">🍦</div>
                <div class="admin-stat-value"><?php echo $product_count; ?></div>
                <div class="admin-stat-label">Total Products</div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon">📦</div>
                <div class="admin-stat-value"><?php echo $order_count; ?></div>
                <div class="admin-stat-label">Total Orders</div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon">👥</div>
                <div class="admin-stat-value"><?php echo $user_count; ?></div>
                <div class="admin-stat-label">Registered Users</div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon">⏳</div>
                <div class="admin-stat-value"><?php echo $pending_orders; ?></div>
                <div class="admin-stat-label">Pending Orders</div>
            </div>
        </div>

        <h2 style="margin-top:40px;">Recent Orders</h2>
        <?php if (mysqli_num_rows($recent_orders) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo sanitize($order['customer_name']); ?></td>
                            <td><?php echo formatPrice($order['total_amount']); ?></td>
                            <td><?php echo sanitize($order['payment_method']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                    <?php echo sanitize($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div style="text-align:center; margin-top:20px;">
                <a href="orders.php" class="btn btn-primary">View All Orders</a>
            </div>
        <?php else: ?>
            <p style="color:var(--neutral-500);">No orders yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
