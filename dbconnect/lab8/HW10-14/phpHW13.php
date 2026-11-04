<?php include "../connect.php"?>
<html>
    <body>
        <?php
            $stmt = $pdo->prepare("SELECT * FROM product");
            $stmt->execute();
            while($row = $stmt->fetch()):
        ?>
        <img src="../product_photo/<?=$row["pid"]?>.jpg?<?=time()?>" width="100"><br>
        รหัสสินค้า: <?=$row["pid"]?><br>
        ชื่อสินค้า: <?=$row["pname"]?><br>
        รายละเอียดสินค้า: <?=$row["pdetail"]?><br>
        ราคา: <?=$row["price"]?> บาท<br>
        <a href="editformHW13.php?pid=<?=$row["pid"]?>">แก้ไข</a>
        <hr>

        <?php endwhile;?>
    </body>
</html>