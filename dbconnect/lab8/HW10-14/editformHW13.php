<?php include "../connect.php"?>
<html>
    <body>
        <?php
            $stmt = $pdo->prepare("SELECT * FROM product WHERE pid = ?");
            $stmt->bindParam(1,$_GET["pid"]);
            $stmt->execute();
            $row = $stmt->fetch();
            
            //move_uploaded_file(
            //    $_FILES["product_photo"]["tmp_name"],"../product_photo/".$_POST["pid"]. ".jpg"
            //);
        ?>
        <form action="edit-productHW13.php" method="post" enctype="multipart/form-data">
            old img: <br><img src="../product_photo/<?=$row["pid"]?>.jpg?<?=time()?>" width="100"><br>
            new img: <input type="file" name="product_photo" >
            <input type="hidden" name="pid" value="<?=$row["pid"]?>"><br>
            ชื่อสินค้า: <input type="text" name="pname" value="<?=$row["pname"]?>"><br>
            รายละเอียดสินค้า: <br><textarea name="pdetail" ><?=$row["pdetail"]?></textarea><br>
            ราคา: <input type="number" name="price" value="<?=$row["price"]?>"> บาท<br>
            <button type="submit" >แก้ไขสินค้า</button>
        </form>
        
    </body>
</html>