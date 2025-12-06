<?php
$title = "Products";
require_once "includes/header.php";
require_once "db/conn.php";

$search = trim($_GET["search"] ?? "");
$categoryFilter = trim($_GET["category"] ?? "");
$minPrice = trim($_GET["min_price"] ?? "");
$maxPrice = trim($_GET["max_price"] ?? "");
$sortBy = $_GET["sort_by"] ?? "name";
$order = $_GET["order"] ?? "ASC";

$allowedSort = ["name" => "name", "price" => "price", "created_at" => "created_at"];
$sortColumn = $allowedSort[$sortBy] ?? "name";
$order = strtoupper($order) === "DESC" ? "DESC" : "ASC";

$categories = [];
$catSql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> '' ORDER BY category";
$catResult = mysqli_query($conn, $catSql);
if ($catResult) {
    while ($row = mysqli_fetch_assoc($catResult)) {
        $categories[] = $row["category"];
    }
}

$sql = "SELECT id, name, description, price, image_url, category FROM products WHERE 1";
$params = [];
$types = "";

if ($search !== "") {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

if ($categoryFilter !== "" && $categoryFilter !== "all") {
    $sql .= " AND category = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

if ($minPrice !== "" && is_numeric($minPrice)) {
    $sql .= " AND price >= ?";
    $params[] = (float)$minPrice;
    $types .= "d";
}

if ($maxPrice !== "" && is_numeric($maxPrice)) {
    $sql .= " AND price <= ?";
    $params[] = (float)$maxPrice;
    $types .= "d";
}

$sql .= " ORDER BY " . $sortColumn . " " . $order;

$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    echo "Query error";
    require_once "includes/footer.php";
    exit;
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

</div> <!-- close header container -->

<div class="products-page-wrapper py-4">
  <div class="container-fluid px-5">
    <h1 class="mb-4 fw-bold text-light">All Products</h1>

    <div class="card mb-4 products-filter-card w-100">
      <div class="card-header fw-semibold text-white">
        Filter &amp; Search Products
      </div>
      <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="products.php">
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Product name or description" value="<?php echo htmlspecialchars($search); ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
              <option value="all">All Categories</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $categoryFilter === $cat ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label">Min Price</label>
            <input type="number" step="0.01" name="min_price" class="form-control" placeholder="Min" value="<?php echo htmlspecialchars($minPrice); ?>">
          </div>

          <div class="col-md-2">
            <label class="form-label">Max Price</label>
            <input type="number" step="0.01" name="max_price" class="form-control" placeholder="Max" value="<?php echo htmlspecialchars($maxPrice); ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label">Sort By</label>
            <select name="sort_by" class="form-select">
              <option value="name" <?php echo $sortBy === "name" ? "selected" : ""; ?>>Name</option>
              <option value="price" <?php echo $sortBy === "price" ? "selected" : ""; ?>>Price</option>
              <option value="created_at" <?php echo $sortBy === "created_at" ? "selected" : ""; ?>>Newest</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label">Order</label>
            <select name="order" class="form-select">
              <option value="ASC" <?php echo $order === "ASC" ? "selected" : ""; ?>>Ascending</option>
              <option value="DESC" <?php echo $order === "DESC" ? "selected" : ""; ?>>Descending</option>
            </select>
          </div>

          <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">Apply</button>
          </div>

          <div class="col-md-2 d-grid">
            <a href="products.php" class="btn btn-outline-secondary">Clear</a>
          </div>
        </form>
      </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
      <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <div class="col">
            <div class="card h-100 product-card">
              <?php if (!empty($row["image_url"])): ?>
               <a href="product.php?id=<?php echo $row["id"]; ?>">
                 <img src="<?php echo htmlspecialchars($row["image_url"]); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($row["name"]); ?>">
               </a>
             <?php else: ?>
               <a href="product.php?id=<?php echo $row["id"]; ?>" class="text-decoration-none">
                 <div class="product-image placeholder d-flex align-items-center justify-content-center">
                  <span>No Image</span>
                 </div>
               </a>
             <?php endif; ?>
             <div class="card-body">
  <h5 class="card-title">
    <a href="product.php?id=<?php echo $row["id"]; ?>" class="text-decoration-none text-dark">
      <?php echo htmlspecialchars($row["name"]); ?>
    </a>
  </h5>


                <p class="card-text text-muted small mb-2">
                  <?php echo htmlspecialchars($row["category"]); ?>
                </p>
                <p class="card-text product-description">
                  <?php echo htmlspecialchars(mb_strimwidth($row["description"], 0, 120, "...")); ?>
                </p>
              </div>
              <div class="card-footer d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary">$<?php echo number_format($row["price"], 2); ?></span>
                <a href="cart.php?action=add&id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-outline-primary">
                  Add to Cart
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col">
          <div class="alert alert-secondary">
            No products found.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
mysqli_stmt_close($stmt);
require_once "includes/footer.php";
?>
