<?php
// =============================================================================
// ไฟล์: blueshop_controller.php
// หน้าที่: เป็นไฟล์ PHP ที่เก็บ "ฟังก์ชันทั้งหมด" สำหรับประมวลผล Business Logic
//          ไม่มีโค้ด HTML ปนเลย -> ส่งผลลัพธ์ไปแสดงที่ blueshop_view.php
// วิธีใช้: เปิด URL http://localhost/.../test_1/blueshop_controller.php
// =============================================================================

include "connect.php";

// #############################################################################
// #  ส่วนที่ 1: ฟังก์ชันดึงข้อมูล (Data Access Functions)                      #
// #############################################################################

// =============================================================================
// ฟังก์ชันที่ 1: getProducts($pdo)
// หน้าที่: ดึงข้อมูลสินค้าทั้งหมดจากตาราง product
// พารามิเตอร์: $pdo = PDO Connection Object
// คืนค่า: อาร์เรย์ 2 มิติ [ ['pid'=>1,'pname'=>'Centrum','price'=>350], ... ]
// =============================================================================
function getProducts($pdo) {
    $stmt = $pdo->query("SELECT * FROM product");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// =============================================================================
// ฟังก์ชันที่ 2: getProductById($pdo, $pid)
// หน้าที่: ดึงข้อมูลสินค้า 1 ชิ้นจากรหัสสินค้า (pid)
// พารามิเตอร์: $pdo = PDO Object, $pid = รหัสสินค้า
// คืนค่า: อาร์เรย์ 1 มิติ ['pid'=>1,'pname'=>'Centrum','price'=>350]
//         หรือ false ถ้าไม่พบ
// =============================================================================
function getProductById($pdo, $pid) {
    $stmt = $pdo->prepare("SELECT * FROM product WHERE pid = ?");
    $stmt->execute([$pid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// =============================================================================
// ฟังก์ชันที่ 3: checkMember($pdo, $username)
// หน้าที่: ตรวจสอบว่า username มีอยู่ในตาราง member หรือไม่
// พารามิเตอร์: $pdo = PDO Object, $username = ชื่อผู้ใช้ที่ต้องการตรวจ
// คืนค่า: อาร์เรย์ข้อมูลสมาชิก ถ้าพบ / false ถ้าไม่พบ
// =============================================================================
function checkMember($pdo, $username) {
    $stmt = $pdo->prepare("SELECT * FROM member WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// #############################################################################
// #  ส่วนที่ 2: ฟังก์ชันคำนวณ (Calculation Functions)                          #
// #############################################################################

// =============================================================================
// ฟังก์ชันที่ 4: calculateOrder($products, $qty)
// หน้าที่: คำนวณยอดเงินรวม, จำนวนชิ้นรวม, ราคาสินค้าชิ้นที่ซื้อ
// พารามิเตอร์:
//   $products = อาร์เรย์สินค้าทั้งหมด (จากฟังก์ชัน getProducts)
//   $qty = อาร์เรย์จำนวนที่ซื้อ [pid => จำนวน] (จากฟอร์ม)
// คืนค่า: อาร์เรย์ ['total'=>ยอดรวม, 'count'=>จำนวนชิ้น, 'buy_price'=>ราคาชิ้นที่ซื้อ]
// =============================================================================
function calculateOrder($products, $qty) {
    $total     = 0;
    $count     = 0;
    $buy_price = 0;

    foreach ($products as $p) {
        $q = (int)($qty[$p['pid']] ?? 0);
        if ($q > 0) {
            $total += $p['price'] * $q;   // ยอดเงิน = ราคา x จำนวน
            $count += $q;                 // สะสมจำนวนชิ้น
            $buy_price = $p['price'];     // เก็บราคาสินค้าชิ้นที่ซื้อ
        }
    }

    return [
        'total'     => $total,
        'count'     => $count,
        'buy_price' => $buy_price
    ];
}

// =============================================================================
// ฟังก์ชันที่ 5: calculateDiscount($total, $isMember)
// หน้าที่: คำนวณส่วนลดสำหรับสมาชิก (เตรียมไว้กรณีโจทย์เพิ่มเรื่องส่วนลด)
// ตัวอย่าง: สมาชิกได้ส่วนลด 10%
// คืนค่า: ['discount'=>จำนวนเงินที่ลด, 'net'=>ยอดหลังหักส่วนลด]
// =============================================================================
function calculateDiscount($total, $isMember) {
    $discount = 0;
    $rate = 0;
    if ($isMember == 1) {
        $rate = 0.10;                    // สมาชิกลด 10%
        $discount = $total * $rate;
    }
    return [
        'rate'     => $rate,
        'discount' => $discount,
        'net'      => $total - $discount
    ];
}

// =============================================================================
// ฟังก์ชันที่ 6: calculateFreeQuota($total, $threshold)
// หน้าที่: คำนวณจำนวนชิ้นที่ได้แถม (เตรียมไว้กรณีโจทย์เปลี่ยน)
// ตัวอย่าง: ซื้อครบทุกๆ 500 บาท ได้แถม 1 ชิ้น
//          ซื้อ 1000 ได้ 2 ชิ้น, ซื้อ 1500 ได้ 3 ชิ้น
// =============================================================================
function calculateFreeQuota($total, $threshold) {
    return floor($total / $threshold);
}


// #############################################################################
// #  ส่วนที่ 3: ฟังก์ชันตรวจสอบโปรโมชัน (Promotion Functions)                  #
// #############################################################################

// =============================================================================
// ฟังก์ชันที่ 7: checkPromotion($products, $member, $total, $count, $buy_price)
// หน้าที่: ตรวจสอบเงื่อนไขโปรโมชัน (ตามโจทย์ข้อ 1)
//
// >> เงื่อนไขที่ 1 (สมาชิก):
//    ลูกค้าเป็นสมาชิก เมื่อซื้อสินค้า 1 ชิ้นใดๆ
//    มีสิทธิ์เลือกได้อีก 1 ชิ้นที่ราคาเท่ากันหรือน้อยกว่า
//    โดยลูกค้าอาจใช้สิทธิ์หรือไม่ก็ได้
//
// >> เงื่อนไขที่ 2 (ไม่ใช่สมาชิก):
//    ลูกค้าไม่เป็นสมาชิก เมื่อซื้อสินค้าไม่จำกัดจำนวนชิ้น
//    ที่มียอดซื้อ 500 บาทขึ้นไป
//    มีสิทธิ์เลือกได้อีก 1 ชิ้นที่ราคาเท่ากับหรือน้อยกว่า น้อยกว่า 500
//    โดยลูกค้าอาจใช้สิทธิ์หรือไม่ก็ได้
//
// คืนค่า: อาร์เรย์ของสินค้าที่สามารถเลือกเป็นของแถมได้
// =============================================================================
function checkPromotion($products, $member, $total, $count, $buy_price) {
    $eligible = [];

    // เงื่อนไขที่ 1: สมาชิก + ซื้อ 1 ชิ้น
    if ($member == 1 && $count == 1) {
        foreach ($products as $p) {
            if ($p['price'] <= $buy_price) {
                $eligible[] = $p;  // สินค้าราคา <= ชิ้นที่ซื้อ
            }
        }
    }
    // เงื่อนไขที่ 2: ไม่ใช่สมาชิก + ยอดรวม >= 500
    elseif ($member == 0 && $total >= 500) {
        foreach ($products as $p) {
            if ($p['price'] <= 500) {
                $eligible[] = $p;  // สินค้าราคา <= 500 บาท
            }
        }
    }

    return $eligible;
}


// #############################################################################
// #  ส่วนที่ 4: ฟังก์ชันบันทึกข้อมูล (Database Write Functions)                #
// #############################################################################

// =============================================================================
// ฟังก์ชันที่ 8: saveOrder($pdo, $username, $products, $qty, $free_pid)
// หน้าที่: บันทึกคำสั่งซื้อลงฐานข้อมูล (ตาราง orders + item)
// ขั้นตอน:
//   1. INSERT ลงตาราง orders -> ได้ ord_id
//   2. วนลูป INSERT สินค้าที่ซื้อลงตาราง item
//   3. ถ้ามีของแถม INSERT ของแถมลง item ด้วย
// คืนค่า: ord_id ที่เพิ่งสร้าง หรือ false ถ้าเกิดข้อผิดพลาด
// =============================================================================
function saveOrder($pdo, $username, $products, $qty, $free_pid) {
    try {
        $pdo->beginTransaction();

        // 1. สร้าง Order ใหม่
        $stmt = $pdo->prepare("INSERT INTO orders (username, ord_date, status) VALUES (?, NOW(), 'wait')");
        $stmt->execute([$username]);
        $ord_id = $pdo->lastInsertId();

        // 2. บันทึกสินค้าที่ซื้อ
        $stmt_item = $pdo->prepare("INSERT INTO item (ord_id, pid, quantity) VALUES (?, ?, ?)");
        foreach ($products as $p) {
            $q = (int)($qty[$p['pid']] ?? 0);
            if ($q > 0) {
                $stmt_item->execute([$ord_id, $p['pid'], $q]);
            }
        }

        // 3. บันทึกของแถม (ถ้าเลือก)
        if (!empty($free_pid)) {
            $stmt_item->execute([$ord_id, $free_pid, 1]);
        }

        $pdo->commit();
        return $ord_id;

    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

// =============================================================================
// ฟังก์ชันที่ 9: getOrderSummary($pdo, $ord_id)
// หน้าที่: ดึงข้อมูลสรุปคำสั่งซื้อ (เตรียมไว้กรณีโจทย์สั่งให้แสดงประวัติ)
// คืนค่า: อาร์เรย์ข้อมูลคำสั่งซื้อพร้อมรายการสินค้า
// =============================================================================
function getOrderSummary($pdo, $ord_id) {
    $stmt = $pdo->prepare("
        SELECT o.ord_id, o.username, o.ord_date, o.status,
               i.pid, i.quantity, p.pname, p.price
        FROM orders o
        JOIN item i ON o.ord_id = i.ord_id
        JOIN product p ON i.pid = p.pid
        WHERE o.ord_id = ?
    ");
    $stmt->execute([$ord_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// #############################################################################
// #  ส่วนประมวลผลหลัก (Main Controller) - เรียกใช้ฟังก์ชันข้างต้นทั้งหมด     #
// #############################################################################

// 1. ดึงสินค้าทั้งหมดจากฐานข้อมูล
$products = getProducts($pdo);

// 2. รับค่าจากแบบฟอร์ม HTTP POST
$step     = $_POST['step'] ?? 0;        // สเต็ปปัจจุบัน (0=เลือกซื้อ, 1=ตรวจสิทธิ์, 2=สรุป, 3=บันทึก)
$member   = $_POST['member'] ?? 1;      // ประเภทลูกค้า (1=สมาชิก, 0=ไม่ใช่)
$qty      = $_POST['qty'] ?? [];        // อาร์เรย์จำนวนสินค้า [pid => จำนวน]
$free_pid = $_POST['free_pid'] ?? '';   // รหัสสินค้าของแถมที่เลือก
$username = $_POST['username'] ?? '';   // username สมาชิก

// 3. คำนวณยอดเงินและจำนวนชิ้น
$calc      = calculateOrder($products, $qty);
$total     = $calc['total'];
$count     = $calc['count'];
$buy_price = $calc['buy_price'];

// 4. ตรวจสอบสิทธิ์โปรโมชัน
$eligible = checkPromotion($products, $member, $total, $count, $buy_price);

// 5. คำนวณส่วนลด (เตรียมไว้)
$discountInfo = calculateDiscount($total, $member);

// 6. ถ้าเป็น Step 3 -> บันทึกลงฐานข้อมูล
$saved_ord_id = null;
if ($step == 3) {
    $save_username = ($username !== '') ? $username : 'guest';
    $saved_ord_id = saveOrder($pdo, $save_username, $products, $qty, $free_pid);
}

// 7. ส่งตัวแปรทั้งหมดไปแสดงผลที่ View Template
include "blueshop_view.php";
?>