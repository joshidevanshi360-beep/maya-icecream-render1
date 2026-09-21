<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'About';
require_once __DIR__ . '/includes/header.php';
?>

<section class="about-section">
    <div class="about-container">
        <div class="about-image">
            <img src="https://images.pexels.com/photos/1352271/pexels-photo-1352271.jpeg?auto=compress&cs=tinysrgb&h=650&w=940" alt="Ice cream shop display">
        </div>
        <div class="about-text">
            <h2>Our Story</h2>
            <p>Maya Ice Cream Shop was born in 12th September 2026 from a simple idea — that everyone deserves a perfect scoop of ice cream. What started as a small family business has grown into a beloved local brand known for quality and taste.</p>
            <p>We believe that great ice cream starts with great ingredients. That's why we use only the finest dairy, freshest fruits, and premium flavorings to create our treats. Every batch is made fresh daily in our shop.</p>
            <p>Our mission is simple: to bring smiles to faces, one scoop at a time. Whether you're celebrating a special occasion or just treating yourself, we're here to make your day a little sweeter.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-title">
        <h2>Why Choose Us?</h2>
        <div class="accent-line"></div>
        <p>We take pride in every scoop we serve</p>
    </div>
    <div class="reviews-grid">
        <div class="review-card" style="text-align:center;">
            <div style="font-size:3rem; margin-bottom:16px;">🌿</div>
            <h3 style="margin-bottom:8px; color:var(--neutral-800);">100% Natural</h3>
            <p style="color:var(--neutral-500);">No artificial colors, flavors, or preservatives. Just pure, natural goodness in every scoop.</p>
        </div>
        <div class="review-card" style="text-align:center;">
            <div style="font-size:3rem; margin-bottom:16px;">🥛</div>
            <h3 style="margin-bottom:8px; color:var(--neutral-800);">Fresh Daily</h3>
            <p style="color:var(--neutral-500);">Our ice cream is made fresh every morning to ensure you get the creamiest, most delicious treat.</p>
        </div>
        <div class="review-card" style="text-align:center;">
            <div style="font-size:3rem; margin-bottom:16px;">🚚</div>
            <h3 style="margin-bottom:8px; color:var(--neutral-800);">Fast Delivery</h3>
            <p style="color:var(--neutral-500);">We deliver your favorite ice cream cold and fast, right to your doorstep. Order online today!</p>
        </div>
    </div>
</section>

<section class="offers-banner">
    <h2>Ready to Order?</h2>
    <p>Browse our full menu and get your favorite ice cream delivered today!</p>
    <a href="menu.php" class="btn btn-secondary btn-lg">View Menu</a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
