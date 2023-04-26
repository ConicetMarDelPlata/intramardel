<?php
	include_once ('includes/class.Tpl.php');

	$tpl = new tpl("templates/panel_control/barra_lat.html");
	$bd->AbrirBd();
	$tpl->setVar("user",$nombre_usuario);
	
	$oPanelCtrl = $bd->getPanel("panel_control_capital");
	
	if($oPanelCtrl){
		$sBlockData = $tpl->beginBlock("PC");
		foreach($oPanelCtrl as $Item){
			//if($bd->getPermisos($userData,$Item['acceso'])){
			if ($bd->checkAccess($_SESSION["id_usuario"],$Item['id_permiso'],$Item['acceso'])){
				$vVars = array(
				'linkPC'=>$Item['link'],
				'iconoPC'=>'iconos/'.$Item['icono'],
				'nombrePC'=>$Item['nombre']
				);
				$tpl->addToBlock($sBlockData,$vVars);
			}
		}
		$tpl->endBlock();
	}
	$tpl->printTpl();
?>
