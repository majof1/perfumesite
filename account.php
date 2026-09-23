<?php
// account.php - User Account Profile & Order History

$page_title = "My Account";
require_once __DIR__ . '/includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$db = getDBConnection();

$msg = "";

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    $stmt_u = $db->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
    if ($stmt_u->execute([$phone, $address, $user_id])) {
        $msg = "Profile information updated successfully!";
    }
}

// Fetch user profile
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch order history
$stmt_orders = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt_orders->execute([$user_id]);
$orders = $stmt_orders->fetchAll();
?>

<section style="padding: 50px 0 80px;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 2.2rem; margin-bottom: 4px;">My Account Dashboard</h1>
                <p style="color: var(--text-muted);">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
            </div>
            <a href="logout.php" class="btn-glass btn-outline-glass btn-sm" style="color: var(--red-main); border-color: rgba(239, 68, 68, 0.3);">
                🚪 Sign Out
            </a>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="glass-panel" style="padding: 16px; margin-bottom: 24px; border-color: rgba(34,197,94,0.4); background: rgba(34,197,94,0.15); color: #4ade80;">
                ✨ <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            <!-- Left: Profile Info -->
            <div class="glass-panel" style="padding: 30px; height: fit-content; border-color: var(--glass-border-glow);">
                <h3 style="font-size: 1.3rem; margin-bottom: 20px; color: var(--blue-light);">Personal Profile</h3>

                <form method="POST" action="account.php">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled class="form-control" style="opacity: 0.7;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled class="form-control" style="opacity: 0.7;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="form-control" placeholder="0803 123 4567">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Delivery Address</label>
                        <textarea name="address" rows="3" class="form-control" placeholder="Street name, City, State"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" name="update_profile" class="btn-glass btn-blue btn-sm" style="width: 100%; padding: 12px;">
                        💾 Save Profile
                    </button>
                </form>
            </div>

            <!-- Right: Order History -->
            <div class="glass-panel" style="padding: 30px; border-color: var(--glass-border-red);">
                <h3 style="font-size: 1.3rem; margin-bottom: 20px; color: var(--red-main);">My Orders (Naira ₦)</h3>

                <?php if (count($orders) > 0): ?>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Order Ref</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total (₦)</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #fff;"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td style="color: var(--text-muted); font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <span style="padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3);">
                                            <?php echo htmlspecialchars($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 700; color: var(--blue-light);"><?php echo formatNaira($order['total_amount']); ?></td>
                                    <td>
                                        <a href="order-confirmation.php?id=<?php echo $order['id']; ?>" class="btn-glass btn-outline-glass btn-sm" style="padding: 4px 10px; font-size: 0.75rem;">
                                            📄 View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 0; color: var(--text-muted);">
                        <div style="font-size: 2.5rem; margin-bottom: 12px;">📦</div>
                        <p>You haven't placed any perfume orders yet.</p>
                        <a href="catalog.php" class="btn-glass btn-primary-gradient btn-sm" style="margin-top: 12px;">Shop Perfumes Now</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
