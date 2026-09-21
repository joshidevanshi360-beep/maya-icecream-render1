<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$message = '';
$message_type = '';

// Handle Add/Edit/Delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category = sanitize($_POST['category'] ?? '');
        $image = sanitize($_POST['image'] ?? '');

        if (empty($name) || empty($price) || empty($category)) {
            $message = 'Please fill in all required fields.';
            $message_type = 'error';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $name, $description, $price, $category, $image);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Product added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to add product.';
                $message_type = 'error';
            }
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category = sanitize($_POST['category'] ?? '');
        $image = sanitize($_POST['image'] ?? '');

        $stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, description = ?, price = ?, category = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image, $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Product updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update product.';
            $message_type = 'error';
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Product deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete product.';
            $message_type = 'error';
        }
    }
}

// Fetch all products
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");

$categories = ['Chocolate', 'Vanilla', 'Strawberry', 'Mango', 'Butterscotch', 'Cookies & Cream', 'Special Flavours'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
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
        <a href="products.php" class="active">Products</a>
        <a href="orders.php">Orders</a>
        <a href="../index.php" target="_blank">View Website</a>
    </nav>

    <div class="admin-container">
        <h2>Manage Products</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Add Product Form -->
        <div class="form-card" style="box-shadow:var(--shadow-sm); margin-bottom:32px;">
            <h3 style="margin-bottom:20px; color:var(--neutral-800);">Add New Product</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (₹) *</label>
                        <input type="number" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" name="image" placeholder="https://...">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>

        <!-- Products Table -->
        <h3 style="margin-bottom:16px; color:var(--neutral-800);">All Products (<?php echo mysqli_num_rows($products); ?>)</h3>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td>#<?php echo $product['id']; ?></td>
                            <td><img src="<?php echo sanitize($product['image']); ?>" class="admin-product-img" alt=""></td>
                            <td><?php echo sanitize($product['name']); ?></td>
                            <td><?php echo sanitize($product['category']); ?></td>
                            <td><?php echo formatPrice($product['price']); ?></td>
                            <td>
                                <button class="admin-action-btn admin-edit" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>)">Edit</button>
                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="admin-action-btn admin-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center;" class="modal-overlay">
        <div class="form-card" style="max-width:550px; max-height:90vh; overflow-y:auto;">
            <h3 style="margin-bottom:20px; color:var(--neutral-800);">Edit Product</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="edit_category" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" name="price" id="edit_price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image" id="edit_image">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" rows="3"></textarea>
                </div>
                <div style="display:flex; gap:12px;">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function editProduct(product) {
        document.getElementById('edit_id').value = product.id;
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_category').value = product.category;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_image').value = product.image;
        document.getElementById('edit_description').value = product.description;
        var modal = document.getElementById('editModal');
        modal.style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    </script>
</body>
</html>
