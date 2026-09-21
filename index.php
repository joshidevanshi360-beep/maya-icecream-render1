<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Home';

// Fetch featured products (first 6)
$featured_sql = "SELECT * FROM products ORDER BY id LIMIT 6";
$featured_result = mysqli_query($conn, $featured_sql);

// Fetch all products for "special offers" section (last 3)
$offers_sql = "SELECT * FROM products ORDER BY id DESC LIMIT 3";
$offers_result = mysqli_query($conn, $offers_sql);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <img src="https://images.pexels.com/photos/1352271/pexels-photo-1352271.jpeg?auto=compress&cs=tinysrgb&h=650&w=940" class="hero-bg" alt="">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Beat the Heat with <span>Maya Ice Cream</span></h1>
            <p>Handcrafted with the finest ingredients, our ice creams come in over 15 delightful flavors. From classic chocolate to exotic mango — there's a scoop for everyone!</p>
            <div class="hero-buttons">
                <a href="menu.php" class="btn btn-primary btn-lg">Order Now</a>
                <a href="about.php" class="btn btn-outline btn-lg">Learn More</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.pexels.com/photos/5796721/pexels-photo-5796721.jpeg?auto=compress&cs=tinysrgb&h=650&w=940" alt="Delicious ice cream cone">
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section">
    <div class="section-title">
        <h2>Featured Flavours</h2>
        <div class="accent-line"></div>
        <p>Our most loved ice cream flavors, loved by customers</p>
    </div>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($featured_result)): ?>
            <div class="product-card">
                <div class="product-image-wrap">
                    <span class="product-category-tag"><?php echo sanitize($product['category']); ?></span>
                    <img src="<?php echo sanitize($product['image']); ?>" class="product-image" alt="<?php echo sanitize($product['name']); ?>">
                </div>
                <div class="product-info">
                    <div class="product-id">ID: #<?php echo $product['id']; ?></div>
                    <h3 class="product-name"><?php echo sanitize($product['name']); ?></h3>
                    <p class="product-desc"><?php echo sanitize($product['description']); ?></p>
                    <div class="product-footer">
                        <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                        <form action="cart_action.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <div style="text-align:center; margin-top:40px;">
        <a href="menu.php" class="btn btn-secondary btn-lg">View Full Menu</a>
    </div>
</section>

<!-- Special Offers Banner -->
<div class="offers-banner">
    <h2>Special Summer Offer!</h2>
    <p>Buy any 3 scoops and get 1 absolutely FREE. Limited time only!</p>
    <a href="menu.php" class="btn btn-secondary btn-lg">Grab the Deal</a>
</div>

<!-- Special Offers Products -->
<section class="section">
    <div class="section-title">
        <h2>Special Offers</h2>
        <div class="accent-line"></div>
        <p>Try our newest and most exciting flavors</p>
    </div>
    <div class="product-grid">
        <?php while ($product = mysqli_fetch_assoc($offers_result)): ?>
            <div class="product-card">
                <div class="product-image-wrap">
                    <span class="product-category-tag"><?php echo sanitize($product['category']); ?></span>
                    <img src="<?php echo sanitize($product['image']); ?>" class="product-image" alt="<?php echo sanitize($product['name']); ?>">
                </div>
                <div class="product-info">
                    <div class="product-id">ID: #<?php echo $product['id']; ?></div>
                    <h3 class="product-name"><?php echo sanitize($product['name']); ?></h3>
                    <p class="product-desc"><?php echo sanitize($product['description']); ?></p>
                    <div class="product-footer">
                        <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                        <form action="cart_action.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="about-container">
        <div class="about-image">
            <img src="https://images.pexels.com/photos/449730/pexels-photo-449730.jpeg?auto=compress&cs=tinysrgb&h=650&w=940" alt="Ice cream shop">
        </div>
        <div class="about-text">
            <h2>About Maya Ice Cream</h2>
            <p>Founded in 2020, Maya Ice Cream Shop started as a small family business with a big dream — to bring joy to every customer through the perfect scoop of ice cream.</p>
            <p>Today, we offer over 15 unique flavors made fresh daily with premium ingredients. From traditional favorites to innovative special flavors, every scoop is crafted with love and care.</p>
            <div class="about-features">
                <div class="about-feature">
                    <span class="about-feature-icon">🌿</span>
                    <div>
                        <h4>Natural Ingredients</h4>
                        <p>No artificial colors or preservatives</p>
                    </div>
                </div>
                <div class="about-feature">
                    <span class="about-feature-icon">🥛</span>
                    <div>
                        <h4>Fresh Daily</h4>
                        <p>Made fresh every single morning</p>
                    </div>
                </div>
                <div class="about-feature">
                    <span class="about-feature-icon">🚚</span>
                    <div>
                        <h4>Fast Delivery</h4>
                        <p>Delivered cold to your doorstep</p>
                    </div>
                </div>
                <div class="about-feature">
                    <span class="about-feature-icon">❤️</span>
                    <div>
                        <h4>Made with Love</h4>
                        <p>Every scoop crafted with passion</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Customer Reviews -->
<section class="section">
    <div class="section-title">
        <h2>What Our Customers Say</h2>
        <div class="accent-line"></div>
        <p>Real reviews from our happy customers</p>
    </div>
    <div class="reviews-grid">
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p class="review-text">"The best ice cream I've ever had! The Alphonso Mango flavor is absolutely divine. Will definitely order again!"</p>
            <div class="review-author">
                <div class="review-avatar">P</div>
                <div>
                    <div class="review-name">Priya Sharma</div>
                    <small style="color:var(--neutral-400);">Verified Customer</small>
                </div>
            </div>
        </div>
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p class="review-text">"Maya Ice Cream never disappoints. The Cookies & Cream is my family's favorite. Fast delivery and always fresh!"</p>
            <div class="review-author">
                <div class="review-avatar">R</div>
                <div>
                    <div class="review-name">Rahul Verma</div>
                    <small style="color:var(--neutral-400);">Verified Customer</small>
                </div>
            </div>
        </div>
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p class="review-text">"I ordered the Sundae Special for my birthday and it was a hit! Beautiful presentation and amazing taste. Highly recommend!"</p>
            <div class="review-author">
                <div class="review-avatar">A</div>
                <div>
                    <div class="review-name">Anjali Gupta</div>
                    <small style="color:var(--neutral-400);">Verified Customer</small>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
