<?php include "../connect.php"?>
<?php
    $stmt = $pdo->prepare("INSERT INTO member VALUES(?,?,?,?,?,?)");
    $stmt->bindParam(1,$_POST["username"]);
    $stmt->bindParam(2,$_POST["password"]);
    $stmt->bindParam(3,$_POST["name"]);
    $stmt->bindParam(4,$_POST["address"]);
    $stmt->bindParam(5,$_POST["mobile"]);
    $stmt->bindParam(6,$_POST["email"]);
    $stmt->execute();

    $username = $_POST["username"];
    $password = $_POST["password"];
    $name = $_POST["name"];
    $address = $_POST["address"];
    $mobile = $_POST["mobile"];
    $email = $_POST["email"];

    move_uploaded_file(
        $_FILES["member_photo"]["tmp_name"],"../member_photo/".$username.".jpg"
    );
?>
<html>
    <head><meta charset="utf-8"></head>
    <body>
        เพิ่มข้อมูลสมาชิกสำเร็จ<br>
        img: <br><img src="../member_photo/<?=$username?>.jpg" width="100"><br>
        username: <?=$username?><br>
        password: <?=$password?><br>
        ชื่อ-นามสกุล: <?=$name?><br>
        ที่อยู่: <?=$address?><br>
        เบอร์โทรศัพท์: <?=$mobile?><br>
        email:<?=$email?>
    </body>
</html>