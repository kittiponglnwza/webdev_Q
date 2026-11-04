<?php include "../connect.php"?>
<html>
    <body>
        <?php
            $stmt = $pdo->prepare("UPDATE member SET password=?,name=?,address=?,mobile=?,email=? WHERE username=?");
            $stmt->bindParam(1,$_POST["password"]);
            $stmt->bindParam(2,$_POST["name"]);
            $stmt->bindParam(3,$_POST["address"]);
            $stmt->bindParam(4,$_POST["mobile"]);
            $stmt->bindParam(5,$_POST["email"]);
            $stmt->bindParam(6,$_POST["username"]);
            if($stmt->execute()){
                move_uploaded_file(
                    $_FILES["member_photo"]["tmp_name"],
                    "../member_photo/".$_POST["username"].".jpg"
                );
        ?>
        แก้ไขข้อมูล <?=$_POST["username"]?> สำเร็จ
        <?php
            }
        ?>
    </body>
</html>
