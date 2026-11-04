<?php
include "connect.php";
session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'baramee'; 

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (username, ord_date, status) VALUES (?, NOW(), 'wait')");
    $stmt->execute([$username]);
    
    $ord_id = $pdo->lastInsertId();

    $stmt_item = $pdo->prepare("INSERT INTO item (ord_id, pid, quantity) VALUES (?, ?, ?)");

    foreach ($_SESSION["cart"] as $item) {
        $stmt_item->execute([
            $ord_id, 
            $item["pid"], 
            $item["qty"]
        ]);
    }

    $pdo->commit();

    unset($_SESSION['cart']);

    echo "<h2>บันทึกคำสั่งซื้อเรียบร้อยแล้ว!</h2>";
    echo "<p>รหัสคำสั่งซื้อของคุณคือ: " . $ord_id . "</p>";
    echo '<a href="index.php">กลับสู่หน้าหลัก</a>';

?>