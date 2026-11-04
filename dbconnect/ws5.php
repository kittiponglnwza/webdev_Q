<?php include "connect.php"; ?>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>

<?php

if (isset($_GET["username"])) {
    $username = $_GET["username"];

   
    $stmt = $pdo->prepare("SELECT * FROM member WHERE username = ?");
    $stmt->bindParam(1, $username);
    $stmt->execute();
    $row = $stmt->fetch();
}
?>

<?php if (!empty($row)) : ?>
    <h2>รายละเอียดสมาชิก</h2>
    <div style="display: flex; gap: 20px;">
        <div>
            <img src='img_member/<?=$row["username"]?>.jpg' width='180'>
        </div>
        <div>
            <p><b>Username:</b> <?=$row["username"]?></p>
            <p><b>ชื่อ-นามสกุล:</b> <?=$row["name"]?></p>
            <p><b>ที่อยู่:</b> <?=$row["address"]?></p>
            <p><b>เบอร์โทร:</b> <?=$row["mobile"]?></p>
            <p><b>อีเมล์:</b> <?=$row["email"]?></p>
        </div>
    </div>

<br>
<a href="ws4.php"><< ย้อนกลับหน้าค้นหา</a>

</body>
</html>