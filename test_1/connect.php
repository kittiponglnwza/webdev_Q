<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=169DB_24;charset=utf8", "169Muser24", "yUjv4edE");
    $pdo-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e) {
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
}