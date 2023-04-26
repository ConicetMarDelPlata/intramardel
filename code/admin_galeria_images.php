<?php
	include_once("includes/class.Tpl.php");
	
	$oTpl = new tpl("templates/galeria/admin_images.html");
	
	$iID 	= $_GET['id'];
	$sDir 	= "../fotos_album/".str_pad($iID, 5, "0", STR_PAD_LEFT)."/";
	$vFiles = scandir($sDir, 1);

	array_pop($vFiles);
	array_pop($vFiles);
	
	$oTpl->setVar("iID",$iID);
	$sBlockData = $oTpl->beginBlock("IMG");
	$i=0;
	foreach($vFiles as $Item){
		$vVars = array(
		'sTitle'=>$Item,
		'iID-DIV'=>"divImg_$i",
		'i'=>$i,
		'sPath'=>$sDir.$Item
		);
		$oTpl->addToBlock($sBlockData,$vVars);
		$i++;
	}
	$oTpl->endBlock();
	$oTpl->clearFields();
	$oTpl->printTpl();

?>