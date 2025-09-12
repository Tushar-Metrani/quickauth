<?php
error_reporting(0);

require_once '_partials/db_connect.php';
require_once '_partials/refresh_session.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $uname = trim($_POST["uname"]);
    $about = $_POST['about'];
    $email = $_SESSION['email'];

    $sql = "UPDATE `users` SET `name`=?,`about`=? WHERE `email` = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $uname, $about, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($stmt->execute()) {
        //header("Location: login.php?success=1");
        setcookie("success", "Account updated Successfully!", time() + 600, "/");
    } else {
        //header("Location: signup.php?success=0");
        setcookie("error", "Account Could not be updated!", time() + 600, "/");
    }

    refresh_variables();
    header('location:home.php', true, 302);
    exit();
}
