<?php
require_once "db/conn.php";
session_start();

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$action = $_GET["action"] ?? "";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($action === "add" && $id > 0) {
    $quantity = isset($_POST["quantity"]) ? (int)$_POST["quantity"] : 1;
    if ($quantity < 1) {
        $quantity = 1;
    }

    $sql = "SELECT id, name, price, image_url FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($product) {
        if (!isset($_SESSION["cart"][$id])) {
            $_SESSION["cart"][$id] = [
                "id" => $product["id"],
                "name" => $product["name"],
                "price" => (float)$product["price"],
                "image_url" => $product["image_url"],
                "quantity" => 0
            ];
        }
        $_SESSION["cart"][$id]["quantity"] += $quantity;
    }

    header("Location: cart.php");
    exit;
}

if ($action === "remove" && $id > 0) {
    if (isset($_SESSION["cart"][$id])) {
        unset($_SESSION["cart"][$id]);
    }
    header("Location: cart.php");
    exit;
}

if ($action === "clear") {
    $_SESSION["cart"] = [];
    header("Location: cart.php");
    exit;
}

if ($action === "update" && $_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($_POST["qty"] ?? [] as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;
        if ($qty < 1) {
            $qty = 1;
        }
        if (isset($_SESSION["cart"][$pid])) {
            $_SESSION["cart"][$pid]["quantity"] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

$title = "Cart";
require_once "includes/header.php";

$cartItems = $_SESSION["cart"];
$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item["price"] * $item["quantity"];
}
?>

</div>

<div class="cart-page-wrapper py-4">
  <div class="container-fluid px-5">

    <h1 class="mb-4 fw-bold text-light">Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>
      <div class="alert alert-secondary">
        Your cart is empty.
      </div>
      <a href="products.php" class="btn btn-primary">Browse Products</a>
    <?php else: ?>

      <form action="cart.php?action=update" method="post">
        <div class="card cart-card mb-4">
          <div class="card-body table-responsive">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th style="width:80px;">Image</th>
                  <th>Product</th>
                  <th style="width:120px;">Price</th>
                  <th style="width:120px;">Quantity</th>
                  <th style="width:120px;">Subtotal</th>
                  <th style="width:80px;"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cartItems as $item): ?>
                  <?php $subtotal = $item["price"] * $item["quantity"]; ?>
                  <tr>
                    <td>
                      <?php if (!empty($item["image_url"])): ?>
                        <img src="<?php echo htmlspecialchars($item["image_url"]); ?>" class="cart-item-image" alt="<?php echo htmlspecialchars($item["name"]); ?>">
                      <?php else: ?>
                        <div class="cart-item-placeholder d-flex align-items-center justify-content-center">
                          <span>No Image</span>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="product.php?id=<?php echo $item["id"]; ?>">
                        <?php echo htmlspecialchars($item["name"]); ?>
                      </a>
                    </td>
                    <td>$<?php echo number_format($item["price"], 2); ?></td>
                    <td>
                      <input type="number" name="qty[<?php echo $item["id"]; ?>]" value="<?php echo $item["quantity"]; ?>" min="1" class="form-control form-control-sm">
                    </td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                    <td>
                      <a href="cart.php?action=remove&id=<?php echo $item["id"]; ?>" class="btn btn-sm btn-outline-danger">X</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
              <button type="submit" class="btn btn-outline-secondary btn-sm me-2">Update Cart</button>
              <a href="cart.php?action=clear" class="btn btn-outline-danger btn-sm">Clear Cart</a>
            </div>
            <div class="fw-bold fs-5">
              Total: $<?php echo number_format($cartTotal, 2); ?>
            </div>
          </div>
        </div>
      </form>

      <div class="d-flex justify-content-between">
        <a href="products.php" class="btn btn-outline-secondary">Continue Shopping</a>
        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
      </div>

    <?php endif; ?>
  </div>
</div>

<?php
require_once "includes/footer.php";
?>
