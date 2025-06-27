
<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: homepage.php");
  }
?>

<?php include_once "header.php";?>
<body style="background-image:url('背景图.jpg');background-size:cover;background-repeat:no-repeat">
  <div class="wrapper">
    <section class="form login" >
      <header>iPlant</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input" style="color:yellowgreen">
          <label>Email</label>
          <input type="text" name="email"required>
        </div>
        <div class="field input" style="color:yellowgreen">
          <label>Password</label>
          <input type="password" name="password" required>
          <i class="fas fa-eye"></i>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="登录">
        </div>
      </form>
      <div class="link">没有登陆账号？<a href="index.php">注册</a></div>
      <div><a href="admin_login.php">管理员登录</a></div>
    </section>
  </div>

  
  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/login.js"></script>

</body>
</html>
