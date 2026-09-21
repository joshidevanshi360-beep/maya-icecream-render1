<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Login';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Fetch user by email
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, email, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];

// Move guest cart into user's cart
if (!empty($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart'])) {

    foreach ($_SESSION['guest_cart'] as $product_id => $quantity) {

        $product_id = (int)$product_id;
        $quantity = (int)$quantity;

        if ($quantity <= 0) {
            continue;
        }

        // Check if product already exists in user's cart
        $stmt2 = mysqli_prepare($conn,
            "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?"
        );
        mysqli_stmt_bind_param($stmt2, "ii", $user['id'], $product_id);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);

        if ($row = mysqli_fetch_assoc($result2)) {

            // Increase existing quantity
            $new_quantity = $row['quantity'] + $quantity;

            $stmt3 = mysqli_prepare($conn,
                "UPDATE cart SET quantity = ? WHERE id = ?"
            );
            mysqli_stmt_bind_param($stmt3, "ii", $new_quantity, $row['id']);
            mysqli_stmt_execute($stmt3);

        } else {

            // Add new product to user's cart
            $stmt3 = mysqli_prepare($conn,
                "INSERT INTO cart (user_id, product_id, quantity)
                 VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt3, "iii",
                $user['id'], $product_id, $quantity);
            mysqli_stmt_execute($stmt3);
        }
    }

    // Clear guest cart after transferring
    unset($_SESSION['guest_cart']);
}

setAlert('Welcome back, ' . $user['full_name'] . '!', 'success');
redirect('index.php');
            } else {
                $error = 'Incorrect email or password.';
            }
        } else {
            $error = 'No account found with this email. Please register first.';
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="form-page">
    <div class="form-card">
        <h2>Welcome Back!</h2>
        <p class="form-subtitle">Login to your Maya Ice Cream account</p>

        <?php if ($error): ?>
            <div class="form-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div style="text-align:right; margin-bottom:20px;">
                <a href="#" style="color:var(--primary); font-size:0.9rem;">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p class="form-link">Don't have an account? <a href="register.php">Register here</a></p>

        <div style="margin-top:24px; padding:16px; background:var(--neutral-50); border-radius:var(--radius-md); font-size:0.85rem; color:var(--neutral-500); text-align:center;">
            <strong>Test Account:</strong><br>
            Email: devanshi06<br>
            Password: password123
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
