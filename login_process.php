<?php
session_start();
require_once "db/conn.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    echo "Email and password are required. <a href='login.php'>Back</a>";
    exit;
}

$sql = "SELECT id, name, email, password, is_admin FROM users WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $hash = $row["password"];
    if (password_verify($password, $hash)) {
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["user_name"] = $row["name"];
        $_SESSION["is_admin"] = (int)$row["is_admin"];
        mysqli_stmt_close($stmt);
        header("Location: index.php");
        exit;
    } else {
        mysqli_stmt_close($stmt);
        echo "Incorrect password. <a href='login.php'>Back</a>";
        exit;
    }
} else {
    mysqli_stmt_close($stmt);
    echo "User not found. <a href='register.php'>Register</a> or <a href='login.php'>Back</a>";
    exit;
}
