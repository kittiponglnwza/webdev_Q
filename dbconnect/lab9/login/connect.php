<?php
try {
	$pdo = new PDO("mysql:host=localhost;dbname=169DB_24;charset=utf8", "169Muser24", "yUjv4edE");
} catch (PDOException $e) {
	echo "เกิดข้อผิดพลาด : ".$e->getMessage();
}
?>