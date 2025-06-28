<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        $msg_type = isset($_POST['msg_type']) ? $_POST['msg_type'] : 'text';
        $file_path = NULL;

        // Handle image upload
        if($msg_type === 'image' && isset($_FILES['image'])){
            $img = $_FILES['image'];
            $img_name = time().'_'.$img['name'];
            $img_tmp = $img['tmp_name'];
            $img_folder = '../images/'.$img_name;
            if(move_uploaded_file($img_tmp, $img_folder)){
                $file_path = 'images/'.$img_name;
            }
        }
        // Handle file upload
        elseif($msg_type === 'file' && isset($_FILES['file'])){
            $file = $_FILES['file'];
            $file_name = time().'_'.$file['name'];
            $file_tmp = $file['tmp_name'];
            $file_folder = '../images/'.$file_name;
            if(move_uploaded_file($file_tmp, $file_folder)){
                $file_path = 'images/'.$file_name;
            }
        }

        // Handle emoji image (本地表情包图片)
        if($msg_type === 'image' && isset($_POST['emoji_path'])){
            $file_path = mysqli_real_escape_string($conn, $_POST['emoji_path']);
        }

        // Insert message into database
        if($msg_type === 'text' && !empty($message)){
            $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, msg_type) VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', 'text')");
        }
        elseif($msg_type === 'image' && $file_path){
            $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, msg_type, file_path) VALUES ({$incoming_id}, {$outgoing_id}, '[图片]', 'image', '{$file_path}')");
        }
        elseif($msg_type === 'file' && $file_path){
            $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, msg_type, file_path) VALUES ({$incoming_id}, {$outgoing_id}, '[文件]', 'file', '{$file_path}')");
        }
    }else{
        header("location: ../login.php");
    }
?>