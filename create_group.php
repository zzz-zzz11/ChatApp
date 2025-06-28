<?php
session_start();
if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
    exit();
}
include_once "php/config.php";
$self_id = $_SESSION['unique_id'];
// 获取所有用户，排除自己
$users = mysqli_query($conn, "SELECT unique_id, fname, lname, img, status FROM users WHERE unique_id != $self_id");
?>
<?php include_once "header.php"; ?>
<body>
<div class="wrapper">
    <section class="form">
        <header>创建新群聊</header>
        <form id="createGroupForm" action="php/create_group.php" method="POST" enctype="multipart/form-data">
            <div class="field input">
                <label>群聊名称</label>
                <input type="text" name="group_name" placeholder="输入群名称" required>
        <input type="file" name="group_avatar" accept="image/png, image/jpeg" required style="margin: 10px 0;">
            </div>
            <div class="field input">
                <label>邀请好友进群（可多选）</label>
                <div style="max-height:120px;overflow-y:auto;border:1px solid #ccc;padding:5px;">
                <?php while($row = mysqli_fetch_assoc($users)): ?>
                    <?php $offline = ($row['status'] == 'Offline now') ? 'offline' : ''; ?>
                    <label style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eee;">
                        <div style="display:flex;align-items:center;">
                            <img src="images/<?php echo htmlspecialchars($row['img']); ?>" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">
                            <span><?php echo htmlspecialchars($row['fname'].' '.$row['lname']); ?></span>
                            <div class="status-dot <?php echo $offline; ?>" style="display:inline-block;margin-left:0px;"><i class="fas fa-circle"></i></div>
                        </div>
                        <input type="checkbox" name="invite_users[]" value="<?php echo $row['unique_id']; ?>" style="margin-left:160px;width:18px;height:18px;">
                    </label>
                <?php endwhile; ?>
                </div>
            </div>
            <div class="field button">
                <input type="submit" value="创建">
            </div>
            <div class="error-text" style="color:red;"></div>
        </form>
    </section>
</div>
<script>
document.getElementById("createGroupForm").onsubmit = function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/create_group.php", true);
    xhr.onload = function() {
        if(xhr.status === 200) {
            console.log('服务器响应:', xhr.responseText);
            if(xhr.responseText.trim() === "success") {
                window.alert('创建成功');
                window.location.href = "users.php";
            } else {
                document.querySelector(".error-text").textContent = xhr.responseText;
            }
        }
    }
    xhr.send(formData);
}
</script>
</body>
</html>