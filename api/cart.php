<?php
// api/cart.php - Shopping Cart API Handler

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid action'];

$db = getDBConnection();

if ($action === 'add') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    if ($product_id > 0) {
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'brand' => $product['brand'],
                    'price' => floatval($product['price']),
                    'image_url' => $product['image_url'],
                    'quantity' => $quantity,
                    'volume' => $product['volume']
                ];
            }

            $total_count = array_sum(array_column($_SESSION['cart'], 'quantity'));

            $response = [
                'status' => 'success',
                'message' => htmlspecialchars($product['name']) . " added to cart!",
                'total_count' => $total_count,
                'cart' => $_SESSION['cart']
            ];
        } else {
            $response['message'] = "Product not found";
        }
    }
} elseif ($action === 'update') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = max(0, intval($_POST['quantity'] ?? 0));

    if ($quantity === 0) {
        unset($_SESSION['cart'][$product_id]);
    } elseif (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }

    $total_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
    $response = [
        'status' => 'success',
        'message' => "Cart updated",
        'total_count' => $total_count
    ];
} elseif ($action === 'remove') {
    $product_id = intval($_POST['product_id'] ?? $_GET['id'] ?? 0);
    unset($_SESSION['cart'][$product_id]);
    
    $total_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
    
    if (isset($_GET['redirect'])) {
        header("Location: ../cart.php");
        exit();
    }

    $response = [
        'status' => 'success',
        'message' => "Item removed from cart",
        'total_count' => $total_count
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
