<?php
	include_once("../config.php");
	
	$sala   = $_GET['sala'];
	
	echo json_encode($conference->getEquipmentBySala($sala)); 
?>