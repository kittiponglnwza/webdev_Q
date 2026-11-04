<?php
/**
 * ไฟล์เชื่อมต่อฐานข้อมูล MySQL ด้วย PDO
 * Database: 169DB_24 | User: 169Muser24
 */
try {
    $pdo = new PDO("mysql:host=localhost;dbname=169DB_24;charset=utf8", "169Muser24", "yUjv4edE");
    // กำหนดให้แจ้งเตือนข้อผิดพลาดในรูปแบบ Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
}
?>
