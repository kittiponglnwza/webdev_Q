<?php
// เชื่อมต่อฐานข้อมูลผ่าน PDO จากไฟล์ connect.php
include "connect.php";

// ดึงข้อมูลสินค้าทั้งหมดจากตาราง product มาเก็บในตัวแปร $products
$stmt = $pdo->query("SELECT * FROM product");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// รับค่าจากฟอร์มผ่าน method POST (ถ้าไม่มีค่า ให้ใช้ค่า default)
$step     = $_POST['step'] ?? 0;       // สเต็ปการทำงาน (0 = เลือกซื้อ, 1 = ตรวจสิทธิ์, 2 = สรุปผล)
$member   = $_POST['member'] ?? 1;     // ประเภทลูกค้า (1 = สมาชิก, 0 = ไม่ใช่สมาชิก)
$qty      = $_POST['qty'] ?? [];       // อาเรย์เก็บจำนวนที่ซื้อ [pid => จำนวน]
$free_pid = $_POST['free_pid'] ?? '';  // รหัสสินค้าที่เลือกรับฟรี (ถ้ามี)

// -------------------------------------------------------------
// คำนวณ: ยอดซื้อรวม ($total), จำนวนชิ้นรวม ($count), ราคาสินค้าที่ซื้อ ($buy_price)
// -------------------------------------------------------------
$total = 0; 
$count = 0; 
$buy_price = 0;

foreach ($products as $p) {
    $q = (int)($qty[$p['pid']] ?? 0); // ดึงจำนวนที่กรอกของสินค้านั้นๆ
    if ($q > 0) {
        $total += $p['price'] * $q;   // ราคาสินค้า x จำนวน
        $count += $q;                 // นับจำนวนชิ้นรวม
        $buy_price = $p['price'];     // บันทึกราคาของชิ้นที่ซื้อ (ใช้กรณีสมาชิกซื้อ 1 ชิ้น)
    }
}

// -------------------------------------------------------------
// ตรวจสอบเงื่อนไขโปรโมชันตามโจทย์
// -------------------------------------------------------------
$eligible = []; // รายการสินค้าที่มีสิทธิ์เลือกรับฟรี

// เงื่อนไขที่ 1: ลูกค้าเป็นสมาชิก ซื้อสินค้า 1 ชิ้น -> เลือกเพิ่มได้ 1 ชิ้น ที่ราคา <= ชิ้นที่ซื้อ
if ($member == 1 && $count == 1) {
    foreach ($products as $p) {
        if ($p['price'] <= $buy_price) {
            $eligible[] = $p; // เก็บสินค้าที่ราคาเท่ากับหรือน้อยกว่าชิ้นที่ซื้อ
        }
    }
} 
// เงื่อนไขที่ 2: ไม่ใช่สมาชิก ซื้อกี่ชิ้นก็ได้ แต่ยอดรวม >= 500 บาท -> เลือกเพิ่มได้ 1 ชิ้น ที่ราคา <= 500 บาท
elseif ($member == 0 && $total >= 500) {
    foreach ($products as $p) {
        if ($p['price'] <= 500) {
            $eligible[] = $p; // เก็บสินค้าที่ราคาไม่เกิน 500 บาท
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Blueshop</title>
</head>
<body>
<h2>Blueshop</h2>

<?php 
// =============================================================
// สเต็ป 0: หน้าเลือกประเภทลูกค้า และกรอกจำนวนสินค้าที่ต้องการซื้อ
// =============================================================
if ($step == 0): 
?>
    <form method="post">
        <!-- ส่งค่า step = 1 เพื่อบอกว่าเมื่อกดปุ่มให้ไปขั้นตอนตรวจสอบสิทธิ์ -->
        <input type="hidden" name="step" value="1">

        ประเภทลูกค้า: 
        <input type="radio" name="member" value="1" checked> สมาชิก
        <input type="radio" name="member" value="0"> ไม่ใช่สมาชิก<br><br>

        <table border="1" cellpadding="5">
            <tr>
                <th>สินค้า</th>
                <th>ราคา</th>
                <th>จำนวน</th>
            </tr>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['pname'] ?></td>
                <td><?= $p['price'] ?></td>
                <!-- ส่งจำนวนเป็น array โดยมี key เป็นรหัสสินค้า: qty[รหัส] -->
                <td><input type="number" name="qty[<?= $p['pid'] ?>]" value="0" min="0"></td>
            </tr>
            <?php endforeach; ?>
        </table><br>

        <button type="submit">ตกลง</button>
    </form>

<?php 
// =============================================================
// สเต็ป 1: หน้าแสดงยอดซื้อรวม ตรวจสอบสิทธิ์ และเลือกของแถม (ถ้ามี)
// =============================================================
elseif ($step == 1): 
?>
    <form method="post">
        <!-- ส่ง step = 2 เพื่อบอกว่าจะไปหน้าสรุปผล -->
        <input type="hidden" name="step" value="2">
        <input type="hidden" name="member" value="<?= $member ?>">

        <!-- ส่งจำนวนสินค้าที่เลือกจากขั้นตอนก่อนหน้าต่อไปยังขั้นตอนถัดไป -->
        <?php foreach ($qty as $pid => $q): ?>
            <input type="hidden" name="qty[<?= $pid ?>]" value="<?= $q ?>">
        <?php endforeach; ?>
        
        <p>ยอดซื้อรวม: <b><?= $total ?></b> บาท</p>

        <!-- ถ้ามีสิทธิ์ได้รับของแถม ($eligible มีข้อมูล) -->
        <?php if ($eligible): ?>
            <p>มีสิทธิ์เลือกสินค้าเพิ่ม 1 ชิ้น:
            <select name="free_pid">
                <option value="">-- ไม่ใช้สิทธิ์ --</option>
                <?php foreach ($eligible as $p): ?>
                    <option value="<?= $p['pid'] ?>"><?= $p['pname'] ?> (<?= $p['price'] ?> บาท)</option>
                <?php endforeach; ?>
            </select></p>
        <?php else: ?>
            <p>ไม่มีสิทธิ์เลือกสินค้าเพิ่ม</p>
        <?php endif; ?>

        <button type="submit">ยืนยัน</button>
        <a href="blueshop.php"><button type="button">กลับ</button></a>
    </form>

<?php 
// =============================================================
// สเต็ป 2: หน้าสรุปรายการสินค้าที่ซื้อ ของแถม และยอดชำระทั้งสิ้น
// =============================================================
elseif ($step == 2): 
?>
    <h3>สรุปรายการ</h3>
    <ul>
        <!-- วนลูปแสดงสินค้าที่ซื้อ (จำนวน > 0) -->
        <?php foreach ($products as $p): 
            $q = (int)($qty[$p['pid']] ?? 0);
            if ($q > 0): ?>
                <li><?= $p['pname'] ?> x <?= $q ?> = <?= $p['price'] * $q ?> บาท</li>
            <?php endif;
        endforeach; ?>

        <!-- ถ้ามีการเลือกสินค้าแถม -->
        <?php if ($free_pid): 
            foreach ($products as $p):
                if ($p['pid'] == $free_pid): ?>
                    <li><?= $p['pname'] ?> x 1 (สินค้าที่เลือกเพิ่มตามสิทธิ์ ฟรี)</li>
                <?php endif;
            endforeach;
        endif; ?>
    </ul>

    <?php if (!$free_pid) echo "<p>ไม่ได้ใช้สิทธิ์เลือกสินค้าเพิ่ม</p>"; ?>
    <p>ยอดชำระทั้งสิ้น: <b><?= $total ?></b> บาท</p>
    <a href="blueshop.php">กลับหน้าแรก</a>
<?php endif; ?>

</body>
</html>
