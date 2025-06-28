const form = document.querySelector(".typing-area");
const incoming_id = form.querySelector(".incoming_id").value;
const inputField = form.querySelector(".input-field");
const sendBtn = form.querySelector("button");
const chatBox = document.querySelector(".chat-box");
const imgBtn = document.getElementById("imgBtn");
const imgInput = document.getElementById("imgInput");
const fileBtn = document.getElementById("fileBtn");
const fileInput = document.getElementById("fileInput");
const previewArea = document.getElementById("preview-area");

let pendingFile = null;
let pendingType = null;

// 初始化按钮状态 - 确保图片和文件按钮始终可点击
imgBtn.disabled = false;
imgBtn.style.pointerEvents = 'auto';
imgBtn.style.opacity = '1';
fileBtn.disabled = false;
fileBtn.style.pointerEvents = 'auto';
fileBtn.style.opacity = '1';

// 更新发送按钮状态
function updateSendBtnState() {
    const hasContent = inputField.value.trim() !== '' || pendingFile;
    sendBtn.classList.toggle('active', hasContent);
    sendBtn.disabled = !hasContent;
}

// 图片按钮点击处理
imgBtn.onclick = (e) => {
    e.stopPropagation();
    e.preventDefault();
    imgInput.click();
    updateSendBtnState();
};

// 文件按钮点击处理
fileBtn.onclick = () => {
    fileInput.click();
    updateSendBtnState();
};

// 图片选择处理
imgInput.onchange = function() {
    if (this.files.length > 0) {
        pendingFile = this.files[0];
        pendingType = 'image';
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewArea.innerHTML = `
                <img src='${e.target.result}' style='max-width:120px;max-height:120px;border-radius:8px;'>
                <span style='cursor:pointer;color:red;font-size:18px;margin-left:8px;' id='removePreview'>&times;</span>
            `;
            previewArea.style.display = 'block';
            
            document.getElementById('removePreview').onclick = () => {
                previewArea.style.display = 'none';
                previewArea.innerHTML = '';
                pendingFile = null;
                pendingType = null;
                imgInput.value = '';
                updateSendBtnState();
            };
        };
        reader.readAsDataURL(pendingFile);
        updateSendBtnState();
    }
};

// 文件选择处理
fileInput.onchange = function() {
    if (this.files.length > 0) {
        pendingFile = this.files[0];
        pendingType = 'file';
        
        previewArea.innerHTML = `
            <span style='font-size:16px;'>${pendingFile.name}</span>
            <span style='cursor:pointer;color:red;font-size:18px;margin-left:8px;' id='removePreview'>&times;</span>
        `;
        previewArea.style.display = 'block';
        
        document.getElementById('removePreview').onclick = () => {
            previewArea.style.display = 'none';
            previewArea.innerHTML = '';
            pendingFile = null;
            pendingType = null;
            fileInput.value = '';
            updateSendBtnState();
        };
        updateSendBtnState();
    }
};

// 表单提交处理
form.onsubmit = (e) => {
    e.preventDefault();
    let hasContent = false;

    // 发送图片
    if (pendingFile && pendingType === 'image') {
        hasContent = true;
        const formData = new FormData();
        formData.append("incoming_id", incoming_id);
        formData.append("image", pendingFile);
        formData.append("msg_type", "image");
        
        fetch("php/insert-chat.php", {
            method: "POST",
            body: formData
        }).then(() => {
            pendingFile = null;
            pendingType = null;
            imgInput.value = '';
            previewArea.style.display = 'none';
            previewArea.innerHTML = '';
            updateSendBtnState();
        });
    }
    
    // 发送文件
    else if (pendingFile && pendingType === 'file') {
        hasContent = true;
        const formData = new FormData();
        formData.append("incoming_id", incoming_id);
        formData.append("file", pendingFile);
        formData.append("msg_type", "file");
        
        fetch("php/insert-chat.php", {
            method: "POST",
            body: formData
        }).then(() => {
            pendingFile = null;
            pendingType = null;
            fileInput.value = '';
            previewArea.style.display = 'none';
            previewArea.innerHTML = '';
            updateSendBtnState();
        });
    }
    
    // 发送文字
    else if (inputField.value.trim() !== "") {
        hasContent = true;
        const formData = new FormData(form);
        
        fetch("php/insert-chat.php", {
            method: "POST",
            body: formData
        }).then(() => {
            inputField.value = '';
            updateSendBtnState();
        });
    }

    if (hasContent) {
        scrollToBottom();
    }
};

// 实时聊天更新
setInterval(() => {
    fetch("php/get-chat.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `incoming_id=${incoming_id}`
    })
    .then(response => response.text())
    .then(data => {
        chatBox.innerHTML = data;
        if (!chatBox.classList.contains("active")) {
            scrollToBottom();
        }
    });
}, 500);

// 辅助函数
function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}

chatBox.onmouseenter = () => chatBox.classList.add("active");
chatBox.onmouseleave = () => chatBox.classList.remove("active");

// 初始化
inputField.onkeyup = updateSendBtnState;
inputField.focus();
updateSendBtnState();

// ====== 表情包功能 ======
const emojiBtn = document.getElementById('emojiBtn');
const emojiPanel = document.getElementById('emojiPanel');

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
        formData.append("incoming_id", form.querySelector('.incoming_id').value);
        formData.append("msg_type", "image");
        // 直接发送图片URL（后端需支持本地图片路径）
        formData.append("emoji_path", this.getAttribute('src'));
        fetch("php/insert-chat.php", {
            method: "POST",
            body: formData
        }).then(() => {
            emojiPanel.style.display = 'none';
            updateSendBtnState();
            scrollToBottom();
        });
    };
});