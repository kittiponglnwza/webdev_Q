<?php include "connect.php" ?>
<?php

session_start();

// เพิ่มสินค้า
if ($_GET["action"]=="add") {
    $pid = $_GET['pid'];
    $buy_qty = intval($_POST['qty']);

    $stmt_chk = $pdo->prepare("SELECT stock FROM product WHERE pid = ?");
    $stmt_chk->execute([$pid]);
    $prod = $stmt_chk->fetch();

    if ($prod && $buy_qty <= $prod["stock"]) {
        $cart_item = array(
            'pid' => $pid,
            'pname' => $_GET['pname'],
            'price' => $_GET['price'],
            'qty' => $buy_qty
        );

        if(empty($_SESSION['cart']))
            $_SESSION['cart'] = array();
     
        if(array_key_exists($pid, $_SESSION['cart'])) {
            // เช็กถ้ารวมกับของเดิมแล้วเกินสต็อกไหม
            if(($_SESSION['cart'][$pid]['qty'] + $buy_qty) <= $prod["stock"]) {
                $_SESSION['cart'][$pid]['qty'] += $buy_qty;
            } else {
                echo "สินค้าในสต็อกไม่พอ (เหลือ {$prod['stock']} ชิ้น)";
            }
        } else {
            $_SESSION['cart'][$pid] = $cart_item;
        }
    } else {
        echo "จำนวนสินค้าที่เลือกเกินกว่าที่มีในสต็อก";
    }


// ปรับปรุงจำนวนสินค้า
} 
// else if ($_GET["action"]=="update") {
// 	$pid = $_GET["pid"];     
// 	$qty = $_GET["qty"];
// 	$_SESSION['cart'][$pid]['qty'] = $qty;


// } 
 elseif ($_GET['action']) {
    $pid = $_GET['pid'] ?? '';
    $qty = filter_var($_GET['qty'] ?? null, FILTER_VALIDATE_INT);

    if (isset($_SESSION['cart'][$pid])) {
        $stmt_chk = $pdo->prepare("SELECT stock FROM product WHERE pid = ?");
        $stmt_chk->execute([$pid]);
        $prod = $stmt_chk->fetch(PDO::FETCH_ASSOC);

        if ($qty > $prod['stock']) {
            $_SESSION['cart'][$pid]['qty'] = (int)$prod['stock'];
            if ($prod['stock'] == 0) {
                echo "สินค้าหมดสต็อก";
            } else {
                echo "จำนวนเกินสต็อก ";
            }
        } else {
            $_SESSION['cart'][$pid]['qty'] = $qty;
        }
    }
 }
// ลบสินค้า
else if ($_GET["action"]=="delete") {
	
	$pid = $_GET['pid'];
	unset($_SESSION['cart'][$pid]);
}
?>

<html>
<head>
<script>
	// ใช้สำหรับปรับปรุงจำนวนสินค้า
	function update(pid) {
		var input = document.getElementById(pid);
		var qty = parseInt(input.value, 10);
		var max = parseInt(input.max, 10);
		if (qty > max) {
			alert("สินค้าในสต็อกมีเพียง " + max + " ชิ้น");
			input.value = max;
			return;
		}
		document.location = "cart.php?action=update&pid=" + encodeURIComponent(pid) + "&qty=" + qty;
	}

</script>
</head>
<body>
<form>
<table border="1">
<?php 
	$sum = 0;
	foreach ($_SESSION["cart"] as $item) {
		$sum += $item["price"] * $item["qty"];
?>
	<tr>
		<td><?=$item["pname"]?></td>
		<td><?=$item["price"]?></td>
		<td>			
			<input type="number" id="<?=$item["pid"]?>" value="<?=$item["qty"]?>" min="1" max="9">
			<a href="#" onclick="update(<?=$item["pid"]?>)">แก้ไข</a>
			<a href="?action=delete&pid=<?=$item["pid"]?>">ลบ</a>
		</td>
	</tr>
<?php } ?>
<tr><td colspan="3" align="right">รวม <?=$sum?> บาท</td></tr>
</table>

<div style="margin-top: 15px;">
    <a href="confirm.php" style="padding: 10px 20px;">ยืนยันการสั่งซื้อ</a>
</div>
</form>

<a href="index.php">< เลือกสินค้าต่อ</a>
</body>
</html>