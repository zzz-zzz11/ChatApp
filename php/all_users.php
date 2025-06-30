<?php
// 获取所有用户信息，返回json，供管理员后台和其它功能调用
session_start();
include_once "config.php";
header('Access-Control-Allow-Origin: *');

// 管理员操作接口
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $admin_id = $_SESSION['unique_id'] ?? 0;
    $admin_check = mysqli_query($conn, "SELECT is_admin FROM users WHERE unique_id = {$admin_id}");
    $admin_row = mysqli_fetch_assoc($admin_check);
    if (!$admin_row || $admin_row['is_admin'] != 1) {
        echo json_encode(['error' => '无权限']);
        exit;
    }
    $user_id = intval($_POST['user_id']);
    if ($_POST['action'] === 'ban') {
        $sql = "UPDATE users SET is_banned=1 WHERE unique_id={$user_id}";
        mysqli_query($conn, $sql);
        echo json_encode(['success' => true, 'msg' => '已禁用']);
        exit;
    } elseif ($_POST['action'] === 'unban') {
        $sql = "UPDATE users SET is_banned=0 WHERE unique_id={$user_id}";
        mysqli_query($conn, $sql);
        echo json_encode(['success' => true, 'msg' => '已允许登录']);
        exit;
    } elseif ($_POST['action'] === 'kick') {
        $sql = "UPDATE users SET status='Offline now' WHERE unique_id={$user_id}";
        mysqli_query($conn, $sql);
        echo json_encode(['success' => true, 'msg' => '已踢出']);
        exit;
    }
}

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['error' => '未登录']);
    exit;
}
$admin_id = $_SESSION['unique_id'];
$admin_check = mysqli_query($conn, "SELECT is_admin FROM users WHERE unique_id = {$admin_id}");
$admin_row = mysqli_fetch_assoc($admin_check);
if (!$admin_row || $admin_row['is_admin'] != 1) {
    echo json_encode(['error' => '无权限']);
    exit;
}
$sql = "SELECT * FROM users ORDER BY user_id DESC";
$query = mysqli_query($conn, $sql);
$output = "";
if(mysqli_num_rows($query) == 0){
    echo "No users are available to chat";
    exit;
}elseif(mysqli_num_rows($query) > 0){
    $users = array();
    while($row = mysqli_fetch_assoc($query)){
        $users[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($users);
    exit;
}
echo $output;
?>