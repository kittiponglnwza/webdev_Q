<?php
// =============================================================================
// ไฟล์: get_products.php
// หน้าที่: รับคำขอ (action) จาก JavaScript ฝั่ง HTML แล้วตอบกลับเป็น JSON
// รองรับหลาย action: ดึงสินค้า, เช็คสมาชิก, บันทึกคำสั่งซื้อ
// =============================================================================
header('Content-Type: application/json; charset=utf-8');
include "connect.php";

// รับค่า action จาก GET หรือ POST (ถ้าไม่มีให้ใช้ "list" เป็นค่าเริ่มต้น)
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

// =============================================================================
// Action 1: "list" = ดึงรายการสินค้าทั้งหมดจากตาราง product
// วิธีเรียก: fetch('get_products.php') หรือ fetch('get_products.php?action=list')
// =============================================================================
if ($action === 'list') {
    $stmt = $pdo->query("SELECT pid, pname, pdetail, price FROM product");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products, JSON_UNESCAPED_UNICODE);
}

// =============================================================================
// Action 2: "check_member" = ตรวจสอบว่า username มีอยู่ในตาราง member หรือไม่
// วิธีเรียก: fetch('get_products.php?action=check_member&username=somsak')
// ตอบกลับ: { "found": true, "name": "สมศักดิ์ สุรเสถียร" }
//       หรือ { "found": false }
// =============================================================================
elseif ($action === 'check_member') {
    $username = $_GET['username'] ?? '';
    $stmt = $pdo->prepare("SELECT name FROM member WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode(["found" => true, "name" => $row['name']], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["found" => false]);
    }
}

// =============================================================================
// Action 3: "save_order" = บันทึกคำสั่งซื้อลงตาราง orders + item
// วิธีเรียก: fetch('get_products.php', { method:'POST', body: JSON.stringify({...}) })
// รับข้อมูลแบบ JSON: { action, username, items:[{pid,qty}], free_pid }
// =============================================================================
elseif ($action === 'save_order') {
    // อ่าน JSON ที่ส่งมาจาก JavaScript
    $input = json_decode(file_get_contents('php://input'), true);
    $username  = $input['username'] ?? 'guest';
    $items     = $input['items'] ?? [];
    $free_pid  = $input['free_pid'] ?? '';

    try {
        // เริ่ม Transaction เพื่อให้แน่ใจว่าข้อมูลถูกบันทึกครบทุกตาราง
        $pdo->beginTransaction();

        // 1. บันทึกลงตาราง orders (สร้างคำสั่งซื้อใหม่)
        $stmt = $pdo->prepare("INSERT INTO orders (username, ord_date, status) VALUES (?, NOW(), 'wait')");
        $stmt->execute([$username]);
        $ord_id = $pdo->lastInsertId(); // ดึง ord_id ที่เพิ่งสร้าง

        // 2. วนลูปบันทึกสินค้าแต่ละชิ้นลงตาราง item
        $stmt_item = $pdo->prepare("INSERT INTO item (ord_id, pid, quantity) VALUES (?, ?, ?)");
        foreach ($items as $item) {
            if ($item['qty'] > 0) {
                $stmt_item->execute([$ord_id, $item['pid'], $item['qty']]);
            }
        }

        // 3. ถ้าเลือกของแถม ให้บันทึกของแถมลง item ด้วย (จำนวน 1 ชิ้น)
        if (!empty($free_pid)) {
            $stmt_item->execute([$ord_id, $free_pid, 1]);
        }

        // ยืนยัน Transaction
        $pdo->commit();
        echo json_encode(["success" => true, "ord_id" => $ord_id], JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        // ถ้าเกิดข้อผิดพลาด ยกเลิก Transaction ทั้งหมด
        $pdo->rollBack();
        echo json_encode(["success" => false, "error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

// =============================================================================
// Action อื่นๆ ที่ไม่รู้จัก
// =============================================================================
else {
    http_response_code(400);
    echo json_encode(["error" => "ไม่รู้จัก action: " . $action], JSON_UNESCAPED_UNICODE);
}
?>