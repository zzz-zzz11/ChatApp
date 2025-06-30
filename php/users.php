<?php
session_start();
include_once "config.php";
$output = "";
if(!isset($_SESSION['unique_id'])){
    header("location: ../login.php");
    exit;
}
$unique_id = $_SESSION['unique_id'];
$sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id != {$unique_id}");
if(mysqli_num_rows($sql) == 0){
    $output .= "No users are available to chat";
}elseif(mysqli_num_rows($sql) > 0){
    while($row = mysqli_fetch_assoc($sql)){
        $offline = ($row['status'] == 'Offline now') ? 'offline' : '';
        $output .= '<a href="chat.php?user_id='.$row['unique_id'].'">
                    <div class="content">
                    <img src="images/'.$row['img'].'" alt="">
                    <div class="details">
                        <span>'.$row['fname'].' '.$row['lname'].'</span>
                        <p>'.$row['status'].'</p>
                    </div>
                    </div>
                    <div class="status-dot '.$offline.'"><i class="fas fa-circle"></i></div>
                </a>';
    }
}
echo $output;
?> 