<?php
	include "includes/header.php";
	include "seguridad_bd.php";
	include_once ("includes/class.Tpl.php");
	
	$oTpl = new tpl("templates/rendiciones/anexo/lista.php");
	
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");	
		exit();
	}

	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$cnx = $bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_DON');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],7,''); //7-Anexo donacion
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}
	//***********************************BARRA LATERAL*****************************************
	$oTpl->setVar("sBack","Volver");
	$oTpl->setVar("linkBack","panel_control_modulos.php");
	
	$oPanelCtrl = $bd->getPanel("panel_control_capital");
	if($oPanelCtrl){
		$sBlockData = $oTpl->beginBlock("PC");
		foreach($oPanelCtrl as $Item){
			//if($bd->getPermisos($userData,$Item['acceso'])){
			if($bd->checkAccess($_SESSION["id_usuario"],$Item['id_permiso'],$Item['acceso'])){				$vVars = array(
				'linkPC'=>$Item['link'],
				'iconoPC'=>'iconos/'.$Item['icono'],
				'nombrePC'=>$Item['nombre']
				);
				$oTpl->addToBlock($sBlockData,$vVars);
			}
		}
		$oTpl->endBlock();
	}
	$oTpl->setVar("User",$nombre_usuario);
	//******************************************************************************************
	$iSearchID 		= (isset($_REQUEST['searchID']))?(int)$_REQUEST['searchID']:'';
	$iSearchUI 		= (isset($_REQUEST['searchUI']))?(int)$_REQUEST['searchUI']:'';
	$iSearchStatus 		= (isset($_REQUEST['searchStatus']))?(int)$_REQUEST['searchStatus']:1;
	$iPage			= (isset($_REQUEST['page']))?(int)$_REQUEST['page']:1;
	$iRecPerPage	= 15;
	$iFirstRec		= ($iPage-1)*$iRecPerPage;


	if($iPage == 1){
		$oTpl->setVar("Prev","previous-off");
		$oTpl->deleteBlock("AINI");
		$oTpl->deleteBlock("AFIN");
	}else{
		$oTpl->setVar("Prev","previous");
		$oTpl->setVar("iPrev",$iPage - 1 . '&searchStatus='.$iSearchStatus.'&searchUI='.$iSearchUI);
	}
	$oTpl->setVar("S$iSearchStatus","selected");

	$oUI = $bd->getUEs();
	
	$sBlock = $oTpl->beginBlock("UI");
	$iFlag = 0;
	foreach($oUI as $Item){
		if($iSearchUI == (int)$Item['id_unidad_ejecutora']){
			$sSel = "selected = 'selected'";
			$iFlag = 1;
		}else{
			$sSel = "";
		}
		$vVars = array(
		'iUI'=>$Item['id_unidad_ejecutora'],
		'sUI'=>$Item['nombre'],
		'sSelUI'=>$sSel
		);
		$oTpl->addToBlock($sBlock,$vVars);
		$vVars=null;
	}
	$oTpl->endBlock($sBlock);
	
	if($iFlag === 0){
		$oTpl->setVar("sSelAll","selected = 'selected'");
	}else{
		$oTpl->setVar("sSelAll","");
	}
	$adAlta = $bd->checkPerm($_SESSION["id_usuario"],7,'alta');
	$adBaja = $bd->checkPerm($_SESSION["id_usuario"],7,'baja');
	$adModificacion = $bd->checkPerm($_SESSION["id_usuario"],7,'modificacion');
	$adConsulta = $bd->checkPerm($_SESSION["id_usuario"],7,'consulta');
	$adEspecial = $bd->checkPerm($_SESSION["id_usuario"],7,'especial');
	$adItemAlta = $bd->checkPerm($_SESSION["id_usuario"],8,'alta');
	$adItemBaja = $bd->checkPerm($_SESSION["id_usuario"],8,'baja');
	$adItemModificacion = $bd->checkPerm($_SESSION["id_usuario"],8,'modificacion');
	
	//Solo si tengo permiso de consulta muestro la lista de Anexos	
	if ($adConsulta) {
		$vData = $bd->getAnexos($_SESSION["id_usuario"], $iSearchID, $iSearchUI, $iSearchStatus, $iFirstRec, $iRecPerPage, $iNumRows, $iLastPage);
	}

	if($iPage >= $iLastPage){
		$oTpl->setVar("Next","next-off");
		$oTpl->deleteBlock("SINI");
		$oTpl->deleteBlock("SFIN");
	}else{
		$oTpl->setVar("Next","next");
		$oTpl->setVar("iNext",$iPage + 1 .'&searchStatus='.$iSearchStatus.'&searchUI='.$iSearchUI);
	}
		
	$sBlock = $oTpl->beginBlock("PAGES");
	for($i=1;$i<=$iLastPage;$i++){
		if($iPage == $i){
			$sActive ="active";
			$sPage = $i;
		}else{
			$sActive ="";
			$sPage = '<a href="?page='.$i.'&searchStatus='.$iSearchStatus.'&searchUI='.$iSearchUI.'">'.$i.'</a>';
		}
		$vVars = array(
			'sPageActive'=>$sActive, 
			'sPage'=>$sPage 
		);
		$oTpl->addToBlock($sBlock,$vVars);
		$vVars=null;
	}
	$oTpl->endBlock($sBlock);
	
	if($adAlta){
		$oTpl->setVar("anexo_alta","abm_anexo_donacion.php?op=1");
		$oTpl->setVar("path_agregar_anexo","agregar.png");
	}else{
		$oTpl->setVar("anexo_alta","#");
		$oTpl->setVar("path_agregar_anexo","iconos/noImg_16x16.png");
	}

	if($vData){
		
		$sBlock = $oTpl->beginBlock("DESC");
		foreach($vData as $Item){
			
			//Estado
			if((int)$Item['estado'] == 1){
				$sEstado = "Abierto";
				if($adEspecial){
					if($bd->haveAnexoItemsNotSended($Item['id'])){
						$sOpenClose	 = "Tiene env&iacute;os pendientes";
						$sImgE	 = "cerrar_anexo_gris.png";
						$sLinkE	= "#";
						$W = 16;
					}else{
						$sImgE	= "cerrar_anexo.png";
						$sLinkE	= "abm_anexo_donacion.php?op=9&id=".$Item['id']; 
						$sOpenClose	 = "Cerrar";
						$W = 16;
					}
				}else{
					$sImgE	 = "cerrar_anexo_gris.png";
					$sLinkE	= "#"; 
					$sOpenClose	 = "Sin permisos";
					$W = 16;
				}

				// Icono y link Borrar
				$sLinkB = $bd->getLink(6,$adBaja,$Item['id']);
				$sIconB = $bd->getIcon(2,$adBaja);

				// Icono y link Modificar
				if($adModificacion || $adItemModificacion || $adItemAlta || $adItemBaja){
					$sLinkM = $bd->getLink(7,1,$Item['id']);
					$sIconM = $bd->getIcon(3,1);
				}else{
					$sLinkM = $bd->getLink(7,0,$Item['id']);
					$sIconM = $bd->getIcon(3,0);
				}
				
				// Icono y link Ver (generar pdf)
				//$sLinkV = $bd->getLink(8,$userData['ad_lista'],$Item['id']);
				//Nota Vani: como este permiso se guardaba siempre en 1, lo elimine
				$sLinkV = $bd->getLink(8,1,$Item['id']);
			}else{
				$sEstado = "Cerrado";
				// Icono y link Borrar
				$sLinkB = $bd->getLink(6,0,$Item['id']);
				$sIconB = $bd->getIcon(2,0);

				$sLinkM = $bd->getLink(7,0,$Item['id']);
				$sIconM = $bd->getIcon(3,0);
				
				// Icono y link Ver
				//Nota Vani: como este permiso se guardaba siempre en 1, lo elimine
				//$sLinkV = $bd->getLink(8,$userData['ad_lista'],$Item['id']);
				$sLinkV = $bd->getLink(8,1,$Item['id']);
				
				//if($userData['ad_open']){
				//TODO Vanina el permiso de open estaba cableado a 1 para Ines y Andrea, cambio la comprobacion por el permiso especial
				if ($adEspecial){
					$sImgE	 = "abrir_anexo.png";
					$sLinkE	= "abm_anexo_donacion.php?op=10&id=".$Item['id']; 
				}else{
					$sImgE	 = "abrir_anexo_gris.png";
					$sLinkE	= "#"; 
				}
				$sOpenClose	 = "Reabrir";
				$W = 23;
				
			}
			
			
			$vTitular = $bd->getTitular($Item['titular']);
			$vVars = array(
				'ID'=>str_pad($Item['id'], 5, "0", STR_PAD_LEFT), 
				'UE'=>$Item['ue_inv_nombre'],
				'Titular'=>$vTitular['apellido'].", ".$vTitular['nombre'], 
				'Fecha'=>convertir_fecha($Item['fecha']), 
				'Estado'=>$sEstado, 
				'ImgE'=>$sImgE, 
				'sOpenClose'=>$sOpenClose, 
				'LinkB'=>$sLinkB, 
				'ImgB'=>$sIconB, 
				'LinkM'=>$sLinkM, 
				'ImgM'=>$sIconM, 
				'LinkV'=>$sLinkV, 
				'LinkE'=>$sLinkE, 
				'W'=>$W 
			);
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);
	}else{
		$oTpl->deleteBlock("DESC");
		$oTpl->setVar("anexo_alta","ERROR");
		//$oTpl->setVar("path_agregar_anexo","iconos_grises/agregarg.png");
	}
	
	$oTpl->clearFields();
	$oTpl->printTpl();
 
	if (isset($_SESSION["message"])) {
		echo '<script language=javascript>';
		$message=$_SESSION["message"];
		if ($message!="") { 
			echo 'alert("'.$message.'")'; 
			$_SESSION["message"]="";
			}
		echo '</script>';
	}
?>