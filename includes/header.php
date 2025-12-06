<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($title)) {
    $title = "Online Computer Store";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <title><?php echo $title; ?></title>
</head>
<body>
<div class="page-wrapper d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">My Computer Store</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
        </ul>

        <div class="d-flex">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="navbar-text text-white me-3">
                Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>
            <a class="btn btn-outline-light me-2" href="orders.php">My Orders</a>
            <a class="btn btn-warning" href="logout.php">Logout</a>
        <?php else: ?>
            <a class="btn btn-outline-light me-2" href="login.php">Login</a>
            <a class="btn btn-warning" href="register.php">Register</a>
        <?php endif; ?>
        </div>
    </div>
  </div>
</nav>

<div class="container mt-4">
