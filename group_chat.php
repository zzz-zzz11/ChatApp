<?php
session_start();
include_once "php/config.php";
if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
}
$group_id = $_GET['group_id'];
$group = mysqli_query($conn, "SELECT * FROM groups WHERE group_id = {$group_id}");
$group = mysqli_fetch_assoc($group);
?>
<?php include_once "header.php"; ?>
<div class="wrapper">
    <section class="chat-area">
      <header>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="images/<?php echo htmlspecialchars($group['group_avatar'] ?? 'default_group.png'); ?>" alt="群头像" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
        <div class="details">
          <span><?php echo htmlspecialchars($group['group_name']); ?></span>
        </div>
        <button id="inviteFriendBtn" class="invite-btn" style="margin-left: auto;"><i class="fas fa-user-plus"></i></button>
      </header>
        <div id="inviteModal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; border:1px solid #ccc; padding:20px; z-index:1000;">
          <h4>邀请好友进群</h4>
          <div id="inviteList"></div>
          <button onclick="document.getElementById('inviteModal').style.display='none'">关闭</button>
        </div>
            <div class="chat-box" style="background: #f8f8f8;height: 480px;overflow-y: auto;padding: 20px;border-radius: 8px;margin-bottom: 15px;">
      </div>
      <div id="preview-area" style="display:none;margin:0 0 10px 0;"></div>
      <form action="#" class="typing-area" enctype="multipart/form-data" style="display: flex;align-items: center;gap: 10px;padding: 18px 20px;background: #fff;">
        <input type="text" class="group_id" name="group_id" value="<?php echo $_GET['group_id']; ?>" hidden>
        <button type="button" id="imgBtn" style="margin-right:5px;"><i class="fas fa-image"></i></button>
        <input type="file" id="imgInput" name="image" accept="image/*" style="display:none;">
        <button type="button" id="fileBtn" style="margin-right:5px;"><i class="fas fa-paperclip"></i></button>
        <input type="file" id="fileInput" name="file" style="display:none;">
        <input type="text" name="message" class="input-field" placeholder="输入消息..." autocomplete="off">
        <button type="submit"><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>
<script>
document.getElementById("inviteFriendBtn").onclick = function() {
    let modal = document.getElementById("inviteModal");
    modal.style.display = "block";
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "php/get-invite-users.php?group_id=<?php echo $group_id; ?>", true);
    xhr.onload = function() {
        if(xhr.status === 200) {
            document.getElementById("inviteList").innerHTML = xhr.responseText;
            // 绑定邀请按钮事件
            document.querySelectorAll('.invite-btn').forEach(function(btn){
                btn.onclick = function() {
                    let userId = this.getAttribute('data-userid');
                    let xhr2 = new XMLHttpRequest();
                    xhr2.open("POST", "php/invite-to-group.php", true);
                    xhr2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr2.onload = function() {
                        if(xhr2.status === 200) {
                            alert(xhr2.responseText);
                            if(xhr2.responseText.trim() === 'success') {
                                btn.parentElement.remove(); // 移除已邀请
                            }
                        }
                    }
                    xhr2.send("group_id=<?php echo $group_id; ?>&user_id=" + userId);
                }
            });
        }
    }
    xhr.send();
}
</script>
<script src="javascript/group_chat.js"></script>