<?php 
/**
 * Workshop 1: แสดงข้อมูลสินค้าทั้งหมดในรูปแบบตาราง
 */
include "connect.php"; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Workshop 1 - รายการสินค้า</title>
</head>
<body>
    <?php
    // 1. เตรียมคำสั่ง SQL เพื่อดึงข้อมูลสินค้าทั้งหมดจากตาราง product
    $stmt = $pdo->prepare("SELECT * FROM product");
    // 2. ประมวลผลคำสั่ง SQL
    $stmt->execute();
    ?>

    <!-- แสดงผลข้อมูลในรูปแบบตาราง -->
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr bgcolor="#f2f2f2">
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>รายละเอียด</th>
                <th>ราคา</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // 3. วนลูปอ่านข้อมูลทีละแถวด้วย fetch()
            while ($row = $stmt->fetch()): 
            ?>
            <tr>
                <td><?=$row["pid"]?></td>
                <td><?=$row["pname"]?></td>
                <td><?=$row["pdetail"]?></td>
                <td><?=$row["price"]?> บาท</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
