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
                        $output .= '<img src="'.$row['file_path'].'" style="max-width:180px;max-height:180px;border-radius:8px;display:block;margin-bottom:4px;">';
                    }elseif($row['msg_type'] === 'file' && $row['file_path']){
                        $output .= '<a href="'.$row['file_path'].'" download style="color:#007bff;">[文件] 点击下载</a>';
                    }else{
                        $output .= '<p>'. $row['msg'] .'</p>';
                    }
                    $output .= '</div></div>';
                }else{
                    $output .= '<div class="chat incoming">';
                    $output .= '<img src="images/'.$row['img'].'" alt="">';
                    $output .= '<div class="details">';
                    if($row['msg_type'] === 'image' && $row['file_path']){
                        $output .= '<img src="'.$row['file_path'].'" style="max-width:180px;max-height:180px;border-radius:8px;display:block;margin-bottom:4px;">';
                    }elseif($row['msg_type'] === 'file' && $row['file_path']){
                        $output .= '<a href="'.$row['file_path'].'" download style="color:#007bff;">[文件] 点击下载</a>';
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