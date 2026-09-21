// =====================================================
// Maya Ice Cream Shop - JavaScript
// =====================================================

document.addEventListener('DOMContentLoaded', function () {
    // Mobile navigation toggle
    var navToggle = document.getElementById('navToggle');
    var navMenu = document.getElementById('navMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }

    // Category filter on menu page
    var categoryButtons = document.querySelectorAll('.category-btn');
    var productCards = document.querySelectorAll('.product-card');

    categoryButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var category = this.getAttribute('data-category');

            categoryButtons.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            productCards.forEach(function (card) {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Payment method selection
    var paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            paymentOptions.forEach(function (o) { o.classList.remove('selected'); });
            this.classList.add('selected');
            var radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    // Auto-hide alerts after 5 seconds
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 5000);
    });
});
