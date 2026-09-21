<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

/*
|--------------------------------------------------------------------------
| Database connection fallback
|--------------------------------------------------------------------------
*/

if (!isset($conn) || !$conn) {
    $conn = mysqli_connect(
        "127.0.0.1",
        "root",
        "",
        "maya_icecream"
    );
}

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

/*
|--------------------------------------------------------------------------
| Category filter
|--------------------------------------------------------------------------
*/

$category = $_GET['category'] ?? 'all';

$allowed_categories = [
    'all',
    'Chocolate',
    'Vanilla',
    'Strawberry',
    'Mango',
    'Butterscotch',
    'Cookies & Cream',
    'Special Flavours'
];

if (!in_array($category, $allowed_categories, true)) {
    $category = 'all';
}

/*
|--------------------------------------------------------------------------
| Fetch products
|--------------------------------------------------------------------------
*/

if ($category === 'all') {
    $sql = "
        SELECT id, name, description, price, image, category
        FROM products
        ORDER BY category, id
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die('Product query failed: ' . mysqli_error($conn));
    }
} else {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, description, price, image, category
         FROM products
         WHERE category = ?
         ORDER BY id"
    );

    if (!$stmt) {
        die('Product statement failed: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die('Product result failed: ' . mysqli_error($conn));
    }
}

$categories = [
    'all' => 'All',
    'Chocolate' => 'Chocolate',
    'Vanilla' => 'Vanilla',
    'Strawberry' => 'Strawberry',
    'Mango' => 'Mango',
    'Butterscotch' => 'Butterscotch',
    'Cookies & Cream' => 'Cookies & Cream',
    'Special Flavours' => 'Special Flavours'
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="menu-header">
    <h1>Our Ice Cream Menu</h1>

    <p>
        Explore our delicious range of flavors —
        there's something for everyone!
    </p>
</div>

<div class="category-filter">
    <?php foreach ($categories as $key => $label): ?>

        <a
            href="?category=<?php echo urlencode($key); ?>"
            class="category-btn <?php echo $category === $key
                ? 'active'
                : ''; ?>"
            data-category="<?php echo sanitize($key); ?>"
        >
            <?php echo sanitize($label); ?>
        </a>

    <?php endforeach; ?>
</div>

<section class="section" style="padding-top: 0;">

    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="product-grid">

            <?php while ($product = mysqli_fetch_assoc($result)): ?>

                <div
                    class="product-card"
                    data-category="<?php echo sanitize($product['category']); ?>"
                >
                    <div class="product-image-wrap">

                        <span class="product-category-tag">
                            <?php echo sanitize($product['category']); ?>
                        </span>

                        <img
                            src="<?php echo sanitize($product['image']); ?>"
                            class="product-image"
                            alt="<?php echo sanitize($product['name']); ?>"
                        >
                    </div>

                    <div class="product-info">

                        <div class="product-id">
                            Product ID:
                            #<?php echo (int) $product['id']; ?>
                        </div>

                        <h3 class="product-name">
                            <?php echo sanitize($product['name']); ?>
                        </h3>

                        <p class="product-desc">
                            <?php echo sanitize($product['description']); ?>
                        </p>

                        <div class="product-footer">

                            <span class="product-price">
                                <?php echo formatPrice($product['price']); ?>
                            </span>

                            <!-- Add to Cart -->
                            <form
                                action="cart_action.php"
                                method="POST"
                                style="display:inline;"
                            >
                                <input
                                    type="hidden"
                                    name="action"
                                    value="add"
                                >

                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?php echo (int) $product['id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="quantity"
                                    value="1"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm"
                                >
                                    Add to Cart
                                </button>
                            </form>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty-state">
            <div class="empty-state-icon">🍦</div>

            <h2>No Products Found</h2>

            <p>
                No ice creams are available in this category right now.
            </p>
        </div>

    <?php endif; ?>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>