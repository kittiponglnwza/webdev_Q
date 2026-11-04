<?php include "../connect.php"?>
<html>
    <body>
        <?php
            $stmt = $pdo->prepare("UPDATE product SET pname=?,pdetail=?,price=? WHERE pid = ?");
            $stmt->bindParam(1,$_POST["pname"]);
            $stmt->bindParam(2,$_POST["pdetail"]);
            $stmt->bindParam(3,$_POST["price"]);
            $stmt->bindParam(4,$_POST["pid"]);
            if($stmt->execute()){
                if(
                    isset($_FILES["product_photo"]) &&
                    $_FILES["product_photo"]["error"] == 0
                ){

                    if(move_uploaded_file(
                        $_FILES["product_photo"]["tmp_name"],
                        "../product_photo/" . $_POST["pid"] . ".jpg"
                    )){

                        echo "Upload รูปสำเร็จ<br>";

                    }
                }
        ?>
        แก้ไขสินค้า <?=$_POST["pname"]?> สำเร็จ
        <?php }?>
    </body>
</html>