<?php
include "connect.php";
session_start();
?>

<html>
<body>
<h1>สวัสดี <?=$_SESSION["fullname"]?></h1>
หากต้องการออกจากระบบโปรดคลิก <a href='logout.php'>ออกจากระบบ</a>
<?php
    // $role = $_SESSION["role"];
    if($_SESSION["role"]=="user"){
        echo "<h1>รายการสั่งซื้อทั้งหมดของคุณ</h1>";
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE username = ?");
        $stmt->execute([$_SESSION["username"]]);
        $Allorder = $stmt->fetchAll();
        // if(count($order)>0){
?>       
            <table>
                <tr>
                    <th>order ID</th>
                    <th>username</th>
                    <th>วันที่สั่ง</th>
                    <th>status</th>
                </tr>
                <?php foreach($Allorder as $order){?>
                <tr>
                    <td><?=$order["ord_id"]?></td>
                    <td><?=$order["username"]?></td>
                    <td><?=$order["ord_date"]?></td>
                    <td><?=$order["status"]?></td>
                </tr>
                <?php }?>
            </table>
<?php
        // }
    }
    elseif($_SESSION["role"]=="admin"){
        echo "<h1>จำนวน order ผู้ใช้แต่ละคน</h1>";
        $stmt = $pdo->query("SELECT member.username, COUNT(orders.ord_id) AS order_count
        FROM member JOIN orders 
        ON member.username = orders.username 
        GROUP BY member.username");
        $Allorder = $stmt->fetchAll();
    
?>     
        <table border="1" cellpadding="5">
            <tr>
                <th>username</th>
                <th>จำนวน order</th>
            </tr>
        
        <?php foreach($Allorder as $order){?>
            <tr>
                <td><?=$order["username"]?></td>
                <!-- ปรับแก้ตรงนี้: ทำเป็นลิงก์คลิกส่ง username ไปยังหน้าแสดง Order รายบุคคล -->
                <td align="center">
                    <a href="admin-orders.php?username=<?=$order["username"]?>">
                        <?=$order["order_count"]?>
                    </a>
                </td>
            </tr>
        <?php }?>
        </table>
<?php }?>
       

</body>
</html>
