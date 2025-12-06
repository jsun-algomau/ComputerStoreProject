<?php
require_once "db/conn.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];

$sql = "SELECT id, total_price, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$ordersResult = mysqli_stmt_get_result($stmt);
$orders = [];
while ($row = mysqli_fetch_assoc($ordersResult)) {
    $orders[] = $row;
}
mysqli_stmt_close($stmt);

$orderIds = array_column($orders, "id");
$itemsByOrder = [];

if (!empty($orderIds)) {
    $placeholders = implode(",", array_fill(0, count($orderIds), "?"));
    $types = str_repeat("i", count($orderIds));

    $sqlItems = "
        SELECT oi.order_id, oi.product_id, oi.quantity, oi.price, p.name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id IN ($placeholders)
        ORDER BY oi.order_id DESC, p.name
    ";
    $stmtItems = mysqli_prepare($conn, $sqlItems);
    mysqli_stmt_bind_param($stmtItems, $types, ...$orderIds);
    mysqli_stmt_execute($stmtItems);
    $itemsResult = mysqli_stmt_get_result($stmtItems);

    while ($row = mysqli_fetch_assoc($itemsResult)) {
        $oid = $row["order_id"];
        if (!isset($itemsByOrder[$oid])) {
            $itemsByOrder[$oid] = [];
        }
        $itemsByOrder[$oid][] = $row;
    }
    mysqli_stmt_close($stmtItems);
}

$title = "My Orders";
require_once "includes/header.php";
?>

<div class="orders-page-wrapper py-4">
  <div class="container">
    <h1 class="mb-4 fw-bold text-light">My Orders</h1>

    <?php if (empty($orders)): ?>
      <div class="alert alert-secondary">
        You have no orders yet.
      </div>
      <a href="products.php" class="btn btn-primary">Shop Now</a>
    <?php else: ?>
      <?php foreach ($orders as $order): ?>
        <div class="card mb-4 order-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <span class="fw-semibold">Order ID:</span>
              <span><?php echo $order["id"]; ?></span>
            </div>
            <div>
              <span class="fw-semibold">Date:</span>
              <span><?php echo $order["created_at"]; ?></span>
            </div>
            <div class="fw-bold">
              Total: $<?php echo number_format($order["total_price"], 2); ?>
            </div>
          </div>
          <div class="card-body table-responsive">
            <table class="table mb-0 align-middle">
              <thead>
                <tr>
                  <th>Product</th>
                  <th style="width:120px;">Price</th>
                  <th style="width:120px;">Quantity</th>
                  <th style="width:120px;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($itemsByOrder[$order["id"]] ?? [] as $item): ?>
                  <?php $subtotal = $item["price"] * $item["quantity"]; ?>
                  <tr>
                    <td><?php echo htmlspecialchars($item["name"]); ?></td>
                    <td>$<?php echo number_format($item["price"], 2); ?></td>
                    <td><?php echo $item["quantity"]; ?></td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php
require_once "includes/footer.php";
?>
