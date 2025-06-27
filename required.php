<?php 
        session_start();
        include_once "php/config.php";
        if(!isset($_SESSION['unique_id'])){
        header("location: login.php");
        }
        ?>
        <?php 
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
        if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
        }
        ?>
        <?php include_once "header.php"; ?>