<?php
include "connect.php";
session_start();

?>
<html>
<head>
    <meta charset="utf-8">
    <title>รายการ Order ของ: <?= $_GET['username']?></title>
</head>
<body>
    <h1>รายการ Order ทั้งหมดของลูกค้า: <?= $_GET['username'] ?></h1>
    
    <hr>

    <?php
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE username = ?");
    $stmt->execute([$_GET['username']]);
    $orders = $stmt->fetchAll();

    ?>
        <table >
            <tr>
                <th>Order ID</th>
                <th>Username</th>
                <th>วันที่สั่งซื้อ</th>
                <th>สถานะ (Status)</th>
            </tr>
            <?php foreach ($orders as $ord) { ?>
            <tr>
                <td><?=$ord["ord_id"]?></td>
                <td><?=$ord["username"]?></td>
                <td><?=$ord["ord_date"]?></td>
                <td><?=$ord["status"]?></td>
            </tr>
            <?php } ?>
        </table>
    <?php 
   
    ?>

</body>
</html>