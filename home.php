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
  <style>
    body{
      background: linear-gradient(135deg, #2c2c2c, #1a1a1a);
    }
    .profile-img {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #ffffff;
      background-color: #ffffff;
    }

    .max-var{
      max-width: 500px;
    }

  </style>
</head>

<body class="bg-body-secondary min-vh-100">

  <div id="profile" class="container d-flex felx-column justify-content-center p-0">
    <div class="toast-container position-fixed bottom-0 end-0 p-3 mb-2 text-center d-flex justify-content-center">
        <div class="alert text-center shadow" role="alert" id="msg"></div>
    </div>
    <div class="container p-0">
        <div class="d-flex justify-content-end align-items-center my-4">
          <button type="button" id="edit-btn" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="toggleEdit()">
            <span class="d-none fw-medium fs-6 me-1 d-sm-inline-block">Edit</span>
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="22px" fill="#FFFFFF">
              <path d="M186.67-186.67H235L680-631l-48.33-48.33-445 444.33v48.33ZM120-120v-142l559.33-558.33q9.34-9 21.5-14 12.17-5 25.5-5 12.67 0 25 5 12.34 5 22 14.33L821-772q10 9.67 14.5 22t4.5 24.67q0 12.66-4.83 25.16-4.84 12.5-14.17 21.84L262-120H120Zm652.67-606-46-46 46 46Zm-117 71-24-24.33L680-631l-24.33-24Z" />
            </svg>
          </button>
        </div>

        <div id="pf-name" class="d-flex flex-column align-items-center mb-5 text-light">
          <img src="img/profile.png" alt="Profile Picture" class="profile-img mb-3 shadow">
          <h3 class="mb-1 fw-bold"><?php echo $_SESSION['username']; ?></h3>
          <p class="mb-3 fs-6"><?php echo $_SESSION['email']; ?></p>

        </div>

        <div class="container d-flex flex-column flex-lg-row justify-content-center gap-4">

        <div id="left" class="text-light max-var mx-3">
          <div id="about" class="card text-start p-4 rounded-4 border-0 shadow-sm">
            <h5 class="fs-5 fw-semibold mb-3">About Me</h5>
            <p class="fs-6 text-secondary mb-0">
              <?php
              echo
              $_SESSION['about'] !== NULL ? $_SESSION['about'] :
                "Web developer with a focus on clean UI, resilient backend systems, and thoughtful, sustainable design.
          Fueled by code, curiosity, and the occasional oat latte."
              ?>
            </p>
          </div>
          <div class="d-flex mt-4">
            <form method="POST" action="logout.php">
              <button class="btn btn-outline-warning rounded-pill px-4 m-2 fw-medium">Logout</button>
            </form>

            <form method="POST" action="delete_account.php">
              <button class="btn btn-outline-danger rounded-pill px-4 m-2 fw-medium">Delete Account</button>
            </form>
          </div>
        </div>

        <div id="right mx-3 max-var container">
          <div class="d-flex">
            <div class="card p-4 border-0 shadow-sm rounded-4 w-100">
              <h5 class="fw-semibold mb-1">Recent Posts</h5>
              <div id="posts" class="mt-3"></div>
            </div>
          </div>
        </div>

        </div>

      </div>

    </div>


  <div id="edit-container" class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow rounded-4">
          <div class="card-header bg-primary bg-gradient text-white rounded-top-4 border-0 py-3">
            <h5 class="mb-0 fw-semibold">Edit Profile Information</h5>
          </div>
          <div class="card-body p-4">
            <form id="edit-form" action="edit.php" method="post">

              <div class="mb-3">
                <label for="name" class="form-label fw-medium">Name</label>
                <input type="text" class="form-control rounded-3" id="uname" name="uname" value=<?php echo $_SESSION['username'] ?>>
                <div id="nameError" class="error"></div>
              </div>

              <div class="mb-4">
                <label for="about" class="form-label fw-medium">About</label>
                <textarea class="form-control rounded-3" id="about" rows="4" name="about"><?php echo ($_SESSION['about'] !== NULL) ? $_SESSION['about'] : "" ?></textarea>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-outline-danger rounded-pill px-4" onclick="toggleEdit()">Discard</button>
                <button type="submit" id="submit-btn" class="btn btn-success rounded-pill px-4 fw-medium">Save</button>
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
    profile.style.display = "flex";
    editform.style.display = "none";

    function toggleEdit() {
      profile.classList.toggle("d-none");
      editform.style.display = editform.style.display == "none" ? "block" : "none";
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

  <script>
    const posts = [{
        title: "How I Built My Portfolio",
        content: "A breakdown of tools and design choices..."
      },
      {
        title: "Top 10 VS Code Extensions",
        content: "These extensions improved my workflow..."
      },
      {
        title: "Why UI/UX Matters",
        content: "Design shapes experience."
      }
    ];

    const postsContainer = document.getElementById("posts");
    posts.forEach(post => {
      postsContainer.innerHTML += `
      <div class="bg-body-secondary border-0 rounded-3 p-3 mb-3">
        <h6 class="mb-1 fw-semibold">${post.title}</h6>
        <p class="text-muted mb-0 small">${post.content}</p>
      </div>`;
    });

    // Load from localStorage
    const data = JSON.parse(localStorage.getItem("profileData"));
    if (data) {
      document.getElementById("pf-name").textContent = data.name;
      document.getElementById("pf-bio").textContent = data.bio;
      document.getElementById("pf-about").textContent = data.about;
      if (data.image) document.getElementById("pf-img").src = data.image;
    }
  </script>
</body>

</html>