<?php 

include_once "seguridad_bd.php";

function escribirLog ($textoAAgregar) {
	$textoAAgregar .= file_get_contents('../envioAvisosPago.log');
	file_put_contents('../envioAvisosPago.log', $textoAAgregar);
}
date_default_timezone_set('America/Argentina/Buenos_Aires');

escribirLog("========================================================================== \r\n");
escribirLog(date("d/m/Y - H:i:s.- ") . " Inicia el proceso. \r\n");

$bd = new Bd;
$bd->AbrirBd();
$ordenes = $bd->consultar_orden_pago_avisos_pendientes();

$subject = "CONICET MAR DEL PLATA - Aviso de pago";
$emailsCount = 0; //Cuento la cantidad para no superar limite por blacklists
while (($op = mysqli_fetch_assoc($ordenes)) and ($emailsCount<=80)){
	$cc = ""; //, mibello@conicet.gov.ar
	$cco = "mibello@conicet.gov.ar,amoyano@conicet.gov.ar"; //mibello@conicet.gov.ar
	$to = $op['proveedor_email'];
	$emailsCount = $emailsCount + 1;
	if ($op['no_enviar_aviso_pago_adicional'] == 0) {
		$to = $to.",".$op['titular_aviso_pago_email'];
		$emailsCount = $emailsCount + 1;
	}

	separaFecha($op['fecha_aviso_pago'],$anio, $mes, $dia, $nombreMes);
	if ($op['forma_pago'] == 1)
		$forma_pago = "un ".$op['forma_pago_descripcion'];
	else
		$forma_pago = "una ".$op['forma_pago_descripcion'];

	if ($op['condicion_venta_ce'] == 0)
		$solicitud_recibo = "Se solicita enviar recibo de pago correspondiente.";
	else
		$solicitud_recibo = "";
	
	//Copio calculos del pdf de Orden de Pago
	$fMonto 		= (float)$op['importe'];
	$fCM_Porciento 		= (float)$op['cm'];
	$fIVA			= (float)$op['iva'];
	$fAlicuota		= (float)$op['alicuota'];

	$fCM_Monto		= $fMonto * $fCM_Porciento / 100;
	$fBase_Imponible	= $fCM_Monto - (($fIVA * $fCM_Monto) / (100+$fIVA));
	$fMonto_a_Retener	= $fAlicuota * $fBase_Imponible / 100;
	$fMonto_a_Pagar		= $fMonto - $fMonto_a_Retener;

	//[Nota: El siguiente es un email de prueba, en realidad deber&iacute;a enviarse a $to_futuro]
	$message = 
	"<html>
	<head>
	<title>Aviso de Pago</title>
	<style>
		table{
		font-family:'Terminal Dosis', Arial, sans-serif;;
		width:700px;
		}		
	</style>
	</head>
	<body>
	<table style=\"font-family:'Terminal Dosis', Arial, sans-serif;\">
		<tr>
			<td style=\"padding-left:0px;\" colspan=2>
				<img src='conicet120px.jpg' />
			</td>
		</tr>
		<tr>
			<td style=\"text-align: right;margin-right: 1em;\" colspan=2>
				Mar del Plata, ".htmlentities($dia)." de $nombreMes de $anio
				<br>
				<br>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			Por el presente, se comunica que el d&iacute;a ".htmlentities($dia."/".$mes."/".$anio)." se realiz&oacute; $forma_pago a favor de ".htmlentities($op['proveedor_razon_social']).".
			<br>
			<br>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			<table style=\"font-family:'Terminal Dosis', Arial, sans-serif;border:2px rgb(89, 146, 196) solid;\">
				<tr>
					<td colspan=2 style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding: 2px 10px;\">
						Detalle del pago
					</td>
				</tr>
				<tr>
					<td colspan=2>
						Nro. factura/comprobante: ".htmlentities($op['factura'])."
					</td>
				</tr>
				<tr>
					<td>
						".ucwords($op['forma_pago_descripcion'])." a pagar
					</td>
					<td style=\"text-align: right;margin-right: 1em;background-color:#BBBBBB;\">
						".$op['signoMoneda']." ".(number_format($fMonto_a_Pagar, 2, ',' , '.'))."
					</td>
				</tr>
				<tr>
					<td>
						Retenci&oacute;n de Ingresos Brutos
					</td>
					<td style=\"text-align: right;margin-right: 1em;background-color:#BBBBBB;\">
						".$op['signoMoneda']." ".(number_format($fMonto_a_Retener, 2, ',' , '.'))."
					</td>
				</tr>
				<tr>
					<td>
						<b>Importe total</b>
					</td>
					<td style=\"text-align: right;margin-right: 1em;background-color:#BBBBBB;\">
						<b>".$op['signoMoneda']." ".(number_format($fMonto_a_Pagar+$fMonto_a_Retener, 2, ',' , '.'))."</b>
					</td>
				</tr>
			</table>
			</td>
		</tr>
		<!--<tr>
			<td  colspan=2 style=\"background-color:rgb(89, 146, 196);\" >
			</td>
		</tr>-->
		<tr>
			<td colspan=2>
				<br>
				<b>$solicitud_recibo</b><br>
				<!--<br>
				Horario de atenci&oacute;n y pago a proveedores: Martes y jueves de 9 hs. a 13 hs.<br>
				Domicilio: Moreno 3527 piso 3, Mar del Plata.
				<br>-->
				<br>
			</td>
		</tr>
		<tr>
			<td colspan=2 style=\"text-align: right;margin-right: 1em;\">
				CCT Conicet Mar delPlata
				<br>
				<br>
			</td>			
		</tr>
		<tr>
			<td colspan=2 style=\"text-align: center;\">
				<br>
				<br>
				[Este es un email autom&aacute;tico del sistema. Por favor no responder a este remitente.]
			</td>
		</tr>
	</table>
	</body>
	</html>";
	//Adjunto recibo de retencion si corresponde
	$recibo_retencion = "";
	if ($op['cert_ret'] != 0) {
		//No haga esto en sus casas: genero el recibo de retencion seteando las variables GET
		$_GET['numero_orden_pago']=$op['numero_orden_pago'];
		$_GET['anio_numero_orden_pago']=$op['anio_numero_orden_pago'];
		$_GET['confecciono']=$op['confecciono'];
		$_GET['toFile']='F';
		include ("recibo_retencion_pdf.php");
		//echo 'recibo_retencion_pdf.php?numero_orden_pago='.$op['numero_orden_pago'].'&anio_numero_orden_pago='.$op['anio_numero_orden_pago'].'&confecciono='.rawurlencode($op['confecciono']).'&toFile=F';
		$recibo_retencion = 'recibosRetencion/reciboRetencion'.$op['cert_ret'].'.pdf';
	}

	$cc = "victoriaganuza@gmail.com"; //, mibello@conicet.gov.ar
	$cco = "victoriaganuza@gmail.com"; //mibello@conicet.gov.ar
	$to ="victoriaganuza@gmail.com,vicdepatas@gmail.com";/**/

	if ($mess = send_email ($to,$cc,$cco,$subject,$message,dirname( __FILE__ )."/images/conicet120px.jpg",$recibo_retencion)){
		escribirLog(date("d/m/Y - H:i:s.- ") . " Aviso de pago enviado! OP: ".$op['numero_orden_pago']."/".$op['anio_numero_orden_pago']." \r\n");
		//"correo enviado";
		if (!$bd->modificar_op_aviso_pago_enviado($op['numero_orden_pago'],$op['anio_numero_orden_pago'],1,$error)) {
			escribirLog(date("d/m/Y - H:i:s.- ") . " ERROR: Hubo problemas al grabar 'aviso de pago enviado' OP: ".$op['numero_orden_pago']."/".$op['anio_numero_orden_pago']." \r\n");
		}
	}
	else {
		//"correo no enviado";
		escribirLog(date("d/m/Y - H:i:s.- ") . " ERROR: Hubo problemas al enviar el email de aviso de pago OP: ".$op['numero_orden_pago']."/".$op['anio_numero_orden_pago']." \r\n");
		escribirLog($mess." \r\n");
	}

}

escribirLog(date("d/m/Y - H:i:s.- ") . " Fin del proceso. Cantidad de emails enviados: $emailsCount \r\n");
escribirLog("========================================================================== \r\n");

?>
