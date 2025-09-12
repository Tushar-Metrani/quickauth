<?php
error_reporting(0);
//refreshing session variables
function refresh_variables()
{
  global $conn;
  $email = $_SESSION['email'];
  $sql = "SELECT `name`, `email`, `about` FROM `users` WHERE `email` = ? ;";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
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
      $_SESSION['loggedin'] = true;
      $_SESSION['about'] = $about;
    } else {
      $_SESSION = [];
      session_unset();
      session_destroy();
      header("location:login.php",true);
      exit();
    }
  }
}

?>
