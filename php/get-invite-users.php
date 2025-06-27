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
        $output .= '<div class="content" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;padding:8px 0;border-bottom:1px solid #eee;">
            <div style="display:flex;align-items:center;">
                <img src="images/'. $row['img'] .'" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">
                <div class="details">
                    <span>'. $row['fname']. " " . $row['lname'] .'</span>
                    <div class="status-dot '. $offline .'" style="display:inline-block;margin-left:8px;"><i class="fas fa-circle"></i></div>
                </div>
            </div>
            <button class="invite-btn" data-userid="'.$row['unique_id'].'" style="padding:6px 16px;border:none;background:#4e73df;color:#fff;border-radius:4px;cursor:pointer;">邀请</button>
        </div>';
    }
} else {
    $output = "暂无可邀请的好友";
}
echo $output;
?> 