<?php 
/**
 * Workshop 2: แสดงข้อมูลสมาชิก (ชื่อ, ที่อยู่, อีเมล)
 */
include "connect.php"; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Workshop 2 - ข้อมูลสมาชิก</title>
</head>
<body>
    <?php
    // 1. ดึงข้อมูลสมาชิกทั้งหมดจากตาราง member
    $stmt = $pdo->prepare("SELECT * FROM member");
    $stmt->execute();

    // 2. วนลูปแสดงข้อมูลของสมาชิกแต่ละคน
    while ($row = $stmt->fetch()):
    ?>
        ชื่อสมาชิก: <?=$row["username"]?><br>
        ที่อยู่: <?=$row["address"]?><br>
        อีเมล์: <?=$row["email"]?><br>
        <hr>
    <?php endwhile; ?>
</body>
</html>
