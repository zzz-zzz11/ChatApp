<?php
session_start();
include_once "config.php";
if (!isset($_SESSION['unique_id'])) {
    echo "请先登录";
    exit();
}
$group_id = intval($_GET['group_id']);
$sql = "SELECT users.img, users.fname, users.lname, users.status FROM group_members LEFT JOIN users ON group_members.user_id = users.unique_id WHERE group_members.group_id = {$group_id}";
$query = mysqli_query($conn, $sql);
if (!$query) {
    echo "获取成员失败";
    exit();
}
if (mysqli_num_rows($query) == 0) {
    echo "暂无成员";
    exit();
}
while($row = mysqli_fetch_assoc($query)) {
    $offline = ($row['status'] == 'Offline now') ? 'offline' : '';
    echo '<div class="content-card user-list-item" style="display:flex;align-items:center;gap:16px;margin-bottom:8px;">';
    echo '<img src="images/'.htmlspecialchars($row['img']).'" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">';
    echo '<span style="font-size:16px;font-weight:500;">'.htmlspecialchars($row['fname'].' '.$row['lname']).'</span>';
    echo '<span class="status-dot '.$offline.'" style="margin-left:10px;font-size:12px;">'.($offline ? '离线' : '在线').'</span>';
    echo '</div>';
} 