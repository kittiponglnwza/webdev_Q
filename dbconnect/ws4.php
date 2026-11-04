<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "connect.php";
?>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>

    <form method="get" action="">
        <input type="text" name="keyword" value="<?=isset($_GET['keyword']) ? $_GET['keyword'] : ''?>" placeholder="พิมพ์ชื่อ, username หรืออีเมล">
        <input type="submit" value="ค้นหา">
    </form>

    <hr>

    <?php
    try {
        $value = isset($_GET["keyword"]) ? $_GET["keyword"] : "";
        $keyword = "%" . $value . "%";

        $stmt = $pdo->prepare("SELECT * FROM member WHERE username LIKE :kw OR name LIKE :kw OR email LIKE :kw");
        $stmt->execute([':kw' => $keyword]);

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) :
            ?>
                
                ชื่อสมาชิก: <a href="ws5.php?username=<?=$row["username"]?>"><?=$row["name"]?></a><br>
                ที่อยู่: <?=$row["address"]?><br>
                อีเมล์: <?=$row["email"]?><br>
                
                <a href="ws5.php?username=<?=$row["username"]?>">
                    <img src='img_member/<?=$row["username"]?>.jpg' width='100'>
                </a><br>

                
                <a href="ws6.php?username=<?=$row["username"]?>" onclick="return confirm('คุณต้องการลบสมาชิกคนนี้ใช่หรือไม่?')">ลบสมาชิก</a>
                <hr>

            <?php
            endwhile;
        } else {
            echo "<p>ไม่พบข้อมูลสมาชิกที่ตรงกับคำค้นหา '$value'</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</p>";
    }
    ?>

</body>
</html>