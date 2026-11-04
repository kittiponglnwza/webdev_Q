<?php include "connect.php"?>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <form action="insertws7.php" method="post">
            username: <input type="text" name="username" ><br>
            password: <input type="password" name="password" ><br>
            ชื่อ-นามสกุล: <input type="text" name="name" ><br>
            ที่อยู่: <input type="text" name="address" ><br>
            เบอร์โทรศัพท์: <input type="tel" name="mobile" ><br>
            email: <input type="text" name="email" ><br>
            <button type="submit">เพิ่ม</button>
        </form>
    </body>
</html>