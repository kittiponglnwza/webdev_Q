<?php include "connect.php"; ?>
<?php

$username = $_GET["username"];

$stmt = $pdo->prepare("DELETE FROM member WHERE username = ?");
$stmt->bindParam(1, $username);


if ($stmt->execute()) {

    header("location: ws4.php");
}
?>