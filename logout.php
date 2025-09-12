<?php
error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_COOKIE['remember_token'])) {
    require_once '_partials/db_connect.php';

    $token = hash("sha256",$_COOKIE['remember_token']);

    $stmt = $conn->prepare("UPDATE `users` SET `remember_token`= NULL,`remember_expiry`= NULL WHERE `remember_token`= ?;");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();

    setcookie('remember_token', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => false,           
    'httponly' => true,
    'samesite' => 'Strict'
    ]);
}

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']==true){
    $_SESSION = [];
    session_unset();
    session_destroy();
    header("location:login.php");
    exit();
}
exit();
?>