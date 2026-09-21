<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Contact';
require_once __DIR__ . '/includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // In a real project, you would save this to a database or send an email.
        // For this college project, we just show a success message.
        $success = 'Thank you for your message, ' . $name . '! We will get back to you soon.';
    }
}
?>

<div class="contact-container">
    <div class="section-title">
        <h2>Get In Touch</h2>
        <div class="accent-line"></div>
        <p>We'd love to hear from you. Send us a message!</p>
    </div>

    <div class="contact-grid">
        <div class="contact-info-box">
            <h3>Contact Information</h3>
            <div class="contact-info-item">
                <span class="contact-info-icon">📍</span>
                <div>
                    <strong>Address</strong><br>
                    123 Ice Cream Lane, Sweet City, India
                </div>
            </div>
            <div class="contact-info-item">
                <span class="contact-info-icon">📞</span>
                <div>
                    <strong>Phone</strong><br>
                    +91 98765 43210
                </div>
            </div>
            <div class="contact-info-item">
                <span class="contact-info-icon">✉️</span>
                <div>
                    <strong>Email</strong><br>
                    mayaicecreamshop06@gmail.com
                </div>
            </div>
            <div class="contact-info-item">
                <span class="contact-info-icon">🕒</span>
                <div>
                    <strong>Hours</strong><br>
                    Mon - Sun: 10:00 AM - 10:00 PM
                </div>
            </div>
        </div>

        <div class="contact-form-box">
            <h3 style="margin-bottom:24px; color:var(--neutral-800);">Send Us a Message</h3>
            <?php if ($error): ?>
                <div class="form-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="form-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
