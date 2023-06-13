<?php
	// CLASS CALENDAR
	Class Email{
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

		function send_email($to, $from, $subject, $bodyHTML, $image) {
			//return true;
			require_once "Mail.php";
			require_once "Mail/mime.php";    
			$crlf = "\n";
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

			//$file = "imagenes/conicet120px.jpg";
			//$mimetype = "image/jpeg";
			//$mime->addAttachment($file, $mimetype); 
			$mime->addHTMLImage(file_get_contents($image),mime_content_type($image),basename($image),false);
			// specify the SMTP server credentials to be used for delivery
			// if using a third party mail service, be sure to use their hostname
			$host = "smtp.gmail.com";
			$port    =  "587";
			$username = "notificaciones.conicet.mdp@gmail.com";
			$password = "pxtffyhjjfqscpwg";
			//$password = "FILE32SERVER67";
			
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
				
				error_log($mail->getMessage());
				return false;
			} else {
				return true; 
			}

		}
	}
?>
