<?php
session_start();
include_once "config.php";
if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['error' => '未登录']);
    exit;
}
$uid = $_SESSION['unique_id'];
$sql = mysqli_query($conn, "SELECT status FROM users WHERE unique_id = {$uid}");
if($row = mysqli_fetch_assoc($sql)){
    echo json_encode(['status' => $row['status']]);
} else {
    echo json_encode(['error' => '用户不存在']);
} 