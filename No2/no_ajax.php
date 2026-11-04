<?php
// =============================================================
// ข้อ 2 - A. ไม่ใช้ AJAX (Server-Side Rendering)
// คอนเซปต์: PHP อ่านไฟล์ JSON ที่ฝั่งเซิร์ฟเวอร์ แปลงเป็น Array แล้วเรนเดอร์เป็น HTML ส่งให้เบราว์เซอร์
// =============================================================

// 1. อ่านไฟล์ priv_hos.json ออกมาเป็นข้อความ string
$json_string = file_get_contents("priv_hos.json");

// 2. แปลงข้อความ JSON ให้เป็น Associative Array ใน PHP (ใส่ true เพื่อให้ได้ array)
$data = json_decode($json_string, true);

// 3. ดึงก้อน features ออกมา ซึ่งเก็บรายการโรงพยาบาลทั้งหมด
$features = $data['features'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ข้อ 2 A (ไม่ใช้ AJAX)</title>
</head>
<body>
    <table border="1">
        <tr>
            <th>No.</th>
            <th>ชื่อโรงพยาบาล</th>
            <th>ขนาดใหญ่</th>
            <th>ขนาดกลาง</th>
            <th>ขนาดเล็ก</th>
        </tr>
        <?php 
        // 4. วนลูปข้อมูลโรงพยาบาลทีละแห่ง
        // $i คือ index เริ่มที่ 0 (นำมาทำลำดับที่ โดยบวก 1)
        // $h คือข้อมูลโรงพยาบาลแต่ละแห่ง
        foreach ($features as $i => $h): 
            $bed = $h['properties']['num_bed']; // ดึงจำนวนเตียง

            // ตรวจสอบเงื่อนไขตามจำนวนเตียง:
            // - ขนาดใหญ่: ตั้งแต่ 91 เตียงขึ้นไป
            // - ขนาดกลาง: 31 ถึง 90 เตียง
            // - ขนาดเล็ก: ไม่เกิน 30 เตียง
            $big   = ($bed >= 91) ? "✔" : "";
            $mid   = ($bed >= 31 && $bed <= 90) ? "✔" : "";
            $small = ($bed <= 30) ? "✔" : "";
        ?>
        <tr>
            <td><?= $i + 1 ?>.</td>
            <td><?= $h['properties']['name'] ?></td>
            <td align="center"><?= $big ?></td>
            <td align="center"><?= $mid ?></td>
            <td align="center"><?= $small ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
