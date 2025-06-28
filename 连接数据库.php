<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ihello";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT id,name, 图片链接 FROM plant";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<li>";
        echo "<div class='img'><a href='plant_detail.php?id=" . $row["id"] . "'><img src='" . $row["图片链接"] . "'></a>";
        echo "<div class='price_box'>";
        echo "<p class='show_link'><a href='plant_detail.php?id=" . $row["id"] . "'>查看详细</a></p>";
        echo "</div>";
        echo "</div>";
        echo "<div class='text'>";
        echo "<p><a href='plant_detail.php?id=" . $row["id"] . "'>" . $row["name"] . "</a></p>";
        echo "</div>";
        echo "</li>";
    }
} else {
    echo "0 results";
}
$conn->close();
?>

