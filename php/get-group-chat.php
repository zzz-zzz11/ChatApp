<?php
session_start();
include_once "config.php";

if (!isset($_SESSION['unique_id'])) {
    echo "请先登录";
    exit();
}

$group_id = intval($_POST['group_id']);
$output = "";

$sql = "SELECT group_messages.*, users.fname, users.lname, users.img 
        FROM group_messages 
        LEFT JOIN users ON group_messages.user_id = users.unique_id
        WHERE group_id = {$group_id} ORDER BY msg_id";
$query = mysqli_query($conn, $sql);

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $name = $row['fname'] . " " . $row['lname'];
        $img = $row['img'];
        $msg = $row['msg'];
        $is_me = ($row['user_id'] == $_SESSION['unique_id']);
        if ($is_me) {
            $output .= '<div class="chat outgoing"><div class="details">';
            if($row['msg_type'] === 'image' && $row['file_path']){
                // 判断是否为表情包图片
                if(strpos($row['file_path'], 'images/emojis/') === 0){
                    $output .= '<img class="emoji-img" src="'.$row['file_path'].'" alt="emoji">';
                }else{
                    $output .= '<img src="'.$row['file_path'].'" style="max-width:180px;max-height:180px;border-radius:8px;display:block;margin-bottom:4px;">';
                }
            }elseif($row['msg_type'] === 'file' && $row['file_path']){
                // 获取文件名和大小
                $file_name = basename($row['file_path']);
                $file_size = file_exists('../'.$row['file_path']) ? filesize('../'.$row['file_path']) : 0;
                $file_size_str = $file_size >= 1048576 ? round($file_size/1048576,2).' MB' : ($file_size >= 1024 ? round($file_size/1024,1).' KB' : $file_size.' B');
                $output .= '<div class="wx-file-card"><div class="wx-file-icon"><i class="fas fa-file-alt"></i></div><div class="wx-file-info"><div class="wx-file-name">'.htmlspecialchars($file_name).'</div><div class="wx-file-size">'.$file_size_str.'</div></div><a class="wx-file-download" href="'.$row['file_path'].'" download><i class="fas fa-download"></i> 下载</a></div>';
            }else{
                $output .= '<p>' . $msg . '</p>';
            }
            $output .= '</div></div>';
        } else {
            $output .= '<div class="chat incoming"><img src="images/' . $img . '" alt=""><div class="details">';
            if($row['msg_type'] === 'image' && $row['file_path']){
                // 判断是否为表情包图片
                if(strpos($row['file_path'], 'images/emojis/') === 0){
                    $output .= '<img class="emoji-img" src="'.$row['file_path'].'" alt="emoji">';
                }else{
                    $output .= '<img src="'.$row['file_path'].'" style="max-width:180px;max-height:180px;border-radius:8px;display:block;margin-bottom:4px;">';
                }
            }elseif($row['msg_type'] === 'file' && $row['file_path']){
                // 获取文件名和大小
                $file_name = basename($row['file_path']);
                $file_size = file_exists('../'.$row['file_path']) ? filesize('../'.$row['file_path']) : 0;
                $file_size_str = $file_size >= 1048576 ? round($file_size/1048576,2).' MB' : ($file_size >= 1024 ? round($file_size/1024,1).' KB' : $file_size.' B');
                $output .= '<div class="wx-file-card"><div class="wx-file-icon"><i class="fas fa-file-alt"></i></div><div class="wx-file-info"><div class="wx-file-name">'.htmlspecialchars($file_name).'</div><div class="wx-file-size">'.$file_size_str.'</div></div><a class="wx-file-download" href="'.$row['file_path'].'" download><i class="fas fa-download"></i> 下载</a></div>';
            }else{
                $output .= '<p><b>' . $name . ':</b> ' . $msg . '</p>';
            }
            $output .= '</div></div>';
        }
    }
} else {
    $output .= '<div class="text">暂无消息</div>';
}
echo $output;
?>