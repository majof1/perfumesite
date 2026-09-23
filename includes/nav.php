<!-- includes/nav.php - Glassmorphism Navigation Bar -->
<header class="site-header">
    <div class="container navbar">
        <a href="index.php" class="brand-logo">
            <div class="brand-logo-icon">✨</div>
            <span class="brand-name">Élixir De Luxe</span>
        </a>

        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="catalog.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'catalog.php' && !isset($_GET['gender'])) ? 'active' : ''; ?>">All Perfumes</a></li>
            <li><a href="catalog.php?gender=Men" class="nav-link <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'Men') ? 'active' : ''; ?>">For Him</a></li>
            <li><a href="catalog.php?gender=Women" class="nav-link <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'Women') ? 'active' : ''; ?>">For Her</a></li>
            <li><a href="catalog.php?gender=Unisex" class="nav-link <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'Unisex') ? 'active' : ''; ?>">Unisex</a></li>
        </ul>

        <div class="nav-actions">
            <?php if ($is_logged_in): ?>
                <a href="account.php" class="btn-outline-glass btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
                    <span>👤</span> <span><?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
                </a>
                <a href="logout.php" class="btn-outline-glass btn-sm" title="Logout" style="padding:8px 12px; color:#ef4444; border-color:rgba(239, 68, 68, 0.3);">🚪</a>
            <?php else: ?>
                <a href="login.php" class="btn-outline-glass btn-sm">Sign In</a>
                <a href="register.php" class="btn-primary-gradient btn-sm">Join</a>
            <?php endif; ?>

            <a href="cart.php" class="cart-icon-btn" title="View Shopping Cart">
                🛍️
                <span class="cart-badge" style="<?php echo ($cart_count > 0) ? 'display:flex;' : 'display:none;'; ?>">
                    <?php echo $cart_count; ?>
                </span>
            </a>
        </div>
    </div>
</header>
