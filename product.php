<?php
require_once "db/conn.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$product = null;

if ($id > 0) {
    $sql = "SELECT id, name, description, price, image_url, category FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($stmt);
}

if (!$product) {
    $title = "Product Not Found";
    require_once "includes/header.php";
    ?>
    <div class="container mt-4">
      <div class="alert alert-danger">Product not found.</div>
      <a href="products.php" class="btn btn-primary">Back to Products</a>
    </div>
    <?php
    require_once "includes/footer.php";
    exit;
}

$title = $product["name"];
require_once "includes/header.php";
?>

</div>

<div class="products-page-wrapper product-detail-wrapper py-4">
  <div class="container-fluid px-5">
    <div class="row g-4">
      <div class="col-md-5">
        <?php if (!empty($product["image_url"])): ?>
          <img src="<?php echo htmlspecialchars($product["image_url"]); ?>"
               class="img-fluid rounded product-detail-image"
               alt="<?php echo htmlspecialchars($product["name"]); ?>">
        <?php else: ?>
          <div class="d-flex align-items-center justify-content-center bg-dark text-muted rounded" style="height:320px;">
            No Image
          </div>
        <?php endif; ?>
      </div>

      <div class="col-md-7 text-light">
        <h1 class="mb-3"><?php echo htmlspecialchars($product["name"]); ?></h1>

        <?php if (!empty($product["category"])): ?>
          <p class="text-secondary mb-2"><?php echo htmlspecialchars($product["category"]); ?></p>
        <?php endif; ?>

        <h3 class="text-primary mb-3">
          $<?php echo number_format($product["price"], 2); ?>
        </h3>

        <p class="mb-4">
          <?php echo nl2br(htmlspecialchars($product["description"])); ?>
        </p>

        <div class="d-flex gap-3">
          <a href="cart.php?action=add&id=<?php echo $product["id"]; ?>" class="btn btn-primary btn-lg">
            Add to Cart
          </a>
          <a href="products.php" class="btn btn-outline-light">
            Back to Products
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once "includes/footer.php";
?>
