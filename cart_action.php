 <?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('menu.php');
}

$action = $_POST['action'] ?? '';
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

// Validate product ID
if ($product_id <= 0) {
    setAlert('Invalid product. Please try again.', 'error');
    redirect('menu.php');
}

// Verify product exists in the database
$product_check = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ?");
mysqli_stmt_bind_param($product_check, "i", $product_id);
mysqli_stmt_execute($product_check);
mysqli_stmt_store_result($product_check);

if (mysqli_stmt_num_rows($product_check) === 0) {
    setAlert('Product not found.', 'error');
    redirect('menu.php');
}

if ($action === 'add') {
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        // Check if product already in cart
        $stmt = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $new_qty = $row['quantity'] + 1;
            $stmt = mysqli_prepare($conn, "UPDATE cart SET quantity = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $new_qty, $row['id']);
            mysqli_stmt_execute($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
            mysqli_stmt_execute($stmt);
        }
    } else {
        // Guest cart stored in session
        if (!isset($_SESSION['guest_cart'])) {
            $_SESSION['guest_cart'] = [];
        }
        if (isset($_SESSION['guest_cart'][$product_id])) {
            $_SESSION['guest_cart'][$product_id] += 1;
        } else {
            $_SESSION['guest_cart'][$product_id] = 1;
        }
    }
    setAlert('Product added to cart!', 'success');
    $redirect_url = isset($_POST['redirect']) ? $_POST['redirect'] : 'menu.php';
    redirect($redirect_url);
} elseif ($action === 'update') {
    $quantity = max(1, $quantity);
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $user_id, $product_id);
        mysqli_stmt_execute($stmt);
    } else {
        if (isset($_SESSION['guest_cart'][$product_id])) {
            $_SESSION['guest_cart'][$product_id] = $quantity;
        }
    }
    setAlert('Cart updated.', 'info');
    redirect('cart.php');
} elseif ($action === 'remove') {
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
    } else {
        unset($_SESSION['guest_cart'][$product_id]);
    }
    setAlert('Product removed from cart.', 'info');
    redirect('cart.php');
} else {
    redirect('menu.php');
}
