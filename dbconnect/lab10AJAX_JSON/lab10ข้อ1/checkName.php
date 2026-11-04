<?php
include "connect.php";
// $takenUsernames = array ("bill", "ted");
$stmt = $pdo->prepare("SELECT username FROM member WHERE  username = ?");
$stmt->bindParam(1,$_GET["username"]);
$stmt->execute();
// $stmt->fetch();
sleep(1);

if ($stmt->fetchColumn()) {
	echo "denied";
} else {
	echo "okay";
}


?>
