<?php include "../connect.php"?>
<html>
    <body>
        <form action="detailHW10.php" method="post" enctype="multipart/form-data">
            ใส่รูปภาพ: <input type="file" name="product_photo" width="100"><br>
            ชื่อสินค้า: <input type="text" name="pname" id="pname" ><br>
            รายละเอียดสินค้า: <br><textarea name="pdetail" id="pdetail" ></textarea><br>
            ราคา: <input type="number" name="price" id="price" ><br>
            <button type="submit">เพิ่มสินค้า</button>
        </form>
        
    </body>
</html>