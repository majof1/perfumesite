<?php
// checkout.php - Checkout & Payment Simulation Page

$page_title = "Checkout";
require_once __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: catalog.php");
    exit();
}

$db = getDBConnection();
$error_msg = "";

// Pre-fill user data if logged in
$user_id = $_SESSION['user_id'] ?? null;
$customer_name = $_SESSION['user_name'] ?? '';
$customer_email = $_SESSION['user_email'] ?? '';
$customer_phone = '';
$shipping_address = '';

if ($user_id) {
    $stmt_u = $db->prepare("SELECT phone, address FROM users WHERE id = ?");
    $stmt_u->execute([$user_id]);
    $u_data = $stmt_u->fetch();
    if ($u_data) {
        $customer_phone = $u_data['phone'] ?? '';
        $shipping_address = $u_data['address'] ?? '';
    }
}

// Calculate totals
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping_fee = 3500.00;
$total_amount = $subtotal + $shipping_fee;

// Process Order Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? 'Paystack / Card (Naira)');

    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($state)) {
        $error_msg = "Please fill in all required shipping fields.";
    } else {
        try {
            $db->beginTransaction();

            $order_number = 'NGN-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            // Insert into orders table
            $stmt = $db->prepare("INSERT INTO orders (order_number, user_id, customer_name, customer_email, customer_phone, shipping_address, city, state, subtotal, shipping_fee, total_amount, payment_method, payment_status, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Paid', 'Processing')");
            $stmt->execute([
                $order_number,
                $user_id,
                $name,
                $email,
                $phone,
                $address,
                $city,
                $state,
                $subtotal,
                $shipping_fee,
                $total_amount,
                $payment_method
            ]);

            $order_id = $db->lastInsertId();

            // Insert order items
            $stmt_item = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($cart as $item) {
                $item_subtotal = $item['price'] * $item['quantity'];
                $stmt_item->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item_subtotal
                ]);
            }

            $db->commit();

            // Clear Cart
            unset($_SESSION['cart']);

            // Redirect to Order Confirmation
            header("Location: order-confirmation.php?id=" . $order_id);
            exit();
        } catch (Exception $e) {
            $db->rollBack();
            $error_msg = "Error processing order: " . $e->getMessage();
        }
    }
}
?>

<section style="padding: 50px 0 80px;">
    <div class="container">
        <h1 style="font-size: 2.2rem; margin-bottom: 8px;">Checkout & Payment</h1>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Complete your purchase with instant Naira (₦) payment simulation.</p>

        <?php if (!empty($error_msg)): ?>
            <div class="glass-panel" style="padding: 16px; margin-bottom: 24px; border-color: rgba(239,68,68,0.5); background: rgba(239,68,68,0.15); color: #f87171;">
                ⚠️ <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="checkout.php">
            <div class="cart-grid">
                <!-- Left: Delivery Details & Payment Selection -->
                <div>
                    <!-- Shipping Address Card -->
                    <div class="glass-panel" style="padding: 30px; margin-bottom: 30px; border-color: var(--glass-border-glow);">
                        <h3 style="font-size: 1.3rem; margin-bottom: 20px; color: var(--blue-light);">1. Delivery Shipping Information</h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($customer_name); ?>" required class="form-control" placeholder="e.g. Babatunde Adeleke">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($customer_email); ?>" required class="form-control" placeholder="babatunde@example.com">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" name="phone" value="<?php echo htmlspecialchars($customer_phone); ?>" required class="form-control" placeholder="0803 123 4567">
                            </div>

                            <div class="form-group">
                                <label class="form-label">State (Nigeria) *</label>
                                <select name="state" required class="form-control">
                                    <option value="">Select State</option>
                                    <option value="Lagos" selected>Lagos State</option>
                                    <option value="Abuja FCT">Abuja FCT</option>
                                    <option value="Rivers">Rivers State (Port Harcourt)</option>
                                    <option value="Oyo">Oyo State (Ibadan)</option>
                                    <option value="Kano">Kano State</option>
                                    <option value="Enugu">Enugu State</option>
                                    <option value="Ogun">Ogun State</option>
                                    <option value="Delta">Delta State</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">City / LGA *</label>
                                <input type="text" name="city" required class="form-control" placeholder="e.g. Ikeja / Victoria Island">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Full Street Address *</label>
                                <input type="text" name="address" value="<?php echo htmlspecialchars($shipping_address); ?>" required class="form-control" placeholder="House number, street name, building">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Card -->
                    <div class="glass-panel" style="padding: 30px; border-color: var(--glass-border-red);">
                        <h3 style="font-size: 1.3rem; margin-bottom: 20px; color: var(--red-main);">2. Select Payment Method</h3>

                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <label class="glass-panel" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; cursor: pointer; border-color: var(--blue-main);">
                                <input type="radio" name="payment_method" value="Paystack / Debit Card (Naira)" checked style="accent-color: var(--blue-main);">
                                <div>
                                    <div style="font-weight: 700; color: #fff;">💳 Paystack / Nigerian Bank Card</div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted);">Instant approval via Visa, Mastercard, or Verve cards in Naira (₦)</div>
                                </div>
                            </label>

                            <label class="glass-panel" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; cursor: pointer;">
                                <input type="radio" name="payment_method" value="Direct Bank Transfer (Naira)" style="accent-color: var(--blue-main);">
                                <div>
                                    <div style="font-weight: 700; color: #fff;">🏦 Direct Naira Bank Transfer</div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted);">Transfer to GTBank / Zenith / Access Bank Account</div>
                                </div>
                            </label>

                            <label class="glass-panel" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; cursor: pointer;">
                                <input type="radio" name="payment_method" value="Cash on Delivery (Lagos)" style="accent-color: var(--blue-main);">
                                <div>
                                    <div style="font-weight: 700; color: #fff;">💵 Pay on Delivery</div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted);">Available in Lagos State & Port Harcourt City</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Review Sidebar -->
                <div>
                    <div class="glass-panel summary-card">
                        <h3 style="font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px;">Order Summary</h3>

                        <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px; padding-right: 6px;">
                            <?php foreach ($cart as $item): ?>
                                <div style="display: flex; gap: 12px; margin-bottom: 14px; align-items: center;">
                                    <div style="width: 48px; height: 48px; background: rgba(15,23,42,0.6); border-radius: 8px; padding: 4px;">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <div style="font-size: 0.9rem; font-weight: 600; color: #fff;"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $item['quantity']; ?> x <?php echo formatNaira($item['price']); ?></div>
                                    </div>
                                    <div style="font-weight: 700; color: var(--blue-light);">
                                        <?php echo formatNaira($item['price'] * $item['quantity']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span style="font-weight:600; color:#fff;"><?php echo formatNaira($subtotal); ?></span>
                        </div>

                        <div class="summary-row">
                            <span>Shipping (Nigeria):</span>
                            <span style="font-weight:600; color:#fff;"><?php echo formatNaira($shipping_fee); ?></span>
                        </div>

                        <div class="summary-row total">
                            <span>Total Billed (₦):</span>
                            <span style="color: var(--red-main);"><?php echo formatNaira($total_amount); ?></span>
                        </div>

                        <div style="margin-top: 24px;">
                            <button type="submit" name="place_order" class="btn-glass btn-primary-gradient" style="width: 100%; padding: 16px 20px; font-size: 1rem;">
                                🔒 Pay <?php echo formatNaira($total_amount); ?> & Complete Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
