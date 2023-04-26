<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_GET["opcion"];
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_OP');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],4,''); //4=Orden de pago
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
	var monthFirstDay = new Date();
	var dd = '01';
	var mm = monthFirstDay.getMonth()+1; //January is 0!
	var yyyy = monthFirstDay.getFullYear();

	if(mm<10) {
	    mm='0'+mm
	} 

	monthFirstDay = dd+'-'+mm+'-'+yyyy;
	select = inForm.forma_pago;
	var forma_pago = select.options[select.selectedIndex].value;

	var email_proveedeor= $("#proveedor option:selected").attr("email");
	var c_iva_proveedor= $("#proveedor option:selected").attr("c_iva");

	var email_titular= $("#id_titular_aviso_pago option:selected").attr("email");
	var retiene = $("#emisor option:selected").attr("retiene");
	var importe  = parseFloat($("#importe").val());

	if (inForm.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if (isEmpty(inForm.fecha)) {
			alert("Por favor, complete la fecha.");
			inForm.fecha.focus();
			return false;
		}else if (!(isDataFormatValid(inForm.fecha.value, 'dateOnly'))) {
			alert("El formato de la fecha es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
			inForm.fecha.focus();
			return false;	
		}else if (isEmpty(inForm.factura)) {
			alert("Por favor, complete la factura.");
			inForm.factura.focus();
			return false;
		}else if ((importe >= 2000) && (inForm.aretener.value <= 0) && (c_iva_proveedor == 1 || c_iva_proveedor == 2) && (retiene == 1)) {
			//Solo debe aparecer el cartel cuando el proveedor es responsable inscripto o monotributista
			//Y cuando el emisor es agente de retencion
			var opcion = confirm("El importe a retener es menor o igual a cero. Est\u00E1 segura que desea continuar?");
			if (opcion == true) {
				//inForm.submit();
			} else {
				return false;
			}			
		}
		//La siguiente verificacion no tiene sentido ya que la fecha de alta esta fijada al dia de hoy y el dato es readonly
		//}else if ((document.form3.opcion.value == 1) && (compareDates(inForm.fecha.value, dateFormatPattern, monthFirstDay, dateFormatPattern) == 1)) {
		//	alert("La fecha no puede ser anterior a "+monthFirstDay+". Por favor, corrijala.");
		//	inForm.fecha.focus();
		//	return false;
		//Si forma de pago es "pago de servicios" 3, el aviso de pago adicional solo puede tomar el valor "no enviar"
		if ((forma_pago == 3) && (inForm.aviso_pago_adicional.value == 2)) {
			alert("Debido a que la forma de pago seleccionada es Pago de servicios, no es posible realizar un aviso de pago adicional. Por favor, seleccione No Enviar en Aviso de pago adicional.");
			inForm.forma_pago.focus();
			return false;	
		//Si forma de pago es "Cheque" o "Transferencia" solo puede elegir un proveedor con email
		}else if ((forma_pago != 3) && (email_proveedeor == "")) {
			alert("Debido a que la forma de pago es Cheque o Transferencia, el proveedor seleccionado debe tener su email configurado. Por favor, complete el dato del proveedor.");
			inForm.proveedor.focus();
			return false;	
		//Si envia aviso de pago de Titular solo puede elegir un Titular con email
		}else if ((inForm.aviso_pago_adicional.value == 2) && (email_titular == "")) {
			alert("El Titular seleccionado debe tener su email configurado. Por favor, complete el dato del titular.");
			inForm.id_titular_aviso_pago.focus();
			return false;	
		}else{
			//enviar = window.confirm('Se enviarán todos los datos del formulario');
			//(enviar)?form.submit():'return false';
			if(document.form3.opcion.value == 1){
				var opEmisor = $("#emisor > option:selected").html();
				var opEmisorTxt = document.getElementById("opEmisor");
			
				opEmisorTxt.innerHTML = opEmisor;
				fadeIn(lightbox);
			}else{
				inForm.submit();
			}
		}
	}
	else //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		inForm.submit();
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
#lightbox {
    position:fixed; /* keeps the lightbox window in the current viewport */
    top:0; 
    left:0; 
    width:100%; 
    height:100%; 
    background: rgba(0,0,0,.6); 
    text-align:center;
	display:none;
}
#lightbox p {
    text-align:right; 
    color:#fff; 
    margin-right:20px; 
    font-size:12px; 
}
#content {
	margin-top:23%;
	align:center;
}
#box{
	font-family: Verdana, Geneva, sans-serif;
	background-color: #0099cc;
    color: #ddd;
    border: solid 1px #666666;
    box-shadow: 0px 1px 10px #222;
    padding: 20px 10px 35px 10px;
    text-align: center;
    font-weight: bold;
    font-size: 15px;
    z-index: 151;
    position: absolute;
    margin-left: -15%;
    width: 30%;
    left: 50%;
    top: 25%;	
}
#box button{
	margin: 10px 5px;
    padding: 5px 10px;
    text-align: center;
    background-color: #0099cc;
    border: 2px solid #ddd;
	/*border-radius:30px;*/
    color: #ddd;
    font-weight: bold;
}
	
#box button:hover {
	background-color: #00bbee;
	cursor: pointer;
	color:#000000;
}
	
#lightbox img {
    box-shadow:0 0 25px #111;
    -webkit-box-shadow:0 0 25px #111;
    -moz-box-shadow:0 0 25px #111;
    max-width:940px;
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
			<a href="lista_ordenes_pago.php" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">Volver</a>
			<!--<a href="javascript:history.back(-1);" class="tituloweb2Copia" style="font-weight:bold; font-size:10px" title="Ir la página anterior">Volver</a>-->
		</td>
	</tr>
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      Orden de Pago ::</span><a href="form_orden_pago.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> <p>
        <?php
		$emisor_disabled = "";
		$alicuota_disabled = "";
		switch ($opcion){
			case 1: // OPCION ALTA
				//$ultimo_numero_orden_pago = $bd->ultimo_numero_orden_pago();
				//$numero_orden_pago = ++$ultimo_numero_orden_pago;
				//$numero_orden_pago = $bd->getConfig('last_op_id');
				//Nota Vani: asigno el numero al momento de grabar
				$numero_orden_pago = "";
				$anio_numero_orden_pago = date('Y');
				$fecha = date('d-m-Y');
				$confeccionador = 0;
				$proveedor = 0;
				$factura = "";
				$objeto = "";							
				$asignacion_rendicion = "";
				$id_moneda = 1;
				$importe = 0;
				$aclaraciones = "";
				$firmante = 0;
				$firmante2 = 0;
				$cbilateral = 0;
				$id_iva = 0;
				$alicuota = 0;
				$cuenta = 0;
				$emisor = 0;
				$estado = 0;
				$forma_pago = "2"; //Default Transferencia porque es la que mas usan
				$id_titular_aviso_pago = "";
				$fecha_aviso_pago = "";
				$aviso_pago_enviado = "0";
				$aviso_pago_no_enviar_checked = " checked ";
				$aviso_pago_ue_checked = "";
				$aviso_pago_titular_checked = "";
				$condicion_venta_ce_si_checked = " checked ";
				$condicion_venta_ce_no_checked = "";
			break;
			case 2: // OPCION BAJA
				//break;
			case 3: // OPCION MODIFICACION 
			case 4: // OPCION CONSULTA
				$numero_orden_pago = $_GET['numero_orden_pago'];
				$anio_numero_orden_pago = $_GET['anio'];
				$row = $bd->consultar_orden_pago($numero_orden_pago, $anio_numero_orden_pago);
				if($bd->isRetentionAgent($row["id_unidad_ejecutora"]) || $bd->availRetention($row['proveedor'])){
					$alicuota_disabled = "";
				}else{
					$alicuota_disabled = "disabled='disabled'";
				}
				$fecha = convertir_fecha($row["fecha"]);	
				$confeccionador = $row["confeccionador"];
				$proveedor = $row["proveedor"];
				$factura = $row["factura"];
				$objeto = $row["objeto"];				
				$asignacion_rendicion = $row["asignacion_rendicion"];
				$id_moneda = $row["id_moneda"];
				$importe = $row["importe"];
				$aclaraciones = $row["aclaraciones"];
				$firmante = $row["firmante"];
				$firmante2 = $row["firmante2"];
				$cbilateral = $bd->getCMPercent((int)$row["proveedor"]);
				$id_iva = $row["iva"];
				$alicuota = $row["alicuota"];
				$estado = $row["estado"];
				$emisor = $row["id_unidad_ejecutora"];
				$cuenta = $row["cuenta"];
				$emisor_disabled = "disabled='disabled'";/*no se puede modificar, no se porque*/
				$forma_pago = $row["forma_pago"];
				$id_titular_aviso_pago = $row["id_titular_aviso_pago"];
				$fecha_aviso_pago =  convertir_fecha($row["fecha_aviso_pago"]);
				$aviso_pago_enviado = $row["aviso_pago_enviado"];
				if ($row["no_enviar_aviso_pago_adicional"] == 1) {
					$aviso_pago_no_enviar_checked = " checked ";
					$aviso_pago_titular_checked = "";
				}
				else if (!is_null($row["id_titular_aviso_pago"])) {
					$aviso_pago_no_enviar_checked = "";
					$aviso_pago_titular_checked = " checked ";
				}
				if ($row["condicion_venta_ce"] == 1) {
					$condicion_venta_ce_si_checked = " checked ";
					$condicion_venta_ce_no_checked = "";
				}
				else {
					$condicion_venta_ce_si_checked = "";
					$condicion_venta_ce_no_checked = " checked ";
				}

				break;			
		} // FIN SWITCH
		if ($opcion == 4) $textoReadOnly = " disabled "; 
		else $textoReadOnly = "";
?>
      </p>
      <form action="abm_orden_pago.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <input type="text" hidden name='estado' id='estado' value="<?php echo $estado; ?>"></input>
		<table align="center" class="tabla_form">
          <tr> 
            <td class="modo1"><div align="right">N&uacute;mero de Orden de Pago:</div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="numero_orden_pago" type="text" id="numero_orden_pago" value="' . $numero_orden_pago .'"' . 'size="12" maxlength="15" disabled>';
		echo "<input name=\"anio_numero_orden_pago\" type=\"text\" id=\"anio_numero_orden_pago\" value=\"$anio_numero_orden_pago \"  size=\"13\" maxlength=\"13\" $textoReadOnly ></td>";
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Fecha:*</div></td>
            <?php 
		echo "<td class=\"modo2\"><div align=\"left\"><input name=\"fecha\" type=\"text\" id=\"fecha\" value=\"$fecha\" size=\"25\" maxlength=\"25\" readonly $textoReadOnly>";
		//echo '<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Inicial" id="lanzador">';
		//oculto la imagen del calendario porque por pedido de Andrea Moyano la fecha debe ser read only
		echo '</td>';
		//Y el codigo javascript asociado tambien
		/*<script type="text/javascript"> 
			Calendar.setup({ 
				inputField     :    "fecha",     // id del campo de texto 
				ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
				button     :    "lanzador"     // el id del botón que lanzará el calendario 
			}); 
		</script>*/
	?>
            
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Emisor:</div></td>
			<td class="modo2"><div align="left">
				<!--El emisor pasa a ser una unidad. Mostrar todas menos CCT Z Influencia, UAT y OVT-->
				<select name="emisor" id='emisor' onchange="changeIIBB();" <?php echo $emisor_disabled." ".$textoReadOnly;?> >
					<?php $unidades = $bd->getUEs();
					foreach($unidades as $unidad){
						//MdP Zona de Influencia no debe figurar
						if((int)$unidad['id_unidad_ejecutora'] != 11 and
							(int)$unidad['id_unidad_ejecutora'] != 12 and
							(int)$unidad['id_unidad_ejecutora'] != 15){
							if((int)$emisor == (int)$unidad['id_unidad_ejecutora']){
								//TODO ver si el nroiibb luego se agrega como dato en vez de tomar el cuit
								echo '<option nroiibb="'.$unidad['cuit'].'" retiene="'.$unidad['agente_retencion'].'" value="'.$unidad['id_unidad_ejecutora'].'" selected>'.$unidad['nombre'].'</option>';
							}else{
								echo '<option nroiibb="'.$unidad['cuit'].'" retiene="'.$unidad['agente_retencion'].'" value="'.$unidad['id_unidad_ejecutora'].'">'.$unidad['nombre'].'</option>';
							}
						}
					}
					?>
				</select>
			</div></td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Cuenta:</div></td>
			<td class="modo2"><div align="left">
			<select name='cuenta' id='cuenta' <?php echo $textoReadOnly;?>>
				<?php $cuentasUnidades = $bd->getCuentasUnidades((int)$cuenta);
				foreach($cuentasUnidades as $cuentaUnidad){
					if((int)$cuenta == (int)$cuentaUnidad['id']){
						echo '<option id_unidad="'.$cuentaUnidad['id_unidad_ejecutora'].'" value="'.$cuentaUnidad['id'].'" selected>'.$cuentaUnidad['nro_cuenta'].'</option>';
					}else{
						echo '<option id_unidad="'.$cuentaUnidad['id_unidad_ejecutora'].'" value="'.$cuentaUnidad['id'].'">'.$cuentaUnidad['nro_cuenta'].'</option>';
					}
				}
				?>
			</select>
			</div></td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Nro. IIBB:</div></td>
			<td class="modo2"><div align="left">
			<input type="text" name='iibb' id='iibb' value="" disabled="disabled" <?php echo $textoReadOnly;?>></input>
			</div></td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Proveedor:</div></td>
            <?php 
				if ( ($opcion == 2 ) || ($opcion == 3) || ($opcion == 4)){ // BAJA O MODIFICACION O CONSULTA
					$bd->listar_proveedores($proveedor, $textoReadOnly);
				}else //ALTA
				{				
					$bd->listar_proveedores(0, $textoReadOnly);
				}
			?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Nro. IIBB:</div></td>
			<td class="modo2"><div align="left">
			<input type="text" name='iibbprov' id='iibbprov' value="" disabled="disabled" ></input><label id='cuitprov' style="color:black; font-weight:bolder;"></label>
			</div>
			</td>
          </tr>
          <tr id="tr-banco" style="display:none;"> 
            <td class="modo1">Banco:</div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="usa_banco" type="radio" value="1" checked="checked"/><label id="cbu1"></label>';
				echo '<input name="usa_banco" type="radio" value="2"/><label id="cbu2"></label></div></td>';
			?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Factura*:</div></td>
            <?php 
		echo "<td class=\"modo2\"><div align=\"left\"><input name=\"factura\" type=\"text\" id=\"factura\" value=\"$factura\" size=\"50\" maxlength=\"75\" $textoReadOnly ></td>";
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Objeto:</div></td>
            <?php 
		echo "<td class=\"modo2\"><div align=\"left\"><input name=\"objeto\" type=\"text\" id=\"objeto\" value=\"$objeto\" size=\"50\" maxlength=\"75\" $textoReadOnly></td>";
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Asignaci&oacute;n de rendici&oacute;n:</div></td>
            <?php 
			echo "<td class=\"modo2\"><div align=\"left\"><input name=\"asignacion_rendicion\" type=\"text\" id=\"asignacion_rendicion\" value=\"$asignacion_rendicion\" size=\"50\" maxlength=\"75\" $textoReadOnly></td>";
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Moneda:</div></td>
			<td class="modo2"><div align="left">
				<?php $enabled = ($opcion != 4);
				$bd->listar_monedas($id_moneda, $enabled); ?>
			</td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">Importe:</div></td>
            <?php 
			echo "<td class=\"modo2\"><div align=\"left\"><input name=\"importe\" type=\"text\" id=\"importe\" value=\"$importe\"' size=\"50\" maxlength=\"25\" onchange=\"calcularBI();\" $textoReadOnly></td>";
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">C. Multilateral:</div></td>
			<td class="modo2"><div align="left">
            <?php 
			echo '<input name="cm" type="hidden" id="cm" value="' . $cbilateral .'"/>';
			echo '<input id="cm_view" value="' . $cbilateral .'" style="width:58px;" maxlength="25" disabled><label style="font-weight:bold; padding-left:5px; color:white; font-size: 16px;">%</label>';
			?>
			</div>
			</td>
          </tr>

          <tr> 
            <td class="modo1"><div align="right">Monto Resultante:</div></td>
			<td class="modo2"><div align="left">
			<input type="text" name='monto_multilateral' id='monto_multilateral' value="" disabled="disabled" ></input>
			</div></td>
          </tr>

          <tr> 
            <td class="modo1"><div align="right">IVA:</div></td>
			<td class="modo2"><div align="left">
			<select name="id_iva" id="id_iva" style="width:58px;" maxlength="25" onchange="calcularBI();" <?php echo $alicuota_disabled; echo $textoReadOnly; ?>>
				<option value="0" <?php if($id_iva == '0') echo "selected='selected'"; ?>>0</option>
				<option value="10.5" <?php if($id_iva == '10.5') echo "selected='selected'"; ?>>10.5</option>
				<option value="21" <?php if($id_iva == '21') echo "selected='selected'"; ?>>21</option>
			</select>
			<label style="font-weight:bold; padding-left:5px; color:white; font-size: 16px;">%</label>
			</td>
          </tr>

          <tr> 
            <td class="modo1"><div align="right">Base imponible:</div></td>
			<td class="modo2"><div align="left">
			<input type="text" name='base_imponible' id='base_imponible' value="" disabled="disabled" ></input>
			</div></td>
          </tr>

          <tr> 
            <td class="modo1"><div align="right">Al&iacute;cuota Ret.:</div></td>
			<td class="modo2"><div align="left">
			<input type="text" name='alicuota' id='alicuota' value="<?php echo $alicuota; ?>" <?php echo $alicuota_disabled." ".$textoReadOnly; ?> style="width:58px;" onchange="calcularBI();"></input>
			<label style="font-weight:bold; padding-left:5px; color:white; font-size: 16px;">%</label>
			</div></td>
          </tr>

          <tr> 
            <td class="modo1"><div align="right">A Pagar: $</div></td>
			<td class="modo2"><div align="left">
			<input type="text" name='apagar' id='apagar' value="" disabled></input>
			</div></td>
          </tr>

          <tr> 
            <td class="modo1"><div align="right">A Retener: $</div></td>
			<td class="modo2"><div align="left">
			<input type="text" name='aretener' id='aretener' value="" disabled></input>
			</div></td>
          </tr>

          <tr> 
            	<td class="modo1"><div align="right">Forma de Pago:</div></td>
		<td class="modo2"><div align="left">
			<select name="forma_pago" id="forma_pago"  <?php echo $textoReadOnly;?> >
				<option value="1" <?php if($forma_pago == 1) echo "selected"; ?>>Cheque</option>
				<option value="2" <?php if($forma_pago == 2) echo "selected"; ?>>Transferencia</option>
				<option value="3" <?php if($forma_pago == 3) echo "selected"; ?>>Pago de servicios</option>
			</select>
			
		</td>
          </tr>
          <tr> 
            	<td class="modo1"><div align="right">El comprobante a pagar,<br> posee condici&oacute;n de venta<br> Contado/Efectivo?:</div></td>
		<td class="modo2"><div align="left">
			<input type="radio" name="condicion_venta_ce" value="1" <?php echo $condicion_venta_ce_si_checked;?> <?php echo $textoReadOnly;?>> Si
			<input type="radio" name="condicion_venta_ce" value="0" <?php echo $condicion_venta_ce_no_checked;?> <?php echo $textoReadOnly;?>> No

		</td>
          </tr>
          <tr> 
            	<td class="modo1"><div align="right">Aviso de pago adicional:</div></td>
		<td class="modo2"><div align="left">
			<input type="radio" name="aviso_pago_adicional" value="0" <?php echo $aviso_pago_no_enviar_checked;?> onChange="changeAvisoPago(this)" <?php echo $textoReadOnly;?>> No enviar </br>
			<input type="radio" name="aviso_pago_adicional" value="2" <?php echo $aviso_pago_titular_checked;?> onChange="changeAvisoPago(this)" <?php echo $textoReadOnly;?>> Enviar a Titular 
			<?php echo "<select name=\"id_titular_aviso_pago\" id=\"id_titular_aviso_pago\" $textoReadOnly>";
				$arrayTitulares = $bd->listar_titulares($id_titular_aviso_pago);
				foreach($arrayTitulares as $row){
				//while ( $row = mysqli_fetch_array($arrayTitulares) ){
					if ($row['id_titular'] == $id_titular_aviso_pago){
						echo '<option selected value=';
					}else{
						echo '<option value=';
					}
					echo $row['id_titular'] .' email="'.trim($row['email']).'">'. $row['apellido'].', '.$row['nombre'].'</option>';
				}
			echo '</select>';?>
		</td>
          </tr>

          <!--Se quita para no crear confusion solo se muestra en modo consulta-->
	<?php if ($opcion == 4) {?> <tr> 
            	<td class="modo1"><div align="right">Fecha de aviso de pago:</div></td>
		<td class="modo2"><div align="left">
			<input name="fecha_aviso_pago" type="text" id="fecha_aviso_pago" value="<?php echo $fecha_aviso_pago;?>"  size="10" maxlength="25" readonly disabled>
			(definida en la confirmaci&oacute;n de pago)</br>
		</td>
          </tr>
          <tr> 
            	<td class="modo1"><div align="right">Aviso de pago enviado:</div></td>
		<td class="modo2"><div align="left">
			<input name="aviso_pago_enviado" type="checkbox" <?php if ($aviso_pago_enviado == 1) echo " checked ";?>  readonly disabled>			
		</td>
          </tr>
	<?php } //if ($opcion == 4) ?>

          <tr> 
            <td class="modo1"><div align="right">Aclaraciones:</div></td>
            <?php 
			echo "<td class=\"modo2\"><div align=\"left\"><input name=\"aclaraciones\" type=\"text\" id=\"aclaraciones\" value=\"$aclaraciones\" size=\"50\" maxlength=\"75\"  $textoReadOnly></td>";
		?>
          </tr>
          <tr>		  
            <td class="modo1"><div align="right">Firmante 1:</div></td>
            <?php 
				if ( ($opcion == 2 ) || ($opcion == 3) ){ // BAJA O MODIFICACION
					$bd->listar_firmantes($firmante,"firmante","false", $textoReadOnly);
				}else //ALTA
				{				
					$bd->listar_firmantes(17,"firmante","false", $textoReadOnly); //default
				}
			?>
          </tr>
          <tr>		  
            <td class="modo1"><div align="right">Firmante 2:</div></div></td>
            <?php 
				if ( ($opcion == 2 ) || ($opcion == 3) ){ // BAJA O MODIFICACION
					$bd->listar_firmantes($firmante2,"firmante2","false", $textoReadOnly);
				}else //ALTA
				{				
					$bd->listar_firmantes(2,"firmante2","false", $textoReadOnly); //default
				}
			?>
          </tr>
          <tr> 
            <td colspan="2" class="modo1" align="center">* Datos obligatorios.</td>
          </tr>
        </table>
        <?php
		switch ($opcion){
			case 1: // ALTA 
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';
				echo '<input type="hidden" name="numero_orden_pago" id="numero_orden_pago" value="'.$numero_orden_pago.'">';				
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(this.form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<input type="hidden" name="numero_orden_pago" id="numero_orden_pago" value="'.$numero_orden_pago.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(this.form)" alt="Eliminar Registro"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<input type="hidden" name="numero_orden_pago" id="numero_orden_pago" value="'.$numero_orden_pago.'">';				
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(this.form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;				
	?>
      </form>
<div id="lightbox">
    <div id="content">
		<div id="box">
		<h3>Confirma la creacion de la O.P. con emisor <br/>
		<label id="opEmisor">456/1234</label>?</h3>
		<button onclick="ConfirmaGen('lightbox');">S&iacute;</button><button onclick="CancelaGen('lightbox');">No</button>
		</div>
    </div>
</div>
	  
      <script>
	document.form3.fecha.focus();
	function buscarBancos(obj){
		if(obj.value!=''){			
			$.post('buscarbancos.php?prov='+obj.value, function(data) {
				eval('var obj='+data);
				if((obj.banco1 && obj.banco2) && (obj.banco1 != '----' && obj.banco2 != '----')){
					$("#tr-banco").removeAttr("style");
					if(obj.banco1 == obj.banco2){
						$("#cbu1").html(obj.banco1+"("+obj.cuenta1+")");
						$("#cbu2").html(obj.banco2+"("+obj.cuenta2+")");
					}else{
						$("#cbu1").html(obj.banco1);
						$("#cbu2").html(obj.banco2);					
					}
				}else{
					$("#tr-banco").css("display","none");
					$("#cbu1").html("");
					$("#cbu2").html("");
				}
			});
		}
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
					buscarBancos(obj);
					changeIIBB(1);
				}
			});
		}else{
			alert("\n                CUIT INVALIDO");
		}
	}
	
	function changeIIBB(op){
		var estado 		= $("#estado").val();
		var retiene 	= $("#emisor option:selected").attr("retiene");
		var emisor 		= $("#emisor option:selected").attr("value");
		var c_iva 		= $("#proveedor option:selected").attr("c_iva");
		var c_iibb 		= $("#proveedor option:selected").attr("c_iibb");
		var importe 	= parseFloat($("#importe").val()).toFixed(2);
		
		//console.log("CONDICION IVA:"+c_iva+"\nCONDICION IIBB: "+c_iibb+"\nRETIENE: "+retiene+"\nIMPORTE: "+importe);
		//c_iva = 1 Responsable Inscripto c_iva = 2 Monotributista
		//c_iibb == 3 Exento
		//modificacion vani if(importe < 2000 || retiene == '0' || (c_iva != '1' && c_iva != '2') || c_iibb == 3){
		//Alicuota
		if((importe < 2000) || (retiene == '0') || (c_iva != '1' && c_iva != '2')|| (c_iibb == 3)){
			$("#alicuota").attr("disabled","disabled");
			if (estado != '0') {
				$("#alicuota").val("0");
			}
		}else{
			$("#alicuota").removeAttr("disabled");	
		}
		//IVA (a diferencia de la alicuota, no se habilita para monotributista)
		if((importe < 2000) || (retiene == '0') || (c_iva != '1')|| (c_iibb == 3)){
			$("#id_iva").val("0");
			$("#id_iva").attr("disabled","disabled");
		}else{
			$("#id_iva").removeAttr("disabled");	
		}
		
		if(!op){
			var iibb = $("#emisor option:selected").attr("nroiibb");			
			$("#iibb").val(iibb);
		}else{
			var iibb = $("#proveedor option:selected").attr("nroiibb");
			var cuitprov = $("#proveedor option:selected").attr("nrocuit");
			var cm_porciento = $("#proveedor option:selected").attr("cm");
			
			$("#iibbprov").val(iibb);
			$("#cuitprov").html(" CUIT:"+cuitprov);
			//alert(cm_porciento);
			$("#cm").val(cm_porciento);
			$("#cm_view").val(cm_porciento);
		}
	}
	
	function calcularBI(){
		var iva 	 = parseFloat($("#id_iva").val());
		var importe  = parseFloat($("#importe").val());
		var alicuota = parseFloat($("#alicuota").val());
		var cbilateral = parseFloat($("#cm").val());
		var cm 		   = (importe * cbilateral / 100);
		//var mbilateral = importe - (importe * cbilateral / 100);
		
		var res		 = cm - ((iva * cm) / (100+iva));
		var aretener = alicuota * res / 100;
		var apagar	 = importe - aretener;
		
		$("#monto_multilateral").val(cm.toFixed(2));
		$("#base_imponible").val(res.toFixed(2));
		$("#aretener").val(aretener.toFixed(2));
		$("#apagar").val(apagar.toFixed(2));
	}
	
	$("#emisor").change(function () {
		var i = 0;
		var c = <?php echo (int)$cuenta;?>;
		$("#cuenta").children('option').hide();
		$("#cuenta").children("option[id_unidad^=" + $(this).val() + "]").show();
		
		if(i == 0 && c == 0){
			//$("#cuenta").children[0].attr("selected","selected");
			$("#cuenta").children("option[id_unidad^=" + $(this).val() + "]").attr("selected","selected");
			i=1;
		}
	});

	$("#proveedor").change(function(){
		changeIIBB(1);
	});
	
	$("#importe").change(function(){
		changeIIBB(1);
		calcularBI();
	});
	
	$(document).ready(function(){
		changeIIBB();
		changeIIBB(1);
		calcularBI();
		$("#emisor").change();
	});

	function changeAvisoPago (radioButton) {
		if (radioButton.value == 0) {
			//No enviar
			document.getElementById('id_titular_aviso_pago').disabled = true;
		} else if (radioButton.value == 2) {
			//Titulares
			document.getElementById('id_titular_aviso_pago').disabled = false;
		}
	}

	changeAvisoPago(document.form3.aviso_pago_adicional);

	
</script></td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
<script language="javascript" type="text/javascript" src="funciones.js"></script>

</html>
