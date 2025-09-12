<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host="localhost";
$username="root";
$password="";
$db_name="quickauth";

mysqli_report(MYSQLI_REPORT_STRICT);

try{
$conn = mysqli_connect($host,$username,$password,$db_name);
unset($_SESSION['dberror']);
}
catch(mysqli_sql_exception $e){
    //die("Error". mysqli_connect_error());
    $_SESSION['dberror']= true;
    header("location:error.php",true,302);
    exit();
}
?>