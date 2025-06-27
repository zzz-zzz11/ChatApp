<?php
session_start();
include_once "config.php";

if (!isset($_SESSION['unique_id'])) {
    echo "请先登录";
    exit();
}

$group_id = intval($_POST['group_id']);
$user_id = $_SESSION['unique_id'];
$message = mysqli_real_escape_string($conn, $_POST['message']);

if (!empty($message)) {
    $sql = "INSERT INTO group_messages (group_id, user_id, msg) VALUES ({$group_id}, {$user_id}, '{$message}')";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "发送失败";
    }
} else {
    echo "消息不能为空";
}
?>