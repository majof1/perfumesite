<?php
// register.php - User Account Registration

$page_title = "Create Account";
require_once __DIR__ . '/includes/header.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "An account with this email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt_ins = $db->prepare("INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
            if ($stmt_ins->execute([$full_name, $email, $hashed_password, $phone, $address])) {
                $_SESSION['user_id'] = $db->lastInsertId();
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_email'] = $email;

                header("Location: account.php");
                exit();
            } else {
                $error = "Failed to create account. Please try again.";
            }
        }
    }
}
?>

<section style="padding: 60px 0 80px;">
    <div class="container" style="max-width: 520px;">
        <div class="glass-panel" style="padding: 40px; border-color: var(--glass-border-glow);">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="font-size: 2.5rem; margin-bottom: 8px;">✨</div>
                <h1 style="font-size: 2rem; margin-bottom: 8px;">Join Élixir De Luxe</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Create your account for express checkout & order tracking in Nigeria.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div style="padding: 14px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 12px; color: #f87171; margin-bottom: 20px; font-size: 0.9rem;">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" required class="form-control" placeholder="Babatunde Adeleke">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" required class="form-control" placeholder="babatunde@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" required class="form-control" placeholder="At least 6 characters">
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="0803 123 4567">
                </div>

                <div class="form-group">
                    <label class="form-label">Delivery Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Street name, City, State">
                </div>

                <button type="submit" class="btn-glass btn-primary-gradient" style="width: 100%; padding: 14px; margin-top: 10px;">
                    ✨ Create Account
                </button>
            </form>

            <div style="text-align: center; margin-top: 24px; color: var(--text-muted); font-size: 0.9rem;">
                Already have an account? <a href="login.php" style="color: var(--blue-light); font-weight: 600;">Sign In</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
