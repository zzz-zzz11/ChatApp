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
            $output .= '<div class="chat outgoing"><div class="details"><p>' . $msg . '</p></div></div>';
        } else {
            $output .= '<div class="chat incoming"><img src="images/' . $img . '" alt=""><div class="details"><p><b>' . $name . ':</b> ' . $msg . '</p></div></div>';
        }
    }
} else {
    $output .= '<div class="text">暂无消息</div>';
}
echo $output;
?>