<?php
// =====================================================
// Helper Functions - Maya Ice Cream Shop
// =====================================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sanitize input to prevent XSS
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

// Get cart count for the current user
function getCartCount($conn, $user_id) {
    $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ? $row['total'] : 0;
}

// Get cart count for guest (session-based)
function getGuestCartCount() {
    if (isset($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart'])) {
        return array_sum($_SESSION['guest_cart']);
    }
    return 0;
}

// Get total cart count (user or guest)
function getTotalCartCount($conn) {
    if (isLoggedIn()) {
        return getCartCount($conn, $_SESSION['user_id']);
    }
    return getGuestCartCount();
}

// Format price with Indian Rupee symbol
function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

// Display alert message
function displayAlert() {
    if (isset($_SESSION['message'])) {
        $type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
        $msg = $_SESSION['message'];
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return "<div class='alert alert-$type'>$msg</div>";
    }
    return '';
}

// Set alert message
function setAlert($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}




