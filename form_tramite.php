<?php
	include "includes/header.php";
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
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_NOT_GRAL');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],33,''); //33=Tramites
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Seguimiento Rendiciones Administraci&oacute;n</title>
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
function enviar(inForm, enviarEmail){
	if (inForm.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if (inForm.rendicion.value.trim() == "") {
			alert("Debe indicar una rendicion asociada antes de grabar.");
			inForm.rendicion.focus();
			return (false);
		}

		var id_titular_proyecto = $("#id_titular_proyecto option:selected").val();
		var id_titular_adm_proyecto = $("#id_titular_adm_proyecto option:selected").val();  
		var email_titular_proyecto = $("#id_titular_proyecto option:selected").attr("email");
		var email_titular_adm_proyecto = $("#id_titular_adm_proyecto option:selected").attr("email");
		var motivo_tramite = $("input[type='radio'][name='motivo_tramite']:checked").val();

		if (id_titular_proyecto == "-1") {
			alert("Debe indicar un titular de proyecto.");
			inForm.id_titular_proyecto.focus();
			return (false);
		}

		if (enviarEmail) textoEnviarEmail = " Luego, retorne a esta pantalla y reintente el envio.";
		else textoEnviarEmail = "";
		//Ambos deben tener email, si no, aviso
		if (email_titular_proyecto == "") {
			alert("El Titular del Proyecto seleccionado debe tener su email configurado para realizar el envio. Por favor, complete el dato del titular."+textoEnviarEmail);
			inForm.id_titular_proyecto.focus();
			//cancelo el envio
			enviarEmail = false;
			//return false;	Deja grabar igual
		}
		if (id_titular_adm_proyecto !="-1" && email_titular_adm_proyecto == "") {
			alert("El Titular Adm. del Proyecto seleccionado debe tener su email configurado para realizar el envio. Por favor, complete el dato del titular administrador."+textoEnviarEmail);
			inForm.id_titular_proyecto.focus();
			//cancelo el envio
			enviarEmail = false;
			//return false;	Deja grabar igual
		}

		if (inForm.id_estado.value == "1") {	
			//Realizo esta comprobacion solo si estoy en estado Inicio (antes de enviar primer email)	 
			if (motivo_tramite == "1"){ //Es un tramite de solicitud de documentacion
				//Recorro la lista de comprobantes para ver si hay al menos uno especificado (puede no ser comprobante1 si lo elimino)
				var maxOrden = parseInt($("#orden").val());
			  	for (i=1; i<=maxOrden; i++) 
					{textName='comprobante'+i;
					element = document.getElementById(textName);
					if (typeof(element) != 'undefined' && element != null)
						{// exists.
						//alert(textName);
						//Si esta en el formulario debe tener un valor el comprobante asociado y haber elegido al menos un reclamo sobre ese comprobante
						if (element.value.trim() == "" ) {
							alert("Debe indicar el comprobante asociado antes de grabar.");
							element.focus();
							return (false);
						}
						textName2='montocomprobante'+i;
						element2 = document.getElementById(textName2);
						if (element2.value.trim() == "" || !isFloatValid(element2.value)) {
							alert("Debe indicar un monto valido para el comprobante asociado antes de grabar.");
							element2.focus();
							return (false);
						}
						textName3='fechacomprobante'+i;
						element3 = document.getElementById(textName3);
						if (element3.value.trim() != "" && !(isDataFormatValid(element3.value.trim(), 'dateOnly'))) {
							alert("Debe indicar una fecha valida para el comprobante asociado antes de grabar.");
							element3.focus();
							return (false);
						}

						if (!$('input[name=tipo_reclamo'+i+'[]]:checked').length > 0) {
							alert("Debe indicar al menos un reclamo para el comprobante "+element.value+" antes de grabar.");
							element.focus();
							return false;
						}
						//Si esta chequeado tipo_reclamo1 con valor = 10 "Copia de pasaporte sellado" debe indicar el texto destino1
						var salir = false;
						$.each($("input[name=tipo_reclamo"+i+"[]]:checked"), function(){  
							if (!salir && $(this).val() == 9 && inForm['destino'+i].value.trim() == "") {
								alert("Debe indicar el destino para la copia del pasaporte sellado antes de grabar.");
								inForm['destino'+i].focus();
								salir = true;
							}
							if (!salir && $(this).val() == 11 && inForm['monto'+i].value.trim() == "") {
								alert("Debe indicar un monto para la rendicion de fondos de adelanto antes de grabar.");
								inForm['monto'+i].focus();
								salir = true;
							}
							if (!salir && $(this).val() == 11 && !isFloatValid(inForm['monto'+i].value)) {
								alert("Debe indicar un monto valido para la rendicion de fondos de adelanto antes de grabar.");
								inForm['monto'+i].focus();
								salir = true;
							}
							if (!salir && $(this).val() == 12 && inForm['motivo'+i].value.trim() == "") {
								alert("Debe especificar el motivo para el reclamo 'Otro' antes de grabar.");
								inForm['motivo'+i].focus();
								salir = true;
							}						
						});
						if (salir) {
							return false;
						}
						} //end if (typeof(element) != 'undefined' && element != null)
					} //end for
				} 
			else {//El motivo del tramite es solicitar la firma de la rendicion
				if (inForm.rendicion_codigo.value.trim() == "") {
					alert("Debe indicar un codigo de rendicion antes de grabar.");
					inForm.rendicion_codigo.focus();
					return (false);
					}				
				}
		 } else if (inForm.id_estado.value == "2") { 
			if (motivo_tramite == "1"){ //Es un tramite de solicitud de documentacion
				//Si el estado es 'en curso' me fijo si clickeo todos los comprobantes como entregados, en ese caso no debe enviar email?>
				if (enviarEmail) {
					var chequeoTodo = true;
					var maxOrden = parseInt($("#orden").val());
				  	for (i=1; i<=maxOrden; i++) 
						{//Me fijo si ya estan marcada como entregada toda la documentacion.
						//alert($('input[name=presentado'+i+'[]]:checked').length);
						//alert($('input[name=presentado'+i+'[]]').length);
					
						if ($('input[name=presentado'+i+'[]]:checked').length != $('input[name=presentado'+i+'[]]').length) {
							//alert("Falta alguna documentacion del comprobante "+i);
							chequeoTodo = false;
							}
						else {
							//alert("NO falta alguna documentacion del comprobante "+i);
							}
						} //end for
					if (chequeoTodo) {
						alert("Se guardar\u00E1n los cambios pero no se enviar\u00E1 el email ya que toda la documentaci\u00F3n se encuentra presentada.");
						enviarEmail = false;
					}
				} // fi enviarEmail
			}
			//Si es un tramite de firma de rendicion y esta en curso, no debo chequear nada mas
		 } 
		inForm.enviar_email.value = enviarEmail;
		if (enviarEmail){
			var opcion = confirm("Se guardar\u00E1n los cambios y se enviar\u00E1 un email al Titular del Proyecto para solicitarle la documentaci\u00F3n. Est\u00E1 seguro de que desea continuar?");
			if (opcion == true) {
				inForm.submit();
			}
		} else {
			inForm.submit();
		}
	}
	else //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		inForm.submit();
}
function buscarPorCUIT(){
		var cuit = prompt("INGRESE NRO DE CUIT\n(Sin espacios ni guiones)");
		if(cuit!='' && validaCuit(cuit)){
			var cuit = cuit.substr(0, 2)+"-"+cuit.substr(2, 8)+"-"+cuit.substr(10, 2);
			$.post('buscarcuitprov.php?cuit='+cuit, function(data) {
				eval('var obj='+data);
				if(obj.OK){
					$("#opprov_"+obj.id).attr("selected","selected");
					obj.value=obj.id;
				//	buscarBancos(obj);
				//	changeIIBB(1);
				}
			});
		}else{
			alert("\n                CUIT INVALIDO");
		}
	}
</script>
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>

<link href="calendario/css/calendario.css" type="text/css" rel="stylesheet">
<script src="calendario/js/calendar.js" type="text/javascript"></script>
<script src="calendario/js/calendar-es.js" type="text/javascript"></script>
<script src="calendario/js/calendar-setup.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript" src="js/misc.js"></script>
<script language="javascript" type="text/javascript" src="js/validaciones.js"></script>

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
	<tr align="right" valign="top">
		<td colspan="10" width="552">
			<a href="lista_tramites.php" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">Volver</a>
		</td>
	</tr>
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      M&oacute;dulo Seguimiento Rendiciones Administraci&oacute;n ::</span><a href="form_tramite.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> <p> 
        <?php
	switch ($opcion){
			case 1: // OPCION ALTA 
				$id_tramite=0;
				//Nota Vani: asigno el numero al momento de grabar
				$numero = "";
				$anio = date('Y');
				$fecha_inicio = date('d-m-Y');
				$rendicion = "";
				$id_titular_proyecto = 0;
				$id_titular_adm_proyecto = 0;
				$rendicion_codigo = "";
				$observaciones = "";
				$orden = 1;
				$id_estado = 1;
				$txt_disabled = "";
				$txt_readonly_motivo = "";
				$motivo_tramite = "1"; //Default Solicitud de documentacion
				$motivo_tramite_1_checked = " checked ";
				$motivo_tramite_2_checked = "";
				break;
			case 2: // OPCION BAJA 
				//break;
			case 3: // OPCION MODIFICACION
			case 4: // OPCION CONSULTA
				$id_tramite=$_GET['id_tramite'];
				$row = $bd->consultar_tramite($id_tramite);
				$numero = $row['numero'];
				$anio = $row['anio'];
				$fecha_inicio = convertir_fecha($row['fecha_inicio']);
				$rendicion =  $row['rendicion'];
				$observaciones =  $row['observaciones'];
				
				$id_titular_proyecto =  $row['id_titular_proyecto'];
				$id_titular_adm_proyecto =  $row['id_titular_adm_proyecto'];
				if (is_Null($id_titular_adm_proyecto)) $id_titular_adm_proyecto = "-1";
				$rendicion_codigo =  $row['rendicion_codigo'];
				$orden =  $row['cant_comprobantes'];
				$id_estado = $row['id_estado'];
				if ($id_estado !=1) {
					$txt_disabled = " disabled ";
				} else
					{ $txt_disabled = "";}
				$txt_readonly_motivo = " onclick='return false;' ";//En la modificacion el motivo siempre esta disabled
				if ($id_estado !=1) {
					$txt_disabled_firma = ""	;
				} else
					{ $txt_disabled_firma = " disabled ";}
				$motivo_tramite = $row['motivo_tramite'];
				$motivo_tramite_1_checked = "";
				$motivo_tramite_2_checked = "";
				if ($motivo_tramite == 1) {
					$motivo_tramite_1_checked =  " checked ";
				} else {
					$motivo_tramite_2_checked =  " checked ";
				}
				break;
		} // FIN SWITCH
		if ($opcion == 4) $textoReadOnly = " disabled "; 
		else $textoReadOnly = "";
?>
      </p>
      <form action="abm_tramite.php" method="post" enctype="multipart/form-data" name="frmTramite" id="frmTramite">
        <table align="center" class="tabla_form">
          <tr> 
            <td class="modo1"><div align="right">N&uacute;mero tr&aacute;mite:</div></td>
            <?php
		echo '<td class="modo2"><div align="left"><input name="numero" type="text" id="numero" value="' . $numero .'"' . 'size="5" maxlength="25" disabled>';
   		echo '<input name="anio" type="text" id="anio" value="' . $anio .'"' . 'size="5" maxlength="5" disabled></td>';
	  ?>
          </tr>
	  <tr> 
            <td class="modo1"><div align="right">Fecha inicio:</div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="fecha_inicio" type="text" id="fecha_inicio" value="' . $fecha_inicio .'"' . 'size="25" maxlength="25" readonly '.$txt_disabled.'>';echo '</td>';?>
          </tr>
	  <tr> 
            <td class="modo1"><div align="right">Rendici&oacute;n asociada*:</div></td>
	    <td class="modo2"><div align="left">
			<input name="rendicion" type="text" id="rendicion" value="<?php echo $rendicion;?>" <?php echo $txt_disabled.$textoReadOnly;?>  size="50" maxlength="200">
	    </div></td>
	  </tr>
	  <tr> 
            <td class="modo1"><div align="right">Titular proyecto*:</div></td>
	    <td class="modo2"><div align="left">
			<?php echo "<select name=\"id_titular_proyecto\" id=\"id_titular_proyecto\" $txt_disabled $textoReadOnly>";
				echo "<option value='-1' ";
					if ($opcion==1) { echo " selected ";} //En el alta el default es rayas y luego algun titular previamente elegido
				echo " email=''>----</option>";
				$arrayTitulares = $bd->listar_titulares($id_titular_proyecto);
				foreach($arrayTitulares as $row){
				//while ( $row = mysqli_fetch_array($arrayTitulares) ){
					if ($row['id_titular'] == $id_titular_proyecto){
						echo '<option selected value=';
					}else{
						echo '<option value=';
					}
					echo $row['id_titular'] .' email="'.trim($row['email']).'">'. $row['apellido'].', '.$row['nombre'].'</option>';
				}
			echo '</select>';?>
	   </div></td>
          </tr>
	  <tr> 
            <td class="modo1"><div align="right">Administrador proyecto:</div></td>
	    <td class="modo2"><div align="left">
			<?php echo "<select name=\"id_titular_adm_proyecto\" id=\"id_titular_adm_proyecto\" $txt_disabled $textoReadOnly>";
				echo "<option value='-1' ";
				if ($opcion==1) { echo " selected ";} //En el alta el default es rayas
				else {if ($id_titular_adm_proyecto == "-1") echo " selected ";} 
				echo " email=''>----</option>";

				$arrayTitulares = $bd->listar_titulares($id_titular_adm_proyecto);
				foreach($arrayTitulares as $row){
				//while ( $row = mysqli_fetch_array($arrayTitulares) ){
						if ($row['id_titular'] == $id_titular_adm_proyecto){
							echo '<option selected value=';
						}else{
							echo '<option value=';
						}
						echo $row['id_titular'] .' email="'.trim($row['email']).'">'. $row['apellido'].', '.$row['nombre'].'</option>';
				}
			echo '</select>';?>
	   </div></td>
          </tr>
	  <tr> 
            <td class="modo1"><div align="right">Motivo del seguimiento*:</div></td>
	    <td class="modo2"><div align="left">
		<input name="motivo_tramite" type="radio" value="1" <?php echo $txt_readonly_motivo.$textoReadOnly;?> onchange="changeReason(this)" <?php echo $motivo_tramite_1_checked;?>> Solicitud de documentaci&oacute;n<br>
		<input name="motivo_tramite" type="radio" value="2" <?php echo $txt_readonly_motivo.$textoReadOnly;?> onchange="changeReason(this)" <?php echo $motivo_tramite_2_checked;?>> Firma para cierre de rendici&oacute;n <br>
	    </div></td>
	  </tr>
	  <tr id="firma_rendicion"> 
	    <td class="modo1"><div align="right">Datos de firma para cierre*:</div></td>
	    <td class="modo2"><div align="left">
			
		&nbsp;C&oacute;digo de rendici&oacute;n a solicitar al titular del proyecto:&nbsp;
		<input name="rendicion_codigo" type="text" id="rendicion_codigo" value="<?php echo $rendicion_codigo;?>" <?php echo $txt_disabled.$textoReadOnly;?>  size="15" maxlength="200">
		<?php if ($id_estado != 1) {?>
		<br>
		&nbsp;Firma realizada
		<input name="firma_realizada" type="checkbox" value="1" <?php echo $txt_disabled_firma.$textoReadOnly; if ($id_estado == 3) echo "checked";?> >		
		<br>
		<br>
		<?php }?>
	    </div></td>
	  </tr>			
          <tr id="solicitud_documentacion"> 
            <td class="modo1" valign="top" ><div align="right"><br/>
		<?php
			//Mientras este en estado Inicio, puede agregar/modificar comprobantes y sus reclamos, luego no
			if ($id_estado ==1) {
				echo "Documentaci&oacute;n* a <u>solicitar</u> al titular del proyecto:"; 
				}
			else {
				echo "Documentaci&oacute;n* a <u>entregar</u> por el titular del proyecto:"; 
			}
		?>
		</div></td>
	    <td>
			<input type="hidden" name="orden" id="orden" value="<?php echo $orden; ?>"/>
			<?php if ($id_estado ==1 and $opcion != 4) { ?>
				<table border="0" cellpadding="8" cellspacing="0" style="margin-top:0px;" class="tabla">
					<tr>
						<td class="modo2"><div align="right"><b>Agregar comprobante asociado</b>&nbsp;<a href="#" onclick="addComprobante();" title="Agregar comprobante"><img src="agregar.png" width="20" height="20" border="0"></a></div></td>
					</tr>				
				</table>
			<?php } ?>
			<div id="comprobantes">
			 <?php	
				switch ($opcion){
				case 1: // ALTA: 1 comprobante vacio de datos ?>
					<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center" id='tabla_comprobante_1'>
					<thead class="th">
						<tr>
							<td colspan="2" class="left-align">
								Comprobante asoc.*&nbsp;
								<input name="comprobante1" id="comprobante1" type="text" value="" size="15" maxlength="200">&nbsp;
							</td>
						</tr>
						<tr height="7"><td colspan="2"></tr>
						<tr>
							<td colspan="2" class="left-align">
								Moneda*
								<?php 	$monedaEnabled = true;
									$monedaName="monedacomprobante1";
									$monedaSelected=1; //pesos
									$bd->listar_monedas($monedaSelected, $monedaEnabled, $monedaName);  ?>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Monto* <input name="montocomprobante1" id="montocomprobante1" type="number" value="" size="10" maxlength="15"></td>
						</tr>
						<tr height="7"><td colspan="2"></tr>
						<tr>
							<td colspan="2" class="left-align">
								Fecha <input name="fechacomprobante1" type="text" id="fechacomprobante1" value="" size="10" maxlength="12">
									<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha comprobante" id="lanzador_fechacomprobante1">
							</td>
						</tr>
						<tr height="7"><td colspan="2"></tr>
						<tr>
							<?php
								$bd->listar_proveedores(0, 0, 1,1);
							?>
						</tr>
						<tr>
							<td  colspan="2" class="left-align">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input name="proveedorcomprobante1" id="proveedorcomprobante1" type="text" value="" size="15" maxlength="40">
							</td>
						</tr>
						<tr>
						<td colspan="2">
								<img src="eliminar.png" title="Eliminar comprobante" style="width:20px;cursor: pointer;vertical-align: middle;" onclick="delComprobante(1);">
							</td>
						</tr>
					</thead>
					<?php	$arrayTiposReclamos = $bd->getTiposReclamos();
						while ( $row = mysqli_fetch_array($arrayTiposReclamos) ){
							echo "<tr class=\"modo1\">
								<td>
									<input name=\"tipo_reclamo1[]\" type=\"checkbox\" value=\"".$row['id_tramite_reclamo_tipo']."\">
								</td>
								<td><div align=\"left\">".$row['nombre'];
							switch ($row['id_tramite_reclamo_tipo']){
								case 9: //copia de pasaporte sellado (debe indicar destino)
									echo "&nbsp;<input name=\"destino1\" type=\"text\" size=\"15\" maxlength=\"300\">";
									break;
								case 11: //rendicion de fondos (debe indicar monto)
									echo "&nbsp;<input name=\"monto1\" type=\"text\" size=\"7\" maxlength=\"300\">";
									break;
								case 12: //otro (debe indicar motivo del reclamo)
									echo "&nbsp;<input name=\"motivo1\" type=\"text\" size=\"25\" maxlength=\"300\">";
									break;
							}// end switch
							
							echo "</div></td></tr>";
						}
						?>
					</table>
								
				<?php	break;
				case 2: // BAJA
				case 4: // CONSULTA
				case 3: // MODIFICACION: lista de comprobantes ya cargados
					//Aqui varia la interfaz si el estado del tramite es 1 (inicio) o no
					if ($id_estado ==1) {
						$arrayComprobantes = $bd->getTramiteComprobantes($id_tramite);
						$ordenComprobante = 0;
						while ( $row = mysqli_fetch_array($arrayComprobantes) ){	 
							$ordenComprobante = $ordenComprobante + 1;
							?>
							<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center" id="tabla_comprobante_<?php echo $ordenComprobante;?>">
							<thead class="th">
								<tr>
									<td colspan="2" class="left-align">
										Comprobante *&nbsp;
											<input name="comprobante<?php echo $ordenComprobante;?>" id="comprobante<?php echo $ordenComprobante;?>" type="text" value="<?php echo $row["comprobante"];?>" size="9" maxlength="200" <?php echo $textoReadOnly;?> >
									</td>
								</tr>
								<tr height="7"><td colspan="2"></tr>
								<tr>
									<td colspan="2" class="left-align">
										Moneda*
										<?php 	$monedaEnabled = ($opcion != 4);
											$monedaName="monedacomprobante".$ordenComprobante;
											$monedaSelected=$row["id_moneda"]; 
											$bd->listar_monedas($monedaSelected, $monedaEnabled, $monedaName); ?>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Monto*
										<input name="montocomprobante<?php echo $ordenComprobante;?>" id="montocomprobante<?php echo $ordenComprobante;?>" type="text" value="<?php echo $row["monto"];?>" size="3" maxlength="15">
									</td>
								</tr>
								<tr height="7"><td colspan="2"></tr>
								<tr>
									<td colspan="2" class="left-align">
										Fecha <input name="fechacomprobante<?php echo $ordenComprobante;?>" type="text" id="fechacomprobante<?php echo $ordenComprobante;?>" value="<?php if (!is_null($row["fecha"])) echo convertir_fecha($row["fecha"]);?>" size="7" maxlength="12">
											<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha comprobante" id="lanzador_fechacomprobante<?php echo $ordenComprobante;?>">&nbsp;&nbsp;
									</td>
								</tr>
								<tr height="7"><td colspan="2"></tr>
								<tr>
									<?php
										$bd->listar_proveedores($row['proveedor_id'] === null ? 0 : $row['proveedor_id'], 0, 1,$ordenComprobante);
									?>
								</tr>
								<tr>
							<td  colspan="2" class="left-align">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input name="proveedorcomprobante<?php echo $ordenComprobante;?>" id="proveedorcomprobante<?php echo $ordenComprobante;?>" type="text" value="<?php echo $row["proveedor"];?>" size="15" maxlength="40">
								</td>
							</tr>
								<?php //El boton de eliminar no debe mostrarse si estoy en modo consulta 
								if ($opcion != 4) {?>
								<tr>
									<td colspan="2">
										<img src="eliminar.png" title="Eliminar comprobante" style="width:20px;cursor: pointer;vertical-align: middle;" onclick="delComprobante(<?php echo $ordenComprobante;?>);">
									</td>
								</tr>
								<?php }?>
							</thead>
								<?php $arrayTramiteReclamos = $bd->getTramiteReclamos($row["id_tramite_comprobante"]);
								$arrayTiposReclamos = $bd->getTiposReclamos();
								while ( $row3 = mysqli_fetch_array($arrayTiposReclamos) ){
									//Determino si este id_tramite_reclamo_tipo fue previamente seleccionado
									$encontro = false;
									// set the pointer back to the beginning
									mysqli_data_seek($arrayTramiteReclamos, 0);
									while (!$encontro && $row2 = mysqli_fetch_array($arrayTramiteReclamos)){
										$encontro = ($row2["id_tramite_reclamo_tipo"] == $row3['id_tramite_reclamo_tipo']);
									}
									if ($encontro) {
										$tipo_reclamo_checked = " checked ";
										$descripcion = $row2["descripcion"];
									}
									else {
										$tipo_reclamo_checked = "";
										$descripcion = "";
									}
									echo "<tr class=\"modo1\">
										<td>
											<input name=\"tipo_reclamo".$ordenComprobante."[]\" type=\"checkbox\" value=\"".$row3['id_tramite_reclamo_tipo']."\" $tipo_reclamo_checked $textoReadOnly>
										</td>
										<td><div align=\"left\">".$row3['nombre'];
									switch ($row3['id_tramite_reclamo_tipo']){
										case 9: //copia de pasaporte sellado (debe indicar destino)
											echo "&nbsp;<input name=\"destino$ordenComprobante\" type=\"text\" size=\"15\" maxlength=\"300\" value=\"$descripcion\" $textoReadOnly>";
											break;
										case 11: //rendicion de fondos (debe indicar monto)
											echo "&nbsp;<input name=\"monto$ordenComprobante\" type=\"text\" size=\"7\" maxlength=\"300\" value=\"$descripcion\" $textoReadOnly>";
											break;
										case 12: //otro (debe indicar motivo del reclamo)
											echo "&nbsp;<input name=\"motivo$ordenComprobante\" type=\"text\" size=\"25\" maxlength=\"300\" value=\"$descripcion\" $textoReadOnly>";
											break;
									}// end switch
									
									echo "</div></td></tr>";
								}
								?>
							</table>
							
						<?php	} //end while array comprobantes
						}
					else {
						//Si estado !=1
						//Lista de reclamos para que marque cuales fueron entregados
						$arrayComprobantes = $bd->getTramiteComprobantes($id_tramite);
						$ordenComprobante = 0;
						while ( $row = mysqli_fetch_array($arrayComprobantes) ){	
							$ordenComprobante = $ordenComprobante + 1;
							$prov = $bd->consultar_proveedor_por_id($row['proveedor_id']);
							?>
							<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center" id="tabla_comprobante_<?php echo $ordenComprobante;?>">
								<tr>
									<th colspan="2">Comprobante: <?php echo $row["comprobante"]?> 
											&nbsp;&nbsp;&nbsp;&nbsp;Moneda: <?php echo $row["moneda"]?>
											&nbsp;&nbsp;&nbsp;&nbsp;Monto: <?php echo $row["monto"]?></br>
											<?php if (!is_null($row["fecha"])) echo "Fecha: ".$row["fecha"]; ?> 
											&nbsp;&nbsp;&nbsp;&nbsp;<?php if (!is_null($row["proveedor"]) and $row["proveedor"]!="") echo "Proveedor: ".$row["proveedor"]; else echo "Proveedor: ".$prov["razon_social"];?>   
									</th>
								</tr>
								<input name="id_tramite_comprobante<?php echo $ordenComprobante;?>" id="id_tramite_comprobante<?php echo $ordenComprobante;?>" type="hidden" value="<?php echo $row["id_tramite_comprobante"];?>">							
								<?php 
								$arrayTramiteReclamos = $bd->getTramiteReclamos($row["id_tramite_comprobante"]);
								while ( $row3 = mysqli_fetch_array($arrayTramiteReclamos) ){
									if ($row3['presentado'] == "1") {
										//Si fue presentado previamente tampoco puede ser cambiado el valor
										$presentado_checked = " checked ";
										$presentado_disabled = " disabled ";
										$style = "color: #777777;";
									} else {
										$presentado_checked = "";
										$presentado_disabled = "";
										$style = "";
									}
									echo "<tr class=\"modo1\">
											<td><div align=\"left\" style=\"$style\">".$row3['reclamo_nombre']." ".$row3['descripcion'].
										"<td width='25px'>
											<input name=\"presentado".$ordenComprobante."[]\" type=\"checkbox\" value=\"".$row3['id_tramite_reclamo_tipo']."\" $presentado_checked $presentado_disabled $textoReadOnly>
										</td>";
									echo "</div></td></tr>";
								}
								?>
							</table>
						<?php	
						} //end while array comprobantes
									
						}
					break;
			}
			?>	
		 </div> <!--id comprobantes-->
           </td>
		  </tr>
		  <tr> 
            <td class="modo1">
				<div align="right">Observaciones:</div>
			</td>
            <td class="modo2">
				<div align="left">
					<?php
					echo ('<textarea name="observaciones" id="observaciones" cols="50" rows="10">' . $observaciones . '</textarea>')
					
					?>
				</div>
			</td>
		  </tr>
        </table>
        <?php		
		echo "<input type=\"hidden\" name=\"opcion\" id=\"opcion\" value=\"$opcion\">";
		echo '<input type="hidden" name="numero" id="numero" value="'.$numero.'">';
		echo '<input type="hidden" name="anio" id="anio" value="'.$anio.'">';
		echo '<input type="hidden" name="id_tramite" id="id_tramite" value="'.$id_tramite.'">';
		echo '<input type="hidden" name="enviar_email" id="enviar_email" value="false">';
		echo '<input type="hidden" name="id_estado" id="id_estado" value="'.$id_estado.'">';
		switch ($opcion){
			case 1: // ALTA  
				$textoAlt = "Guardar datos";			
				$imgSrc = "grabar_datos.png";
				break;
			case 2: // BAJA
				$textoAlt = "Eliminar";			
				$imgSrc = "eliminar.png";
				break;
			case 3: // MODIFICACION 
				$textoAlt = "Guardar datos";			
				$imgSrc = "actualizar_datos.png";
				break;
		}
		if ($opcion != 4) {
			echo "<p align=\"center\"><button type=\"button\" class=\"boton\" name=\"btn_enviar\" id=\"btn_enviar\" onClick=\"enviar(form,false)\"><img src=\"$imgSrc\" title=\"$textoAlt\" width=\"30\" heigth=\"30\" border=\"0\"></button>";	
			//Si es alta o modificacion, tambien mostrar boton "Guardar y enviar"		
			if ($opcion == 1 or $opcion == 3)
				echo "&nbsp;&nbsp;<button type=\"button\" class=\"boton\" name=\"btn_enviar2\" id=\"btn_enviar2\" onClick=\"enviar(form,true)\" ><img src=\"iconos/enviar_datos.png\" title=\"Guardar y enviar\" width=\"30\" heigth=\"30\" border=\"0\"></button>";
			echo "</p>";	
		}	
	?>
      </form>
	</td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
<script>
	function delComprobante(id) {
		$("#tabla_comprobante_"+id).remove();
	}

	function addComprobante(id) {
		var orden = parseInt($("#orden").val()) + 1;
		var moneda = "Moneda*<?php 
				$monedaEnabled = true;$monedaName='monedacomprobante1'; $monedaSelected=1; 
				$bd->listar_monedas($monedaSelected, $monedaEnabled, $monedaName); ?>";
		moneda = moneda.replace("monedacomprobante1", "monedacomprobante"+orden);
		var proveedor = '<tr><?php $bd->listar_proveedores(0, 0, 1,1); ?>	</tr>';
		proveedor = proveedor.replace("proveedor1","proveedor"+orden);
		var comprobante = "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" class=\"tabla\" align=\"center\" id=\"tabla_comprobante_"+orden+"\"><thead class=\"th\">";
				comprobante = comprobante +"<tr><td colspan=\"2\" class=\"left-align\">Comprobante asoc.*&nbsp;";
				comprobante = comprobante + "<input name=\"comprobante"+orden+"\" id=\"comprobante"+orden+"\" type=\"text\" size=\"7\" maxlength=\"200\">&nbsp;</td></tr>";
				comprobante = comprobante + "<tr height=\"7\"><td colspan=\"2\"></td></tr>";
				comprobante = comprobante + "<tr><td colspan=\"2\" class=\"left-align\">";
				comprobante = comprobante + moneda + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
				comprobante = comprobante + "Monto* <input name=\"montocomprobante"+orden+"\" id=\"montocomprobante"+orden+"\" type=\"text\" value=\"\" size=\"10\" maxlength=\"15\"></td></tr>";
				comprobante = comprobante + "<tr height=\"7\"><td colspan=\"2\"></td></tr>";
				comprobante = comprobante + "<tr><td colspan=\"2\" class=\"left-align\">";
				comprobante = comprobante + "Fecha <input name=\"fechacomprobante"+orden+"\" type=\"text\" id=\"fechacomprobante"+orden+"\" value=\"\" size=\"10\" maxlength=\"12\">";
				comprobante = comprobante + "<img src=\"calendario/ima/calendario.png\" width=\"16\" height=\"16\" border=\"0\" title=\"Fecha comprobante\" id=\"lanzador_fechacomprobante"+orden+"\"></td></tr>";
				comprobante = comprobante + "<tr height=\"7\"><td colspan=\"2\"></td></tr>";
				comprobante = comprobante + proveedor;
				comprobante = comprobante + "<tr><td colspan=\"2\" class=\"left-align\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"proveedorcomprobante"+orden+"\" id=\"proveedorcomprobante"+orden+"\" type=\"text\" value=\"\" size=\"15\" maxlength=\"150\"></td></tr>";
				comprobante = comprobante + "<tr><td colspan=\"2\">";
				/* comprobante = "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" class=\"tabla\" align=\"center\" id=\"tabla_comprobante_"+orden+"\">";
		    comprobante = comprobante +"<tr><th colspan=\"2\">Comprobante asoc.*&nbsp;";
		    comprobante = comprobante + "<input name=\"comprobante"+orden+"\" id=\"comprobante"+orden+"\" type=\"text\" size=\"7\" maxlength=\"200\">&nbsp;&nbsp;";
		    comprobante = comprobante + moneda + "&nbsp;&nbsp;";
		    comprobante = comprobante + "Monto* <input name=\"montocomprobante"+orden+"\" id=\"montocomprobante"+orden+"\" type=\"text\" value=\"\" size=\"3\" maxlength=\"15\">";
		    comprobante = comprobante + "<br>";
		    comprobante = comprobante + "Fecha <input name=\"fechacomprobante"+orden+"\" type=\"text\" id=\"fechacomprobante"+orden+"\" value=\"\" size=\"7\" maxlength=\"12\">";
		    comprobante = comprobante + "<img src=\"calendario/ima/calendario.png\" width=\"16\" height=\"16\" border=\"0\" title=\"Fecha comprobante\" id=\"lanzador_fechacomprobante"+orden+"\">&nbsp;&nbsp;";
		    comprobante = comprobante + "Proveedor <input name=\"proveedorcomprobante"+orden+"\" id=\"proveedorcomprobante"+orden+"\" type=\"text\" value=\"\" size=\"15\" maxlength=\"150\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		    */comprobante = comprobante + "<img src=\"eliminar.png\" title=\"Eliminar comprobante\" style=\"width:20px;cursor: pointer;vertical-align: middle;\" onclick=\"delComprobante("+orden+");\"></td></tr>";
		    comprobante = comprobante + "<script>configurarCalendar (\"fechacomprobante"+orden+"\");<\/script>";
		    comprobante = comprobante + "</th></tr>"; 
		    comprobante = comprobante + "<?php 
$arrayTiposReclamos = $bd->getTiposReclamos(); 
while ($row=mysqli_fetch_array($arrayTiposReclamos) ){
	echo "<tr class='modo1'><td><input name='tipo_reclamo{orden}[]' type='checkbox' value='".$row['id_tramite_reclamo_tipo']."'></td><td><div align='left'>".$row['nombre'];
switch ($row['id_tramite_reclamo_tipo']){ case 9: echo "&nbsp;<input name='destino{orden}' type=`text' size='15' maxlength='300'>";break; case 11: echo "&nbsp;<input name='monto{orden}' type='text' size='7' maxlength='300'>"; break; case 12: echo "&nbsp;<input name='motivo{orden}' type='text' size='25' maxlength='300'>"; break;}
	echo "</div></td></tr>";}?>".replace(/{orden}/g, orden);
		    comprobante = comprobante + "</thead></table>";
		$("#comprobantes").prepend(comprobante);
		$("#orden").val(parseInt($("#orden").val()) + 1);
	}


	function showRow(id) {
	      var row = document.getElementById(id);
	      row.style.display = '';
	}

	function hideRow(id) {
	      var row = document.getElementById(id);
	      row.style.display = 'none';
	}

	function changeReason(radio) {
		var selected = radio.value;
		if (selected == '1') {
			showRow('solicitud_documentacion');
			hideRow('firma_rendicion');
		}
		else {
			showRow('firma_rendicion');
			hideRow('solicitud_documentacion');
		}
	}
	
	if (<?php echo $motivo_tramite;?> == 1) {
		showRow('solicitud_documentacion');
		hideRow('firma_rendicion');
		}
	else {
		showRow('firma_rendicion');
		hideRow('solicitud_documentacion');
	}

	$("#rendicion").focus();

	function configurarCalendar(campoTxt) {
		Calendar.setup({ 
			inputField:    campoTxt, // id del campo de texto 
			ifFormat  :    "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
			button    :    "lanzador_"+ campoTxt   // el id del botón que lanzará el calendario
			}); 
	}

	$.each($("input[name^='fechacomprobante'"), function(i, val){  
			configurarCalendar (val.name);
			});

</script>
</body>
</html>
