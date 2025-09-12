<?php
error_reporting(0);

require_once '_partials/db_connect.php';
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = $_SESSION['email'];
    $sql = "DELETE FROM `users` WHERE `email` = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        if ($stmt->affected_rows === 1) {
            setcookie('remember_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            $_SESSION = [];
            session_unset();
            session_destroy();
            setcookie("success", "Account Deleted Successfully!", time() + 600, "/");
            //header("location:login.php");
            //exit();
        } 
        else {
            setcookie("error", "Account Could Not Be Deleted, Account does not exist !", time() + 600, "/");
        }
    }
}
else{
    header("location:home.php",true,302);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deletion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <div class="conatiner text-center d-flex justify-content-center align-items-center vh-100">
        <div class="alert" role="alert" id="msg"></div>
    </div>
    <script>
        const msgbox = document.getElementById("msg");

        //Cookie value retriving function
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) {
                return parts.pop().split(';').shift();
            }
            return null;
        }

        //Displaying messsages set using cookies
        if (getCookie("msg") !== null) {
            const msgvalue = getCookie("msg");
            msgbox.classList.add("alert-primary");
            msgbox.innerText = decodeURIComponent(msgvalue);
            msgbox.style.display = "block";
            document.cookie = "msg=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 5000);
        }
        if (getCookie("error") !== null) {
            const msgvalue = getCookie("error");
            msgbox.classList.add("alert-danger");
            msgbox.innerText = decodeURIComponent(msgvalue);
            msgbox.style.display = "block";
            document.cookie = "error=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 5000);
        }

        if (getCookie("success") !== null) {
            const msgvalue = getCookie("success");
            msgbox.classList.add("alert-success");
            msgbox.innerText = decodeURIComponent(msgvalue);
            msgbox.style.display = "block";
            document.cookie = "success=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 5000);
        }
    </script>
</body>

</html>