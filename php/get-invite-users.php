<?php
session_start();
include_once "config.php";

if (!isset($_SESSION['unique_id'])) {
    echo "请先登录";
    exit();
}

$group_id = intval($_GET['group_id']);
$outgoing_id = $_SESSION['unique_id'];

// 获取所有未在该群的用户
$sql = "SELECT * FROM users WHERE unique_id NOT IN (
            SELECT user_id FROM group_members WHERE group_id = {$group_id}
        ) AND unique_id != {$outgoing_id}";
$query = mysqli_query($conn, $sql);

$output = "";
if(mysqli_num_rows($query) > 0){
    while($row = mysqli_fetch_assoc($query)){
        $offline = ($row['status'] == "Offline now") ? "offline" : "";
        $output .= '<div class="content content-card">
            <div style="display:flex;align-items:center;">
                <img src="images/'. $row['img'] .'" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">
                <div class="details">
                    <span>'. $row['fname']. " " . $row['lname'] .'</span>
                    
                </div>
            </div>
            <button class="invite-btn" data-userid="'.$row['unique_id'].'" style="padding:8px 20px;border:none;background:#000000;color:#ffffff;border-radius:20px;cursor:pointer;box-shadow:0 2px 6px rgba(0,198,255,0.3);">邀请</button>
        </div>';
    }
} else {
    $output = "暂无可邀请的好友";
}
echo $output;
?>