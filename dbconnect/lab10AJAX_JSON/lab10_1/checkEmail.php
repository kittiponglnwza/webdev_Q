<?php
include "connect.php";
// $takeEmailuser = array("bill@email.com","ted@email.com");
$stmt = $pdo->prepare("SELECT email FROM member WHERE email = ? ");
$stmt->bindParam(1,$_GET["email"]);
$stmt->execute();
sleep(1);

if($stmt->fetchColumn()){
	echo "denied";
}
else{
	echo "okay";
}

?>
