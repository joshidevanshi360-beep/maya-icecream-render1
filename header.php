<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$cart_count = getTotalCartCount($conn);
$user_name = isLoggedIn() ? $_SESSION['user_name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Maya Ice Cream Shop</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <div class="nav-container">
            <a href="<?php echo BASE_URL; ?>index.php" class="logo">
                <span class="logo-icon">🍨</span>
                <span class="logo-text">Maya Ice Cream</span>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <nav class="nav-menu" id="navMenu">
                <a href="<?php echo BASE_URL; ?>index.php" class="nav-link">Home</a>
                <a href="<?php echo BASE_URL; ?>menu.php" class="nav-link">Menu</a>
                <a href="<?php echo BASE_URL; ?>about.php" class="nav-link">About</a>
                <a href="<?php echo BASE_URL; ?>contact.php" class="nav-link">Contact</a>
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>my_orders.php" class="nav-link">My Orders</a>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="nav-link">Logout (<?php echo sanitize($user_name); ?>)</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login.php" class="nav-link">Login</a>
                    <a href="<?php echo BASE_URL; ?>register.php" class="nav-link nav-btn">Register</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>cart.php" class="nav-link cart-link">
                    🛒 Cart
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>
    <main class="page-content">
        <?php echo displayAlert(); ?>
