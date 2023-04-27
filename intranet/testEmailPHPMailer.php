<?php
//include_once "seguridad_bd.php";

/*PHP Mailer*/
/*ini_set("include_path",ini_get("include_path").PATH_SEPARATOR.dirname( __FILE__ )."/includes");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//TODO PROBAR PEAR CON OPEN SSL
//$to,$cc,$bcc string separado por coma con las direcciones de email
//$attach string separado por coma con los path de archivos a atachar
function SendMailPHPMailer($to,$cc,$subject,$body,$attach,&$error) {
	$error = "";
	$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
	try {
	    //Server settings
	    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
	    $mail->isSMTP();                                      // Set mailer to use SMTP
	    $mail->Host = 'mail.mardelplata-conicet.gob.ar';  // Specify main and backup SMTP servers
	    $mail->SMTPAuth = true;                               // Enable SMTP authentication
	    $mail->Username = 'no-reply@mardelplata-conicet.gob.ar';                 // SMTP username
	    $mail->Password = 'n0r3sp0nd3r';                           // SMTP password
	    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	    $mail->Port = 465;                                    // TCP port to connect to

	    $mail->setFrom('no-reply@mardelplata-conicet.gob.ar');
	    $mail->addReplyTo('no-reply@mardelplata-conicet.gob.ar');

	    //Recipients
	    $to_array = explode(',', $to);
	    foreach($to_array as $address)
	    {
		if (trim($address) != '') $mail->addAddress($address);
	    }
	    $cc_array = explode(',', $cc);
	    foreach($cc_array as $address)
	    {
	    	if (trim($address) != '') $mail->addCC($address);
	    }
	   
	    //$mail->addBCC('bcc@example.com');

	    //Attachments
	    $attach_array = explode(',', $attach);
	    foreach($attach_array as $file)
	    {
	    	if (trim($file) != '') $mail->addAttachment($file); 
	    }
	    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = $subject;
	    $mail->Body    = $body;
	    $mail->AltBody = $body; //plain text

	    $mail->send();

	    echo 'Message has been sent';
	    return true;
	} catch (Exception $e) {
	    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
	    $error = "Error en el envio del email ".$mail->ErrorInfo;
	    return false;
	}
}*/

//echo SendMailPHPMailer("sopranoeli@gmail.com","sopranoeli@hotmail.com","2da Prueba email de Vanina","Cuerpo del mensaje 2.0","",$error);
//echo $error;
	/**
	 * send_email
	 * Sends mail via SMTP
	 * uses Pear::Mail
	 * @author Andrew McCombe <andrew@iweb.co.uk>
	 * 
	 * @param string $to Email address of the recipient in 'Name <email>' format
	 * @param string $from Email address of sender
	 * @param string $subject Subject of email
	 * @param string $body Content of email
	 * 
	 * @return boolean if false, error message written to error_log
	 */
	function send_email($to, $subject, $bodyHTML, $image, $attach) {
		require_once "Mail.php";
		require_once "Mail/mime.php";    
	 	$crlf = "\n";
		$from = 'CCT CONICET Mar Del Plata <no-reply@mardelplata-conicet.gob.ar>';

		 // create a new Mail_Mime for use
		 $mime = new Mail_mime($crlf); 
		 // define body for Text only receipt
		 //$mime->setTXTBody($text); 
		 // define body for HTML capable recipients
		 $mime->setHTMLBody($bodyHTML);
		 
		 // specify a file to attach below, relative to the script's location
		 // if not using an attachment, comment these lines out
		 // set appropriate MIME type for attachment you are using below, if applicable
		 // for reference see http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types

		 if ($attach != "") {
			 //TODO en un futuro cambiar el mime type segun tipo de attach	 
			 $mimetype = "application/pdf";
			 $mime->addAttachment($attach, $mimetype); 
		}

		 $mime->addHTMLImage(file_get_contents($image),mime_content_type($image),basename($image),false);
		 // specify the SMTP server credentials to be used for delivery
		 // if using a third party mail service, be sure to use their hostname
		 $host = "ssl://mail.mardelplata-conicet.gob.ar";
		 $port    =  "465"; //con el 587 465 (ssl) no funcionaba
		 $username = "no-reply@mardelplata-conicet.gob.ar";
		 $password = "n0r3sp0nd3r";
		 /*$host = "smtp.gmail.com";
		 $port    =  "587";
		 $username = "fileserver.conicet@gmail.com";
		 $password = "FILE32SERVER67";*/
		 
		 $headers = array ('From' => $from,
		  		'To' => $to,
		  		'Subject' => $subject);
		 $smtp = Mail::factory('smtp',
			array ('host' => $host,
				'port'=>$port,
				'auth' => true,
				'username' => $username,
				'password' => $password));

		 $body = $mime->get();
		 $headers = $mime->headers($headers); 
		 
		 $mail = $smtp->send($to, $headers, $body);
		 
		
		if (PEAR::isError($mail)) {
			echo ($mail->getMessage());
		        return false;
		} else {
		        return true; 
		}
	}

echo send_email ("victoriaganuza@gmail.com","Prueba envio","Cuerpo del mensaje",dirname( __FILE__ )."/images/conicet10a170px.jpg","");

?>
