<?php
  $hostname = "localhost";
  $username = "root";
  $password = "123456";
  $dbname = "ihello";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
?>
