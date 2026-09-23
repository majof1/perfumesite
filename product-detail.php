<?php
// product-detail.php - Product Detail Page

require_once __DIR__ . '/config/db.php';

$id = intval($_GET['id'] ?? 0);
$db = getDBConnection();

$stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: catalog.php");
    exit();
}

$page_title = $product['name'];
require_once __DIR__ . '/includes/header.php';

// Calculate discount %
$discount_pct = 0;
if ($product['original_price'] && $product['original_price'] > $product['price']) {
    $discount_pct = round((($product['original_price'] - $product['price']) / $product['original_price']) * 100);
}
?>

<section style="padding: 50px 0 80px;">
    <div class="container">
        <!-- Breadcrumb -->
        <div style="margin-bottom: 24px; color: var(--text-muted); font-size: 0.9rem;">
            <a href="index.php">Home</a> &nbsp;/&nbsp;
            <a href="catalog.php">Catalog</a> &nbsp;/&nbsp;
            <span style="color: #fff;"><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: start;">
            <!-- Left: Glass Bottle Image -->
            <div class="glass-panel" style="padding: 40px; text-align: center; position: relative;">
                <?php if ($discount_pct > 0): ?>
                    <span style="position: absolute; top: 20px; right: 20px; background: var(--red-main); color: #fff; font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; box-shadow: 0 0 15px var(--red-glow);">
                        SAVE <?php echo $discount_pct; ?>%
                    </span>
                <?php endif; ?>

                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 320px; width: 100%; height: auto; filter: drop-shadow(0 20px 30px rgba(0,0,0,0.6));">
            </div>

            <!-- Right: Product Info & Purchase Form -->
            <div>
                <span class="badge-gender badge-<?php echo htmlspecialchars($product['gender']); ?>" style="position:static; display:inline-block; margin-bottom:12px;">
                    <?php echo htmlspecialchars($product['gender']); ?> &bull; <?php echo htmlspecialchars($product['category_name'] ?? 'Niche Fragrance'); ?>
                </span>

                <h1 style="font-size: 2.5rem; margin-bottom: 8px;"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p style="color: var(--blue-light); font-weight: 600; margin-bottom: 16px;"><?php echo htmlspecialchars($product['brand']); ?> &bull; <?php echo htmlspecialchars($product['volume']); ?></p>

                <!-- Pricing -->
                <div style="display: flex; align-items: baseline; gap: 14px; margin-bottom: 24px;">
                    <span style="font-size: 2.2rem; font-weight: 700; color: #fff; font-family: var(--font-body);"><?php echo formatNaira($product['price']); ?></span>
                    <?php if ($product['original_price']): ?>
                        <span style="font-size: 1.2rem; color: var(--text-muted); text-decoration: line-through;"><?php echo formatNaira($product['original_price']); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 30px;">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>

                <!-- Fragrance Notes Pyramid -->
                <div class="glass-panel" style="padding: 24px; margin-bottom: 30px; border-color: var(--glass-border-glow);">
                    <h3 style="font-size: 1.1rem; margin-bottom: 16px; color: var(--blue-light); display: flex; align-items: center; gap: 8px;">
                        <span>🧪</span> Fragrance Pyramid Notes
                    </h3>

                    <div class="notes-pyramid">
                        <div class="note-tier top">
                            <span class="note-label">Top Notes</span>
                            <span class="note-text"><?php echo htmlspecialchars($product['top_notes']); ?></span>
                        </div>
                        <div class="note-tier heart">
                            <span class="note-label">Heart Notes</span>
                            <span class="note-text"><?php echo htmlspecialchars($product['heart_notes']); ?></span>
                        </div>
                        <div class="note-tier base">
                            <span class="note-label">Base Notes</span>
                            <span class="note-text"><?php echo htmlspecialchars($product['base_notes']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Purchase Controls -->
                <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 30px;">
                    <div class="qty-control">
                        <button type="button" class="qty-btn minus">&minus;</button>
                        <input type="text" id="detail_qty" value="1" readonly class="qty-input">
                        <button type="button" class="qty-btn plus">&plus;</button>
                    </div>

                    <button onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('detail_qty').value)" class="btn-glass btn-primary-gradient" style="flex-grow: 1; padding: 14px 28px;">
                        🛒 Add To Cart (Naira)
                    </button>
                </div>

                <!-- Stock & Fast Delivery Badge -->
                <div style="display: flex; gap: 20px; font-size: 0.88rem; color: var(--text-muted);">
                    <div>✅ In Stock (<?php echo intval($product['stock']); ?> units available)</div>
                    <div>🚀 Dispatch in 24 hrs (Nigeria)</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
