<?php
	include 'seguridad_bd.php';
	include_once ('includes/class.Tpl.php');
	
	$bd = new Bd;
	$bd->AbrirBd();
	if(!$_POST['sAction']){
	
		$tpl = new tpl("templates/registro/registro.html");
		$tpl->setVar("img1","noImg_16x16.png");
		$tpl->setVar("v",$_GET['v']);
		$vUEs=$bd->getUEs();

		if($_GET['v']){
			$oUserData = $bd->getUserByUserName(base64_decode($_GET['v']));
			$tpl->setVar("i",$oUserData['id_usuario']);
			if($oUserData){
				$tpl->setVar("sLastName",$oUserData['apellido']);
				$tpl->setVar("sName",$oUserData['nombre']);
				$tpl->setVar("sEmail",$oUserData['email']);
			}else{
				$tpl->setVar("sLastName","");
				$tpl->setVar("sName","");
				$tpl->setVar("sEmail","");
			}
		}else{
			$tpl->setVar("sLastName","");
			$tpl->setVar("sName","");
			$tpl->setVar("sEmail","");
		}

		foreach($vUEs as $UE){
			if($UE['id_unidad_ejecutora']!=13){
				if($oUserData){
					if((int)$oUserData['unidad_ejecutora'] == (int)$UE['id_unidad_ejecutora']){
						$tpl->setVar("sUE",$UE['nombre']);
					}
				}
			}
		}
	}else{
		if(!$_POST['v']){
			$oRet = $bd->registrarUsuario($_POST);
		}else{
			$oRet = $bd->actualizarRegistroUsuario($_POST);
		}
		if($oRet['ok']){
			$tpl = new tpl("templates/registro/ok.html");
		}else{
			$tpl = new tpl("templates/registro/error.html");
			$tpl->setVar("Error",$oRet['msj']);
			
			if($_POST['sEmail']){
				$tpl->setVar("Recovery",$_POST['sEmail']);
			}else{
				$tpl->setVar("Recovery"," ");
			}
		}
	}
	
	if($tpl){
		$tpl->printTpl();
	}
?>