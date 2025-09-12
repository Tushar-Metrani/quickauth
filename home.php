<?php
error_reporting(0);

error_reporting(0);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

require_once '_partials/refresh_session.php';
require_once '_partials/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['username'])) {
  header("location:login.php");
  exit();
}

if (!empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  refresh_variables();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      overflow: clip;
      background-color: #f0f2f5 !important;
      background-image: none;
      margin: 10px 0px;
      max-height: max-content;
    }

    .profile-card {
      max-width: 600px;
      max-height: max-content;
      margin: 30px auto;
      padding: 30px;
      border-radius: 15px;
      background-color: #fff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-pic {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #007bff;
    }
  </style>
</head>

<body>

  <div id="profile" >
  <div class="profile-card">
    <div class="container mb-2 text-center d-flex justify-content-center">
      <div class="alert text-center" role="alert" id="msg"></div>
    </div>

    <div class="text-center position-relative">

      <div class="d-flex position-absolute end-0 top-0 m-0 sm-m-3 align-items-center">
        <button type="button" id="edit-btn" class="btn btn-dark" onclick="toggleEdit()">
          <span class="d-none fw-medium fs-6 me-1 d-sm-inline-block">Edit</span>
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="22px" fill="#FFFFFF">
            <path d="M186.67-186.67H235L680-631l-48.33-48.33-445 444.33v48.33ZM120-120v-142l559.33-558.33q9.34-9 21.5-14 12.17-5 25.5-5 12.67 0 25 5 12.34 5 22 14.33L821-772q10 9.67 14.5 22t4.5 24.67q0 12.66-4.83 25.16-4.84 12.5-14.17 21.84L262-120H120Zm652.67-606-46-46 46 46Zm-117 71-24-24.33L680-631l-24.33-24Z" />
          </svg>
        </button>
      </div>
      <img src="img/profile.png" alt="Profile Picture" class="profile-pic mb-3">
      <h3 class="mb-1"><?php echo $_SESSION['username']; ?></h3>
      <p class="text-muted mb-3"><?php echo $_SESSION['email']; ?></p>
      <hr>
      <h5>About Me</h5>
      <p class="text-justify">
        <?php
        echo
        $_SESSION['about'] !== NULL ? $_SESSION['about'] :
          "Web developer with a focus on clean UI, resilient backend systems, and thoughtful, sustainable design.
          Fueled by code, curiosity, and the occasional oat latte."
        ?>
      </p>
      <div class="mt-4">
        <form method="POST" action="logout.php">
          <button class="btn btn-warning">Logout</button>
        </form>
      </div>
    </div>

  </div>

  <div class="container d-flex justify-content-center">
  <form method="POST" action="delete_account.php">
    <button class="btn btn-danger">Delete Account</button>
  </form>
  </div>
  </div>


  <div id="edit-container" class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Edit Profile Information</h5>
          </div>
          <div class="card-body">
            <form id="edit-form" action="edit.php" method="post">

              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="uname" name="uname" value=<?php echo $_SESSION['username'] ?>>
                <div id="nameError" class="error"></div>
              </div>

              <div class="mb-3">
                <label for="about" class="form-label">About</label>
                <textarea class="form-control" id="about" rows="4" name="about"><?php echo ($_SESSION['about'] !== NULL) ? $_SESSION['about'] : "" ?></textarea>
              </div>

              <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-outline-danger me-2" onclick="toggleEdit()">Discard</button>
                <button type="submit" id="submit-btn" class="btn btn-success ms-2">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <script>
    const msgbox = document.getElementById("msg");

    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) {
        return parts.pop().split(';').shift();
      }
      return null;
    }

    if (getCookie("msg") !== null) {
      const msgvalue = getCookie("msg");
      msgbox.classList.add("alert-primary");
      msgbox.innerText = decodeURIComponent(msgvalue);
      msgbox.style.display = "block";
      setTimeout(() => {
        msgbox.style.display = "none";
        document.cookie = "msg=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      }, 5000);
    }
    if (getCookie("error") !== null) {
      const msgvalue = getCookie("error");
      msgbox.classList.add("alert-danger");
      msgbox.innerText = decodeURIComponent(msgvalue);
      msgbox.style.display = "block";

      setTimeout(() => {
        msgbox.style.display = "none";
        document.cookie = "error=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      }, 5000);
    }

    if (getCookie("success") !== null) {
      const msgvalue = getCookie("success");
      msgbox.classList.add("alert-success");
      msgbox.innerText = decodeURIComponent(msgvalue);
      msgbox.style.display = "block";
      setTimeout(() => {
        msgbox.style.display = "none";
        document.cookie = "success=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      }, 5000);
    }
    const profile = document.getElementById("profile");
    const editform = document.getElementById("edit-container");
    profile.style.display = "block";
    editform.style.display = "none";

    function toggleEdit() {
      profile.style.display = profile.style.display === "none" ? "block" : "none";
      editform.style.display = editform.style.display === "none" ? "block" : "none";
    }
    const form = document.getElementById("edit-form");
    const name = form.elements["uname"];

    name.addEventListener("input", () => {
      document.getElementById("nameError").style.display = "none";
    });

    function validateForm() {
      const name = form.elements["uname"].value.trim();

      let isValid = true;
      document.getElementById("nameError").textContent = "";

      if (name === "") {
        document.getElementById("nameError").textContent = "Name is required.";
        document.getElementById("nameError").style.display = "block";
        isValid = false;
      }
      console.log(isValid);
      return isValid;
    }


    submitbtn = document.getElementById("submit-btn");

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (validateForm()) {
        submitbtn.disabled = true;
        form.submit();
      }
    });
  </script>
</body>

</html>