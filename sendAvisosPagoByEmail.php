<?php 

include "seguridad_bd.php";

$subject = "CONICET MAR DEL PLATA - Aviso de pago";

//$sFecha = date("Y-m-d");
//$sFecha = date("Y-m-d",strtotime(date("Y-m-d", strtotime($sFecha)) . " +1 day"));

//$vTasks = $oCalendar->getTasks(null, $sFecha);
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
<tr><td class='headerList' colspan=4 style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\" ></tr>
<tr rowspan=3>
<td style=\"text-align:left;padding-left:10px;font-size:13px;\" ><img src='conicet120px.jpg' style=\"width:120px;\" /></td>
<td colspan=3 style=\"text-align:left;padding-left:10px;font-size:13px;\" >CCT CONICET Mar Del Plata</td>
</tr>
<tr>
<td class='headerList' colspan=4 style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\" >Listado de reservas del d&iacute;a ".$sFecha."				</td>
</tr>
<tr>
<th style=\"width:70px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >Inicio</th>
<th style=\"width:70px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >Fin</th>
<th style=\"width:440px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >T&iacute;tulo</th>
<th style=\"width:120px;text-align:left;padding-left:10px;font-size:13px;background-color:#BBBBBB;\" >Reservado por...</th>
</tr>";
/*foreach($vTasks as $oTask){
	$message .= "
	<tr>
		<td>".$oTask['inicio']."</td>
		<td>".$oTask['fin']."</td>
		<td>".nl2br(htmlentities($oTask['titulo']))."</td>
		<td>".utf8_decode($oUser->getFirstName($oTask['creator']))." ".utf8_decode($oUser->getLastName($oTask['creator']))."</td> 
	</tr>
	<tr>
		<td colspan='4' style='background-color:#CCCCCC'></td>
	</tr>";
}*/
$message .= "<tr>
<td colspan='4' class='headerList' style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding-top:2px;padding-bottom:2px;padding-right:10px;padding-left:10px;\" >Para m&aacute;s informaci&oacute;n dir&iacute;jase a su Intranet.</td>
</tr>
</table></body></html>";

$to = "sopranoeli@gmail.com,sopranoeli@hotmail.com";
//$to = $oUser->getAllEmails("informatica@mardelplata-conicet.gob.ar"); //, sps_mdq@hotmail.com

//$headers .= 'Cc: myboss@example.com' . "\r\n";
//if($vTasks){
	if (send_email ($to,$subject, $message,"")) //dirname( __FILE__ )."/imagenes/conicet120px.jpg"
		echo "correo enviado";
	else echo "correo no enviado";
//}else{
//	echo "NO TASKS";
//}


?>
