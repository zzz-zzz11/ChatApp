<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
  // 判断是否为管理员
  $is_admin = 0;
  $sql_admin = mysqli_query($conn, "SELECT is_admin FROM users WHERE unique_id = {$_SESSION['unique_id']}");
  if(mysqli_num_rows($sql_admin) > 0){
    $row_admin = mysqli_fetch_assoc($sql_admin);
    $is_admin = $row_admin['is_admin'];
  }
?>
<?php include_once "header.php"; ?>
<body  style="background-image:url('4.jpg');background-size:cover;background-repeat:no-repeat">
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <img src="images/<?php echo $row['img']; ?>" alt="">
          <div class="details">
            <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>
        <div class="header-btns">
        <?php if($is_admin == 1): ?>
        <a href="admin_panel.php" class="header-btn">管理成员</a>
        <?php endif; ?>
        <a href="create_group.php" class="header-btn">创建群聊</a>
        <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="header-btn">Logout</a>
        </div>
      </header>
      <div id="allUsersModal" class="modal" style="display:none;">
        <div class="modal-header">
          <h4 style="margin:0;color:#333;font-size:18px;">所有用户</h4>
        </div>
        <div id="allUsersList" class="modal-body"></div>
        <div style="text-align:right;padding:10px 24px 10px 0;">
          <button id="closeAllUsersModal" class="logout" style="background:#6366f1;color:#fff;border-radius:8px;padding:6px 18px;">关闭</button>
        </div>
      </div>
      <div class="group-chats" style="margin-bottom:20px;">
        <h3>我的群聊</h3>
        <div id="group-list" style="margin-top:10px;">
    <!-- 群聊列表由get-groups.php动态加载 -->
</div>
      </div>
      <div class="search">
        <span class="text">Select an user to start chat</span>
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
  
      </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>
  <script>
  window.onload = function() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "php/get-groups.php", true);
    xhr.onload = function() {
      if(xhr.status === 200) {
        document.getElementById("group-list").innerHTML = xhr.responseText;
      }
    }
    xhr.send();
  }
  </script>

  <script>
  document.getElementById('showAllUsersBtn') && (document.getElementById('showAllUsersBtn').onclick = function() {
    document.getElementById('allUsersModal').style.display = 'block';
    document.getElementById('allUsersModal').classList.add('show');
    fetch('php/all_users.php')
      .then(res => res.json())
      .then(data => {
        if(data.error){ document.getElementById('allUsersList').innerHTML = data.error; return; }
        let html = '';
        data.forEach(user => {
          html += `<div class='user-list-item' style='display:flex;align-items:center;gap:16px;margin-bottom:8px;'>
            <img src='images/${user.img}' alt='' style='width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;'>
            <span style='font-size:16px;font-weight:500;'>${user.fname} ${user.lname}</span>
            <span class='status-dot ${user.status==="Offline now"?"offline":""}' style='margin-left:10px;font-size:12px;'>${user.status==="Offline now"?"离线":"在线"}</span>
          </div>`;
        });
        document.getElementById('allUsersList').innerHTML = html;
      });
  });
  document.getElementById('closeAllUsersModal') && (document.getElementById('closeAllUsersModal').onclick = function() {
    document.getElementById('allUsersModal').style.display = 'none';
    document.getElementById('allUsersModal').classList.remove('show');
  });
  window.addEventListener('click', function(e) {
    const modal = document.getElementById('allUsersModal');
    if(e.target === modal) {
      modal.style.display = 'none';
      modal.classList.remove('show');
    }
  });
  </script>

</body>
</html>
