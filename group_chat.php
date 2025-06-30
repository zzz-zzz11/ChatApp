<?php
session_start();
include_once "php/config.php";
if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
}
$group_id = $_GET['group_id'];
$group = mysqli_query($conn, "SELECT * FROM `groups` WHERE group_id = {$group_id}");
if (!$group) {
    die("SQL错误: " . mysqli_error($conn));
}
$group = mysqli_fetch_assoc($group);
?>
<?php include_once "header.php"; ?>
<div class="wrapper">
    <section class="chat-area">
      <header>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="images/<?php echo htmlspecialchars($group['group_avatar'] ?? 'default_group.png'); ?>" alt="群头像" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
        <div class="details">
          <?php
            // 获取群成员数量
            $member_count = 0;
            $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM group_members WHERE group_id = {$group_id}");
            if($result && $row = mysqli_fetch_assoc($result)){
              $member_count = $row['cnt'];
            }
          ?>
          <span style="font-size:16px;color:#1f2937;font-weight:600;flex-grow:1;">
            <?php echo htmlspecialchars($group['group_name']); ?>
            <span style="font-size:13px;color:#6366f1;font-weight:400;margin-left:8px;">（<?php echo $member_count; ?>人）</span>
          </span>
        </div>
        <button id="inviteFriendBtn" class="invite-btn" style="margin-left: auto;padding:8px 20px;border:none;border-radius:20px;background:linear-gradient(135deg, #6366f1, #8b5cf6);color:#fff;cursor:pointer;transition:all 0.2s ease;"><i class="fas fa-user-plus"></i> 邀请用户</button>
        <button id="showMembersBtn" class="invite-btn" style="margin-left:10px;padding:8px 20px;border:none;border-radius:20px;background:linear-gradient(135deg,#43cea2,#185a9d);color:#fff;cursor:pointer;transition:all 0.2s ease;"><i class="fas fa-users"></i> 群成员</button>
      </header>
      <div id="inviteModal" class="modal">
        <div class="modal-header">
          <h4 style="margin:0;color:#333;font-size:18px;">邀请好友进群</h4>
        </div>
        <div id="inviteList" class="modal-body"></div>
        <div style="text-align:right;padding:10px 24px 10px 0;">
          <button id="closeInviteModal" class="logout" style="background:#6366f1;color:#fff;border-radius:8px;padding:6px 18px;">关闭</button>
        </div>
      </div>
      <div id="membersModal" class="modal" style="display:none;">
        <div class="modal-header">
          <h4 style="margin:0;color:#333;font-size:18px;">当前群成员</h4>
        </div>
        <div id="membersList" class="modal-body"></div>
        <div style="text-align:right;padding:10px 24px 10px 0;">
          <button id="closeMembersModal" class="logout" style="background:#43cea2;color:#fff;border-radius:8px;padding:6px 18px;">关闭</button>
        </div>
      </div>
            <div class="chat-box" style="background: #f8f8f8;height: 480px;overflow-y: auto;padding: 20px;border-radius: 8px;margin-bottom: 15px;">
      </div>
      <div id="preview-area" style="display:none;margin:0 0 10px 0;"></div>
      <form action="#" class="typing-area" enctype="multipart/form-data" style="display: flex;align-items: center;gap: 10px;padding: 18px 20px;background: #fff;position:relative;">
        <input type="text" class="group_id" name="group_id" value="<?php echo $_GET['group_id']; ?>" hidden>
        <div style="position:relative;display:inline-block;">
          <button type="button" id="emojiBtn" style="margin-right:5px;"><i class="fas fa-smile"></i></button>
          <div id="emojiPanel" style="display:none;position:absolute;top:48px;left:0;z-index:1001;background:#fff;border:1px solid #eee;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:10px 12px;gap:8px;flex-wrap:wrap;width:260px;">
            <img class="emoji-img" src="images/emojis/smile.png" title="微笑" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/laugh.png" title="大笑" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/cry.png" title="哭" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/angry.png" title="生气" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/thumbsup.png" title="点赞" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/heart.png" title="爱心" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/surprise.png" title="惊讶" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/wink.png" title="眨眼" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/cool.png" title="酷" style="width:32px;height:32px;cursor:pointer;margin:4px;">
            <img class="emoji-img" src="images/emojis/sad.png" title="难过" style="width:32px;height:32px;cursor:pointer;margin:4px;">
          </div>
        </div>
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
    console.log('Invite button clicked, modal:', modal);
    modal.style.display = "block";
    modal.classList.add('show');
    
    // 错误处理
    window.onerror = function(msg, url, line) {
      console.error('Error:', msg, 'at', line);
      alert('发生错误: ' + msg);
      return true;
    };
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "php/get-invite-users.php?group_id=<?php echo $group_id; ?>", true);
    xhr.onload = function() {
        if(xhr.status === 200) {
            document.getElementById("inviteList").innerHTML = xhr.responseText;
            // 用户项基础样式
const userItemStyle = 'display:flex;align-items:center;gap:16px;padding:16px;transition:all 0.2s ease;width:100%;margin-bottom:12px;';
const hoverStyle = 'transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,0.12);';

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

// 关闭模态框事件
document.getElementById('closeInviteModal').onclick = function() {
    document.getElementById('inviteModal').style.display = 'none';
    document.getElementById('inviteModal').classList.remove('show');
};

// 点击外部关闭
window.addEventListener('click', function(e) {
    const modal = document.getElementById('inviteModal');
    if(e.target === modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
    }
});

// 群成员弹窗逻辑
const showMembersBtn = document.getElementById('showMembersBtn');
const membersModal = document.getElementById('membersModal');
const closeMembersModal = document.getElementById('closeMembersModal');
showMembersBtn.onclick = function() {
  membersModal.style.display = 'block';
  membersModal.classList.add('show');
  // 获取群成员
  fetch('php/get-group-members.php?group_id=<?php echo $group_id; ?>')
    .then(res => res.text())
    .then(html => {
      document.getElementById('membersList').innerHTML = html;
    });
}
closeMembersModal.onclick = function() {
  membersModal.style.display = 'none';
  membersModal.classList.remove('show');
};
// 点击外部关闭membersModal
window.addEventListener('click', function(e) {
  if(e.target === membersModal) {
    membersModal.style.display = 'none';
    membersModal.classList.remove('show');
  }
});
</script>
<script src="javascript/group_chat.js"></script>