<?php 
/**
 * Workshop 4: ค้นหาข้อมูลสมาชิกด้วย Keyword (ชื่อ หรือ อีเมล หรือ username)
 */
include "connect.php"; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Workshop 4 - ค้นหาสมาชิก</title>
</head>
<body>
    <!-- ฟอร์มค้นหาข้อมูล -->
    <form method="get">
        <input type="text" name="keyword" placeholder="พิมพ์ชื่อ, username หรืออีเมล" value="<?=isset($_GET["keyword"]) ? htmlspecialchars($_GET["keyword"]) : ''?>">
        <input type="submit" value="ค้นหา">
    </form>

    <?php
    // 1. ตรวจสอบว่ามีการส่งคำค้นหามาหรือไม่
    $keyword = isset($_GET["keyword"]) ? $_GET["keyword"] : "";
    
    // 2. ใช้ Prepared Statement ค้นหาจาก username (หรือเพิ่ม name/email ได้)
    $stmt = $pdo->prepare("SELECT * FROM member WHERE username LIKE ?");
    $value = '%' . $keyword . '%';
    $stmt->bindParam(1, $value);
    $stmt->execute();

    // 3. วนลูปแสดงผลรายการสมาชิกที่ค้นพบ
    while ($row = $stmt->fetch()): 
    ?>
        <div style="padding: 15px; text-align: center;">
            <img src="member_photo/<?=$row["username"]?>.jpg" width="100" alt="<?=$row["username"]?>"><br>
            ชื่อสมาชิก: <?=$row["username"]?><br>
            ที่อยู่: <?=$row["address"]?><br>
            อีเมล์: <?=$row["email"]?><br>
            <hr>
        </div>
    <?php endwhile; ?>
</body>
</html>
