<?php include "connect.php";?>
<html>
    <head><meta charset="utf-8">
        <script>
            function confirmDelete(username){
                let ans = confirm("ต้องการลบสมาชิก"+username);
                if(ans==true){
                    document.location = "deletews6.php?username="+username;
                }
            }
        </script>
    </head>
    <body>
        <?php
            $stmt = $pdo->prepare("SELECT * FROM member");
            $stmt->execute();
            while($row = $stmt->fetch()): 
        ?>
            
            <a href="detailws5.php?username=<?=$row["username"]?>">
                <img src="img_member/<?=$row["username"]?>.jpg" width="100">
            </a><br>
            ชื่อสมาชิก: <?=$row["username"]?><br>
            ที่อยู่: <?=$row["address"]?><br>
            อีเมล์: <?=$row["email"]?><br>
            <a href="editerformws9.php?username=<?=$row["username"]?>">แก้ไข</a><br><hr>
            
        <?php endwhile;?>
       
    </body>
</html>