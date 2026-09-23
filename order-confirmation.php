<?php
// order-confirmation.php - Order Confirmation & Invoice Page

$page_title = "Order Confirmation";
require_once __DIR__ . '/includes/header.php';

$order_id = intval($_GET['id'] ?? 0);
$db = getDBConnection();

$stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit();
}

// Fetch order items
$stmt_items = $db->prepare("SELECT oi.*, p.image_url FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt_items->execute([$order_id]);
$order_items = $stmt_items->fetchAll();
?>

<section style="padding: 60px 0 80px;">
    <div class="container" style="max-width: 900px;">
        <!-- Success Header -->
        <div class="glass-panel" style="padding: 40px; text-align: center; margin-bottom: 40px; border-color: var(--glass-border-glow); background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.95));">
            <div style="font-size: 3.5rem; margin-bottom: 12px;">🎉</div>
            <span style="background: rgba(34, 197, 94, 0.2); border: 1px solid rgba(34, 197, 94, 0.4); color: #4ade80; padding: 4px 16px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">
                Payment Verified & Order Confirmed
            </span>

            <h1 style="font-size: 2.4rem; margin: 16px 0 8px;">Thank You For Your Order!</h1>
            <p style="color: var(--text-muted); font-size: 1.05rem;">We have received your payment of <strong style="color:#fff;"><?php echo formatNaira($order['total_amount']); ?></strong> for Order <strong style="color:var(--blue-light);"><?php echo htmlspecialchars($order['order_number']); ?></strong>.</p>
        </div>

        <!-- Tracking Steps -->
        <div class="glass-panel" style="padding: 28px; margin-bottom: 40px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 20px; text-align: center;">Order Progress</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; text-align: center;">
                <div style="padding: 12px; background: rgba(59,130,246,0.15); border: 1px solid var(--blue-main); border-radius: 12px;">
                    <div style="font-size: 1.3rem;">✅</div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: #fff;">Order Placed</div>
                </div>
                <div style="padding: 12px; background: rgba(59,130,246,0.15); border: 1px solid var(--blue-main); border-radius: 12px;">
                    <div style="font-size: 1.3rem;">🧪</div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: #fff;">Quality Control</div>
                </div>
                <div style="padding: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;">
                    <div style="font-size: 1.3rem;">📦</div>
                    <div style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted);">Dispatched</div>
                </div>
                <div style="padding: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;">
                    <div style="font-size: 1.3rem;">🏠</div>
                    <div style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted);">Delivered</div>
                </div>
            </div>
        </div>

        <!-- Printable Invoice -->
        <div class="glass-panel" style="padding: 40px; border-color: var(--glass-border-red);" id="printable-invoice">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 20px; margin-bottom: 24px;">
                <div>
                    <div style="font-family: var(--font-heading); font-size: 1.6rem; font-weight: 700; color: #fff;">Élixir De Luxe</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">Official Tax Invoice / Receipt (NGN)</div>
                </div>

                <div style="text-align: right;">
                    <div style="font-weight: 700; color: var(--blue-light); font-size: 1.1rem;"><?php echo htmlspecialchars($order['order_number']); ?></div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);"><?php echo date('M d, Y - H:i', strtotime($order['created_at'])); ?></div>
                </div>
            </div>

            <!-- Customer & Shipping Details -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <div>
                    <h4 style="color: var(--blue-light); margin-bottom: 8px;">Customer Information</h4>
                    <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p style="color: var(--text-muted); margin: 0;"><?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p style="color: var(--text-muted); margin: 0;"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                </div>

                <div>
                    <h4 style="color: var(--red-main); margin-bottom: 8px;">Shipping Address</h4>
                    <p style="color: #fff; margin: 0;"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                    <p style="color: var(--text-muted); margin: 0;"><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?>, Nigeria</p>
                    <p style="color: var(--text-muted); margin: 0;">Payment: <?php echo htmlspecialchars($order['payment_method']); ?></p>
                </div>
            </div>

            <!-- Items Table -->
            <table class="cart-table" style="margin-bottom: 24px;">
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>Price (₦)</th>
                        <th>Qty</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <?php if ($item['image_url']): ?>
                                        <div style="width:40px; height:40px; background:rgba(15,23,42,0.5); border-radius:6px; padding:3px;">
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" style="width:100%; height:100%; object-fit:contain;">
                                        </div>
                                    <?php endif; ?>
                                    <span style="font-weight:600; color:#fff;"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo formatNaira($item['price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td style="text-align:right; font-weight:700; color:var(--blue-light);"><?php echo formatNaira($item['subtotal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Summary Totals -->
            <div style="max-width: 320px; margin-left: auto;">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span style="color:#fff;"><?php echo formatNaira($order['subtotal']); ?></span>
                </div>
                <div class="summary-row">
                    <span>Delivery Charge:</span>
                    <span style="color:#fff;"><?php echo formatNaira($order['shipping_fee']); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total Paid (₦):</span>
                    <span style="color:var(--red-main);"><?php echo formatNaira($order['total_amount']); ?></span>
                </div>
            </div>

            <!-- Invoice Footer Buttons -->
            <div style="margin-top: 30px; display: flex; gap: 16px; justify-content: flex-end;">
                <button onclick="window.print()" class="btn-glass btn-outline-glass btn-sm">🖨️ Print Invoice</button>
                <a href="index.php" class="btn-glass btn-primary-gradient btn-sm">🛍️ Return to Store</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
