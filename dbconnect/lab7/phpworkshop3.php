<?php 
/**
 * Workshop 3: แสดงข้อมูลสมาชิกพร้อมรูปภาพประจำตัว
 */
include "connect.php"; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Workshop 3 - รายชื่อสมาชิกและรูปภาพ</title>
</head>
<body>
    <?php
    // 1. ดึงข้อมูลสมาชิกทั้งหมดจากตาราง member
    $stmt = $pdo->prepare("SELECT * FROM member");
    $stmt->execute();

    // 2. วนลูปแสดงข้อมูลพร้อมแสดงรูปภาพจากโฟลเดอร์ member_photo/
    while ($row = $stmt->fetch()): 
    ?>
        ชื่อสมาชิก: <?=$row["username"]?><br>
        ที่อยู่: <?=$row["address"]?><br>
        อีเมล์: <?=$row["email"]?><br>
        <img src="member_photo/<?=$row["username"]?>.jpg" width="100" alt="<?=$row["username"]?>"><br>
        <hr>
    <?php endwhile; ?>
</body>
</html>
