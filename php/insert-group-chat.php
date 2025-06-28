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
$msg_type = isset($_POST['msg_type']) ? $_POST['msg_type'] : 'text';
$file_path = NULL;

// Handle image upload
if($msg_type === 'image' && isset($_FILES['image'])){
    $img = $_FILES['image'];
    $img_name = time().'_'.$img['name'];
    $img_tmp = $img['tmp_name'];
    $img_folder = '../images/'.$img_name;
    if(move_uploaded_file($img_tmp, $img_folder)){
        $file_path = 'images/'.$img_name;
    }
}
// Handle file upload
elseif($msg_type === 'file' && isset($_FILES['file'])){
    $file = $_FILES['file'];
    $file_name = time().'_'.$file['name'];
    $file_tmp = $file['tmp_name'];
    $file_folder = '../images/'.$file_name;
    if(move_uploaded_file($file_tmp, $file_folder)){
        $file_path = 'images/'.$file_name;
    }
}

// Insert message into database
if($msg_type === 'text' && !empty($message)){
    $sql = "INSERT INTO group_messages (group_id, user_id, msg, msg_type) VALUES ({$group_id}, {$user_id}, '{$message}', 'text')";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "发送失败";
    }
}
elseif($msg_type === 'image' && $file_path){
    $sql = "INSERT INTO group_messages (group_id, user_id, msg, msg_type, file_path) VALUES ({$group_id}, {$user_id}, '[图片]', 'image', '{$file_path}')";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "发送失败";
    }
}
elseif($msg_type === 'file' && $file_path){
    $sql = "INSERT INTO group_messages (group_id, user_id, msg, msg_type, file_path) VALUES ({$group_id}, {$user_id}, '[文件]', 'file', '{$file_path}')";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "发送失败";
    }
}
?>