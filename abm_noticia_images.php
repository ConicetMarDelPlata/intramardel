<?php
	$sImg = $_POST['img'];
	
	if(@unlink($sImg)){
		$bError = false;
	}else{
		$bError = error_get_last();
	}
	echo json_encode($bError);
?>