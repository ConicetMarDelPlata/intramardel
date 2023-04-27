<?php
$to = $vUser['email'];
$subject = 'Su copia del documento';
$repEmail = 'informatica@mardelplata-conicet.gob.ar';

//$fileatt = $pdf->Output($sFileName, 'E');

$attachment = chunk_split(base64_encode(file_get_contents($sFileName)));

//$attachment = chunk_split($fileatt);
$eol = PHP_EOL;
$separator = md5(time());

$headers = 'From: CCT-CONICET Mar Del Plata <'.$repEmail.'>'.$eol;
$headers .= 'MIME-Version: 1.0' .$eol;
$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

/*$message = "--".$separator.$eol;
$message .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
$message .= "<h1>Le enviamos una copia de su documento.</h1>".$eol;
*/
$message .= "--".$separator.$eol;
$message .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
$message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
$message .= "<table>".$eol;
$message .= "	<tr>".$eol;
$message .= "		<td>".$eol;
$message .= "			<img src='http://www.mardelplata-conicet.gob.ar/mail/conicet_mini.jpg'/>".$eol;
$message .= "		</td>".$eol;
$message .= "		<td>".$eol;
$message .= "			<b>CENTRO CIENTÍFICO TECNOLÓGICO</b><br/>CONICET Mar Del Plata".$eol;
$message .= "		</td>".$eol;
$message .= "	</tr>".$eol;
$message .= "</table>".$eol;
$message .= "<hr><br/>".$eol;

$message .= "Estimado/a ".htmlentities($vUser['apellido']).", ".htmlentities($vUser['nombre']).".<br/><br/>".$eol;
$message .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Le enviamos una copia de su documento para que conserve por cualquier inconveniente que pueda suscitarse.<br/><br/><br/>".$eol;
$message .= "Saludos Cordiales.<br/><br/>".$eol;
$message .= "CCT-CONICET Mar Del Plata".$eol;

$message .= "--".$separator.$eol;
$message .= "Content-Type: application/pdf; name=\"".$sFileName."\"".$eol; 
$message .= "Content-Transfer-Encoding: base64".$eol;
$message .= "Content-Disposition: attachment".$eol.$eol;
$message .= $attachment.$eol;
$message .= "--".$separator."--";

if (mail($to, $subject, $message, $headers)){
echo "Email sent";
}

else {
echo "Email failed";
}
?>