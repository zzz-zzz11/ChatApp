<?php
session_start();
include_once "config.php";

if (!isset($_SESSION['unique_id'])) {
    echo "请先登录";
    exit();
}

$group_id = intval($_POST['group_id']);
$user_id = $_SESSION['unique_id'];

// 检查是否已加入
$sql = "SELECT * FROM group_members WHERE group_id = {$group_id} AND user_id = {$user_id}";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    $sql2 = "INSERT INTO group_members (group_id, user_id) VALUES ({$group_id}, {$user_id})";
    if (mysqli_query($conn, $sql2)) {
        echo "success";
    } else {
        echo "加入群聊失败";
    }
} else {
    echo "已在群聊中";
}
?>