<?php
// includes/header.php - Global Header & Head HTML

require_once __DIR__ . '/../config/db.php';

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'Account';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | Élixir De Luxe' : 'Élixir De Luxe - Luxury Fragrances & Perfumes (Nigeria)'; ?></title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Discover premium luxury perfumes, bespoke fragrances, and unisex scents in Nigeria. Prices in Naira (₦) with instant checkout & fast nationwide delivery.">
    <meta name="keywords" content="perfumes, luxury fragrance, perfume online store nigeria, buy cologne lagos, naira perfume store, oud perfume">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Glassmorphism Stylesheet -->
    <link rel="stylesheet" href="assets/css/glassmorphism.css">
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<main class="main-content">
