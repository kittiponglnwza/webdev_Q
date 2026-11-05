<!-- =================================================================== -->
<!-- ไฟล์: blueshop_view.php                                             -->
<!-- หน้าที่: แสดงผล HTML ตามตัวแปรที่ส่งมาจาก blueshop_controller.php    -->
<!--         ไม่มี CSS / ไม่มี Logic คำนวณ / มีเฉพาะโครงสร้าง HTML       -->
<!-- =================================================================== -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Blueshop</title>

    <script>
    // =====================================================================
    // ฟังก์ชัน JavaScript ฝั่ง View (Client-Side Functions)
    // =====================================================================

    // ฟังก์ชันที่ 1: confirmDeleteItem(pname, pid)
    // หน้าที่: ยืนยันการลบสินค้าออกจากรายการ (รีเซ็ตจำนวนเป็น 0)
    // ทำงานเมื่อ: กดปุ่ม "ลบ" ในตารางสินค้า
    function confirmDeleteItem(pname, pid) {
        let input = document.getElementById("qty_" + pid);
        let currentQty = parseInt(input.value) || 0;

        // กรณีที่ 1: สินค้ายังไม่ได้เลือก (จำนวนเป็น 0)
        if (currentQty === 0) {
            alert("สินค้า [" + pname + "] ยังไม่ได้ถูกเลือก");
            return;
        }

        // กรณีที่ 2: มีสินค้า -> ถามยืนยันก่อนลบ
        let answer = confirm("คุณต้องการลบสินค้า [" + pname + "] จำนวน " + currentQty + " ชิ้น ออกจากรายการใช่หรือไม่?");
        if (answer === true) {
            input.value = 0;
            alert("ลบสินค้า [" + pname + "] เรียบร้อยแล้ว");
        }
    }

    // ฟังก์ชันที่ 2: confirmClearAll()
    // หน้าที่: ล้างจำนวนสินค้าทั้งหมดให้เป็น 0
    function confirmClearAll() {
        let answer = confirm("คุณต้องการล้างรายการสินค้าทั้งหมดใช่หรือไม่?");
        if (answer === true) {
            let inputs = document.querySelectorAll('input[type="number"]');
            inputs.forEach(function(inp) { inp.value = 0; });
            alert("ล้างเรียบร้อยแล้ว");
        }
    }

    // ฟังก์ชันที่ 3: confirmOrder()
    // หน้าที่: ยืนยันก่อนบันทึกคำสั่งซื้อลงฐานข้อมูล
    function confirmOrder() {
        return confirm("คุณต้องการยืนยันคำสั่งซื้อและบันทึกลงฐานข้อมูลใช่หรือไม่?");
    }
    </script>
</head>
<body>
<h2>Blueshop</h2>


<?php
// #########################################################################
// สเต็ป 0: หน้าเลือกประเภทลูกค้า + เลือกสินค้า + กรอกจำนวน
// #########################################################################
if ($step == 0):
?>

    <form method="post" action="blueshop_controller.php">
        <!-- ส่ง step = 1 เพื่อไปขั้นตอนตรวจสอบสิทธิ์ -->
        <input type="hidden" name="step" value="1">

        <!-- ==================== เลือกประเภทลูกค้า ==================== -->
        ประเภทลูกค้า:
        <input type="radio" name="member" value="1" <?= ($member == 1 ? 'checked' : '') ?>> สมาชิก
        <input type="radio" name="member" value="0" <?= ($member == 0 ? 'checked' : '') ?>> ไม่ใช่สมาชิก
        <br><br>

        <!-- ==================== ตารางสินค้า ==================== -->
        <table border="1" cellpadding="5">
            <tr>
                <th>สินค้า</th>
                <th>ราคา</th>
                <th>รายละเอียด</th>
                <th>จำนวน</th>
                <th>จัดการ</th>
            </tr>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['pname']) ?></td>
                <td align="center"><?= $p['price'] ?></td>
                <td><?= htmlspecialchars($p['pdetail']) ?></td>
                <td align="center">
                    <!-- ช่องกรอกจำนวน ตั้งชื่อ name เป็น qty[pid] เพื่อส่งเป็น Array -->
                    <input type="number" id="qty_<?= $p['pid'] ?>"
                           name="qty[<?= $p['pid'] ?>]" value="0" min="0" size="4">
                </td>
                <td align="center">
                    <!-- ปุ่มลบ เรียก confirmDeleteItem() -->
                    <button type="button"
                        onclick="confirmDeleteItem('<?= htmlspecialchars($p['pname']) ?>', '<?= $p['pid'] ?>')">
                        ลบ
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>

        <button type="submit">ตกลง</button>
        <button type="button" onclick="confirmClearAll()">ล้างรายการทั้งหมด</button>
    </form>


<?php
// #########################################################################
// สเต็ป 1: หน้าแสดงยอดรวม + ตรวจสิทธิ์โปรโมชัน + เลือกของแถม
// #########################################################################
elseif ($step == 1):
?>

    <form method="post" action="blueshop_controller.php">
        <!-- ส่ง step = 2 เพื่อไปหน้าสรุปผล -->
        <input type="hidden" name="step" value="2">
        <input type="hidden" name="member" value="<?= $member ?>">

        <!-- ส่งจำนวนสินค้าที่เลือกจากขั้นตอนก่อนหน้า (ผ่าน hidden input) -->
        <?php foreach ($qty as $pid => $q): ?>
            <input type="hidden" name="qty[<?= $pid ?>]" value="<?= $q ?>">
        <?php endforeach; ?>

        <!-- ==================== แสดงประเภทลูกค้า ==================== -->
        <p>ประเภทลูกค้า: <b><?= ($member == 1) ? 'สมาชิก' : 'ไม่ใช่สมาชิก' ?></b></p>

        <!-- ==================== แสดงรายการสินค้าที่ซื้อ ==================== -->
        <p>รายการสินค้าที่ซื้อ:</p>
        <ul>
        <?php foreach ($products as $p):
            $q = (int)($qty[$p['pid']] ?? 0);
            if ($q > 0): ?>
                <li><?= htmlspecialchars($p['pname']) ?> x <?= $q ?> = <?= $p['price'] * $q ?> บาท</li>
            <?php endif;
        endforeach; ?>
        </ul>

        <!-- ==================== แสดงยอดซื้อรวม ==================== -->
        <p>ยอดซื้อรวม: <b><?= $total ?></b> บาท</p>

        <!-- ==================== แสดงตัวเลือกของแถม ==================== -->
        <?php if (!empty($eligible)): ?>
            <!-- มีสิทธิ์ -> แสดง Drop-down ของแถม -->
            <p>มีสิทธิ์เลือกสินค้าเพิ่ม 1 ชิ้น:
            <select name="free_pid">
                <option value="">-- ไม่ใช้สิทธิ์ --</option>
                <?php foreach ($eligible as $p): ?>
                    <option value="<?= $p['pid'] ?>">
                        <?= htmlspecialchars($p['pname']) ?> (<?= $p['price'] ?> บาท)
                    </option>
                <?php endforeach; ?>
            </select></p>
        <?php else: ?>
            <!-- ไม่มีสิทธิ์ -->
            <p>ไม่มีสิทธิ์เลือกสินค้าเพิ่ม</p>
        <?php endif; ?>

        <br>
        <button type="submit">ยืนยัน</button>
        <a href="blueshop_controller.php"><button type="button">กลับ</button></a>
    </form>


<?php
// #########################################################################
// สเต็ป 2: หน้าสรุปรายการ + ยอดชำระ + ปุ่มบันทึกลง DB
// #########################################################################
elseif ($step == 2):
?>

    <h3>สรุปรายการ</h3>

    <p>ประเภทลูกค้า: <b><?= ($member == 1) ? 'สมาชิก' : 'ไม่ใช่สมาชิก' ?></b></p>

    <!-- ==================== รายการสินค้าที่ซื้อ ==================== -->
    <ul>
        <?php foreach ($products as $p):
            $q = (int)($qty[$p['pid']] ?? 0);
            if ($q > 0): ?>
                <li><?= htmlspecialchars($p['pname']) ?> x <?= $q ?> = <?= $p['price'] * $q ?> บาท</li>
            <?php endif;
        endforeach; ?>

        <!-- ==================== ของแถม (ถ้าเลือก) ==================== -->
        <?php if ($free_pid):
            foreach ($products as $p):
                if ($p['pid'] == $free_pid): ?>
                    <li><?= htmlspecialchars($p['pname']) ?> x 1 (สินค้าที่เลือกเพิ่มตามสิทธิ์ ฟรี)</li>
                <?php endif;
            endforeach;
        endif; ?>
    </ul>

    <?php if (!$free_pid): ?>
        <p>ไม่ได้ใช้สิทธิ์เลือกสินค้าเพิ่ม</p>
    <?php endif; ?>

    <!-- ==================== ยอดชำระ ==================== -->
    <p>ยอดชำระทั้งสิ้น: <b><?= $total ?></b> บาท</p>

    <!-- ==================== ปุ่มบันทึกลง DB ==================== -->
    <!-- ส่ง step = 3 เพื่อสั่งให้ Controller บันทึกลงฐานข้อมูล -->
    <form method="post" action="blueshop_controller.php" onsubmit="return confirmOrder()">
        <input type="hidden" name="step" value="3">
        <input type="hidden" name="member" value="<?= $member ?>">
        <input type="hidden" name="free_pid" value="<?= $free_pid ?>">
        <?php foreach ($qty as $pid => $q): ?>
            <input type="hidden" name="qty[<?= $pid ?>]" value="<?= $q ?>">
        <?php endforeach; ?>
        <button type="submit">บันทึกคำสั่งซื้อ</button>
    </form>
    <br>
    <a href="blueshop_controller.php">กลับหน้าแรก</a>


<?php
// #########################################################################
// สเต็ป 3: หน้าแสดงผลลัพธ์การบันทึก (Confirmation Page)
// #########################################################################
elseif ($step == 3):
?>

    <?php if ($saved_ord_id): ?>
        <h3>บันทึกคำสั่งซื้อสำเร็จ!</h3>
        <p>หมายเลข Order: <b><?= $saved_ord_id ?></b></p>
        <p>สถานะ: รอชำระเงิน (wait)</p>

        <!-- ==================== แสดงรายละเอียด Order ที่บันทึก ==================== -->
        <?php
        $orderDetails = getOrderSummary($pdo, $saved_ord_id);
        if ($orderDetails):
        ?>
            <table border="1" cellpadding="5">
                <tr>
                    <th>สินค้า</th>
                    <th>ราคา/ชิ้น</th>
                    <th>จำนวน</th>
                    <th>รวม</th>
                </tr>
                <?php
                $grandTotal = 0;
                foreach ($orderDetails as $row):
                    $lineTotal = $row['price'] * $row['quantity'];
                    $grandTotal += $lineTotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['pname']) ?></td>
                    <td align="center"><?= $row['price'] ?></td>
                    <td align="center"><?= $row['quantity'] ?></td>
                    <td align="right"><?= $lineTotal ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" align="right"><b>รวมทั้งหมด</b></td>
                    <td align="right"><b><?= $grandTotal ?></b></td>
                </tr>
            </table>
        <?php endif; ?>
    <?php else: ?>
        <h3>เกิดข้อผิดพลาดในการบันทึก</h3>
        <p>กรุณาลองใหม่อีกครั้ง</p>
    <?php endif; ?>

    <br>
    <a href="blueshop_controller.php">กลับหน้าแรก</a>


<?php endif; ?>

</body>
</html>