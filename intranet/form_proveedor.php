<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_GET['opcion'];
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_PRO');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],12,''); //12=proveedores
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>PANEL CONTROL</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<style type="text/css">
.tituloweb2 {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
	color: #06C;
	font-weight: bold;
	line-height: 10px;
}
.tituloweb2Copia {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
	color: #06C;
	font-weight: normal;
	line-height: 10px;
}

a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: underline;
}
a:active {
	text-decoration: none;
	text-align: right;
}
.cerrar {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 9px;
	color: #333;
}
.pie {	font-family: Tahoma, Geneva, sans-serif;
	font-size: 9px;
	color: #FFF;
	padding-top: 5px;
	padding-right: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
	text-align: center;
}
</style>
<script language="javascript" >
function enviar(inForm){
	var cuit 	  = inForm.cuit.value.trim();
	inForm.razon_social.value = inForm.razon_social.value.trim();
	var razon_social  = inForm.razon_social.value.trim();
	var condicion_iva = inForm.condicion_iva.value;
	var provincia 	  = inForm.provincia.value;
	var nroIIBB  	  = inForm.nroiibb.value.trim();
	var email	  = inForm.email.value.trim();
	var result;

	if (inForm.opcion.value != 2){ //SI NO ELIJE ELIMINAR		
		if (cuit == "" && condicion_iva != 4) {
			//El cuit no es requerido si es extranjero
			alert("Por favor, complete el CUIT.");
			inForm.cuit.focus();			
		}
		else if ((cuit != "") && !(validaCuit(cuit))){
			alert("El CUIT es demasiado corto o no coincide su digito verificador. Verifique los datos.");
			inForm.cuit.style="border-color:red;";
			inForm.cuit.focus();
		}
		else if ((cuit != "") && !(buscarCUITDuplicado(cuit, inForm.id_proveedor.value))){
			alert("El CUIT se encuentra previamente registrado como proveedor. Verifique los datos.");
			inForm.cuit.focus();
			inForm.cuit.style="border-color:red;";
		}
		//razon social es siempre requerida
		else if (razon_social == "") {
			alert("Por favor, complete la razon social.");
			inForm.razon_social.focus();
			}
		else if (nroIIBB == "" && (condicion_iva != 4 && condicion_iva != 5)) {
			//IIBB solo es requerido si no es extranjero o consumidor final
			alert("Por favor, complete el numero de ingresos brutos.");
			inForm.nroiibb.focus();									
		}
		else if (validaEmail(email)) 
		{
			alert("Por favor, complete el email del proveedor con un email valido.");	
			inForm.email.focus();
		}
		else {
			inForm.submit();
		}
		}
	//============== DESCOMENTAR ESTE BLOQUE PARA HACER CHEQUEO ===============================
		// if ( cuit == "" || razon_social == "" || condicion_iva == "" || provincia == ""){
			// alert("Campos obligatorios INCOMPLETOS.");
			// return (false);
		// }else
			// if( condicion_iva != 4 && condicion_iva != 5){
				// if(inForm.iibb.value == ''){
					// alert("Campos obligatorios INCOMPLETOS.");
					// return (false);
				// }
			// }
				// //enviar = window.confirm('Se enviarán todos los datos del formulario');
				// //(enviar)?inForm.submit():'return false';
				// inForm.submit();
	// }
	//============== ELIMINAR LA SIGUIENTE LINEA AL HACER CHEQUEO ===============================
	//			inForm.submit();
	else { //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
			inForm.submit();
	}
}
</script>
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>
<script src="js/jquery.js" type="text/javascript"></script>
<style type="text/css">
.TITULO {font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	color: #333;
	font-variant: normal;
	font-weight: bold;
	text-align: center;
	padding-left: 5px;
	padding-right: 30px;
	vertical-align: bottom;
	padding-bottom: 10px;
}
</style>
</head>
<body>
<p align="center"><img src="cabecera.jpg" width="900" height="101" border="0" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      Proveedores ::</span>
		<?php 
		$puedeAlta = $bd->checkPerm($_SESSION["id_usuario"],12,'alta');
		echo '<a href="'; 
		echo ($puedeAlta)?"form_proveedor.php?opcion=1":"#"; 
		echo '"><img src="';
		echo ($puedeAlta)?"agregar.png":"iconos_grises/agregarg.png";
		echo '" width="25" height="25" border="0"></a>';
		?>
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-gral.php");?>
	</td>
    <td width="722" valign="top"> <p>
        <?php
		switch ($opcion){
			case 1: // OPCION ALTA
				$id_proveedor = 0;
				$cuit = "";
				$razon_social = "";
				$nroIIBB = "";
				$IIBB = "";
				$CMPercent = "100.00";
				$condicion_iva = "";
				$domicilio = "";
				$provincia = "";
				$contacto = "";			
				$telefono = "";			
				$email = "";
				$contacto2 = "";			
				$email2 = "";
				$banco1 = "";
				$tipo_cuenta1 = "";				
				$titular_cuenta1 = "";				
				$cuit_cuenta1 = "";				
				$numero_cuenta1 = "";
				$cbu1 = "";				
				$cuit1 = "";				
				$banco2 = "";
				$tipo_cuenta2 = "";				
				$titular_cuenta2 = "";				
				$cuit_cuenta2 = "";				
				$numero_cuenta2 = "";
				$cbu2 = "";				
				$cuit2 = "";				
				$sDisplayProvincia ='';
				break;
			case 2: // OPCION BAJA
				//$codigo_articulo = $HTTP_GET_VARS['codigo_articulo'];
				//break;
			case 3: // OPCION MODIFICACION 
			case 4: //OPCION CONSULTA
				$id_proveedor = $_GET['id_proveedor'];
				$row = $bd->consultar_proveedor_por_id($id_proveedor);
				$cuit = $row['cuit'];				
				$razon_social = $row["razon_social"];	
				$nroIIBB = trim($row["nro_iibb"]);	
				$IIBB = trim($row["iibb"]);	
				$CMPercent = trim($row["cm_porciento"]);	
				$condicion_iva = $row["condicion_iva"];	
				$domicilio = $row["domicilio"];
				$provincia = $row["provincia"];
				$contacto = $row["contacto"];
				$telefono = $row["telefono"];
				$email = $row["email"];
				$contacto2 = $row["contacto2"];
				$email2 = $row["email2"];
				
				$banco1 = $row["banco1"];
				$titular_cuenta1 = $row["titular_cuenta1"];				
				$cuit1 = $row["cuit_cuenta1"];				
				$tipo_cuenta1 = $row["tipo_cuenta1"];				
				$numero_cuenta1 = $row["numero_cuenta1"];
				$cbu1 = $row["cbu1"];
				$sDisplayProvincia ='';
				
				$banco2 = $row["banco2"];
				$titular_cuenta2 = $row["titular_cuenta2"];				
				$cuit2 = $row["cuit_cuenta2"];				
				$tipo_cuenta2 = $row["tipo_cuenta2"];				
				$numero_cuenta2 = $row["numero_cuenta2"];
				$cbu2 = $row["cbu2"];				
				break;			
		} // FIN SWITCH
		if ($opcion == 4) $textoReadOnly = " disabled "; 
		else $textoReadOnly = "";
?>
      </p>
      <form action="abm_proveedor.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table width="82%" align="center" class="tabla_form">
          <tr> 
            <td colspan="2" class="modo1" align="left"><font color="#000099">Datos 
              del Proveedor</font></td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Condici&oacute;n IVA:*</strong></font></div></td>
            <?php 
				$bd->listar_condicion_iva($condicion_iva, $textoReadOnly);
			?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>N&uacute;mero de CUIT: *</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="cuit" type="text" id="cuit" onblur="PonerGuiones(this)" value="' . $cuit .'"' . 'size="60" maxlength="13" '.$textoReadOnly. '></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Raz&oacute;n Social:*</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="razon_social" type="text" id="razon_social" onblur="RellenarRazonSocial(this)" value="' . $razon_social .'"' . 'size="60" maxlength="75" '.$textoReadOnly. '></td>';
	  ?>
          </tr>
		  <?php
		  	//Si la condicion de IVA es extranjero o exento no se debe mostrar la condicion de ingresos brutos ni el nro de ingresos brutos
			if((int)$condicion_iva != 5 && (int)$condicion_iva != 4 && (int)$condicion_iva != 0 || $condicion_iva == ''){
				$sDisplayIIBB='';
			}else{
				$sDisplayIIBB='none';
				//Si es extranjero ademas,no debo mostrar la provincia
				if ($condicion_iva == 4) {
					$sDisplayProvincia = 'none';
					}
				else {
					$sDisplayProvincia = '';
				}				
			}
          echo "<tr id='trIIBB' style='display:$sDisplayIIBB'>"; 
		  ?>
            <td class="modo1"><div align="right"><font color="#000099"><strong>IIBB: *</strong></font></div></td>
            <?php 
				$bd->listar_iibb($IIBB, $textoReadOnly);
			?>
          </tr>
		  <?php
			  	//Si IIBB es Convenio Multilateral
				if((int)$IIBB == 2){
					//Mostrar campo de 10 digitos + el campo de % de C.M
					$sDisplayIIBB='';
				}else{
					// mostrar campo BLOQUEADO con el CUIT del proveedor
					
					$sDisplayIIBB='';
				}
				
          echo "<tr id='trNroIIBB' style='display:$sDisplayIIBB'>"; 
		  ?>
            <td class="modo1"><div align="right"><font color="#000099"><strong>Nro IIBB: *</strong></font></div></td>
			<td class="modo2"><div align="left"><input name="nroiibb" type="text" id="nroiibb" value="<?php echo $nroIIBB; ?> " size="60" maxlength="11" disabled="disabled"></td>
          </tr>

		  <?php echo "<tr id='trCMPercent' style='display:$sDisplayIIBB'>"; 
		  ?>
            <td class="modo1"><div align="right"><font color="#000099"><strong>C. Multilateral: *</strong></font></div></td>
			<td class="modo2"><div align="left"><input name="cmpercent" type="text" id="cmpercent" value="<?php echo $CMPercent; ?> " size="60" maxlength="6" style="width:105px;"  <?php echo $textoReadOnly;?>> %</td>
          </tr>

          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Domicilio:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="domicilio" type="text" id="domicilio" value="' . $domicilio .'"' . 'size="60" maxlength="55" '.$textoReadOnly. '></td>';
	?>
          </tr>
          <?php echo "<tr id='trProvincia' style='display:$sDisplayProvincia'>"; ?>
            <td class="modo1"><div align="right"><font color="#000099"><strong>Provincia: *</strong></font></div></td>
            <?php 
			if ( ($opcion == 2 ) || ($opcion == 3) || ($opcion == 4) ){ // BAJA O MODIFICACION O CONSULTA
				$bd->listar_provincias($provincia, $textoReadOnly);
			}else //ALTA
			{				
				$bd->listar_provincias(0, $textoReadOnly);
			}
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Contacto:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="contacto" type="text" id="contacto" value="' . $contacto .'"' . 'size="60" maxlength="75" '.$textoReadOnly. '></td>';
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Tel&eacute;fono:</strong></font></div></td>
            <?php 
					echo '<td class="modo2"><div align="left"><input name="telefono" type="text" id="telefono" value="' . $telefono .'"' . 'size="60" maxlength="25" '.$textoReadOnly. '></td>';
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>E-Mail: *</strong></font></div></td>
            <?php 
					echo '<td class="modo2"><div align="left"><input name="email" type="text" id="email" value="' . $email .'"' . 'size="60" maxlength="75" '.$textoReadOnly. '></td>';
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Segundo Contacto:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="contacto2" type="text" id="contacto2" value="' . $contacto2 .'"' . 'size="60" maxlength="75" '.$textoReadOnly. '></td>';
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>E-Mail segundo contacto:</strong></font></div></td>
            <?php 
					echo '<td class="modo2"><div align="left"><input name="email2" type="text" id="email2" value="' . $email2 .'"' . 'size="60" maxlength="75" '.$textoReadOnly. '></td>';
		?>
          </tr>		  
          <tr> 
            <td></td>
          </tr>
          <tr> 
            <td colspan="2" class="modo1" align="left"><font color="#000099">Datos Bancarios</font></td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Nombre del Banco:</strong></font></div></td>
            <?php 
				$bd = new Bd;
				$bd->AbrirBd();
				if ( ($opcion == 2 ) || ($opcion == 3)  || ($opcion == 4)){ // BAJA O MODIFICACION O CONSULTA
					$bd->listar_bancos1($banco1, $textoReadOnly);
				}else //ALTA
				{				
					$bd->listar_bancos1(0, $textoReadOnly);
				}
				$bd = NULL;				
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Titular de cuenta:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="titular_cuenta1" type="text" id="titular_cuenta1" value="' . $titular_cuenta1 .'"' . 'size="60" maxlength="255" '.$textoReadOnly. '></td>';
			?>
          </tr>		  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>CUIT:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="cuit_cuenta1" onblur="PonerGuiones(this)" type="text" id="cuit_cuenta1" value="' . $cuit1 .'"' . 'size="60" maxlength="30" '.$textoReadOnly. '></td>';
			?>
          </tr>			  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Tipo de cuenta:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left">';
				echo '<select name="tipo_cuenta1" id="tipo_cuenta1" '.$textoReadOnly. '>';
				switch($tipo_cuenta1){
					case 0:
						echo '<option value="0" selected>----</option>';
						echo '<option value="1">Caja Ahorros en pesos</option>';
						echo '<option value="2">Cuenta Corriente en pesos</option>';
						echo '<option value="3">Cuenta Unica en pesos</option>';
						break;					
					case 1:
						echo '<option value="1" selected>Caja Ahorros en pesos</option>';
						echo '<option value="2">Cuenta Corriente en pesos</option>';
						echo '<option value="3">Cuenta Unica en pesos</option>';
						echo '<option value="0">----</option>';
						break;
					case 2:
						echo '<option value="1">Caja Ahorros en pesos</option>';
						echo '<option value="2" selected>Cuenta Corriente en pesos</option>';
						echo '<option value="3">Cuenta Unica en pesos</option>';
						echo '<option value="0">----</option>';						
						break;
					case 3:
						echo '<option value="1">Caja Ahorros en pesos</option>';
						echo '<option value="2">Cuenta Corriente en pesos</option>';
						echo '<option value="3" selected>Cuenta Unica en pesos</option>';
						echo '<option value="0">----</option>';						
						break;
					default:
						echo '<option value="0" selected>----</option>';					
						echo '<option value="1">Caja Ahorros en pesos</option>';
						echo '<option value="2">Cuenta Corriente en pesos</option>';
						echo '<option value="3">Cuenta Unica en pesos</option>';
						break;					
				}
				 echo '</select>';
			echo '</td>';
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>N&uacute;mero de cuenta:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="numero_cuenta1" type="text" id="numero_cuenta1" value="' . $numero_cuenta1 .'"' . 'size="60" maxlength="25" '.$textoReadOnly. '></td>';
			?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>CBU:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input onblur="checkCBU(this);" name="cbu1" type="text" id="cbu1" value="' . $cbu1 .'"' . 'size="60" maxlength="22" '.$textoReadOnly. '></td>';
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Nombre 
                del Banco (opcional):</strong></font></div></td>
            <?php 
				$bd = new Bd;
				$bd->AbrirBd();
				if ( ($opcion == 2 ) || ($opcion == 3) || ($opcion == 4)){ // BAJA O MODIFICACION O CONSULTA
					$bd->listar_bancos2($banco2, $textoReadOnly);
				}else //ALTA
				{				
					$bd->listar_bancos2(0, $textoReadOnly);
				}
				$bd = NULL;				
				
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Titular de cuenta:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="titular_cuenta2" type="text" id="titular_cuenta2" value="' . $titular_cuenta2 .'"' . 'size="60" maxlength="255" '.$textoReadOnly. '></td>';
			?>
          </tr>			  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>CUIT:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="cuit_cuenta2" onblur="PonerGuiones(this)" type="text" id="cuit_cuenta2" value="' . $cuit2 .'"' . 'size="60" maxlength="30" '.$textoReadOnly. '></td>';
			?>
          </tr>			  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Tipo de cuenta:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left">';
				echo '<select name="tipo_cuenta2" id="tipo_cuenta2"'.$textoReadOnly. '>';
				switch($tipo_cuenta2){
					case 0:
						echo '<option value="0" selected>----</option>';
						echo '<option value="1">Caja Ahorros en pesos</option>';
						echo '<option value="2">Cuenta Corriente en pesos</option>';
						echo '<option value="3">Cuenta Unica en pesos</option>';
						break;
					case 1:
						echo '<option value="1" selected>Caja Ahorros en pesos</option>';
						echo '<option value="2">Cuenta Corriente en pesos</option>';
						echo '<option value="3">Cuenta Unica en pesos</option>';
						echo '<option value="0">----</option>';
						break;
					case 2:
						echo '<option value="1">Caja Ahorros en pesos</option>';
						echo '<option value="2" selected>Cuenta Corriente en pesos</option>';
						echo '<option value="3">Cuenta Unica en pesos</option>';
						echo '<option value="0">----</option>';						
						break;
					case 3:
						echo '<option value="1">Caja Ahorros en pesos</option>';
						echo '<option value="2">Cuenta Corriente en pesos</option>';
						echo '<option value="3" selected>Cuenta Unica en pesos</option>';
						echo '<option value="0">----</option>';						
						break;
					default:
						echo '<option value="0" selected>----</option>';					
						echo '<option value="1">Caja Ahorros en pesos</option>';
						echo '<option value="2">Cuenta Corriente en pesos</option>';
						echo '<option value="3">Cuenta Unica en pesos</option>';
						break;					
				}
				echo '</select>';			
				echo '</td>';				
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>N&uacute;mero de cuenta:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="numero_cuenta2" type="text" id="numero_cuenta2" value="' . $numero_cuenta2 .'"' . 'size="60" maxlength="25" '.$textoReadOnly. '></td>';
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>CBU:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input onblur="checkCBU(this);" name="cbu2" type="text" id="cbu2" value="' . $cbu2 .'"' . 'size="60" maxlength="22" '.$textoReadOnly. '></td>';
			?>
          </tr>
          <tr> 
            <td colspan="2" class="modo1" align="center"><font color="#000099">* 
              Datos obligatorios.</font></td>
          </tr>
        </table>

        <?php
		
		switch ($opcion){
			case 1: // ALTA 
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';				
				echo '<input type="hidden" name="id_proveedor" id="id_proveedor" value="'.$id_proveedor.'">';
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<input type="hidden" name="id_proveedor" id="id_proveedor" value="'.$id_proveedor.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Proveedor"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<input type="hidden" name="id_proveedor" id="id_proveedor" value="'.$id_proveedor.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 4: // CONSULTA
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onclick="window.history.go(-1); return false;" alt="Volver"><img src="iconos/arrow-back-1.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;
	?>
      </form>
      </td>
  </tr>
</table>
<script>
	document.form3.cuit.focus();	
</script>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
