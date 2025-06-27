<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: users.php");
  }
?>

<?php include_once "header.php"; ?>
<body style="background-image:url('背景图.jpg');background-size:cover;background-repeat:no-repeat">
  <div class="wrapper">
    <section class="form signup">
      <header>Plant</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="name-details">
          <div class="field input">
            <label>First Name</label>
            <input type="text" name="fname" required>
          </div>
          <div class="field input">
            <label>Last Name</label>
            <input type="text" name="lname"required>
          </div>
        </div>
        <div class="field input">
          <label>Email Address</label>
          <input type="text" name="email" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password"required>
          <i class="fas fa-eye"></i>
        </div>
        <div class="field image">
          <label>设置头像</label>
          <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="确认">
        </div>
      </form>
      <div class="link">已有账户？&nbsp;&nbsp;&nbsp;<a href="login.php">登录</a></div>
    </section>
  </div>

  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/signup.js"></script>

</body>
</html>
