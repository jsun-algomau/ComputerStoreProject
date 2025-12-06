<?php
require_once "db/conn.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];
$cartItems = $_SESSION["cart"];
$total = 0;
foreach ($cartItems as $item) {
    $total += $item["price"] * $item["quantity"];
}

mysqli_begin_transaction($conn);

try {
    $sqlOrder = "INSERT INTO orders (user_id, total_price, created_at) VALUES (?, ?, NOW())";
    $stmtOrder = mysqli_prepare($conn, $sqlOrder);
    mysqli_stmt_bind_param($stmtOrder, "id", $userId, $total);
    mysqli_stmt_execute($stmtOrder);
    $orderId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmtOrder);

    $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmtItem = mysqli_prepare($conn, $sqlItem);

    foreach ($cartItems as $item) {
        $pid = (int)$item["id"];
        $qty = (int)$item["quantity"];
        $price = (float)$item["price"];
        mysqli_stmt_bind_param($stmtItem, "iiid", $orderId, $pid, $qty, $price);
        mysqli_stmt_execute($stmtItem);
    }

    mysqli_stmt_close($stmtItem);
    mysqli_commit($conn);

    $_SESSION["cart"] = [];
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: cart.php");
    exit;
}

$title = "Order Placed";
require_once "includes/header.php";
?>

<div class="checkout-page-wrapper py-4">
  <div class="container">
    <h1 class="mb-4 fw-bold text-light">Order Placed</h1>
    <div class="card">
      <div class="card-body">
        <p class="mb-3">Thank you for your order.</p>
        <p class="mb-3">Order ID: <strong><?php echo $orderId; ?></strong></p>
        <p class="mb-4">Total: <strong>$<?php echo number_format($total, 2); ?></strong></p>
        <a href="orders.php" class="btn btn-primary me-2">View My Orders</a>
        <a href="products.php" class="btn btn-outline-secondary">Continue Shopping</a>
      </div>
    </div>
  </div>
</div>

<?php
require_once "includes/footer.php";
?>

