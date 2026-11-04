<?php include "connect.php"?>
<?php
    $stmt = $pdo->prepare("SELECT * FROM member WHERE username = ?");
    $stmt->bindParam(1,$_GET["username"]);
    $stmt->execute();
    $row = $stmt->fetch();
?>
<html>
    <head><meta charset="utf-8"></head>
    <body>
        <form action="edit-memberws9.php" method="post">
            username: <input type="text" name="username" value="<?=$row["username"]?>" readonly><br>
            password: <input type="password" name="password" value="<?=$row["password"]?>"><br>
            ชื่อ-นามสกุล: <input type="text" name="name" value="<?=$row["name"]?>"><br>
            ที่อยู่: <input type="text" name="address" value="<?=$row["address"]?>"><br>
            เบอร์โทรศัพท์: <input type="tel" name="mobile" value="<?=$row["mobile"]?>"><br>
            email: <input type="text" name="email" value="<?=$row["email"]?>"><br>
            <button type="submit">แก้ไขสมาชิก</button>

        </form>
    </body>
</html>