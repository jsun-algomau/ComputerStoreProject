<?php
$title = "Login";
require_once "includes/header.php";
?>

<div class="row justify-content-center mt-4">
  <div class="col-md-6 col-lg-5">
    <h2 class="mb-4 text-center">Login</h2>

    <form action="login_process.php" method="POST">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
      <div class="text-center">
        <a href="register.php">Don't have an account? Register</a>
      </div>
    </form>
  </div>
</div>

<?php
require_once "includes/footer.php";
?>
