<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing"><div class="details">';
                    if($row['msg_type'] === 'image' && $row['file_path']){
                        // 判断是否为表情包图片
                        if(strpos($row['file_path'], 'images/emojis/') === 0){
                            $output .= '<img class="emoji-img" src="'.$row['file_path'].'" alt="emoji">';
                        }else{
                            $output .= '<img src="'.$row['file_path'].'" style="max-width:180px;max-height:180px;border-radius:8px;display:block;margin-bottom:4px;">';
                        }
                    }elseif($row['msg_type'] === 'file' && $row['file_path']){
                        // 获取文件名和大小
                        $file_name = basename($row['file_path']);
                        $file_size = file_exists('../'.$row['file_path']) ? filesize('../'.$row['file_path']) : 0;
                        $file_size_str = $file_size >= 1048576 ? round($file_size/1048576,2).' MB' : ($file_size >= 1024 ? round($file_size/1024,1).' KB' : $file_size.' B');
                        $output .= '<div class="wx-file-card"><div class="wx-file-icon"><i class="fas fa-file-alt"></i></div><div class="wx-file-info"><div class="wx-file-name">'.htmlspecialchars($file_name).'</div><div class="wx-file-size">'.$file_size_str.'</div></div><a class="wx-file-download" href="'.$row['file_path'].'" download><i class="fas fa-download"></i> 下载</a></div>';
                    }else{
                        $output .= '<p>'. $row['msg'] .'</p>';
                    }
                    $output .= '</div></div>';
                }else{
                    $output .= '<div class="chat incoming">';
                    $output .= '<img src="images/'.$row['img'].'" alt="">';
                    $output .= '<div class="details">';
                    if($row['msg_type'] === 'image' && $row['file_path']){
                        // 判断是否为表情包图片
                        if(strpos($row['file_path'], 'images/emojis/') === 0){
                            $output .= '<img class="emoji-img" src="'.$row['file_path'].'" alt="emoji">';
                        }else{
                            $output .= '<img src="'.$row['file_path'].'" style="max-width:180px;max-height:180px;border-radius:8px;display:block;margin-bottom:4px;">';
                        }
                    }elseif($row['msg_type'] === 'file' && $row['file_path']){
                        // 获取文件名和大小
                        $file_name = basename($row['file_path']);
                        $file_size = file_exists('../'.$row['file_path']) ? filesize('../'.$row['file_path']) : 0;
                        $file_size_str = $file_size >= 1048576 ? round($file_size/1048576,2).' MB' : ($file_size >= 1024 ? round($file_size/1024,1).' KB' : $file_size.' B');
                        $output .= '<div class="wx-file-card"><div class="wx-file-icon"><i class="fas fa-file-alt"></i></div><div class="wx-file-info"><div class="wx-file-name">'.htmlspecialchars($file_name).'</div><div class="wx-file-size">'.$file_size_str.'</div></div><a class="wx-file-download" href="'.$row['file_path'].'" download><i class="fas fa-download"></i> 下载</a></div>';
                    }else{
                        $output .= '<p>'. $row['msg'] .'</p>';
                    }
                    $output .= '</div></div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }
?>