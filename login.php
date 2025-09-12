<?php
error_reporting(0);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

require_once '_partials/db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

//remember me check
if (isset($_COOKIE['remember_token'])) {
  $token = hash("sha256", $_COOKIE['remember_token']);
  $sql = "SELECT `name`, `email`, `about` FROM `users` WHERE `remember_token` = ? ;";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $token);
  if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
      $result_data = $result->fetch_assoc();
      $name = $result_data['name'];
      $email = $result_data['email'];
      $about = $result_data['about'];
      session_regenerate_id(true);
      $_SESSION['username'] = $name;
      $_SESSION['email'] = $email;
      $_SESSION['about'] = $about;
      $_SESSION['loggedin'] = true;
      echo $_SESSION['loggedin'];
      header('location:home.php', true, 302);
      exit();
    } else {
      setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict'
      ]);
    }
  }
}

//Loggedin check
if (isset($_SESSION['username'])) {
  header("location:home.php", true, 302);
  exit();
}

//Authenticating user
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $cpass = false;
  $email = $_POST['email'];
  $pass = $_POST['password'];
  $remember = isset($_POST['remember-check']);
  $sql = "SELECT `name`, `email`, `about`, `pass` FROM `users` WHERE `email` = ? ;";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows === 1) {
    $result_data = $result->fetch_assoc();
    $name = $result_data['name'];
    $email = $result_data['email'];
    $about = $result_data['about'];
    $hpass = $result_data['pass'];
    $cpass = password_verify($pass, $hpass);
    if ($cpass == true) {
      if ($remember) {
        $token = bin2hex(random_bytes(16));
        $expiry_timestamp = time() + (30 * 86400);
        $expiry = date('Y-m-d H:i:s', $expiry_timestamp);
        $sql = "UPDATE `users` SET `remember_token`= ?,`remember_expiry`= ? WHERE `email` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", hash("sha256", $token), $expiry, $email);
        $stmt->execute();
        setcookie("remember_token", $token, $expiry_timestamp, "/", "", false, true);
      }
      session_regenerate_id(true);
      $_SESSION['username'] = $name;
      $_SESSION['email'] = $email;
      $_SESSION['about'] = $about;
      $_SESSION['loggedin'] = true;
      //setcookie("success", "Login succesful!", time() + 600, "/");
      header('location:home.php', true, 302);
    } else {
      //echo "invalid password";
      setcookie("error", "Wrong Password!", time() + 600, "/");
    }
  } else {
    //echo "user not found";
    setcookie("msg", "User Not Found! Please Register", time() + 600, "/");
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
</head>

<body class="bg-dark text-white d-flex flex-row align-items-center justify-content-center" style="height:100vh">


  <div class="border border-primary border-2 px-5 py-4 rounded bg-white text-dark">

    <h3 class="text-center mb-3">Login</h3>

    <div class="alert text-center" role="alert" id="msg"></div>

    <form id="login-form" action="login.php" method="POST" novalidate>
      <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required><label for="email">Email</label>
        <div id="emailError" class="error"></div>
      </div>

      <div class="form-floating mb-3">
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required><label for="password">Password</label>
        <div id="passwordError" class="error"></div>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember-check" name="remember-check">
        <label class="form-check-label" for="form-check-input">Remember me for 30 days</label>
      </div>
      <button type="submit" id="login-btn" class="btn btn-primary mx-auto w-100">Login</button>
    </form>
    <div class="container text-center mt-2">
      <span>Don't have an account?</span>
      <b><a href="signup.php">Register</a></b>
    </div>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <script>
    const msgbox = document.getElementById("msg");

    //Cookie value retrieving function
    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) {
        return parts.pop().split(';').shift();
      }
      return null;
    }

    //Displaying messages set using cookie
    if (getCookie("msg") !== null) {
      const msgvalue = getCookie("msg");
      msgbox.classList.add("alert-primary");
      msgbox.innerText = decodeURIComponent(msgvalue);
      msgbox.style.display = "block";
      document.cookie = "msg=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
    if (getCookie("error") !== null) {
      const msgvalue = getCookie("error");
      msgbox.classList.add("alert-danger");
      msgbox.innerText = decodeURIComponent(msgvalue);
      msgbox.style.display = "block";
      document.cookie = "error=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    if (getCookie("success") !== null) {
      const msgvalue = getCookie("success");
      msgbox.classList.add("alert-success");
      msgbox.innerText = decodeURIComponent(msgvalue);
      msgbox.style.display = "block";
      document.cookie = "success=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    const form = document.getElementById("login-form");
    const checkbox = document.getElementById("remember-check");
    const button = document.getElementById("login-btn");

    const email = form.elements["email"];
    const password = form.elements["password"];

    checkbox.checked = false;

    //Hiding input errors on input change
    email.addEventListener("input", () => {
      document.getElementById("emailError").style.display = "none";
    });

    password.addEventListener("input", () => {
      document.getElementById("passwordError").style.display = "none";
    });

    //Form Validation Function
    function validateForm() {
      const email = form.elements["email"].value.trim();
      const password = form.elements["password"].value.trim();

      let isValid = true;
      document.getElementById("emailError").textContent = "";
      document.getElementById("passwordError").textContent = "";
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (email === "") {
        document.getElementById("emailError").textContent = "Email is required.";
        document.getElementById("emailError").style.display = "block";
        isValid = false;
      } else if (!emailRegex.test(email)) {
        document.getElementById("emailError").textContent = "Invalid email address.";
        document.getElementById("emailError").style.display = "block";
        isValid = false;
      }

      if (password === "") {
        document.getElementById("passwordError").textContent = "Password is required.";
        document.getElementById("passwordError").style.display = "block";
        isValid = false;
      }
      //console.log(isValid);
      return isValid;
    }

    //Form submition handling
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (validateForm()) {
        button.disabled = true;
        form.submit();
      }
    });
  </script>
</body>

</html>