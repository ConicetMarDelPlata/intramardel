<?php 
include_once("./includes/class.Conference.php");
include_once("./includes/class.User.php");
include_once("./includes/class.Email.php");
include_once("seguridad_bd.php");

function escribirLog ($textoAAgregar) {
	$textoAAgregar .= file_get_contents('envioReservas.log');
	file_put_contents('envioReservas.log', $textoAAgregar);
}
date_default_timezone_set('America/Argentina/Buenos_Aires');

escribirLog("========================================================================== \r\n");
escribirLog(date("d/m/Y - H:i:s.- ") . " Inicia el proceso. \r\n");

$bd = new Bd;
$bd->AbrirBd();
$conference	= new Conference($bd);
$user		= new User($bd);

$email	= new Email();

//$sCC = "informatica@mardelplata-conicet.gob.ar"; //, sps_mdq@hotmail.com
$subject = "Reservas de Sala";

$dSemana = date("N");
if ($dSemana == 5) {
	//Es viernes entonces busca el prox lunes
	$sFecha = date("Y-m-d",strtotime( "next monday" ));
} else {
	//busca las de mañana
	$sFecha = date("Y-m-d",strtotime( "tomorrow" ));
}

$dia = date('d',strtotime($sFecha));
$mes = date('m',strtotime($sFecha));
$year = date('Y',strtotime($sFecha));

$vTasks = $conference->getAllReservations('09','08','2022');
//$vTasks = $conference->getAllReservations($dia,$mes,$year);
error_log(json_encode($vTasks));
if($vTasks){
	$message = "<html><head><title>Reservas de Sala</title></head>
	<style>
	table{
	font-family: 'Terminal Dosis', Arial, sans-serif;
	width:700px;
	}
	table th{
	text-align:left;
	padding-left: 10px;
	font-size:13px;
	background-color:#BBBBBB;
	}
	table td{
	text-align:left;
	padding-left: 10px;
	font-size:13px;
	}
	table img{
	width:120px;
	}
	.headerList{
	background-color: rgb(89, 146, 196);
	color:white;
	text-align:center;
	font-size:17px;
	padding: 2px 10px;
	}
	</style>
	<body><table style=\"font-family:'Terminal Dosis', Arial, sans-serif;width:700px;\" >
	<tr><td class='headerList' colspan=5 style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\" ></tr>
	<tr rowspan=3>
	<td style=\"text-align:left;padding-left:10px;font-size:13px;\" ><img src='conicet120px.jpg' style=\"width:120px;\" /></td>
	<td colspan=4 style=\"text-align:left;padding-left:10px;font-size:13px;\" >CCT CONICET Mar Del Plata</td>
	</tr>
	<tr>
	<td class='headerList' colspan=5 style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\" >Listado de reservas del d&iacute;a ".$dia."/".$mes.'/'.$year."				</td>
	</tr>
	<tr>
	<th style=\"width:70px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >Inicio</th>
	<th style=\"width:70px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >Fin</th>
	<th style=\"width:440px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >T&iacute;tulo</th>
	<th style=\"width:120px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >Sala</th>
	<th style=\"width:120px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >Reservado por...</th>
	</tr>";
	foreach($vTasks as $oTask){
		$message .= "
		<tr>
			<td>".date('H:i',strtotime($oTask['horaI']))."</td>
			<td>".date('H:i',strtotime($oTask['horaF']))."</td>
			<td>".nl2br(htmlentities($oTask['titulo']))."</td>
			<td>".nl2br(htmlentities($oTask['sala']))."</td>
			<td>".utf8_decode($user->getFullName($oTask['creator']))."</td> 
		</tr>
		<tr>
			<td colspan='5' style='background-color:#CCCCCC'></td>
		</tr>";
	}
	$message .= "<tr>
	<td colspan='5' class='headerList' style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\" >Para m&aacute;s informaci&oacute;n dir&iacute;jase a su Intranet.</td>
	</tr>
	</table></body></html>";

	//$to = "victoriaganuza@gmail.com,victoria.ganuza@frasal.uy"; //, sps_mdq@hotmail.com
	$to = "mibello@conicet.gov.ar,giacconecarla@conicet.gov.ar,drodriguez@conicet.gov.ar,acarricart@conicet.gov.ar,comunicacion@mardelplata-conicet.gob.ar,prensa@mardelplata-conicet.gob.ar,
			amoyano@conicet.gov.ar,administracion@mardelplata-conicet.gob.ar,recursoshumanos@mardelplata-conicet.gob.ar,rrhh@mardelplata-conicet.gob.ar,cct@mardelplata-conicet.gob.ar,
			mesadeentradas@mardelplata-conicet.gob.ar,victoriaganuza@gmail.com";
				error_log("Mails: ".$to);         
	// More headers
	$from = 'CCT CONICET Mar Del Plata<notificaciones.conicet.mdp@gmail.com>';
	
	$to = "victoriaganuza@gmail.com"; //, sps_mdq@hotmail.com
	
	if ($email->send_email ($to,$from,$subject, $message,dirname( __FILE__ )."/images/conicet120px.jpg")){
		$conference->mail_sent($vTasks);
		escribirLog(date("d/m/Y - H:i:s.- ") . " Aviso de reservas enviado! \r\n");
	}
	else {
		escribirLog(date("d/m/Y - H:i:s.- ") . " ERROR: Hubo problemas al enviar el email de reservas \r\n");
	}
	
}else{
	escribirLog(date("d/m/Y - H:i:s.- ") . " No hay reservas que reportar \r\n");
}
escribirLog("========================================================================== \r\n");

?>
