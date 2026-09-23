<?php
// index.php - Home Page

$page_title = "Haute Parfumerie & Luxury Scents";
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();

// Fetch featured products
$stmt = $db->query("SELECT * FROM products WHERE is_featured = 1 ORDER BY id DESC LIMIT 4");
$featured_products = $stmt->fetchAll();

// Fetch bestsellers
$stmt_bs = $db->query("SELECT * FROM products WHERE is_bestseller = 1 ORDER BY rating DESC LIMIT 4");
$bestseller_products = $stmt_bs->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container hero-grid">
        <div class="hero-content">
            <div class="hero-tag">
                <span>✨ Haute Parfumerie Collection</span>
            </div>
            <h1 class="hero-title">Smell Extraordinary in <span>Every Season</span></h1>
            <p class="hero-description">
                Elevate your scent signature with bespoke hand-crafted luxury perfumes, rare Cambodian oud resins, and delicate French rose extracts. Exclusively priced in Nigerian Naira (₦).
            </p>
            <div class="hero-buttons">
                <a href="catalog.php" class="btn-glass btn-primary-gradient">
                    <span>🛍️ Shop Collection</span>
                </a>
                <a href="#featured" class="btn-glass btn-outline-glass">
                    <span>🔥 Featured Scents</span>
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-image-card glass-panel">
                <img src="assets/images/perfume1.svg" alt="Oud Imperial Royale">
                
                <div class="floating-glass-badge">
                    <div class="floating-icon">👑</div>
                    <div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">Flagship Scent</div>
                        <div style="font-weight:700; color:#fff;">Oud Imperial Royale</div>
                        <div style="color:var(--red-main); font-weight:700; font-size:1.1rem;"><?php echo formatNaira(135000); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Row -->
<section style="padding: 40px 0 60px;">
    <div class="container">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:24px;">
            <div class="glass-panel" style="padding:28px; text-align:center;">
                <div style="font-size:2.5rem; margin-bottom:12px;">🇳🇬</div>
                <h3 style="font-size:1.2rem; margin-bottom:8px;">100% Naira Pricing</h3>
                <p style="font-size:0.9rem; color:var(--text-muted);">No currency conversion fees. Pay directly in Naira (₦) with local cards or bank transfer.</p>
            </div>

            <div class="glass-panel" style="padding:28px; text-align:center;">
                <div style="font-size:2.5rem; margin-bottom:12px;">🚚</div>
                <h3 style="font-size:1.2rem; margin-bottom:8px;">Nationwide Express</h3>
                <p style="font-size:0.9rem; color:var(--text-muted);">Fast, door-to-door insured delivery across Lagos, Abuja, Port Harcourt, and all states.</p>
            </div>

            <div class="glass-panel" style="padding:28px; text-align:center;">
                <div style="font-size:2.5rem; margin-bottom:12px;">💎</div>
                <h3 style="font-size:1.2rem; margin-bottom:8px;">Guaranteed Authentic</h3>
                <p style="font-size:0.9rem; color:var(--text-muted);">Every fragrance bottle comes sealed with batch verification codes and authenticity seal.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Fragrances Section -->
<section id="featured" style="padding: 60px 0;">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Curated Elegance</span>
            <h2 class="section-title">Featured Fragrances</h2>
            <p style="color:var(--text-muted); max-width:600px; margin:0 auto;">Handpicked master creations formulated for longevity, projection, and unforgettable impression.</p>
        </div>

        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
                <div class="glass-panel glass-panel-interactive product-card">
                    <div class="product-thumb">
                        <span class="badge-gender badge-<?php echo htmlspecialchars($product['gender']); ?>">
                            <?php echo htmlspecialchars($product['gender']); ?>
                        </span>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                    </div>

                    <div class="product-body">
                        <div class="product-category"><?php echo htmlspecialchars($product['brand']); ?></div>
                        <h3 class="product-title">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" style="color:#fff;">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        
                        <div class="product-notes-preview">
                            🌿 <strong>Notes:</strong> <?php echo htmlspecialchars($product['top_notes']); ?> &bull; <?php echo htmlspecialchars($product['heart_notes']); ?>
                        </div>

                        <div class="product-price-row">
                            <div>
                                <span class="price-naira"><?php echo formatNaira($product['price']); ?></span>
                                <?php if ($product['original_price']): ?>
                                    <span class="original-price"><?php echo formatNaira($product['original_price']); ?></span>
                                <?php endif; ?>
                            </div>

                            <button onclick="addToCart(<?php echo $product['id']; ?>, 1)" class="btn-glass btn-blue btn-sm">
                                🛒 Add
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center; margin-top:40px;">
            <a href="catalog.php" class="btn-glass btn-outline-glass">View All Scents &rarr;</a>
        </div>
    </div>
</section>

<!-- Scent Finder Banner -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="glass-panel" style="padding: 50px; background: linear-gradient(135deg, rgba(30,41,59,0.8), rgba(15,23,42,0.9)); border:1px solid var(--glass-border-glow);">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:40px; align-items:center;">
                <div>
                    <span style="color:var(--red-main); font-weight:700; text-transform:uppercase; letter-spacing:1px; font-size:0.85rem;">Interactive Fragrance Finder</span>
                    <h2 style="font-size:2.2rem; margin:12px 0 16px;">Find Your Signature Fragrance Profile</h2>
                    <p style="color:var(--text-muted); margin-bottom:24px;">Filter perfumes by fragrance notes (Woody Oud, Velvet Rose, Ocean Fresh, Crimson Saffron) or gender categories.</p>
                    
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <a href="catalog.php?gender=Men" class="btn-glass btn-outline-glass btn-sm">👔 Men's Colognes</a>
                        <a href="catalog.php?gender=Women" class="btn-glass btn-outline-glass btn-sm">💃 Women's Perfumes</a>
                        <a href="catalog.php?gender=Unisex" class="btn-glass btn-outline-glass btn-sm">✨ Unisex Elixirs</a>
                    </div>
                </div>

                <div style="text-align:center; padding:20px; background:rgba(255,255,255,0.03); border-radius:20px; border:1px solid rgba(255,255,255,0.08);">
                    <div style="font-size:3rem; margin-bottom:10px;">🧪</div>
                    <h3 style="font-size:1.3rem; margin-bottom:8px;">Bespoke Fragrance Consultation</h3>
                    <p style="font-size:0.88rem; color:var(--text-muted); margin-bottom:16px;">Unsure which notes suit your body chemistry? Chat with our Lagos master perfumers.</p>
                    <a href="catalog.php" class="btn-glass btn-primary-gradient btn-sm">Explore All Perfumes</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bestsellers Section -->
<section style="padding: 40px 0 80px;">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Most Loved Scents</span>
            <h2 class="section-title">Naira Bestsellers</h2>
        </div>

        <div class="products-grid">
            <?php foreach ($bestseller_products as $product): ?>
                <div class="glass-panel glass-panel-interactive product-card">
                    <div class="product-thumb">
                        <span class="badge-gender badge-<?php echo htmlspecialchars($product['gender']); ?>">
                            <?php echo htmlspecialchars($product['gender']); ?>
                        </span>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                    </div>

                    <div class="product-body">
                        <div class="product-category"><?php echo htmlspecialchars($product['brand']); ?></div>
                        <h3 class="product-title">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" style="color:#fff;">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>

                        <div class="product-price-row">
                            <div>
                                <span class="price-naira"><?php echo formatNaira($product['price']); ?></span>
                            </div>

                            <button onclick="addToCart(<?php echo $product['id']; ?>, 1)" class="btn-glass btn-red btn-sm">
                                🛒 Add
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
