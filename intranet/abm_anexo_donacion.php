<?php
	include "includes/header.php";
	include "seguridad_bd.php";
	include_once ("includes/class.Tpl.php");

	$autenticado="";
	$nombre_usuario="";
	$contrasenia="";
	$bd="";
	$cnx="";
	//$userData="";
	$iOP = (int)$_REQUEST['op'];
	if (isset($_GET['id']))
		$iID = (int)$_GET['id'];
	else
		$iID = "";

	if (isset($_GET['iid']))
		$iItemID = (int)$_GET['iid'];
	else
		$iItemID = "";
	
	if (isset($_GET['io']))
		$iItemOrden = (int)$_GET['io'];
	else
		$iItemOrden = "";
	
	if (isset($_POST['sAction']))
		$sAction = $_POST['sAction'];
	else
		$sAction = "";
	
	if (isset($_POST['toSend']))
		$vToSend = explode(",",$_POST['toSend']);
	else
		$vToSend = "";

	chequearDatos();
	switch ($iOP){
		case 1: //Nuevo anexo donaci&oacute;n [Mostrar Form]
			nuevo();
		break;
		case 2: //Nuevo anexo donaci&oacute;n [Agregar datos]
			$noExiste = $bd->checkAnexoId($_POST['iID']);
			if ($noExiste) {
				$bd->addAnexo($_POST);}
			else {
				$_SESSION["message"]='No se grabaron los cambios. El identificador de anexo elegido ya existe, por favor elija otro y carguelo nuevamente.';
			}
			header("location: lista_anexo_donacion.php");
		break;
		case 3: //Eliminar anexo donacion y todos sus items asociados
			$sErr = $bd->delAnexo($iID);
			if(!$sErr){
				header("location: lista_anexo_donacion.php");
			}else{
				echo $sErr;
				exit;
			}
		break;
		case 4: //Modificar anexo donacion. SOLO MUESTRA LA CABECERA Y LOS ITEMS DEL AD
			modificar($iID);
		break;
		case 5: //Modificar anexo donacion. MODIFICA LOS DATOS DE LA CABECERA
			//Nota vanina: me parece que no se ejecuta nunca, siiiii pero no se cuando
			$bd->updateAnexoHeader($_POST);
			//$SU = $userData['ad_open'] && $userData['ad_close'];
			$SU = $bd->checkPerm($_SESSION["id_usuario"],7,'especial');
			if($vToSend){
				$i=0;
				$iOrd = 0;
				foreach($vToSend as $Item){
					if($i === 0){
						$iOrd++;
						$itemID = $Item;
						$vItemsID[]=$Item;
						$vItemsOrd[]=$iOrd;
					}
					if($i === 1){
						$itemChecked = ($Item === 'true')?1:0;
					}
					if($i === 2){
						$itemDisabled = ($Item === 'true')?1:0;
						if($SU){
							$bd->sendAnexoItem($itemID, $itemChecked);
						}else{
							if(!$itemDisabled && $itemChecked){
								$bd->sendAnexoItem($itemID, $itemChecked);
							}else{
								array_pop($vItemsID);
								array_pop($vItemsOrd);
							}
						}
					}
					
					$i++;
					if($i === 3) $i=0;
				}
				if(!$SU){
					//Generar pdf
					if(count($vItemsID)>0){
						$fName = time();
						$fp = fopen("ad_items/$fName.txt", 'w');
						fwrite($fp, implode(",",$vItemsID).",".$iID);
						fclose($fp);
						echo "<script>window.open('anexo_donacion_items_pdf.php?v=$fName&z=".implode(".",$vItemsOrd)."','_blank');</script>";
					}
					//header("location: anexo_donacion_items_pdf.php?v=$fName","_blank");
				}
				echo "<script>window.open('abm_anexo_donacion.php?op=4&id=$iID','_self');</script>";
				//header("location: abm_anexo_donacion.php?op=4&id=".$iID);
				
			}
		break;
		case 6: //AGREGA/MODIFICA REGISTRO ITEM ANEXO DONACION
			if($sAction == "modReg"){
				$iItemID = $_POST['regid'];
				if($bd->updateAnexoItem($_POST,$iItemID)){
					header("location: abm_anexo_donacion.php?op=4&id=".$_POST['id']);
				}else{
					echo "ERROR";exit;
				}
			}else{
				if($bd->addAnexoItem($_POST)){
					header("location: abm_anexo_donacion.php?op=4&id=".$_POST['id']);
				}else{
					echo "ERROR";
					exit;
				}
			}
		break;
		case 7: //MODIFICAR REGISTRO MUESTRA DATOS
			modificar($iID,$iOP);
		break;
		case 8: //ELIMINAR UN REGISTRO
			if($bd->delAnexoItem($iItemID)){
				header("location: abm_anexo_donacion.php?op=4&id=".$iID);
			}else{
				echo "ERROR";exit;
			}
		break;
		case 9: //CERRAR ANEXO
			if($bd->	changeAnexoState($iID,0,date("Y-m-d"))){
				header("location: lista_anexo_donacion.php");
			}else{
				echo "ERROR";exit;
			}
		break;
		case 10: //REABRIR ANEXO
			if($bd->changeAnexoState($iID,1)){
				header("location: lista_anexo_donacion.php");
			}else{
				echo "ERROR";exit;
			}
		break;
		case 99: //PDF
		break;
		default:
			echo "ERROR"; exit;
		break;
	}
	

	
	
	
	
//**********************************************************************************************
//**	                                 FUNCIONES                                            **
//**********************************************************************************************
function nuevo(){
	
	$oTpl = new tpl("templates/rendiciones/anexo/alta.php");
	
	//Nota Vani: es el id de titular que cuando recarga la pagina cambia select de unidades
	if (isset($_GET['titid']))
		$iTitular = $_GET['titid'];
	else
		$iTitular = "0"; //Es el titular ---

	global $bd, $cnx;
	
	//*****************************************************************************************
	$oTpl->setVar("sBack","Volver");
	$oTpl->setVar("linkBack","panel_control_modulos.php");
	if (isset($_GET['adid']))
		$oTpl->setVar("ad-id",$_GET['adid']);
	else
		$oTpl->setVar("ad-id","");

	if (isset($_GET['adtr']))
		$oTpl->setVar("ad-tr",$_GET['adtr']);
	else
		$oTpl->setVar("ad-tr","");
	
	$oPanelCtrl = $bd->getPanel("panel_control_capital");
	if($oPanelCtrl){
		$sBlockData = $oTpl->beginBlock("PC");
		foreach($oPanelCtrl as $Item){
			//if($bd->getPermisos($userData,$Item['acceso'])){
			if($bd->checkAccess($_SESSION["id_usuario"],0,$Item['acceso'])){ 
				$vVars = array(
				'linkPC'=>$Item['link'],
				'iconoPC'=>'iconos/'.$Item['icono'],
				'nombrePC'=>$Item['nombre']
				);
				$oTpl->addToBlock($sBlockData,$vVars);
			}
		}
		$oTpl->endBlock();
	}

	$oTpl->setVar("User",$_SESSION["usuario"]);
	
	$vUE = $bd->getUEs();
	$vTitulares = $bd->listar_titulares();
	
	//if($userData['ad_alta']){
	if($bd->checkPerm($_SESSION["id_usuario"],7,'alta')){
		$oTpl->setVar("anexo_alta","abm_anexo_donacion.php?op=1");
		$oTpl->setVar("path_agregar_anexo","agregar.png");
	}else{
		$oTpl->setVar("anexo_alta","#");
		$oTpl->setVar("path_agregar_anexo","iconos/noImg_16x16.png");
	}

	
	if($vUE){
		$sBlock = $oTpl->beginBlock("UE");
		foreach($vUE as $Item){
			//Este imprimir indica si se muestra o no en la lista de unidades dependiendo
			//del titular que se haya elegido
			if($iTitular == 25){ //CCT CONICET
				if((int)$Item['id_unidad_ejecutora'] == 12 || (int)$Item['id_unidad_ejecutora'] == 13 || (int)$Item['id_unidad_ejecutora'] == 15){
					$imprimir =true;
				}else{
					$imprimir = false;
				}
			}else{
				if(($iTitular == 26) || ($iTitular == 27) || ($iTitular == 28) || ($iTitular == 29) || ($iTitular == 31) || ($iTitular == 32)){
					$imprimir = false;
					switch ($iTitular){
						case 26: //INTEMA
							if((int)$Item['id_unidad_ejecutora'] == 3){
								$imprimir = true;
							}
						break;
						case 27: //IIB
							if((int)$Item['id_unidad_ejecutora'] == 4){
								$imprimir = true;
							}
						break;
						case 28: //IMBIOTEC
							if((int)$Item['id_unidad_ejecutora'] == 6){
								$imprimir = true;
							}
						break;
						case 29: //IFIMAR
							if((int)$Item['id_unidad_ejecutora'] == 5){
								$imprimir = true;
							}
						break;
						case 31: //IIMyC
							if((int)$Item['id_unidad_ejecutora'] == 7){
								$imprimir = true;
							}
						break;
						case 32: //UNIHDO
							if((int)$Item['id_unidad_ejecutora'] == 10){
								$imprimir = true;
							}
						break;
					}
				}else{
					$imprimir = true;
				}
				
			}
			
			if($imprimir){
				$vVars = array(
					'ID-UE'=>$Item['id_unidad_ejecutora'], 
					'Desc-UE'=>$Item['nombre']
				);
				$oTpl->addToBlock($sBlock,$vVars);
				$vVars=null;
			}
		}
		$oTpl->endBlock($sBlock);

		$sBlock = $oTpl->beginBlock("DESC");
		foreach($vUE as $Item){
			$vVars = array(
				'ID'=>$Item['id_unidad_ejecutora'], 
				'CUIT'=>$Item['cuit'], 
				'Dom'=>$Item['domicilio'],
				'Ref'=>$Item['referente'],
				'Dir'=>$Item['director']
			);
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);
	}else{
		$oTpl->deleteBlock("UE");
		$oTpl->deleteBlock("DESC");
	}
	
	if($vTitulares){
		$sBlock = $oTpl->beginBlock("Titulares");
		foreach($vTitulares as $Item){
			if($iTitular == (int)$Item['id_titular']){
				$sSelected = "selected='selected'";
			}else{
				$sSelected = "";
			}
			
			$vVars = array(
				'ID-Tit'=>$Item['id_titular'], 
				'Sel-Tit'=>$sSelected, 
				'Desc-Tit'=>$Item['apellido'].", ".$Item['nombre']
			);
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);
	}else{
		$oTpl->deleteBlock("Titulares");
	}
	
	$oTpl->clearFields();
	$oTpl->printTpl();
}

//************** MODIFICACION *********************

function modificar($iID, $iOP=null){
	
	$oTpl = new tpl("templates/rendiciones/anexo/mod.php");
	
	global $bd, $cnx;
	
	//*****************************************************************************************
	$oTpl->setVar("User",$_SESSION["usuario"]);
	$oTpl->openFile("BarraLateral","templates/menuLateral-gral.php",$bd);
	
	$vHead	= $bd->getAnexoHeader($iID);
	$vReg	= $bd->getAnexoItems($iID);
	$vUE 	= $bd->getUEs($vHead['ue']);		
	$vTitulares = $bd->listar_titulares($vHead['titular']);
	$vCoins	= $bd->getCoins();
	$iItemID= (int)($_GET['iid']??'0');
	
	if(!$iOP){
		$oTpl->setVar("opNewReg",7);
	}else{
		$oTpl->setVar("opNewReg",$iOP);
	}
	
	$oTpl->setVar("regID",$iItemID);
	
	//if($userData['ad_modificacion']){
	if($bd->checkPerm($_SESSION["id_usuario"],7,'modificacion')){
		$oTpl->setVar("HeaderDisabled", "");
	}else{
		$oTpl->setVar("HeaderDisabled", "disabled");
	}

	$oTpl->setVar("iID",$vHead['id']);
	$oTpl->setVar("sSub",$vHead['subsidio']);
	$oTpl->setVar("sResOto",$vHead['res_oto']);
	$oTpl->setVar("lab",$vHead['lab']);
	$oTpl->setVar("sUEActual",$vHead['ue_inv_nombre']); 


	// Icono y link Ver
	//$sLinkV = $bd->getLink(8,$userData['ad_modificacion'],$iID);

	if(!$bd->checkPerm($_SESSION["id_usuario"],8,'alta') && !$iItemID){
		$oTpl->deleteBlock("ADDITEM");
	}
	
	
	if((int)$vHead['ue'] == 13 && (int)$vHead['titular'] == 25){ // CCT Mar del plata en los dos lados
	}else{
		$oTpl->deleteBlock("TDUI");
	}

	if($bd->checkPerm($_SESSION["id_usuario"],8,'baja')){
		$oTpl->setVar("path_eliminar_item","eliminar.png");
		$oTpl->setVar("item_baja","ad_additem.php?id=$iID");
	}else{
		$oTpl->setVar("path_agregar_item","iconos/noImg_16x16.png");
	}

	if($vCoins){
		/*$sBlock = $oTpl->beginBlock("MONEDA");
		foreach($vCoins as $Item){
			$vVars = array(
				'idMoneda'=>$Item['id_moneda'], 
				'signoMoneda'=>$Item['signo']
			);
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);*/

		$sBlock = $oTpl->beginBlock("DESC");
		foreach($vUE as $Item){
			$vVars = array(
				'ID'=>$Item['id_unidad_ejecutora'], 
				'CUIT'=>$Item['cuit'], 
				'Dom'=>$Item['domicilio'],
				'Ref'=>$Item['referente'],
				'Dir'=>$Item['director']
			);
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);
	}else{
		$oTpl->deleteBlock("UE");
		$oTpl->deleteBlock("DESC");
	}
	
	if($vUE){
		$sBlock = $oTpl->beginBlock("UE");
		foreach($vUE as $Item){
			if((int)$Item['id_unidad_ejecutora'] == (int)$vHead['ue']){
				$sSelected = "selected='selected'";
				$sUE = $Item['nombre'];
			}else{
				$sSelected = "";
			}
			$vVars = array(
				'ID-UE'=>$Item['id_unidad_ejecutora'], 
				'Desc-UE'=>$Item['nombre'],
				'UESelected'=>$sSelected
			);
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);

		$sBlock = $oTpl->beginBlock("UI");
		//Nota Vani: Este bloque se arma siempre pero solo se muestra cuando el titular y la UE son CCTMdP
		//mas abajo se hace un delete block. 
		$tempItemAModif = $bd->getAnexoItem($iItemID);
		foreach($vUE as $Item){
			//if($iOP == 7){//Nota Vani: no se porque esta este if, el modificar siempre viene con iOP = 7
				//nop! cuando vuelve de grabar vuelve con 4?!?!?
				
				if((int)$tempItemAModif['ui'] == (int)$Item['id_unidad_ejecutora']){
					$sSel = "selected";
				}else{
					$sSel = null;
				}
			//}else{
				//$sSel = null;
			//}
			if((int)$Item['id_unidad_ejecutora'] != 11 && (int)$Item['id_unidad_ejecutora'] != 13){
				//Nota Vani: El listado no incluye la unidad ejecutora CCTMardelPlata y Zona
				$vVars = array(
					'ID-UI'=>$Item['id_unidad_ejecutora'], 
					'Desc-UI'=>$Item['nombre'],
					'sSelectedUI'=>$sSel
				);
				$oTpl->addToBlock($sBlock,$vVars);
				$vVars=null;
			}
		}
		$oTpl->endBlock($sBlock);

		$sBlock = $oTpl->beginBlock("DESC");
		foreach($vUE as $Item){
			$vVars = array(
				'ID'=>$Item['id_unidad_ejecutora'], 
				'CUIT'=>$Item['cuit'], 
				'Dom'=>$Item['domicilio'],
				'Ref'=>$Item['referente'],
				'Dir'=>$Item['director']
			);
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);
	}else{
		$oTpl->deleteBlock("UE");
		$oTpl->deleteBlock("DESC");
	}
	
	if($vTitulares){
		$sBlock = $oTpl->beginBlock("Titulares");
		foreach($vTitulares as $Item){
			if((int)$Item['id_titular'] == (int)$vHead['titular']){
				$sSelected = "selected='selected'";
				$sTitular = $Item['apellido'].", ".$Item['nombre'];
			}else{
				$sSelected = "";
			}
			
			$vVars = array(
				'ID-Tit'=>$Item['id_titular'], 
				'Desc-Tit'=>$Item['apellido'].", ".$Item['nombre'],
				'TitSelected'=>$sSelected
			);
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);
	}else{
		$oTpl->deleteBlock("Titulares");
	}
	
	if($vReg){
		//Este es el bloque que muestra el listado de items de Anexo Donacion
		$sBlock = $oTpl->beginBlock("REGISTRO");
		$i=0;
		$fTotal = 0;
		foreach($vReg as $Item){
			$adDelItem = $bd->checkPerm($_SESSION["id_usuario"],8,'baja');
			$adModItem = $bd->checkPerm($_SESSION["id_usuario"],8,'modificacion');
			if($Item['enviado']){
				$sSelected = "checked";
				if(!$bd->checkPerm($_SESSION["id_usuario"],7,'especial')){
					$sDisabled = "disabled";
					$sLinkB = $bd->getLink(9,0,$iID,$Item['id']);
					$sIconB = $bd->getIcon(2,0);

					$sLinkM = $bd->getLink(7,0,$iID,$Item['id'],++$i);
					$sIconM = $bd->getIcon(3,0);
				}else{
					$sDisabled = "";
					$sLinkB = $bd->getLink(9,$adDelItem,$iID,$Item['id']);					
					$sIconB = $bd->getIcon(2,$adDelItem);

					$sLinkM = $bd->getLink(7,$adModItem,$iID,$Item['id'],++$i);
					$sIconM = $bd->getIcon(3,$adModItem);
				}
				
			}else{
				$sDisabled = "";
				$sSelected = "";
				//$sLinkB = $bd->getLink(9,$userData['ad_delitem'],$iID,$Item['id']);
				//$sIconB = $bd->getIcon(2,$userData['ad_delitem']);
				//$sLinkM = $bd->getLink(7,$userData['ad_moditem'],$iID,$Item['id'],++$i);
				//$sIconM = $bd->getIcon(3,$userData['ad_moditem']);
				$sLinkB = $bd->getLink(9,$adDelItem,$iID,$Item['id']);
				$sIconB = $bd->getIcon(2,$adDelItem);
				$sLinkM = $bd->getLink(7,$adModItem,$iID,$Item['id'],++$i);
				$sIconM = $bd->getIcon(3,$adModItem);
			}

			//El nombre que va es el que esta almacenado junto al item y no el actual de la UI
			//Si no tiene ui o es cero, se muestra el nombre de la ui principal
			if($Item['ui'] && (int)$Item['ui'] != 0){
				//	$vUE = $bd->getUE((int)$Item['ui']);
				//	$sNombre = $vUE['nombre'];
				$sNombre = $Item['ui_nombre'];
			}else{
				$sNombre = $sUE;
			}
			//Aca se setean las variables cuando modifica un item
			if($iOP == 7){
				if($iItemID == $Item['id']){
					$oTpl->setVar("iOrden",$_GET['io']);
					$oTpl->setVar("iCant",$Item['cant']);
					$oTpl->setVar("sDesc",str_replace('"','&quot;',$Item['descripcion']));
					$oTpl->setVar("sMarca",str_replace('"','&quot;',$Item['marca']));
					$oTpl->setVar("sModelo",str_replace('"','&quot;',$Item['modelo']));
					$oTpl->setVar("sSerie",str_replace('"','&quot;',$Item['serie']));
					$oTpl->setVar("sFCompra",convertir_fecha($Item['fecha_compra']));
					$oTpl->setVar("sImporte",$Item['importe']);
					$oTpl->setVar("iUI",$Item['ui']);
					$oTpl->setVar("sUINombre",$sNombre);
				}
				else{}
			} else {
				if ($iItemID == 0) {
					//No esta modificando, esta preparado para alta
					//esto no funciona pero no afecta //TODO
					$oTpl->setVar("sUINombre",$Item['nombre']??'');
				}
			}
	
			$vVars = array(
				'itemID'=>$Item['id'], 
				'Orden'=>$i, 
				'Cant'=>$Item['cant'],
				'Desc'=>(strlen($Item['descripcion'])>45)?substr($Item['descripcion'],0,44)."...":$Item['descripcion'],
				/*
				'Marca'=>$Item['marca'],
				'Modelo'=>$Item['modelo'],
				*/
				'UINombre'=>$sNombre,
				'Serie'=>$Item['serie'],
				'FCompra'=>convertir_fecha($Item['fecha_compra']),
				'Importe'=>$bd->getCoinSymbol((int)$Item['moneda'])." ".number_format ($Item['importe'] , 2, ',', '.'),
				'ImgB'=>$sIconB,
				'LinkB'=>$sLinkB,
				'ImgM'=>$sIconM,
				'LinkM'=>$sLinkM,
				'regID'=>$Item['id'],
				'RegSelected'=>$sSelected,
				'TrDisabled'=>$sDisabled
			);
			$fTotal += (float)$Item['importe'];
			
			$oTpl->addToBlock($sBlock,$vVars);
			$vVars=null;
		}
		$oTpl->endBlock($sBlock);
		$oTpl->setVar("Total",number_format ($fTotal , 2, ',', '.'));

		if(($iOP == 7 && !$iItemID) || ($iOP != 7)){
			$oTpl->setVar("iOrden",++$i);
			$oTpl->deleteBlock("CANCELAR");
		}
		
	}else{
		$oTpl->deleteBlock("DESC");
		$oTpl->setVar("iOrden",1);
	}
	
	$oTpl->clearFields();
	$oTpl->printTpl();

}

function chequearDatos(){
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");	
		exit();
	}
	
	global $autenticado, $nombre_usuario, $contrasenia, $bd, $cnx;
	
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia = $_SESSION["contrasenia"];
	
	$sesion = NULL;	
	
	$bd = new Bd;
	$cnx = $bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_DON');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],7,''); //7=Anexo donacion
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}

}
?>
