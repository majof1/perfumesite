<?php
// catalog.php - Product Catalog & Search Page

$page_title = "Fragrance Catalog";
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();

// Build dynamic SQL query based on filters
$where_clauses = [];
$params = [];

$search = sanitize($_GET['search'] ?? '');
$gender = sanitize($_GET['gender'] ?? '');
$category_id = intval($_GET['category_id'] ?? 0);
$sort = sanitize($_GET['sort'] ?? 'newest');

if (!empty($search)) {
    $where_clauses[] = "(name LIKE ? OR description LIKE ? OR top_notes LIKE ? OR heart_notes LIKE ? OR base_notes LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term, $term]);
}

if (!empty($gender) && in_array($gender, ['Men', 'Women', 'Unisex'])) {
    $where_clauses[] = "gender = ?";
    $params[] = $gender;
}

if ($category_id > 0) {
    $where_clauses[] = "category_id = ?";
    $params[] = $category_id;
}

$sql = "SELECT * FROM products";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY rating DESC";
        break;
    default:
        $sql .= " ORDER BY id DESC";
        break;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch categories for sidebar
$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<section style="padding: 40px 0 80px;">
    <div class="container">
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 2.2rem; margin-bottom: 8px;">Explore Our Fragrance Collection</h1>
            <p style="color: var(--text-muted);">Curated niche perfumes and bespoke elixirs priced in Nigerian Naira (₦).</p>
        </div>

        <!-- Filter & Search Controls (Glass Bar) -->
        <div class="glass-panel" style="padding: 24px; margin-bottom: 40px; border-color: var(--glass-border-glow);">
            <form method="GET" action="catalog.php" style="display:grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap:16px; align-items:end;">
                <div>
                    <label class="form-label">Search Perfume</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search Oud, Rose, Saffron, Jasmine..." class="form-control">
                </div>

                <div>
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">All Genders</option>
                        <option value="Men" <?php echo ($gender === 'Men') ? 'selected' : ''; ?>>Men</option>
                        <option value="Women" <?php echo ($gender === 'Women') ? 'selected' : ''; ?>>Women</option>
                        <option value="Unisex" <?php echo ($gender === 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id === $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-control">
                        <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>New Arrivals</option>
                        <option value="price_asc" <?php echo ($sort === 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo ($sort === 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="rating" <?php echo ($sort === 'rating') ? 'selected' : ''; ?>>Top Rated</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn-glass btn-blue" style="width:100%; height:48px;">
                        🔍 Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <?php if (count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
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
                                🍃 <strong>Top:</strong> <?php echo htmlspecialchars($product['top_notes']); ?>
                            </div>

                            <div class="product-price-row">
                                <div>
                                    <span class="price-naira"><?php echo formatNaira($product['price']); ?></span>
                                    <?php if ($product['original_price']): ?>
                                        <span class="original-price"><?php echo formatNaira($product['original_price']); ?></span>
                                    <?php endif; ?>
                                </div>

                                <button onclick="addToCart(<?php echo $product['id']; ?>, 1)" class="btn-glass btn-primary-gradient btn-sm">
                                    🛒 Add
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="glass-panel" style="padding: 60px; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 16px;">🔎</div>
                <h2>No Perfumes Found</h2>
                <p style="color: var(--text-muted); margin-bottom: 24px;">No matching fragrances were found for your current search criteria.</p>
                <a href="catalog.php" class="btn-glass btn-outline-glass">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
