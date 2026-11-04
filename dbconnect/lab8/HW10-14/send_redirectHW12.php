<?php include "../connect.php"?>
<?php
    $stmt = $pdo->prepare("INSERT INTO member VALUES(?,?,?,?,?,?)");
    $stmt->bindParam(1,$_POST["username"]);
    $stmt->bindParam(2,$_POST["password"]);
    $stmt->bindParam(3,$_POST["name"]);
    $stmt->bindParam(4,$_POST["address"]);
    $stmt->bindParam(5,$_POST["mobile"]);
    $stmt->bindParam(6,$_POST["email"]);
    $stmt->execute();

    $username = $_POST["username"];
    $password = $_POST["password"];
    $name = $_POST["name"];
    $address = $_POST["address"];
    $mobile = $_POST["mobile"];
    $email = $_POST["email"];

    move_uploaded_file(
        $_FILES["member_photo"]["tmp_name"],"../member_photo/".$username.".jpg"
    );

    header("location: detailHW12.php?username=" .$username);
?>