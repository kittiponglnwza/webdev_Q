<?php

$takeEmailuser = array("bill@email.com","ted@email.com");
sleep(1);

if(!in_array($_GET["email"],$takeEmailuser)){
	echo "okay";
}
else{
	echo "denied";
}

?>
