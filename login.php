<?php
// login.php - User Login Page

$page_title = "Sign In";
require_once __DIR__ . '/includes/header.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter your email and password.";
    } else {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect to checkout if cart is non-empty, else account
            if (!empty($_SESSION['cart'])) {
                header("Location: checkout.php");
            } else {
                header("Location: account.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<section style="padding: 60px 0 80px;">
    <div class="container" style="max-width: 480px;">
        <div class="glass-panel" style="padding: 40px; border-color: var(--glass-border-glow);">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="font-size: 2.5rem; margin-bottom: 8px;">🔑</div>
                <h1 style="font-size: 2rem; margin-bottom: 8px;">Welcome Back</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Sign in to view your orders & manage perfume profile.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div style="padding: 14px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 12px; color: #f87171; margin-bottom: 20px; font-size: 0.9rem;">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" required class="form-control" placeholder="babatunde@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" required class="form-control" placeholder="••••••••">
                </div>

                <button type="submit" class="btn-glass btn-blue" style="width: 100%; padding: 14px; margin-top: 10px;">
                    🔑 Sign In
                </button>
            </form>

            <div style="text-align: center; margin-top: 24px; color: var(--text-muted); font-size: 0.9rem;">
                Don't have an account? <a href="register.php" style="color: var(--blue-light); font-weight: 600;">Create One</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
