<?php
session_start();
require_once "db/conn.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirm = $_POST["confirm"] ?? "";

if ($name === "" || $email === "" || $password === "" || $confirm === "") {
    echo "All fields are required. <a href='register.php'>Back</a>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format. <a href='register.php'>Back</a>";
    exit;
}

if ($password !== $confirm) {
    echo "Passwords do not match. <a href='register.php'>Back</a>";
    exit;
}

$checkSql = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    echo "Email already registered. <a href='login.php'>Login</a> or <a href='register.php'>Back</a>";
    exit;
}
mysqli_stmt_close($stmt);

$hash = password_hash($password, PASSWORD_DEFAULT);

$insertSql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash);

if (mysqli_stmt_execute($stmt)) {
    $newId = mysqli_insert_id($conn);
    $_SESSION["user_id"] = $newId;
    $_SESSION["user_name"] = $name;
    $_SESSION["is_admin"] = 0;
    mysqli_stmt_close($stmt);
    header("Location: index.php");
    exit;
} else {
    mysqli_stmt_close($stmt);
    echo "Registration failed. <a href='register.php'>Back</a>";
    exit;
}
