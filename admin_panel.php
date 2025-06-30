<?php
session_start();
include_once "php/config.php";
include_once "header.php";

// 检查是否为管理员
if (!isset($_SESSION['unique_id'])) {
    header("Location: login.php");
    exit();
}
$admin_id = $_SESSION['unique_id'];
$admin_check = mysqli_query($conn, "SELECT is_admin FROM users WHERE unique_id = {$admin_id}");
$admin_row = mysqli_fetch_assoc($admin_check);
if (!$admin_row || $admin_row['is_admin'] != 1) {
    echo "无权限访问";
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理员后台</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .user-list { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #ccc; padding: 24px; }
        .user-row { display: flex; align-items: center; border-bottom: 1px solid #eee; padding: 12px 0; }
        .user-row:last-child { border-bottom: none; }
        .user-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 16px; }
        .user-info { flex: 1; }
        .user-actions button { margin-left: 8px; padding: 6px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .ban { background: #e74c3c; color: #fff; }
        .unban { background: #27ae60; color: #fff; }
        .kick { background: #f39c12; color: #fff; }
        .admin-badge { color: #fff; background: #2980b9; border-radius: 4px; padding: 2px 8px; font-size: 12px; margin-left: 8px; }
        .banned { color: #e74c3c; font-weight: bold; margin-left: 8px; }
    </style>
</head>
<body>
    <div class="user-list">
        <div style="text-align:right;margin-bottom:10px;">
            <button onclick="window.history.back();" style="padding:6px 16px;background:#2980b9;color:#fff;border-radius:4px;border:none;cursor:pointer;">返回上一页</button>
        </div>
        <h2>所有用户</h2>
        <div id="users"></div>
    </div>
    <script>
    function fetchUsers() {
        fetch('php/all_users.php')
            .then(res => res.json())
            .then(data => {
                if(data.error){ document.getElementById('users').innerHTML = data.error; return; }
                let html = '';
                data.forEach(user => {
                    html += `<div class='user-row'>
                        <img class='user-img' src='images/${user.img}' alt=''>
                        <div class='user-info'>
                            <span>${user.fname} ${user.lname}</span>
                            ${user.is_admin == 1 ? '<span class="admin-badge">管理员</span>' : ''}
                            ${user.is_banned == 1 ? '<span class="banned">已禁用</span>' : ''}
                            <br><small>状态: ${user.status}</small>
                        </div>
                        <div class='user-actions'>
                            ${user.is_admin == 1 ? '' : `
                                <button class='kick' onclick='adminAction(${user.unique_id}, "kick")'>踢出</button>
                                ${user.is_banned == 1 ? `<button class='unban' onclick='adminAction(${user.unique_id}, "unban")'>允许登录</button>` : `<button class='ban' onclick='adminAction(${user.unique_id}, "ban")'>禁用</button>`}
                            `}
                        </div>
                    </div>`;
                });
                document.getElementById('users').innerHTML = html;
            });
    }
    function adminAction(user_id, action) {
        if(!confirm('确定要执行此操作吗？')) return;
        fetch('php/all_users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=${action}&user_id=${user_id}`
        })
        .then(res => res.json())
        .then(data => {
            alert(data.msg || data.error);
            fetchUsers();
        });
    }
    fetchUsers();
    setInterval(function() {
        fetch('php/get-user-status.php')
            .then(res => res.json())
            .then(data => {
                if(data.kicked && data.kicked == 1) {
                    alert('您已被管理员强制下线！');
                    // 调用接口重置 kicked 字段
                    fetch('php/reset-kicked.php', {method: 'POST'});
                    window.location.href = 'login.php';
                }
            });
    }, 5000);
    </script>
</body>
</html> 