<?php
session_start();
include_once "config.php";

if (!isset($_SESSION['unique_id'])) {
    echo "请先登录";
    exit();
}

$group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
$owner_id = $_SESSION['unique_id'];
$avatar_name = '';
if(isset($_FILES['group_avatar']) && !empty($group_name)){
        $avatar_name = $_FILES['group_avatar']['name'];
        $group_avatar = mysqli_real_escape_string($conn, $avatar_name); // 修正变量顺序

        $avatar_tmp_name = $_FILES['group_avatar']['tmp_name'];
        $avatar_size = $_FILES['group_avatar']['size'];
        $avatar_folder = '../images/' . $avatar_name;
        
        if($avatar_size > 2000000){
            echo "文件太大，请上传小于2MB的图片";
            exit();
        }
        
        $allowed_ext = array('png','jpg','jpeg');
        $file_ext = pathinfo($avatar_name, PATHINFO_EXTENSION);
        if(!in_array($file_ext, $allowed_ext)){
            echo "仅支持png/jpg/jpeg格式";
            exit();
        }
        
        if(!move_uploaded_file($avatar_tmp_name, $avatar_folder)){
            echo "头像上传失败";
            exit();
        }
    // 创建群
    $sql = "INSERT INTO groups (group_name, group_avatar, owner_id) VALUES ('{$group_name}', '{$avatar_name}', {$owner_id})"; // 使用实际上传的文件名
    if (mysqli_query($conn, $sql)) {
        $group_id = mysqli_insert_id($conn);
        // 群主自动加入群
        $sql2 = "INSERT INTO group_members (group_id, user_id) VALUES ({$group_id}, {$owner_id})";
        if(!mysqli_query($conn, $sql2)) {
            echo "添加群主失败: " . mysqli_error($conn);
            exit();
        }
        echo "success";
        exit();
    } else {
        // 删除已上传的头像文件
        @unlink('../images/' . $avatar_name);
        echo "群创建失败: " . mysqli_error($conn);
        exit();
    }
} else {
    echo "群名不能为空";
}
?>