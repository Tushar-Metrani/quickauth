<?php
error_reporting(0); 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
require_once '_partials/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

//loggedin check
if (isset($_SESSION['username'])) {
  header("location:home.php", true, 302);
  exit();
}

//remember check
if (isset($_COOKIE['remember_token'])) {
  header("location:login.php", true, 302);
  exit();
}


//Creating Account
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $uname = trim($_POST["uname"]);
  $email = $_POST['email'];
  $pass = $_POST['password'];
  $cpass = $_POST['confirmPassword'];
  $hashpass = password_hash($pass, PASSWORD_DEFAULT);

  $sql = "SELECT `email` FROM `users` WHERE `email` = ? ;";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  //echo $result->num_rows;
  if ($result->num_rows === 0) {
    $stmt->close();

    $sql = "INSERT INTO `users` (`name`, `email`, `pass`) VALUES (?, ?, ?);";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("sss", $uname, $email, $hashpass);

    if ($stmt->execute()) {
      //header("Location: login.php?success=1");
      setcookie("success", "Account created Successfully!", time() + 600, "/");
    } else {
      //header("Location: signup.php?success=0");
      setcookie("error", "Account Could not be created!", time() + 600, "/");
    }
  } else {
    setcookie("msg", "This Email is already registered!", time() + 600, "/");
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup</title>

  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
</head>

<body class="bg-dark text-white d-flex flex-row align-items-center justify-content-center" style="height:100vh">

  <div class="border border-primary border-2 px-5 py-4 rounded bg-white text-dark">


    <h3 class="text-center mb-3 ">Register</h3>
    <div class="alert text-center" role="alert" id="msg"></div>

    <form id="signup-form" method="POST" action="signup.php" novalidate>
      <div class="form-floating mb-3">

        <input type="text" class="form-control" id="uname" name="uname" placeholder="Enter Name" required>
        <label for="uname">Name</label>
        <div id="nameError" class="error"></div>
      </div>
      <div class="form-floating mb-3">

        <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email" required>
        <label for="email">Email address</label>
        <div id="emailError" class="error"></div>
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
      </div>
      <div class="form-floating mb-3">

        <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
        <label for="pass">Password</label>
        <div id="passwordError" class="error"></div>
      </div>
      <div class="form-floating mb-3">

        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Enter Confirm Password" required>
        <label for="cpass">Confirm Password</label>
        <div id="confirmPasswordError" class="error"></div>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="terms-check" required>
        <label class="form-check-label" for="terms-check">I agree with Terms and conditions</label>
      </div>
      <button type="submit" id="submit-btn" class="btn btn-primary text-center mx-auto w-100" disabled>Register</button>
    </form>

    <div class="container text-center mt-2">
      <span>Already have an account ?</span>

      <b><a href="login.php">Login</a></b>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

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

    const form = document.getElementById("signup-form");
    const checkbox = document.getElementById("terms-check");
    const button = document.getElementById("submit-btn");

    const name = form.elements["uname"];
    const email = form.elements["email"];
    const password = form.elements["password"];
    const confirmPassword = form.elements["confirmPassword"];

    checkbox.checked = false;


    checkbox.addEventListener("change", () => {
      button.disabled = !checkbox.checked;
    });

    //Hiding error messages on input change

    name.addEventListener("input", () => {
      document.getElementById("nameError").style.display = "none";
    });

    email.addEventListener("input", () => {
      document.getElementById("emailError").style.display = "none";
    });

    password.addEventListener("input", () => {
      document.getElementById("passwordError").style.display = "none";
    });

    confirmPassword.addEventListener("input", () => {
      document.getElementById("confirmPasswordError").style.display = "none";
    });

    //Form Validation Function
    function validateForm() {
      const form = document.getElementById("signup-form");
      const name = form.elements["uname"].value.trim();
      const email = form.elements["email"].value.trim();
      const password = form.elements["password"].value.trim();
      const confirmPassword = form.elements["confirmPassword"].value.trim();

      let isValid = true;
      document.getElementById("nameError").textContent = "";
      document.getElementById("emailError").textContent = "";
      document.getElementById("passwordError").textContent = "";
      document.getElementById("confirmPasswordError").textContent = "";
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (name === "") {
        document.getElementById("nameError").textContent = "Name is required.";
        document.getElementById("nameError").style.display = "block";
        isValid = false;
        //console.log("n");
      }

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
        //console.log("p");
      } else if (password.length < 6) {
        document.getElementById("passwordError").textContent = "Password must be at least 6 characters.";
        document.getElementById("passwordError").style.display = "block";
        isValid = false;
        //console.log("p");
      }

      if (password !== confirmPassword) {
        document.getElementById("confirmPasswordError").textContent = "Passwords do not match.";
        document.getElementById("confirmPasswordError").style.display = "block";
        isValid = false;
        //console.log("cnf");
      }

      //console.log(isValid);
      return isValid;
    }

    //Form submit handling
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