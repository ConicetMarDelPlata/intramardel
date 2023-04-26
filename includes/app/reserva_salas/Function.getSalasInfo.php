<?php
	include_once("../config.php");
	
	$time = $_GET['iTime'];
	$date   = $_GET['sDate'];
	$sala   = $_GET['sSala'];

	if ($conference->isSalaAvailable($date, $time,$sala)) {
		echo $conference->getLimitTimeToBlock($date, $time, $sala); 
	} else {
		echo "false";
	}
	
?>