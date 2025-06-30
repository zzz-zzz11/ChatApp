const form = document.querySelector(".typing-area"),
    inputField = form.querySelector(".input-field"),
    sendBtn = form.querySelector("button"),
    chatBox = document.querySelector(".chat-box"),
    groupIdField = form.querySelector(".group_id"),
    imgBtn = document.getElementById("imgBtn"),
    imgInput = document.getElementById("imgInput"),
    fileBtn = document.getElementById("fileBtn"),
    fileInput = document.getElementById("fileInput"),
    previewArea = document.getElementById("preview-area");

let pendingFile = null;
let pendingType = null;

// ====== 表情包功能 ======
const emojiBtn = document.getElementById('emojiBtn');
const emojiPanel = document.getElementById('emojiPanel');

// 初始化按钮状态
const initButtons = () => {
    // 图片和文件按钮始终可点击
    imgBtn.disabled = false;
    imgBtn.style.pointerEvents = 'auto';
    imgBtn.style.opacity = '1';
    fileBtn.disabled = false;
    fileBtn.style.pointerEvents = 'auto';
    fileBtn.style.opacity = '1';

    // 初始发送按钮状态
    updateSendBtnState();
};

// 更新发送按钮状态
const updateSendBtnState = () => {
    const hasContent = inputField.value.trim() !== '' || pendingFile;
    sendBtn.classList.toggle('active', hasContent);
    sendBtn.disabled = !hasContent;
    // 保证图片和文件按钮始终可用
    imgBtn.disabled = false;
    imgBtn.style.pointerEvents = 'auto';
    imgBtn.style.opacity = '1';
    fileBtn.disabled = false;
    fileBtn.style.pointerEvents = 'auto';
    fileBtn.style.opacity = '1';
};

// 处理文件预览
const handleFilePreview = (file, type) => {
    const reader = new FileReader();
    const removeHandler = () => {
        previewArea.style.display = 'none';
        previewArea.innerHTML = '';
        pendingFile = null;
        pendingType = null;
        if (type === 'image') imgInput.value = '';
        if (type === 'file') fileInput.value = '';
        updateSendBtnState();
    };

    if (type === 'image') {
        reader.onload = (e) => {
            previewArea.innerHTML = `
                <img src="${e.target.result}" style="max-width:120px;max-height:120px;border-radius:8px;">
                <span style="cursor:pointer;color:red;font-size:18px;margin-left:8px;" id="removePreview">×</span>
            `;
            previewArea.style.display = 'block';
            document.getElementById('removePreview').onclick = removeHandler;
        };
        reader.readAsDataURL(file);
    } else {
        previewArea.innerHTML = `
            <span style="font-size:16px;">${file.name}</span>
            <span style="cursor:pointer;color:red;font-size:18px;margin-left:8px;" id="removePreview">×</span>
        `;
        previewArea.style.display = 'block';
        document.getElementById('removePreview').onclick = removeHandler;
    }
};

// 发送消息到服务器
const sendToServer = (data, clearAfterSend = true) => {
    fetch("php/insert-group-chat.php", {
        method: "POST",
        body: data
    }).then(() => {
        if (clearAfterSend) {
            previewArea.style.display = 'none';
            previewArea.innerHTML = '';
            pendingFile = null;
            pendingType = null;
            imgInput.value = '';
            fileInput.value = '';
            inputField.value = '';
        }
        updateSendBtnState();
        scrollToBottom();
    });
};

// 表单提交处理
form.onsubmit = (e) => {
    e.preventDefault();
    let hasContent = false;

    // 发送图片
    if (pendingFile && pendingType === 'image') {
        hasContent = true;
        const formData = new FormData();
        formData.append("group_id", groupIdField.value);
        formData.append("image", pendingFile);
        formData.append("msg_type", "image");
        sendToServer(formData);
    }

    // 发送文件
    else if (pendingFile && pendingType === 'file') {
        hasContent = true;
        const formData = new FormData();
        formData.append("group_id", groupIdField.value);
        formData.append("file", pendingFile);
        formData.append("msg_type", "file");
        sendToServer(formData);
    }

    // 发送文字
    else if (inputField.value.trim() !== "") {
        hasContent = true;
        const formData = new FormData(form);
        sendToServer(formData, false); // 不清空输入框（因为可能还有文件待发送）
        inputField.value = '';
    }
};

// 实时获取群聊消息
const fetchGroupMessages = () => {
    const formData = new FormData(form);
    formData.append('group_id', groupIdField.value);

    fetch("php/get-group-chat.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        chatBox.innerHTML = data;
        if (!chatBox.classList.contains("active")) {
            scrollToBottom();
        }
    });
};

// 滚动到底部
const scrollToBottom = () => {
    chatBox.scrollTop = chatBox.scrollHeight;
};

// 事件监听器
imgBtn.onclick = (e) => {
    e.stopPropagation();
    e.preventDefault();
    imgInput.click();
    updateSendBtnState();
};

fileBtn.onclick = () => {
    fileInput.click();
    updateSendBtnState();
};

imgInput.onchange = () => {
    if (imgInput.files.length > 0) {
        pendingFile = imgInput.files[0];
        pendingType = 'image';
        handleFilePreview(pendingFile, 'image');
        updateSendBtnState();
    }
};

fileInput.onchange = () => {
    if (fileInput.files.length > 0) {
        pendingFile = fileInput.files[0];
        pendingType = 'file';
        handleFilePreview(pendingFile, 'file');
        updateSendBtnState();
    }
};

inputField.onkeyup = updateSendBtnState;
chatBox.onmouseenter = () => chatBox.classList.add("active");
chatBox.onmouseleave = () => chatBox.classList.remove("active");

// 点击表情按钮，切换面板显示
emojiBtn.onclick = (e) => {
    e.stopPropagation();
    emojiPanel.style.display = emojiPanel.style.display === 'none' ? 'flex' : 'none';
};
// 点击页面其他地方关闭表情面板
window.addEventListener('click', function(e) {
    if (emojiPanel.style.display !== 'none' && !emojiPanel.contains(e.target) && e.target !== emojiBtn) {
        emojiPanel.style.display = 'none';
    }
});
// 阻止点击面板冒泡
emojiPanel.onclick = (e) => { e.stopPropagation(); };
// 选择表情，直接以图片形式发送
const emojiImgs = emojiPanel.querySelectorAll('.emoji-img');
emojiImgs.forEach(img => {
    img.onclick = function() {
        // 以图片消息方式发送表情
        const formData = new FormData();
        formData.append("group_id", groupIdField.value);
        formData.append("msg_type", "image");
        // 直接发送图片URL（后端需支持本地图片路径）
        formData.append("emoji_path", this.getAttribute('src'));
        fetch("php/insert-group-chat.php", {
            method: "POST",
            body: formData
        }).then(() => {
            emojiPanel.style.display = 'none';
            updateSendBtnState();
            scrollToBottom();
        });
    };
});

// 初始化
initButtons();
setInterval(fetchGroupMessages, 500);

setInterval(function() {
    fetch('php/get-user-status.php')
        .then(res => res.json())
        .then(data => {
            if(data.status && data.status !== 'Active now') {
                alert('您已被管理员踢出或下线！');
                window.location.href = 'login.php';
            }
        });
}, 5000);