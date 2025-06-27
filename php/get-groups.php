<?php
session_start();
include_once "config.php";

if (!isset($_SESSION['unique_id'])) {
    echo "请先登录";
    exit();
}

$user_id = $_SESSION['unique_id'];
$output = "";

$sql = "SELECT 
    groups.group_id,
    groups.group_name,
    groups.group_avatar,
    (SELECT msg FROM group_messages 
     WHERE group_id = groups.group_id 
     ORDER BY created_at DESC 
     LIMIT 1) AS last_msg_content,
    MAX(group_messages.created_at) AS last_msg_time
FROM groups
INNER JOIN group_members ON groups.group_id = group_members.group_id
LEFT JOIN group_messages ON groups.group_id = group_messages.group_id
WHERE group_members.user_id = {$user_id}
GROUP BY groups.group_id";
$query = mysqli_query($conn, $sql);

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $output .= '<a href="group_chat.php?group_id=' . $row["group_id"] . '" class="content" style="display:flex;align-items:center;margin-bottom:8px;padding:8px 0;border-bottom:1px solid #eee;">
    <img src="images/' . htmlspecialchars($row["group_avatar"]) . '" alt="群头像" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">
    <div class="details" style="display:flex;flex-direction: column; width: 100%; position: relative;">
        <span style="font-weight: 500;">' . htmlspecialchars($row["group_name"]) . '</span>
        <div class="message-container" style="margin-top: 4px;">
            <p class="last-msg" style="color: #666;font-size: 0.9em;">' . ($row['last_msg_content'] ? htmlspecialchars(substr($row["last_msg_content"], 0, 20)).(strlen($row['last_msg_content']) > 20 ? '...' : '') : '暂无消息') . '</p>
        </div>
        <div class="time-container" style="position: absolute; right: 0; top: 0;">
            <small style="font-size: 0.8em; color: #999;">' . ($row["last_msg_time"] ? date('H:i', strtotime($row["last_msg_time"])) : '') . '</small>
        </div>
</div>
</a>';
    }
} else {
    $output .= "你还没有加入任何群聊";
}
echo $output;
?>