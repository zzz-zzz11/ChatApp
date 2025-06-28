<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
          $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
          if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
          }else{
            header("location: users.php");
          }
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="images/<?php echo $row['img']; ?>" alt="">
        <div class="details">
          <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
          <p><?php echo $row['status']; ?></p>
        </div>
      </header>
      <div class="chat-box">

      </div>
      <div id="preview-area" style="display:none;margin:0 0 10px 0;"></div>
      <form action="#" class="typing-area" enctype="multipart/form-data" style="position:relative;display: flex;align-items: center;gap: 10px;padding: 18px 20px;background: #fff;">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
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
        <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button type="submit"><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <script src="javascript/chat.js"></script>
</body>
</html>