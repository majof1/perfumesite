<?php
// cart.php - Shopping Cart Page

$page_title = "Your Shopping Cart";
require_once __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];

// Handle update/remove actions via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($action === 'update' && $product_id > 0) {
        $qty = max(1, intval($_POST['quantity'] ?? 1));
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $qty;
        }
    } elseif ($action === 'remove' && $product_id > 0) {
        unset($_SESSION['cart'][$product_id]);
    }
    
    // Refresh cart reference
    $cart = $_SESSION['cart'] ?? [];
}

// Calculate subtotal
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping_fee = ($subtotal > 0) ? 3500.00 : 0; // Standard Naira delivery fee
$grand_total = $subtotal + $shipping_fee;
?>

<section style="padding: 50px 0 80px;">
    <div class="container">
        <h1 style="font-size: 2.2rem; margin-bottom: 8px;">Shopping Cart (Naira ₦)</h1>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Review your perfume selections before checkout.</p>

        <?php if (!empty($cart)): ?>
            <div class="cart-grid">
                <!-- Left: Items Table -->
                <div class="glass-panel" style="padding: 24px; border-color: var(--glass-border-glow);">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Perfume Details</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Subtotal (₦)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $id => $item): ?>
                                <tr>
                                    <td>
                                        <div class="cart-product-info">
                                            <div class="cart-thumb">
                                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($item['name']); ?></div>
                                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($item['volume']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-weight: 600; color: #fff;"><?php echo formatNaira($item['price']); ?></td>
                                    <td>
                                        <form method="POST" action="cart.php" class="cart-update-form" style="display:inline-block;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                            <div class="qty-control">
                                                <button type="submit" class="qty-btn minus">&minus;</button>
                                                <input type="text" name="quantity" value="<?php echo $item['quantity']; ?>" class="qty-input" readonly>
                                                <button type="submit" class="qty-btn plus">&plus;</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td style="font-weight: 700; color: var(--blue-light);"><?php echo formatNaira($item['price'] * $item['quantity']); ?></td>
                                    <td>
                                        <form method="POST" action="cart.php" style="display:inline;">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                            <button type="submit" style="background:none; border:none; color:var(--red-main); cursor:pointer; font-size:1.1rem;" title="Remove Item">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center;">
                        <a href="catalog.php" class="btn-glass btn-outline-glass btn-sm">&larr; Continue Shopping</a>
                    </div>
                </div>

                <!-- Right: Order Summary Panel -->
                <div>
                    <div class="glass-panel summary-card" style="border-color: var(--glass-border-red);">
                        <h3 style="font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px;">Summary (NGN)</h3>

                        <div class="summary-row">
                            <span>Bag Subtotal:</span>
                            <span style="font-weight:600; color:#fff;"><?php echo formatNaira($subtotal); ?></span>
                        </div>

                        <div class="summary-row">
                            <span>Estimated Shipping (Nigeria):</span>
                            <span style="font-weight:600; color:#fff;"><?php echo formatNaira($shipping_fee); ?></span>
                        </div>

                        <div class="summary-row total">
                            <span>Total Amount:</span>
                            <span style="color: var(--red-main);"><?php echo formatNaira($grand_total); ?></span>
                        </div>

                        <div style="margin: 24px 0 20px;">
                            <a href="checkout.php" class="btn-glass btn-primary-gradient" style="width: 100%; text-align: center; padding: 14px 20px;">
                                🚀 Proceed to Checkout
                            </a>
                        </div>

                        <div style="text-align: center; font-size: 0.8rem; color: var(--text-muted);">
                            🔒 256-Bit SSL Encrypted Checkout &bull; Paystack / Card Ready
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="glass-panel" style="padding: 60px; text-align: center;">
                <div style="font-size: 3.5rem; margin-bottom: 16px;">🛍️</div>
                <h2>Your Shopping Cart is Empty</h2>
                <p style="color: var(--text-muted); margin-bottom: 24px;">Discover our collection of rare Cambodian Oud, Rose, and Amber perfumes.</p>
                <a href="catalog.php" class="btn-glass btn-primary-gradient">Explore Perfumes</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
