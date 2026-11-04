<?php include "../connect.php"?>
<?php
    $stmt = $pdo->prepare("INSERT INTO product (pname,pdetail,price) VALUES (?,?,?)");
    $stmt->bindParam(1,$_POST["pname"]);
    $stmt->bindParam(2,$_POST["pdetail"]);
    $stmt->bindParam(3,$_POST["price"]);
    $stmt->execute();
    $pid = $pdo->lastInsertId();
    move_uploaded_file(
        $_FILES["product_photo"]["tmp_name"],"../product_photo/".$pid.".jpg"
    );
?>
<html>
    <body>
        <?php 
            $stmt = $pdo->prepare("SELECT * FROM product");
            $stmt->execute();
            while($row = $stmt->fetch()):
        ?>
            <img src="../product_photo/<?=$row["pid"]?>.jpg" width="100"><br>
            ชื่อสินค้า: <input type="text" name="pname" id="pname" value="<?=$row["pname"]?>"><br>
            รายละเอียดสินค้า: <br><textarea name="pdetail" id="pdetail" ><?=$row["pdetail"]?></textarea><br>
            ราคา: <input type="number" name="price" id="price" value="<?=$row["price"]?>"><br><hr>
            <?php endwhile;?>
        
        
    </body>
</html>