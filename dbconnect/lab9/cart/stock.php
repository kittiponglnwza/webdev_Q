
<?php
session_start();
include "connect.php";

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    exit;
}

$stmt = $pdo->prepare("SELECT pid, pname, price, stock FROM product");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สินค้าคงเหลือ</title>
</head>
<body>

<h2>รายการสินค้าคงเหลือ</h2>

<table >
    <tr>
        <th>รหัสสินค้า</th>
        <th>ชื่อสินค้า</th>
        <th>ราคา</th>
        <th>จำนวนคงเหลือ</th>
    </tr>

    <?php foreach ($products as $p) { ?>
    <tr>
        <td><?= $p['pid'] ?></td>
        <td><?= $p['pname'] ?></td>
        <td><?= $p['price'] ?></td>
        <td><?= $p['stock'] ?></td>
    </tr>
    <?php } ?>
</table>

<p><a href="index.php">กลับหน้าหลัก</a></p>

</body>
</html>