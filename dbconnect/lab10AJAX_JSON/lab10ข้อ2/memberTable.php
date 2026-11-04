<?php
include "connect.php";
$keyword = $_GET["keyword"];
$stmt = $pdo->prepare("SELECT * FROM member WHERE username LIKE '%$keyword%'");
$stmt->execute();

// $conn = mysql_connect("localhost", "169Muser24", "yUjv4edE"); 

// if ($conn) {
//     mysql_select_db("169DB_24");     
//     mysql_query("SET NAMES utf8"); 

// } else {
//     echo mysql_errno();
// }

// $sql = "SELECT * FROM product WHERE pname LIKE '%$keyword%'"; 
// $objQuery = mysql_query($sql);
?>

<table border="1">
<?php while($row = $stmt->fetch()): ?>
<tr>
<td><a href="productDetail.php?pid=<?php echo $row["username"]?>"><?php echo $row["username"]?></a></td>
<td><?php echo $row["address"]?></td>
<td><img src="member_photo/<?=$row["username"]?>.jpg" width="100"></td>
<td><?php echo $row["mobile"]?></td>
<td><?php echo $row["email"]?></td>
</tr>
<?php endwhile;?>
</table>
