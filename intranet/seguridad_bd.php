<?php
	//Vani: comento esta linea porque da error, las variables no existen
        //if ($_SERVER['HTTP_ACUNETIX_PRODUCT'] ||    $_SERVER['HTTP_ACUNETIX_SCANNING_AGREEMENT'] ||    $_SERVER['HTTP_ACUNETIX_USER_AGREEMENT']){exit;}
        
	//____________________________________________________________________________________
	// INICIO FUNCIONES GENERALES UTILIZADAS EN DIVERSOS SCRIPTS
	//____________________________________________________________________________________
	// Fecha: 24 de Noviembre de 2012
	// Autor: .j
	// Funci&oacute;n general usada para convertir fechas en formato SQL a PHP y HTML

	function convertir_fecha($fecha_datetime){
		//echo "En convertir fecha: ".$fecha_datetime.'<br>';
		$fecha = preg_split("/\/|-/", $fecha_datetime);
	//	print_r($fecha); 
		if(strlen($fecha[0]) == 4){
			$fecha_convertida=$fecha[2].'-'.$fecha[1].'-'.$fecha[0];
		}else{
			$fecha_convertida=$fecha[0].'-'.$fecha[1].'-'.$fecha[2];
		}
		return $fecha_convertida;
	}

	function convertir_fecha_sql($fecha_datetime){
		$fecha = preg_split("/\/|-/", $fecha_datetime);
		if(strlen($fecha[0]) != 4){
			$fecha_convertida=$fecha[2].'-'.$fecha[1].'-'.$fecha[0];
		}else{
			$fecha_convertida=$fecha[0].'-'.$fecha[1].'-'.$fecha[2];
		}
		return $fecha_convertida;
	}


	function escribirLog ($textoAAgregar) {
		$textoAAgregar .= file_get_contents('../errorLog.log');
		file_put_contents('../errorLog.log', $textoAAgregar);
	}

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
	function send_email($to, $cc, $cco, $subject, $bodyHTML, $image, $attach) {
		require_once "Mail.php";
		require_once "Mail/mime.php";    
	 	$crlf = "\n";
		$from = 'CCT CONICET Mar Del Plata <notificaciones.conicet.mdp@gmail.com>';

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
		 /*$host = "mail.mardelplata-conicet.gob.ar";
		 $port    =  "587"; //con el 465 (ssl) no funcionaba
		 $username = "no-reply@mardelplata-conicet.gob.ar";
		 $password = "n0r3sp0nd3r";*/
		 $host = "smtp.gmail.com";
		 $port    =  "587";
		 $username = "notificaciones.conicet.mdp@gmail.com";
		 $password = "pxtffyhjjfqscpwg";
		 
		 $headers = array ('From' => $from,
		  		'To' => $to,
		  		'Cc' => $cc,
		  		'Cco' => $cco,
		  		'Subject' => $subject);
		 $smtp = Mail::factory('smtp',
			array ('host' => $host,
				'port'=>$port,
				'auth' => true,
				'username' => $username,
				'password' => $password));

		 $body = $mime->get();
		 $headers = $mime->headers($headers); 
		 
		 $recipients = "";
		 if ($to != "") $recipients = $recipients.$to;
		 if ($cc != "") $recipients = $recipients.",".$cc;
		 if ($cco != "") $recipients = $recipients.",".$cco;
		 $mail = $smtp->send($recipients, $headers, $body);
		 
		
		if (PEAR::isError($mail)) {
			error_log($mail->getMessage());
			echo ($mail->getMessage());
		        return false;
		} else {
		        return true; 
		}
	}

	function separaFecha($fecha_SQL,&$anio,&$mes,&$dia,&$nombreMes) {

		$meses = array(
		    "1" => "enero",
		    "2" => "febrero",
		    "3" => "marzo",
		    "4" => "abril",
		    "5" => "mayo",
		    "6" => "junio",
		    "7" => "julio",
		    "8" => "agosto",
		    "9" => "septiembre",
		    "10" => "octubre",
		    "11" => "noviembre",
		    "12" => "diciembre",							
		);

		$fecha = explode('-', $fecha_SQL);
		$anio = $fecha[0];
		$mes = $fecha[1];
		if ($mes < 10) $mes = substr($mes, 1,1);
		$nombreMes = $meses[$mes];
		$dia = $fecha[2];
		if ($dia < 10) $dia = substr($dia, 1,1);
		if ($dia == "1") {
			$dia = "1º";
		}
	}

	// FIN FUNCIONES GENERALES


	// INICIO DE CLASES REFERIDAS A SESSIONES Y BD (USUARIOS Y OTROS, ETC.)
	//____________________________________________________________________________________
	// Fecha: 27 de Octubre de 2012
	// Autor: .j
	// Clase Sesion. Utilizada para establecer y chequear las opciones 
	// seguridad en los scripts de acceso restringido.
	// Los m&eacute;todos utilizados son los siguientes:
	// __constructor: inicia una sesion para un usuario registrado.
	// __destructor: elimina la sesion previamente inciada con el constructor.
	// chequear_sesion: utilizada en el inicio de cada script para verificar que el 
	// se est&aacute; accediendo al script habiendose inciado previamente una sesion.

	Class Sesion{
	
		function nueva_sesion($usuario, $contrasenia){
			$bd = new Bd();
			$bd->AbrirBd();

			session_start();
			$_SESSION["autentificado"]="si";
			$_SESSION["usuario"]=$usuario;
			$_SESSION["contrasenia"]=$contrasenia;
			//Obtengo el id para usar luego en varios llamados
			
			$userData = $bd->getUserByUserName($usuario);
			$_SESSION["id_usuario"] = $userData['id_usuario'];
		//			$_SESSION["nivel_acceso"]=$nivel_acceso;	
			
			/*echo "user " . $_SESSION["autentificado"] . '<br>';
			echo "user " .$_SESSION["usuario"] . '<br>';
			echo "user " . $_SESSION["contrasenia"] . '<br>';
			echo "user " . $_SESSION["nivel_acceso"] . '<br>';*/
		}

		function __destructor(){
			session_start();
			session_destroy();
		}

		function chequear_sesion(){
			session_start();
			if (isset($_SESSION["autentificado"]) and $_SESSION["autentificado"]=="si" ){
				return TRUE;
			}else
			{
				return FALSE;
			}
		}

	} // Fin Clase Sesion
//____________________________________________________________________________________

	// Fecha: 27 de Octubre de 2012
	// Autor: .j
	// Clase BD. Utilizada para abrir una bd, cerrarla y acceder a diferentes m&eacute;todos.
	// propiedades:
	// 	$bd: variable con el nombre de la bd. 
	// Los m&eacute;todos son:
	// 	__constructor: abre la BD.
	// 	__destructor: cierra la bd.
	//	usuario_registrado: chequea que el nombre de usuario este ingresado en la bd.
	//			    devuelve TRUE o FALSE
	//	nivel_acceso: retorna el nivel de acceso del usuario para determinar 
	//	a que opciones tiene permisos.

	Class Bd{
    //Actualizado a PHP > 7 
    //Victoria Ganuza
    //Fecha: 20/09/2022

    public $conn;
    public $userData;
		
		public function actualizarRegistroUsuario($vData){
			$iUserID 	= $vData['i'];
			$sUserName 	= $vData['sCUIT'];;
			$sPass 		=  password_hash($vData['sPass'], PASSWORD_ARGON2I);
			$sLastName	= $vData['sLastName'];
			$sName		= $vData['sName'];
			$sEmail		= $vData['sEmail'];
			
			$sSQL = "UPDATE usuario SET nombre_usuario = '$sUserName', contrasenia = '$sPass', nombre='$sName', apellido='$sLastName', email='$sEmail' WHERE id_usuario = $iUserID";
			if($this->excecuteQuery($sSQL)){
					$oRet['ok']=true;
			}else{
				$oRet['ok']=false;
				$oRet['msj']="Error al registrar el usuario.";
			}
			return $oRet;
		}
		
		public function enviarDatos($sEmail, $sHTML){
			$headers = "From: no_reply@mardelplata-conicet.com.ar\r\nReply-To: no_reply@mardelplata-conicet.com.ar\r\n"; 
			$headers .= "Content-type: text/html\r\n";
			$subject = "Sus datos de acceso";

			$mail_sent = @mail( $sEmail, $subject, $sHTML, $headers ); 
			return $mail_sent ? true : false; 
		}

		
		public function getUserByUserName($sUserName){
      //Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$sSQL = "SELECT * FROM usuario WHERE nombre_usuario = '$sUserName'";
			$res =  $this->excecuteQuery($sSQL);
			$row = mysqli_fetch_assoc($res);
			if($row){
				return $row;
			}else{
				return false;
			}
		}
		
		public function getPanel($sPanel, $iID = null){
      //Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$vData = [];
			if($iID){
				$sWhere = " WHERE id = $iID ";
			}else{
				$sWhere = "";
			}
			
			$sSQL = "SELECT * FROM $sPanel $sWhere ORDER BY id";

			$res = $this->excecuteQuery($sSQL);
			if(!$iID){
				while($row = mysqli_fetch_assoc($res)){
					$vData[] = $row;
				}		
			}else{
				$vData = mysqli_fetch_assoc($res);
			}
			return $vData;
		}
		
		public function getOpRetention($sFIni, $sFFin, $sCUIT){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$sFIni = convertir_fecha_sql($sFIni);
			$sFFin = convertir_fecha_sql($sFFin);
			$sSQL="
			SELECT
				u.id_unidad_ejecutora, 
				u.nombre AS unidad_nombre, 
				u.cuit AS unidad_cuit,
				u.iibb AS unidad_iibb,
				p.id_proveedor AS proveedor_id, 
				p.cuit AS proveedor_cuit, 
				p.`razon_social` AS proveedor_razon_social,
				p.iibb AS proveedor_iibb,
				p.`nro_iibb` AS proveedor_nro_iibb,
				op.`numero_orden_pago` AS op_numero, 
				op.`fecha` AS op_fecha, 
				op.`anio_numero_orden_pago` AS op_anio, 
				op.`proveedor` AS op_proveedor, 
				op.`importe` AS op_importe, 
				op.cm AS op_cm,
				op.iva AS op_iva, 
				op.`alicuota` AS op_alicuota, 
				op.`cert_ret` AS op_cert_ret, 
				op.`estado` AS op_estado,
				op.asignacion_rendicion,
				bc.`nro_cuenta` AS cuenta
			FROM orden_pago op 
			LEFT JOIN unidad_ejecutora u ON op.id_unidad_ejecutora = u.id_unidad_ejecutora
			LEFT JOIN proveedor p ON op.proveedor = p.id_proveedor 
			LEFT JOIN unidad_cuentas bc ON op.cuenta = bc.id 
			WHERE 
				u.cuit = '$sCUIT' AND
				op.fecha >= '$sFIni' AND
				op.fecha <= '$sFFin' AND
				op.cert_ret <> 0
			ORDER BY op.`cert_ret`"; //Vanina: agrego este order by para que funcione el reporte arba
			$r = $this->excecuteQuery($sSQL);
			while($row = mysqli_fetch_array($r)){
				$vData[] = $row;
			}
			return $vData??[];
		}
		
		public function getDCPTData($iTrimIni, $iTrimFin, $iYear=null, $iUE){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sWhereTrim = '';
			if($iUE !== 0){
				$sWhereUE = " (ad.ue = $iUE OR adi.ui = $iUE) AND ";
				$sWhereUE = " IF(adi.ui = 0, ad.ue = $iUE, adi.ui = $iUE) ";
				$sOrderUE = "";
			}else{
				$sWhereUE = "";
				$sOrderUE = " ad.ue,";
			}
			
			if (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/', $iTrimIni)){
				$iTrimIni = convertir_fecha_sql($iTrimIni);
				$sWhereTrim =" AND adi.fecha_compra >= '$iTrimIni' ";
			}else{
				if($iTrimIni){
					$sWhereTrim =" AND MONTH(fecha_cierre) >= $iTrimIni";
				}
			}
			
			if(preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/', $iTrimFin)){
				$iTrimFin = convertir_fecha_sql($iTrimFin);
				if($sWhereTrim){
					$sWhereTrim .=" AND adi.fecha_compra <= '$iTrimFin'";
				}else{
					$sWhereTrim =" adi.fecha_compra <= '$iTrimFin'";
				}
			}else{
				if($iTrimFin){
					if($sWhereTrim){
						$sWhereTrim .=" AND MONTH(fecha_cierre) <= $iTrimFin";
					}else{
						$sWhereTrim =" MONTH(fecha_cierre) <= $iTrimFin";
					}
				}
			}
			
			if($iYear){
				$sWhereYear = " AND YEAR(fecha_cierre) = $iYear AND";
			}else{
				$sWhereYear = "";
			}
			$sSQL="
				SELECT 
					adi.cant,
					adi.descripcion,
					adi.serie,
					adi.fecha_compra,
					adi.moneda,
					adi.marca,
					adi.modelo,
					adi.importe,
					ad.id as id_donacion,
					ad.res_oto,
					CONCAT(ti.apellido,', ',ti.nombre) AS titular,
					ue.nombre
					
				FROM anexo_donacion_items adi
				INNER JOIN anexo_donacion ad ON ad.id = adi.id_ad
				INNER JOIN unidad_ejecutora ue ON ad.ue = ue.id_unidad_ejecutora
				INNER JOIN titular ti ON ad.titular = ti.id_titular
				WHERE 
					$sWhereUE
					$sWhereYear
					$sWhereTrim
				ORDER BY $sOrderUE adi.fecha_compra ASC
			";
			//echo $sSQL;
			$res = $this->excecuteQuery($sSQL);
			if($res){
				while ($row = mysqli_fetch_assoc($res)){
					$vData[] = $row;
				}
				return $vData ?? [];
			}else{
				return false;
			}
		}
		
		function checkAnexoId($id){
			// Fecha: 26-09-2018
			// Autor: Vanina
			// Devuelve true si no existe un anexo con id igual al
			//parametro 1.

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			
			$q = '	SELECT 
					count(*) as cant 
				FROM 
					anexo_donacion
				WHERE 
					id = ' . $id;
			try{
				$r = $this->excecuteQuery($q);			
				$row = mysqli_fetch_array($r);
				$cant = $row['cant'];
				mysqli_free_result($r);
				return $cant==0;
			} catch (Exception $e){
				error_log('Error al chequear');
				error_log($e->message());
			}
			
		}
			
			
		public function addAnexo($vDatos){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$iID 		= $vDatos['iID'];
			$sSub 		= addslashes($vDatos['sSubsidio']);
			$iTitular 	= addslashes($vDatos['iTitular']);
			$iUE 		= $vDatos['iUE'];
			$dFecha 	= date("Y-m-d");
			$sRO 		= addslashes(nl2br($vDatos['sRO']));
			$sLab 		= addslashes(nl2br($vDatos['lab']));

			//Nota Vani: Tomo los datos actuales de la unidad de investigacion y administracion
			//para luego usar en el pdf y que sean fijas por mas que cambien
			
			$vUeAdm = $this->getUE(13);
			$sUeAdmDomicilio = $vUeAdm['domicilio'];
			$vUeInv = $this->getUE((int)$iUE);
			$sUeInvDomicilio = $vUeInv['domicilio'];
			$sUeInvNombre = $vUeInv['nombre'];
			
			$sSQL = "INSERT INTO anexo_donacion 
						(id,
						subsidio,
						titular,
						ue,
						estado,
						fecha,
						res_oto,
						fecha_cierre,
						lab,
						ue_adm_domicilio,
						ue_inv_domicilio,
						ue_inv_nombre)
					VALUES(
						'$iID',
						'$sSub', 
						$iTitular, 
						$iUE, 
						1, 
						'$dFecha', 
						'$sRO',
						null,
						'$sLab',
						'$sUeAdmDomicilio',
						'$sUeInvDomicilio',
						'$sUeInvNombre')";
			try{
				$res = $this->excecuteQuery($sSQL);
				if($res === false){
					throw new Exception('Error al insertar en la BD.');
				}
			}catch(Exception $e){
				error_log($e->getMessage());
			}
		}

		public function delAnexo($iID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			if($this->delAnexoItems($iID)){
				$sSQL = "DELETE FROM anexo_donacion WHERE id=$iID";
				try{
					$res = $this->excecuteQuery($sSQL);
					if($res === false){
						throw new Exception('Error al eliminar Anexo Donaci&oacute;n.');
					}
				}catch(Exception $e){
					return $e->getMessage();
				}
			}
		}

		public function changeAnexoState($iID, $bState, $dFecha_Cierre = null){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			if(!$dFecha_Cierre){
				$dFecha_Cierre = "NULL";
			}else{
				$dFecha_Cierre = "'$dFecha_Cierre'";
			}
			$sSQL = "UPDATE anexo_donacion SET estado=$bState, fecha_cierre = $dFecha_Cierre WHERE id=$iID";
			try{
				$res = $this->excecuteQuery($sSQL);
				if($res === false){
					return false;
				}else{
					return true;
				}
			}catch(Exception $e){
				return $e->getMessage();
			}
		}

		public function addAnexoItem($vDatos){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$iIDAD 		= $vDatos['id'];
			$dFCompra 	= convertir_fecha_sql($vDatos['dFechaCompra']);
			$iCant 		= $vDatos['iCant'];
			$sDesc 		= addslashes($vDatos['sDesc']);
			$sModelo	= addslashes($vDatos['sModelo']);
			$sMarca		= addslashes($vDatos['sMarca']);
			$sSerie		= addslashes(nl2br($vDatos['sSerie']));
			$iImporte	= $vDatos['iImporte'];
			$iUI		= $vDatos['iUI']?$vDatos['iUI']:0;
			//Si la UE no es CCT no viene, se graba en 0.
			//O si no quieren cambiarla viene en -1
			//Como es alta si o si tiene que tener valor!
			if ($iUI != 0 and $iUI != -1) {
				$vUeInv = $this->getUE((int)$iUI);
				$sUINombre = $vUeInv['nombre'];
			}
			else {	$iUI = 0; //Si viene en -1 la seteo en 0
				$sUINombre = "";};
			$iMoneda	= 1; //$vDatos['iMoneda'];
			$iEnviado	= 0;
			
			$sSQL = "INSERT INTO anexo_donacion_items
					(id, 
					id_ad,
					orden,
					fecha_compra,
					cant,
					descripcion,
					modelo,
					marca,
					serie,
					importe,
					moneda,
					enviado,
					ui,
					ui_nombre)
				 VALUES
					(0, 
					$iIDAD, 
					-1, 
					'$dFCompra', 
					$iCant, 
					'$sDesc', 
					'$sModelo', 
					'$sMarca', 
					'$sSerie', 
					$iImporte, 
					$iMoneda, 
					$iEnviado, 
					$iUI,
					'$sUINombre')";
			try{
				$res = $this->excecuteQuery($sSQL);
				if($res === false){
					//return $res;
					throw new Exception(mysqli_error());
				}else{
					return true;
				}
			}catch(Exception $e){
				echo $e->getMessage(); exit;
			}
		}

		public function updateAnexoItem($vDatos,$iItemID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$dFCompra 	= convertir_fecha_sql($vDatos['dFechaCompra']);
			$iCant 		= $vDatos['iCant'];
			$sDesc 		= addslashes($vDatos['sDesc']);
			$sModelo	= addslashes($vDatos['sModelo']);
			$sMarca		= addslashes($vDatos['sMarca']);
			$sSerie		= addslashes(nl2br($vDatos['sSerie']));
			$iImporte	= $vDatos['iImporte'];
			$iUI		= $vDatos['iUI']?$vDatos['iUI']:0;
			//Si la UE no es CCT no viene, se graba en 0.
			//O si no quieren cambiarla viene en -1
			if ($iUI != 0 and $iUI != -1) {
				$vUeInv = $this->getUE($iUI);
				$sUINombre = $vUeInv['nombre'];
			} else {
				$sUINombre = "";
			}
			
			$sSQL = "UPDATE anexo_donacion_items 
					SET fecha_compra = '$dFCompra', 
					cant = $iCant, descripcion ='$sDesc', 
					modelo = '$sModelo', 
					marca = '$sMarca', 
					serie = '$sSerie', 
					importe = $iImporte ";
			if ((int)$iUI != -1) {
				$sSQL = $sSQL. " ,ui = $iUI, 
						ui_nombre = '$sUINombre' ";
			}
			$sSQL = $sSQL. " WHERE id = $iItemID";
			
			try{
				$res = $this->excecuteQuery($sSQL);
				if($res === false){
					return $res;
				}else{
					return true;
				}
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}

		public function sendAnexoItem($iItemID, $iVal = 1){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "UPDATE anexo_donacion_items SET enviado = $iVal WHERE id = $iItemID";
			
			try{
				$res = $this->excecuteQuery($sSQL);
				if($res === false){
					return $res;
				}else{
					return true;
				}
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}

		public function haveAnexoItemsNotSended($iID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "SELECT * FROM anexo_donacion_items WHERE id_ad = $iID AND enviado = 0";
			
			try{
				$res = $this->excecuteQuery($sSQL);
				if($res === false){
					return $res;
				}else{
					if(mysqli_fetch_assoc($res)){
						return true;
					}else{
						return false;
					}
				}
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}

		public function delAnexoItem($iItemID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "DELETE FROM anexo_donacion_items WHERE id = $iItemID";
			
			try{
				$res = $this->excecuteQuery($sSQL);
				if($res === false){
					return $res;
				}else{
					return true;
				}
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}

		public function delAnexoItems($iID){
			$sSQL = "DELETE FROM anexo_donacion_items WHERE id_ad = $iID";
			
			try{
				$res = $this->excecuteQuery($sSQL);
				if($res === false){
					return $res;
				}else{
					return true;
				}
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}

		public function getAnexos($iUserID, $iSearchID=null, $iSearchUI=null, $iSearchStatus=null, $iFirstReg=0, $iRegPerPage=10, &$iNumRows, &$iLastPage){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$sWhere = "";
			if($iSearchID){
				if(!$sWhere){
					$sWhere = "WHERE id = $iSearchID";
				}else{
					$sWhere .= " AND id = $iSearchID";
				}
			}

			if($iSearchUI){
				if(!$sWhere){
					$sWhere = "WHERE ue = $iSearchUI";
				}else{
					$sWhere .= " AND ue = $iSearchUI";
				}
			}
			
			if($iSearchStatus != 2){
				if(!$sWhere){
					$sWhere = "WHERE estado = $iSearchStatus";
				}else{
					$sWhere .= " AND estado = $iSearchStatus";
				}
			}
			
			$sLimit = " LIMIT $iFirstReg, $iRegPerPage";

			$sSQL = "SELECT SQL_CALC_FOUND_ROWS * FROM anexo_donacion $sWhere ORDER BY id ASC $sLimit";
			$res = $this->excecuteQuery($sSQL);
			$res1= $this->excecuteQuery("SELECT FOUND_ROWS() AS iTotal");
			$row1= mysqli_fetch_assoc($res1);
			$iNumRows = (int)$row1['iTotal'];
			$iLastPage = ceil($iNumRows / $iRegPerPage);
			//echo $sSQL;
			if($res){
				while($row = mysqli_fetch_assoc($res)){
					$vData[] = $row;
				}		
			}
			return $vData;
			
		}

		public function getAnexoHeader($iID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "SELECT * FROM anexo_donacion WHERE id = $iID";
			$res = $this->excecuteQuery($sSQL);
			return mysqli_fetch_assoc($res);
		}

		public function updateAnexoHeader($oData){

			//Nota Vani: Tomo los datos actuales de la unidad de investigacion y administracion
			//para luego usar en el pdf y que sean fijas por mas que cambien

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			
			$vUeAdm = $this->getUE(13);
			$sUeAdmDomicilio = $vUeAdm['domicilio'];
			//Si no elije una distinta de 0 significa que no cambia de UE y no debo actualizarla
			if ((int)$oData['iUE'] != 0) {
				$vUeInv = $this->getUE((int)$oData['iUE']);
				$sUeInvDomicilio = $vUeInv['domicilio'];
				$sUeInvNombre = $vUeInv['nombre'];
			}

			$sSQL = "UPDATE anexo_donacion 
				SET subsidio = '".$oData['sSubsidio']."', ".
					"titular = ".$oData['iTitular'].", ".
					"res_oto = '".$oData['sResOto']."', ".
					"lab = '".$oData['lab']."', ".
					"ue_adm_domicilio = '".$sUeAdmDomicilio."' ";

			if ((int)$oData['iUE'] != 0) {
				$sSQL = $sSQL.", ue = ".$oData['iUE'].", ";
				$sSQL = $sSQL." ue_inv_nombre = '".$sUeInvNombre."', ";
				$sSQL = $sSQL." ue_inv_domicilio = '".$sUeInvDomicilio."' ";}
			
			$sSQL = $sSQL." WHERE id = ".$oData['iID'];
			$res = $this->excecuteQuery($sSQL);
			return true;
		}

		public function getAnexoItems($iID, $sFieldToSort = null){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			if($sFieldToSort){
				$sOrder = "ORDER BY $sFieldToSort ASC";
			}else{
				$sOrder = "ORDER BY id";
			}
			$sSQL = "SELECT * FROM anexo_donacion_items WHERE id_ad = $iID $sOrder";
			$res = $this->excecuteQuery($sSQL);
			while($row = mysqli_fetch_assoc($res)){
				$vData[] = $row;
			}		
			return $vData??[];
			
		}


		public function getAnexoItem($iItemID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$sSQL = "SELECT * FROM anexo_donacion_items WHERE id = $iItemID";
			$res = $this->excecuteQuery($sSQL);
			$row = mysqli_fetch_assoc($res);
			if($row){
				return $row;
			}else{
				return false;
			}		
			
		}

		//-------------------------------------------------------------------------------------------
		// INICIO METODOS RELACIONADOS CON ACTAS DE COMPRAS
	
		function get_next_numero_acta_compra($iYear){
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = 'SELECT IFNULL(MAX(id_comp), 0)+1 AS nextNumero FROM actas_compras WHERE anio = '.$iYear;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['nextNumero'];		
		}
				
		function insertAct($vData, $con, $iIdUsr){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$iYear 	= date("Y");
			$sDate 	= date("Y-m-d");
			$sProc 	= $vData['ac_procedimiento'];
			$iFirma = (int)$vData['ac_firmante'];
			$iMoneda= (int)$vData['ac_moneda'];
			$sObj 	= $vData['ac_objeto'];
			$iP1	= (int)$vData['ac_p1'];
			$iP2 	= (int)$vData['ac_p2'];
			$iP3 	= (int)$vData['ac_p3'];
			$fTot1	= (float)$vData['ac_p1_tot'];
			$fTot2 	= (float)$vData['ac_p2_tot'];
			$fTot3 	= (float)$vData['ac_p3_tot'];
			$iP_Sel = (int)($vData['ac_prov_sel']??'0');
			$sComen = $vData['ac_comentario'];
			$lastId = $this->get_next_numero_acta_compra($iYear);
			
			$sSQL = "INSERT INTO actas_compras VALUES(0,$lastId,$iYear, '$sDate', '$sProc', $iFirma, $iMoneda, '$sObj', $iP1, $iP2, $iP3, $fTot1, $fTot2, $fTot3, $iP_Sel, '$sComen', $iIdUsr)";
			$sResult = $this->excecuteQuery($sSQL);
			if(!$sResult){
				echo "<pre>";
				var_dump($sSQL);
				var_dump($sResult);	
				var_dump(mysqli_errno($con) . ": " . mysqli_error($con));			
			}else{
				//TODO ver si hay que corregir algo mas $this->updateLastAC_ID();
				$res = $this->excecuteQuery("SELECT LAST_INSERT_ID()");
				$row = mysqli_fetch_array($res);
				//var_dump($row[0]);exit;
				$this->insertACItems($vData, $row[0],$con);
			}
		}

		function insertACItems($vData, $iLastID, $con){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			//Descarto todos los campos hasta llegar al primer item
			$inicio = false;
			foreach($vData as $Item=>$value){
				$vSwitch = explode("-",$Item);
				if (!$inicio and substr($Item,0,6) == "sel_op") {
					$inicio = true;}
				if ($inicio) {
					switch($vSwitch[2]??$vSwitch[count($vSwitch)-1]){
						case "orden":
							$vOrden[] = $value;
						break;
						case "desc":
							$vDesc[] = $value;
						break;
						case "unidad":
							$vUnidad[] = $value;
						break;
						case "cant":
							$vCant[] = $value;
						break;
						case "p1pu":
							$vP1pu[] = $value;
						break;
						case "p1st":
							$vP1st[] = $value;
						break;
						case "p2pu":
							$vP2pu[] = $value;
						break;
						case "p2st":
							$vP2st[] = $value;
						break;
						case "p3pu":
							$vP3pu[] = $value;
						break;
						case "p3st":
							$vP3st[] = $value;
						break;
						case "sel_op":
							$vST_Sel[] = $value;
						break;
						default:
							$vSwitch = explode("_",$vSwitch[0]);
							if($vSwitch[0] == "sel" && $vSwitch[1] == "op"){
								$vST_Sel[] = $value;
							}
						break;
					}
				}
			}
			for($i=0;$i<count($vOrden);$i++){
				$sData = "VALUES(0, $iLastID, ".(int)($i+1).", ".(int)$vCant[$i].", '".$vDesc[$i]."', '".$vUnidad[$i]."', ".(float)$vP1pu[$i].", ".(float)$vP2pu[$i].", ".(float)$vP3pu[$i].", ".(float)$vP1st[$i].", ".(float)$vP2st[$i].", ".(float)$vP3st[$i].", ".$vST_Sel[$i].")"; 
				$sSQL = "INSERT INTO actas_compras_items () $sData";
				$sRes = $this->excecuteQuery($sSQL);
				if(!$sRes){
					var_dump($sSQL);	
					var_dump(mysqli_errno($con) . ": " . mysqli_error($con));	
					exit;
				}
			}
		}

		function updateAct($vData, $con, $iIdUsr){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$iYear 	= date("Y");
			$sDate 	= date("Y-m-d");
			$sProc 	= $vData['ac_procedimiento'];
			$iFirma = (int)$vData['ac_firmante'];
			$iMoneda= (int)$vData['ac_moneda'];
			$sObj 	= $vData['ac_objeto'];
			$iP1	= (int)$vData['ac_p1'];
			$iP2 	= (int)$vData['ac_p2'];
			$iP3 	= (int)$vData['ac_p3'];
			$fTot1	= (float)$vData['ac_p1_tot'];
			$fTot2 	= (float)$vData['ac_p2_tot'];
			$fTot3 	= (float)$vData['ac_p3_tot'];
			$iP_Sel = (int)($vData['ac_prov_sel']??'0');
			$sComen = $vData['ac_comentario'];
			$lastId = (int)$vData['id_act'];
			
			$sSQL = "UPDATE actas_compras 
			SET 
				procedimiento = '$sProc', 
				firmante = $iFirma, 
				moneda = $iMoneda, 
				objeto = '$sObj', 
				p1 = $iP1, 
				p2 = $iP2, 
				p3 = $iP3, 
				p1_tot = $fTot1, 
				p2_tot = $fTot2, 
				p3_tot = $fTot3, 
				prov_sel = $iP_Sel, 
				comentario = '$sComen',
				auth = $iIdUsr
				WHERE id = $lastId";
			$sResult = $this->excecuteQuery($sSQL);
			if(!$sResult){
				echo "<pre>";
				var_dump($sSQL);
				var_dump($sResult);			
				var_dump(mysqli_errno($con) . ": " . mysqli_error($con));			
			}else{
				$this->delACItems($lastId);
				$this->insertACItems($vData, $lastId,$con);
			}
		}

		function delACItems($iID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$sSQL = "DELETE FROM actas_compras_items WHERE id_ac = $iID";
			$sRes = $this->excecuteQuery($sSQL);
		}
		
		function getActs($con){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "
			SELECT 
				ac.id, 
				CONCAT_WS('/',ac.id_comp,ac.anio) as sNum, 
				CONCAT_WS('/',LPAD(DAY(ac.fecha),2,'0'),LPAD(MONTH(ac.fecha),2,'0'),YEAR(ac.fecha)) as fecha,
				p.descripcion as procedimiento,
				f.titulo_apellido_nombre as firmante,
				ac.moneda,
				ac.objeto,
				CONCAT_WS(', ',(SELECT razon_social FROM proveedor WHERE ac.p1 = id_proveedor), (SELECT razon_social FROM proveedor WHERE ac.p2 = id_proveedor), (SELECT razon_social FROM proveedor WHERE ac.p3 = id_proveedor)) as proveedores
				FROM actas_compras ac
				
				INNER JOIN firmante f ON ac.firmante = f.id_firmante
				INNER JOIN procedimiento p ON ac.procedimiento = p.id_procedimiento
				ORDER BY ac.id DESC";
				
			$sResult = $this->excecuteQuery($sSQL);
			if(!$sResult){
				echo "<pre>";
				var_dump($sSQL);
				var_dump($sResult);			
				var_dump(mysqli_errno($con) . ": " . mysqli_error($con));			
			}
			while($row = mysqli_fetch_assoc($sResult)){
				$vData[]=$row;
			}
			return $vData;
		}

		function getAct($iId, $con){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "
			SELECT 
				ac.id,
				ac.fecha,
				CONCAT_WS('/',ac.id_comp,ac.anio) as sNum, 
				/*CONCAT_WS('/',LPAD(DAY(ac.fecha),2,'0'),LPAD(MONTH(ac.fecha),2,'0'),YEAR(ac.fecha)) as fecha,*/
				p.descripcion as procedimiento,
				ac.objeto AS objeto, 
				f.titulo_apellido_nombre as firmante,
				f.cargo as cargo,
				f.lugar as lugar,
				m.signo AS signo_moneda,
				m.descripcion AS nombre_moneda,
				ac.objeto,
				ac.comentario,
				(SELECT razon_social FROM proveedor WHERE ac.p1 = id_proveedor) AS P1,
				(SELECT razon_social FROM proveedor WHERE ac.p2 = id_proveedor) AS P2, 
				(SELECT razon_social FROM proveedor WHERE ac.p3 = id_proveedor) AS P3, 
				ac.p1_tot AS P1TOT,
				ac.p2_tot AS P2TOT,
				ac.p3_tot AS P3TOT
				
				FROM actas_compras ac
				
				INNER JOIN firmante f ON ac.firmante = f.id_firmante
				INNER JOIN procedimiento p ON ac.procedimiento = p.id_procedimiento
				INNER JOIN moneda m ON ac.moneda = m.id_moneda
				WHERE ac.id = $iId";
				
			$sResult = $this->excecuteQuery($sSQL);
			if(!$sResult){
				echo "<pre>";
				var_dump($sSQL);
				var_dump($sResult);			
				var_dump(mysqli_errno($con) . ": " . mysqli_error($con));			
			}
			$row = mysqli_fetch_assoc($sResult);
			return $row;
		}

		function getActData($iID, $con){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "SELECT * FROM actas_compras WHERE id = $iID";
			$sSQL1 = "SELECT * FROM actas_compras_items WHERE id_ac = $iID";
				
			$sResult = $this->excecuteQuery($sSQL);
			if(!$sResult){
				echo "<pre>";
				var_dump($sSQL);
				var_dump($sResult);			
				var_dump(mysqli_errno($con) . ": " . mysqli_error($con));			
			}else{
				$row = mysqli_fetch_assoc($sResult);
				$vData[]=$row;
				$sResult = $this->excecuteQuery($sSQL1);
				if(!$sResult){
					echo "<pre>";
					var_dump($sSQL);
					var_dump($sResult);			
					var_dump(mysqli_errno($con) . ": " . mysqli_error($con));			
				}else{
					while($row = mysqli_fetch_assoc($sResult)){
						$vData[]=$row;
					}
				}
			}
			return $vData;
		}

		function delAct($iID,$con){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "DELETE FROM actas_compras_items WHERE id_ac = $iID";
			$sSQL1 = "DELETE FROM actas_compras WHERE id = $iID";
				
			$sResult = $this->excecuteQuery($sSQL);
			if(!$sResult){
				echo "<pre>";
				var_dump($sSQL);
				var_dump($sResult);			
				var_dump(mysqli_errno($con) . ": " . mysqli_error($con));			
				return false;
			}else{
				$sResult = $this->excecuteQuery($sSQL1);
				if(!$sResult){
					echo "<pre>";
					var_dump($sSQL1);
					var_dump($sResult);			
					var_dump(mysqli_errno($con) . ": " . mysqli_error($con));			
					return false;
				}else{
					return true;
				}
			}
		}

		//-------------------------------------------------------------------------------------------
		// INICIO METODOS RELACIONADOS CON USUARIOS
	
		
		function __destructor(){
			$con = NULL;
		}

		function consultar_nombre_usuario($nombre_usuario){
			// Fecha: 6 de Enero de 2013
			// Autor: .j
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022
			
			$q = 'SELECT * 
				FROM usuario
				WHERE nombre_usuario ="' . $nombre_usuario . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_permisos_usuario2($id_usuario, $textoReadOnly){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			echo '<table width="400" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    	echo '<th></th>';
			echo '<th>Alta</th>'; 
		    	echo '<th>Baja</th>'; 
		    	echo '<th>Modificaci&oacute;n</th>';
		    	echo '<th>Consulta</th>';
		    	echo '<th>Especial</th>';
			
			//Tiene que ser Left outer join por si agrego un permiso nuevo
			$qPermisos = "SELECT 
						p.id_permiso,
						p.nombre,
						COALESCE(pxu.alta,0) as alta,
						COALESCE(pxu.baja,0) as baja,
						COALESCE(pxu.modificacion,0) as modificacion,
						COALESCE(pxu.consulta,0) as consulta,
						COALESCE(pxu.especial,0) as especial
					FROM permiso p LEFT OUTER JOIN permiso_x_usuario pxu
						ON p.id_permiso = pxu.id_permiso and
						pxu.id_usuario = $id_usuario 
					ORDER BY id_permiso";
			$rPermisos = $this->excecuteQuery($qPermisos);
			while ( $arrayPermisos = mysqli_fetch_array($rPermisos) ){
				echo '<tr class="modo1">';
				echo '<td style="text-align:left!important;padding-left:22px">'.utf8_decode($arrayPermisos['nombre']).'</td>';
				echo '<td><input name="alta'.$arrayPermisos['id_permiso'].'" id="alta'.$arrayPermisos['id_permiso'].'" type="checkbox" value="1"'.($arrayPermisos['alta'] == 1 ? 'checked' : '').$textoReadOnly;
				//Si es para Anexo Donacion Items, el alta tambien implica consulta
				if ($arrayPermisos['id_permiso'] == 8) {
					echo ' onClick="checkConsulta(this,'.$arrayPermisos['id_permiso'].')"';
				}
				echo '></td>';
				echo '<td><input name="baja'.$arrayPermisos['id_permiso'].'" id="baja'.$arrayPermisos['id_permiso'].'" type="checkbox" value="1"'.($arrayPermisos['baja'] == 1 ? 'checked' : '').' onClick="checkConsulta(this,'.$arrayPermisos['id_permiso'].')"'.$textoReadOnly.'></td>';
				echo '<td><input name="modificacion'.$arrayPermisos['id_permiso'].'" id="modificacion'.$arrayPermisos['id_permiso'].'"  type="checkbox" value="1"'.($arrayPermisos['modificacion'] == 1 ? 'checked' : '').' onClick="checkConsulta(this,'.$arrayPermisos['id_permiso'].')"'.$textoReadOnly.'></td>';
				echo '<td><input name="consulta'.$arrayPermisos['id_permiso'].'" id="consulta'.$arrayPermisos['id_permiso'].'" type="checkbox" value="1"'.($arrayPermisos['consulta'] == 1 ? 'checked' : '').''.$textoReadOnly.'></td>';
				//El permiso especial es solo para Orden de Pago y Anexo Donacion
				if (($arrayPermisos['id_permiso'] == 4) or ($arrayPermisos['id_permiso'] == 7)) {
					echo '<td><input name="especial'.$arrayPermisos['id_permiso'].'" id="especial'.$arrayPermisos['id_permiso'].'" type="checkbox" value="1"'.($arrayPermisos['especial'] == 1 ? 'checked' : '').' onClick="checkConsulta(this,'.$arrayPermisos['id_permiso'].')"'.$textoReadOnly.'></td>';				
				}
				else {
					echo '<td></td>';				
				}
			}//end while

			echo '</table>';
		}

		
		function getIcon($iOP,$sPermiso){
			switch ($iOP){
				case 1: //alta
					$sReturn = ((int)$sPermiso == 1)?'agregar.png':'iconos_grises/agregarg.png';
				break;
				case 2: //Baja
					$sReturn = ((int)$sPermiso == 1)?'eliminar.png':'iconos_grises/eliminarg.png';
				break;
				case 3: //Modificacion
					$sReturn = ((int)$sPermiso == 1)?'actualizar_datos.png':'iconos_grises/actualizar_datosg.png';
				break;
				case 4: //Ver
				break;
				case 5: //PDF
					$sReturn = ((int)$sPermiso == 1)?'acrobat.png':'iconos_grises/acrobat.png';
				break;
			}
			
			return $sReturn;
		}

		function getLink($iOP,$sPermiso,$iID,$iItemID=null,$iOrden=null){
			if($iItemID){
				$sItem = "&iid=$iItemID";
			} else {$sItem = "";}
			if($iOrden){
				$sOrden = "&io=$iOrden";
			} else {$sOrden = "";}
			switch ($iOP){
				//ACTAS COMPRAS
				case 1: //alta
					$sReturn = ((int)$sPermiso == 1)?"abm_actas_compras.php?op=$iOP&id=$iID":'#';
				break;
				case 2: //Baja
					$sReturn = ((int)$sPermiso == 1)?"#\" onclick=\"delAct('$iOP','$iID');":'#';
				break;
				case 3: //Modificacion
					$sReturn = ((int)$sPermiso == 1)?"abm_actas_compras.php?op=$iOP&id=$iID":'#';
				break;
				case 4: //Ver
				break;
				case 5: //PDF
					$sReturn = ((int)$sPermiso == 1)?"actas_compras_pdf.php?id=$iID":'#';
				break;
				//ANEXO DONACION
				case 6: //baja
					$sReturn = ((int)$sPermiso == 1)?"abm_anexo_donacion.php?op=3&id=$iID":'#';
				break;
				case 7: //Modificacion ITEMS
					$sReturn = ((int)$sPermiso == 1)?"abm_anexo_donacion.php?op=7&id=$iID".$sItem.$sOrden:'#';
				break;
				case 8: //PDF
					$sReturn = ((int)$sPermiso == 1)?"anexo_donacion_pdf.php?id=$iID":'#';
				break;
				case 9: //DEL ITEM
					$sReturn = ((int)$sPermiso == 1)?"abm_anexo_donacion.php?op=8&id=$iID".$sItem:'#';
				break;
			}
			
			return $sReturn;
		}
		
		
		//Reescritura de funcion getPermiso - Vanina
		//TODO falta mejorar en varios aspectos!
		//si id_permiso viene con valor chequea acceso para ese permiso, si no cheuqea para el modulo
		function checkAccess($id_usuario, $id_permiso=0, $modulo=''){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			if($modulo != '') {
				switch ($modulo) {
					case "CAN_ACCESS_WEB":
					case "CAN_ACCESS_OTHER":
						$id_permiso = "20,21,22,23,24,25,26,27,28,29,30";
					break;
					case "CAN_ACCESS_CAP":
						$id_permiso = "7,8,9,10";
					break;
					case "CAN_ACCESS_MODULES":
						$id_permiso = "1,2,3,4,5,6,7,8,9,10,11";
					break;
					case "CAN_ACCESS_BASES":
						$id_permiso = "12,13,14,15,16,17,18,19";
					break;
					}
			}
			
			$qPermisos = "SELECT count(*) as cant 
					FROM permiso_x_usuario
					WHERE id_usuario = $id_usuario and
						id_permiso IN ($id_permiso) and
						(alta or baja or modificacion or consulta or especial)";
				 	 	$rPermisos = $this->excecuteQuery($qPermisos);
			$arrayPermisos = mysqli_fetch_array($rPermisos);
			return ($arrayPermisos['cant'] > 0);
			
		}

		function checkPerm($id_usuario, $id_permiso, $operacion){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$qPermisos = "SELECT count(*) as cant 
					FROM permiso_x_usuario
					WHERE id_usuario = $id_usuario and
						id_permiso IN ($id_permiso) and
						($operacion)";

			$rPermisos = $this->excecuteQuery($qPermisos);
			$arrayPermisos = mysqli_fetch_array($rPermisos);
			return ($arrayPermisos['cant'] > 0);
			
		}

		function listar_usuarios($usuario=0){
			// Fecha: 18 de Diciembre de 2012
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = 'SELECT 
					* 
				FROM 
					usuario 
				WHERE 
					baja = 0 or
					id_usuario = '.$usuario.'
				Order By 
					apellido, nombre ASC';
			$r = $this->excecuteQuery($q);
			echo '<td>';
			echo '<select name="usuario">';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id_usuario'] == $usuario){
					echo '<option selected value='. $row['id_usuario'] .'>'.  $row['apellido']. ', '.  $row['nombre'] .'</option>';
				}else{
					echo '<option value='. $row['id_usuario'] .'>'. $row['apellido'] . ', '.  $row['nombre'] . '</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}

		function getNextCRID($id_unidad_ejecutora){
			$vData = $this->consultar_unidad_ejecutora($id_unidad_ejecutora);
			return (int)$vData['next_cr_id'] + 1;
		}

		function updateCRID($id_unidad_ejecutora){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$sSQL = "UPDATE unidad_ejecutora SET next_cr_id = next_cr_id + 1 WHERE id_unidad_ejecutora = ".$id_unidad_ejecutora;
			return $this->excecuteQuery($sSQL);
		}

		function isRetentionAgent($id_unidad_ejecutora){
			$vData = $this->consultar_unidad_ejecutora($id_unidad_ejecutora);
			return (bool)$vData['agente_retencion'];
		}

		function usuario_registrado($nombre_usuario, $contrasenia){
      //Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			escribirLog(date("d/m/Y - H:i:s.- ") . " en usuario_registrado. Contraseña: ".$contrasenia." \r\n");


      $q = 'SELECT 
          * 
        FROM 
          usuario
        WHERE 
          baja = 0 and
          nombre_usuario ="' . $nombre_usuario . '"';
        try {
          $r = $this->excecuteQuery($q);
					$row = mysqli_fetch_array($r);
          if ($row['pattern'] == 0){
            return $contrasenia == $row['contrasenia'];
          } else
          {
						$rta = password_verify($contrasenia, $row['contrasenia']);
						if ($rta) {
							escribirLog(date("d/m/Y - H:i:s.- ") .'Verifica'." \r\n");
						} else {
							escribirLog(date("d/m/Y - H:i:s.- ") .'NO Verifica'." \r\n");
						}
            return $rta;
          }
        } catch (exception $e) {
					escribirLog(date("d/m/Y - H:i:s.- ") .'En el Catch'." \r\n");
          escribirLog(date("d/m/Y - H:i:s.- ") . $e->getMessage()." \r\n");
        }
    }
		
		function old_password($nombre_usuario){
			//Victoria Ganuza
      //Fecha: 4/11/2022

      $q = 'SELECT 
          pattern 
        FROM 
          usuario
        WHERE 
          nombre_usuario ="' . $nombre_usuario . '"';
        try {
          $r = $this->excecuteQuery($q);
					$row = mysqli_fetch_array($r);
          return $row['pattern'] == 0;
        } catch (exception $e) {
          echo $e->getMessage();
        }
		}

		function update_user($nombre_usuario,$password){
			//Victoria Ganuza
      //Fecha: 4/11/2022

      $q = 'UPDATE usuario SET contrasenia = "' . $password . '", pattern = "1" 
						WHERE 
          nombre_usuario ="' . $nombre_usuario . '"';
        try {
          $r = $this->excecuteQuery($q);
					return true;
        } catch (exception $e) {
          echo $e->getMessage();
					return false;
        }
		}
		
		function nivel_acceso($nombre_usuario){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 20/09/2022

			$q = 'SELECT 
					* 
				FROM 
					usuario
				WHERE 
					baja = 0 and
					nombre_usuario ="' . $nombre_usuario . '"';
			try {
				$r = $this->excecuteQuery($q);
				$row = mysqli_fetch_array($r);
				return $row["nivel_acceso"];
			} catch (exception $e) {
				echo $e->getMessage();
				return false;
			}
		} 
				
		function agregar_usuario2($nombre_usuario, $contrasenia, $nombre, $apellido, $email, $titulo, $opciones_alta, $opciones_baja, $opciones_modificacion, $opciones_consulta, $opciones_especial){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$pwd = password_hash($contrasenia, PASSWORD_ARGON2I);
			$sSQL ="INSERT INTO usuario 
					(nombre_usuario, 
					contrasenia, 
					nombre, 
					apellido, 
					email,
					titulo,
					pattern) 
				VALUES ('$nombre_usuario', 
					'$pwd', 
					'$nombre', 
					'$apellido', 
					'$email',
					'$titulo',
					1)";
			$usuarioOk = $this->excecuteQuery($sSQL);
			//Obtengo el Id insertado
			if ($usuarioOk) {
				$rLI = $this->excecuteQuery("SELECT LAST_INSERT_ID() as id;");
				$aLI = mysqli_fetch_array($rLI);
				$id_usuario = $aLI['id'];
			
				//Alta de permisos
				$qPermisos = 'SELECT * FROM permiso ORDER BY id_permiso';
				$rPermisos = $this->excecuteQuery($qPermisos);			
				while ( $arrayPermisos = mysqli_fetch_array($rPermisos) ){
					$sSQL ="INSERT INTO permiso_x_usuario 
							(id_permiso, 
							id_usuario, 
							alta, 
							baja, 
							modificacion, 
							consulta,
							especial) 
						VALUES (".$arrayPermisos['id_permiso'].",".
							$id_usuario.",".
							$opciones_alta[$arrayPermisos['id_permiso']].",".
							$opciones_baja[$arrayPermisos['id_permiso']].",".
							$opciones_modificacion[$arrayPermisos['id_permiso']].",".
							$opciones_consulta[$arrayPermisos['id_permiso']].",".
							$opciones_especial[$arrayPermisos['id_permiso']].
							")";
					$this->excecuteQuery($sSQL);	
				}//end while
			}		
			
		}

		function borrar_usuario($id_usuario){
			// Fecha: 27 de Octubre de 2012
			// Autor: .j. Modificado por Vanina por baja logica

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE `usuario` 
					SET baja = 1
					WHERE `id_usuario` = '$id_usuario'");			
		}
		
		function consultar_usuario($id_usuario){
			// Fecha: 27 de Octubre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = 'SELECT * 
				FROM usuario
				WHERE id_usuario ="' . $id_usuario . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
		
		
		function check_nombre_usuario($nombre_usuario, $id_usuario=""){
			// Fecha: 26-05-2017
			// Autor: Vanina
			// Devuelve true si no existe un usuario con nombre_usuario igual al
			//parametro 1. No considera el usuario pasado en parametro 2.
				
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$q = '	SELECT 
					count(*) as cant 
				FROM 
					usuario
				WHERE 
					nombre_usuario ="' . $nombre_usuario . '" and
				 	id_usuario != "'.$id_usuario. '"';
			//baja = 0 and Nota Vani: quito la condicion de baja porque muchas cosas
			//en el sistema se manejaban por el nombre de usuario y no por el id.
			//habria que revisar y cambiar todo para permitir nombres repetidos.
			$r = $this->excecuteQuery($q);			
			$row = mysqli_fetch_array($r);
			$cant = $row['cant'];
			mysqli_free_result($r);
			return $cant==0;
		}
		
								
		function modificar_usuario2($id_usuario, $nombre_usuario, $contrasenia, $nombre, $apellido, $email, $titulo, $opciones_alta, $opciones_baja, $opciones_modificacion, $opciones_consulta, $opciones_especial){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022
			$sql = "UPDATE usuario SET nombre_usuario='$nombre_usuario', nombre='$nombre', apellido='$apellido', email='$email', titulo='$titulo'";
			if ($contrasenia != 'pswdefault') {
				$pwd = password_hash($contrasenia, PASSWORD_ARGON2I);
				$sql .= ", contrasenia='$pwd'";
			}
			$sql .= " WHERE id_usuario='$id_usuario'";
			
			$this->excecuteQuery($sql);
			
			//Permisos: borro anteriores y doy de alta nuevos						
			$this->excecuteQuery("DELETE FROM permiso_x_usuario
					WHERE id_usuario='$id_usuario'");

			//Alta de permisos
			$qPermisos = 'SELECT * FROM permiso ORDER BY id_permiso';
			$rPermisos = $this->excecuteQuery($qPermisos);			
			while ( $arrayPermisos = mysqli_fetch_array($rPermisos) ){
				$sSQL ="INSERT INTO permiso_x_usuario 
						(id_permiso, 
						id_usuario, 
						alta, 
						baja, 
						modificacion, 
						consulta,
						especial) 
					VALUES (".$arrayPermisos['id_permiso'].",".
						$id_usuario.",".
						$opciones_alta[$arrayPermisos['id_permiso']].",".
						$opciones_baja[$arrayPermisos['id_permiso']].",".
						$opciones_modificacion[$arrayPermisos['id_permiso']].",".
						$opciones_consulta[$arrayPermisos['id_permiso']].",".
						$opciones_especial[$arrayPermisos['id_permiso']].
						")";
				$this->excecuteQuery($sSQL);	
			}//end while
		}


		function lista_usuarios($vData){
			// Fecha: 27 de Octubre de 2012
			// Autor: .j
			// Modificado por V.Soprano para agregar filtros

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022
		
			$sHtml = '
			<form name="frmBus" action="lista_usuarios.php" method="POST">
			<table class="tabla">
				<tr><th colspan="6">B&uacute;squeda</th></tr>
				<tr class="modo1">
					<td>
						Nombre de usuario
					</td>
					<td>
						<input type="text" name="nombreUsuarioBus" value="{nombreUsuarioBus}" style="width:150px"/>
					</td>
					<td>
						Email
					</td>
					<td>
						<input type="text" name="emailBus" value="{emailBus}" style="width:150px"/>
					</td>		
					<td>
						
					</td>
				</td></tr>
				<tr class="modo1">
					<td>
						Apellido
					</td>
					<td>
						<input type="text" name="apellidoBus" value="{apellidoBus}" style="width:150px"/>
					</td>
					<td>
						Nombre
					</td>
					<td align="left">
						<input type="text" name="nombreBus" value="{nombreBus}" style="width:150px"/>
					</td>
					<td>
						<input type="submit" name="btnBus" value="Buscar"/>
					</td>
					</td>
				</tr>
			</table>
			</form>';
			if (array_key_exists('nombreUsuarioBus', $vData))
				$varNombreUsuarioBus = $vData['nombreUsuarioBus'];
			else
				$varNombreUsuarioBus = "";

			if (array_key_exists('emailBus', $vData))
				$varEmailBus = $vData['emailBus'];
			else
				$varEmailBus = "";

			if (array_key_exists('apellidoBus', $vData))
				$varApellidoBus = $vData['apellidoBus'];
			else
				$varApellidoBus = "";

			if (array_key_exists('nombreBus', $vData))
				$varNombreBus = $vData['nombreBus'];
			else
				$varNombreBus = "";		
			
			$sHtml = str_replace("{nombreUsuarioBus}",$varNombreUsuarioBus,$sHtml);
			$sHtml = str_replace("{apellidoBus}",$varApellidoBus,$sHtml);
			$sHtml = str_replace("{nombreBus}",$varNombreBus,$sHtml);
			$sHtml = str_replace("{emailBus}",$varEmailBus,$sHtml);

			echo $sHtml;

			$sWhere = '';

			if ($varNombreUsuarioBus != "") {				
				$sWhere .= " AND nombre_usuario like '$varNombreUsuarioBus%' ";
			}
			if ($varEmailBus != "") {				
				$sWhere .= " AND email like '$varEmailBus%' ";
			}
			if ($varApellidoBus != "") {				
				$sWhere .= " AND apellido like '$varApellidoBus%' ";
			}
			if ($varNombreBus != "") {				
				$sWhere .= " AND nombre like '$varNombreBus%' ";
			}

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1"  align="center"  class="tabla table-autosort table-autofilter">';
			echo '<thead>';			
			echo '<tr>';
		    echo '<th class="table-sortable:default">Nombre de usuario</th>';
		    echo '<th class="table-sortable:default">Nombre</th>'; 
		    echo '<th class="table-sortable:default">Apellido</th>';
		    echo '<th class="table-sortable:default">E-Mail</th>';
		    echo '<th class="table-sortable:default" colspan="3">Acciones</th></tr>';
		    echo '</thead>';						
			
			$q = 'SELECT 
					* 
				FROM 
					usuario 
				WHERE
					baja = 0 '.$sWhere.'
				ORDER BY 
					apellido, 
					nombre ASC';
			$r = $this->excecuteQuery($q);
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],19,'modificacion')){
					$lnkmodificar_usuario = 'form_usuario.php?id_usuario=' . $row['id_usuario'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_usuario = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],19,'baja')){
					$lnkborrar_usuario = 'form_usuario.php?id_usuario=' . $row['id_usuario'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_usuario = '#';				
					$src1='iconos_grises/eliminarg.png';
				}
				if($this->checkPerm($_SESSION["id_usuario"],19,'consulta')){
					$lnkconsultar_usuario = 'form_usuario.php?id_usuario=' . $row['id_usuario'] . '&opcion=4';				
					$src2='previsualizar.png';
				}else {
					$lnkconsultar_usuario = '#';				
					$src2='iconos_grises/previsualizarg.png';
				}
				echo '<tr class="modo1">';
				
				echo '<td>' . $row['nombre_usuario'] .'</td>';
				echo '<td>' . $row['nombre'] . '</td>';
				echo '<td>' . $row['apellido'] . '</td>';				
				echo '<td>' . $row['email'] . '</td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_usuario.  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Usuario"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_usuario.  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Usuario"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkconsultar_usuario.  '><img src="'.$src2.'" width="30" height="30" border="0" alt="Ver Registro"></a></td>';				

			}
			echo '</table>';
		}


		// FIN METODOS RELACIONADOS CON USUARIOS

		//-------------------------------------------------------------------------------------------

		// INICIO METODOS RELACIONADOS CON MODULOS WEB 

		function ultimo_id_foto_home(){
			// Fecha: 6 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_foto) as maximo FROM fotos_home';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
	
		function agregar_foto_home($mostrar, $archivo){
			// Fecha: 6 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			//echo  $nombre_usuario . " - " . $contrasenia . " - " . $nombre . " - " . $apellido . " - " . $email . " - " . $nivel_acceso;
			$this->excecuteQuery("INSERT INTO fotos_home (mostrar, archivo) VALUES ('$mostrar', '$archivo')");
		}

		function borrar_foto_home($id_foto, $archivo){
			// Fecha: 6 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
		
			$this->excecuteQuery("DELETE FROM `fotos_home` WHERE `id_foto` = '$id_foto'");
			if ($archivo){
				unlink('../fotos_home/'.$archivo);
			}
		}
		
		function borrar_archivo_fotos_home($id_foto, $archivo){
			// Fecha: 6 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE fotos_home SET archivo='' WHERE id_foto='$id_foto'");
			if ($archivo){
				unlink('../fotos_home/'.$archivo);					
			}
		}

		function modificar_foto_home($id_foto, $mostrar, $archivo){
			// Fecha: 6 de Febrero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE fotos_home SET mostrar='$mostrar', archivo='$archivo' WHERE id_foto='$id_foto'");
		}

		function consultar_foto_home($id_foto){
			// Fecha: 6 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM fotos_home WHERE id_foto ="' . $id_foto . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_fotos_home(){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
			echo '<th>Archivo</th>'; 
			echo '<th>Mostrar</th>'; 			
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = '
			SELECT * FROM fotos_home Order By id_foto DESC';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],28,'modificacion')){
					$lnkmodificar_fotos_home = 'form_fotos_home.php?id_foto=' . $row['id_foto'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_fotos_home = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],28,'baja')){
					$lnkborrar_fotos_home = 'form_fotos_home.php?id_foto=' . $row['id_foto'] . '&opcion=2';
					$src1='eliminar.png';
				}else{
					$lnkborrar_fotos_home = '#';				
					$src1='iconos_grises/eliminarg.png';
				}

				echo '<tr class="modo1">';
				echo '<td>' . '<img src="../fotos_home/'. $row['archivo'] . '" width="75" height="45" border="0"><br/>'. $row['archivo'].'</td>';
				if ($row['mostrar'] == 1 ){
					echo '<td>SI</td>';	
				}else
					echo '<td>NO</td>';						
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_fotos_home .  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Fotos Home"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_fotos_home .  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Fotos Home"></a></td>';
				$fila++;
			}
			echo '</table>';
		}


	// INICIO METODOS RELACIONADOS CON GALERIA DE IMAGENES (ALBUM DE FOTOS)

		function getFolderId($IDAlbum){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT folder FROM album_fotos WHERE id_album ='. $IDAlbum;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['folder'];

		}

		function setPortadaImage($IDAlbum, $iIDImage){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'UPDATE `album_fotos` SET archivo = "'.$iIDImage.'" WHERE id_album ='. $IDAlbum;
			return $this->excecuteQuery($q);
		}

		function borrar_album($id_album){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$sFolder = $this->getAlbumFolder($id_album);
			$this->deleteFolder($sFolder);
			$this->excecuteQuery("DELETE FROM `album_fotos` WHERE `id_album` = '$id_album'");
		}

		function borrar_movimiento_album($id_album, $id_foto){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$row_album = $this->consultar_album_fotos($id_album, $id_foto);
			if ($row_album['archivo']){
				unlink('../fotos_album/'.$row_album['archivo']);
			}
			
			$this->excecuteQuery("DELETE FROM `album_fotos` WHERE `id_album` = '$id_album' AND `id_foto` = '$id_foto'");
			
			$ultimo_id_foto_album = $this>ultimo_id_foto_album($id_album);
			for($i=$id_foto+1; $i <= $ultimo_id_foto_album; $i++){
				if($i == $id_foto + 1){
					$this->excecuteQuery("UPDATE album_fotos SET id_foto='$id_foto' WHERE id_album='$id_album' AND id_foto='$i'");
				}else{
					$nuevo_id = $i-1;
					$this->excecuteQuery("UPDATE album_fotos SET id_foto='$nuevo_id' WHERE id_album='$id_album' AND id_foto='$i'");				
				}
			}
							
		}	

		function modificar_album($id_album, $nombre_album, $comentario, $fecha){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j
			// Modificado por Sebastian Salerno

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE album_fotos SET nombre_album='$nombre_album', comentario='$comentario', fecha='$fecha' WHERE id_album='$id_album'");
		}

		function agregar_album($id_album, $fecha, $nombre_album, $comentario, $sFolder){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("INSERT INTO album_fotos (id_album, fecha, nombre_album, comentario, folder) VALUES ('$id_album', '$fecha', '$nombre_album', '$comentario', '$sFolder')");
		}
		
		function consultar_album_fotos($id_album){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j
			// Modificado por Sebastian Salerno
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM album_fotos WHERE id_album ="' . $id_album . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_album_por_id_album($id_album, $opcion){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
	
			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
			echo '<th>Archivo</th>'; 
			if ($opcion != 1) // NO MUESTRO LAS ACCIONES PORQUE ELIGIO ELIMINAR
			    echo '<th colspan="2">Acciones</tr>';			
			
			$q = 'SELECT * FROM album_fotos WHERE id_album ='. $id_album . ' ORDER By id_foto';
			$r = $this->excecuteQuery($q);
			
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_album = 'form_album_fotos.php?id_album='.$row['id_album'].'&id_foto='.$row['id_foto'].'&opcion=3';
				$lnkborrar_album = 'form_album_fotos.php?id_album='.$row['id_album'].'&id_foto='.$row['id_foto'].'&opcion=5'; // ACA SE ELIJE ELIMINAR UN REGISTRO DEL ALBUM
				echo '<tr class="modo1">';
				
				echo '<td>' . '<img src="../fotos_album/'. $row['archivo'] . '" width="75" height="45" border="0"></td>';
				if ($opcion != 1){ // NO MUESTRO LAS ACCIONES PORQUE ELIGIO ELIMINAR				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_album .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_album .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}
			}
			echo '</table>';
		}
		
		function getAlbumImages($iID){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT folder FROM album_fotos WHERE id_album ='. $iID;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_assoc($r);
			
			$vFiles = scandir('../fotos_album/' . $row['folder'],1);
			array_pop($vFiles);
			array_pop($vFiles);

			return $vFiles;
		}
		
		function deleteFolder($sFolder){
			$sFolder = '../fotos_album/' . $sFolder;
			
			$vFiles = scandir($sFolder,1);
			array_pop($vFiles);
			array_pop($vFiles);
			
			foreach($vFiles as $sFile){
				if(!unlink($sFolder.'/'.$sFile)){
					echo "ERROR AL ELIMINAR ".$sFolder.'/'.$sFile."<br/>";
				}
			}
			
			if(!rmdir($sFolder)){
				echo "ERROR AL ELIMINAR ".$sFolder."<br/>";
			}
		}
		
		function getAlbumFolder($iID){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT folder FROM album_fotos WHERE id_album ='. $iID;
			$r = $this->excecuteQuery($q);
			if($r){
				$row = mysqli_fetch_assoc($r);
				return $row['folder'];
			}
		}
		
		function getAlbumPortada($iID){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT archivo FROM album_fotos WHERE id_album ='. $iID;
			$r = $this->excecuteQuery($q);
			if($r){
				$row = mysqli_fetch_assoc($r);
				return $row['archivo'];
			}
		}
		
		function ultimo_id_foto_album($id_album){
				// Fecha: 5 de Febrero de 2013
			// Autor: .j
				
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_foto) as maximo FROM album_fotos WHERE id_album="' . $id_album . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}

		function ultimo_id_album(){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j
				
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = '
				SELECT Max(id_album) as maximo
				FROM album_fotos
			     ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}


		function lista_album_fotos($nombre_usuario){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Nombre Alb&uacute;m</th>';
		    echo '<th>Fecha</th>';
			echo '<th>Comentario</th>'; 
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = 'SELECT * FROM album_fotos ORDER By id_album DESC';
			$r =$this->excecuteQuery($q);
			$fila = 1;
			$ant_numero_album = 0;
						
			while ( $row = mysqli_fetch_array($r) ){
				$act_numero_album = $row['id_album'];

				if($this->checkPerm($_SESSION["id_usuario"],29,'modificacion')){
					$lnkmodificar_album_foto = 'form_album_fotos.php?id_album=' . $row['id_album'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_album_foto = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],29,'baja')){
					$lnkborrar_album_foto = 'eliminar_album_fotos.php?id_album=' . $row['id_album'];				
					$src1='eliminar.png';
				}else{
					$lnkborrar_album_foto = '#';				
					$src1='iconos_grises/eliminarg.png';
				}

				if ($act_numero_album != $ant_numero_album){
					echo '<tr class="modo1">';
					echo '<td>' . $row['nombre_album'] .'</td>';
					echo '<td>' . convertir_fecha($row['fecha']) .'</td>';
					echo '<td>' . $row['comentario'] . '</td>';
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_album_foto .  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';										
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_album_foto .  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					echo '</tr>';
					$ant_numero_album = $act_numero_album;
				}
				$fila++;
			}
			echo '</table>';

		}

		// FIN METODOS RELACIONADOS CON GALERIA DE IMAGENES


		// INICIO METODOS RELACIONADOS CON ADMINISTRACION
	
		function ultimo_id_administracion(){
			// Fecha: 28 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_administracion) as maximo FROM administracion';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
	
		function agregar_administracion($titulo, $unidad_ejecutora, $comentario, $archivo){
			// Fecha: 28 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("INSERT INTO administracion (titulo, unidad_ejecutora, comentario, archivo) VALUES ('$titulo', '$unidad_ejecutora', '$comentario', '$archivo')");
		}

		function borrar_administracion($id_administracion, $archivo){
			// Fecha: 28 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `administracion` WHERE `id_administracion` = '$id_administracion'");
			if ($archivo){
				unlink('../PDF/'.$archivo);
			}
		}
		
		function borrar_archivo_administracion($id_administracion, $archivo){
			// Fecha: 28 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE administracion SET archivo='' WHERE id_administracion='$id_administracion'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function modificar_administracion($id_administracion, $titulo, $unidad_ejecutora, $comentario, $archivo){
			// Fecha: 28 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE administracion SET titulo='$titulo', unidad_ejecutora='$unidad_ejecutora', comentario='$comentario', archivo='$archivo' WHERE id_administracion='$id_administracion'");
		}
		
		function consultar_administracion($id_administracion){
			// Fecha: 28 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM administracion WHERE id_administracion="' . $id_administracion . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
	
		function lista_administracion(){
			// Fecha: 28 de Enero de 2013
			// Autor: .j
	
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="900" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Titulo</th>';
		    echo '<th>Unidad Ejecutora</th>';
		    echo '<th>Comentario</th>';	    
			echo '<th colspan="2">Acciones</th>';
			echo '</tr>';
			
			$q = 'SELECT * FROM administracion ORDER By titulo';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],25,'modificacion')){
					$lnkmodificar_administracion = 'form_administracion.php?id_administracion=' . $row['id_administracion'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_administracion = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],25,'baja')){
					$lnkborrar_administracion = 'form_administracion.php?id_administracion=' . $row['id_administracion'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_administracion = '#';				
					$src1='iconos_grises/eliminarg.png';
				}

				echo '<tr class="modo1">';
				echo '<td>' . $row['titulo'] . '</td>';
				$row_unidad_ejecutora = $this->consultar_unidad_ejecutora($row['unidad_ejecutora']);
				echo '<td>' . $row_unidad_ejecutora['nombre'] . '</td>';
				echo '<td>' . $row['comentario'] . '</td>';				
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_administracion.  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Administracion"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_administracion.  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Administracion"></a></td>';
				$fila++;
			}
			echo '</table>';
			$bd = NULL;
		}		
	
		// FIN METODOS RELACIONADOS CON ADMINISTRACION

		// INICIO METODOS RELACIONADOS CON NOTICIAS

		function modificar_noticia($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre, $opcion){
			// Fecha: 24 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			switch ($opcion){
				case 0:
					$this->excecuteQuery("UPDATE noticias SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar' WHERE id_noticia='$id_noticia'");
					break;				
				case 1:
					$this->excecuteQuery("UPDATE noticias SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto1='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 2:
					$this->excecuteQuery("UPDATE noticias SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto2='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 3:
					$this->excecuteQuery("UPDATE noticias SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto3='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 4:
					$this->excecuteQuery("UPDATE noticias SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto4='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 5:
					$this->excecuteQuery("UPDATE noticias SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto5='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 6:
					$this->excecuteQuery("UPDATE noticias SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', adjunto='$nombre' WHERE id_noticia='$id_noticia'");
					break;
			}
		}

		function modificar_seg_e_hig($id_noticia, $titulo, $bajada, $fecha, $texto, $mostrar, $nombre, $opcion){
			// Fecha: 24 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

				switch ($opcion){
				case 0:
					$this->excecuteQuery("UPDATE seg_e_hig SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar' WHERE id_noticia='$id_noticia'");
					break;				
				case 1:
					$this->excecuteQuery("UPDATE seg_e_hig SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto1='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 2:
					$this->excecuteQuery("UPDATE seg_e_hig SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto2='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 3:
					$this->excecuteQuery("UPDATE seg_e_hig SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto3='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 4:
					$this->excecuteQuery("UPDATE seg_e_hig SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto4='$nombre' WHERE id_noticia='$id_noticia'");
					break;
				case 5:
					$this->excecuteQuery("UPDATE seg_e_hig SET titulo='$titulo', bajada='$bajada', fecha='$fecha', texto='$texto', mostrar='$mostrar', foto5='$nombre' WHERE id_noticia='$id_noticia'");
					break;
			}
		}

		function borrar_imagen_noticia($id_noticia, $foto, $opcion){
			// Fecha: 24 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			switch ($opcion){
				case 1:
					$this->excecuteQuery("UPDATE noticias SET foto1='' WHERE id_noticia='$id_noticia'");
					break;
				case 2:
					$this->excecuteQuery("UPDATE noticias SET foto2='' WHERE id_noticia='$id_noticia'");
					break;
				case 3:
					$this->excecuteQuery("UPDATE noticias SET foto3='' WHERE id_noticia='$id_noticia'");
					break;
				case 4:
					$this->excecuteQuery("UPDATE noticias SET foto4='' WHERE id_noticia='$id_noticia'");
					break;
				case 5:
					$this->excecuteQuery("UPDATE noticias SET foto5='' WHERE id_noticia='$id_noticia'");
					break;																				
				case 6:
					$this->excecuteQuery("UPDATE noticias SET adjunto='' WHERE id_noticia='$id_noticia'");
					break;																				
			}
			if ($foto){
				unlink('../fotos_noticias/'.$foto);					
			}
		}

		function borrar_imagen_seg_e_hig($id_noticia, $foto, $opcion){
			// Fecha: 24 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			switch ($opcion){
				case 1:
					$this->excecuteQuery("UPDATE seg_e_hig SET foto1='' WHERE id_noticia='$id_noticia'");
					break;
				case 2:
					$this->excecuteQuery("UPDATE seg_e_hig SET foto2='' WHERE id_noticia='$id_noticia'");
					break;
				case 3:
					$this->excecuteQuery("UPDATE seg_e_hig SET foto3='' WHERE id_noticia='$id_noticia'");
					break;
				case 4:
					$this->excecuteQuery("UPDATE seg_e_hig SET foto4='' WHERE id_noticia='$id_noticia'");
					break;
				case 5:
					$this->excecuteQuery("UPDATE seg_e_hig SET foto5='' WHERE id_noticia='$id_noticia'");
					break;																				
			}
			if ($foto){
				unlink('../fotos_seg_e_hig/'.$foto);					
			}
		}

		function borrar_noticia($id_noticia, $nombre1, $nombre2, $nombre3, $nombre4, $nombre5, $adjunto=null){
			// Fecha: 24 de Noviembre de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `noticias` WHERE `id_noticia` = '$id_noticia'");
			if($nombre1){
				unlink('../fotos_noticias/'.$nombre1);
			}
			if($nombre2){
				unlink('../fotos_noticias/'.$nombre2);
			}
			if($nombre3){
				unlink('../fotos_noticias/'.$nombre3);
			}
			if($nombre4){
				unlink('../fotos_noticias/'.$nombre4);									
			}
			if($nombre5){
				unlink('../fotos_noticias/'.$nombre5);
			}
			if($adjunto){
				unlink('../fotos_noticias/'.$adjunto);
			}
		}

		function borrar_seg_e_hig($id_noticia, $nombre1, $nombre2, $nombre3, $nombre4, $nombre5){
			// Fecha: 24 de Noviembre de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `seg_e_hig` WHERE `id_noticia` = '$id_noticia'");
			if($nombre1){
				unlink('../fotos_seg_e_hig/'.$nombre1);
			}
			if($nombre2){
				unlink('../fotos_seg_e_hig/'.$nombre2);
			}
			if($nombre3){
				unlink('../fotos_seg_e_hig/'.$nombre3);
			}
			if($nombre4){
				unlink('../fotos_seg_e_hig/'.$nombre4);									
			}
			if($nombre5){			
				unlink('../fotos_seg_e_hig/'.$nombre5);
			}
		}

		function ultimo_id_noticia(){
			// Fecha: 1 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_noticia) as maximo FROM noticias';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}

		function ultimo_id_seg_hig(){
			// Fecha: 1 de Febrero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_noticia) as maximo FROM seg_e_hig';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}


		function agregar_noticia($titulo, $bajada, $fecha, $texto, $mostrar, $nombre1, $nombre2, $nombre3, $nombre4, $nombre5, $adjunto){
			// Fecha: 24 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("INSERT INTO noticias (titulo, bajada, fecha, texto, mostrar, foto1, foto2, foto3, foto4, foto5, adjunto) VALUES ('$titulo', '$bajada', '$fecha', '$texto', '$mostrar', '$nombre1', '$nombre2', '$nombre3', '$nombre4', '$nombre5', '$adjunto')");
			
			$ultimo_id_noticia = $this->ultimo_id_noticia();
			if ($archivo1) {
				$this->excecuteQuery("INSERT INTO galeria_noticias (id, archivo) VALUES ('$id_noticia', '$nombre1')");
			}	
		}

		function agregar_seg_e_hig($titulo, $bajada, $fecha, $texto, $mostrar, $nombre1, $nombre2, $nombre3, $nombre4, $nombre5){
			// Fecha: 24 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$sSQL = "INSERT INTO seg_e_hig (titulo, bajada, fecha, texto, mostrar, foto1, foto2, foto3, foto4, foto5) VALUES ('$titulo', '$bajada', '$fecha', '$texto', '$mostrar', '$nombre1', '$nombre2', '$nombre3', '$nombre4', '$nombre5')";
			$this->excecuteQuery($sSQL);

			$id_noticia = $this->lastId();
				
			$ultimo_id_noticia = $this->ultimo_id_seg_hig($id_noticia);
			$ultimo_numero_foto = $this->ultimo_numero_foto_seg_e_hig($id_noticia);
			$ultimo_numero_foto++;
			if ($archivo1) {
				$this->excecuteQuery("INSERT INTO galeria_seg_e_hig (id, archivo, foto) VALUES ('$id_noticia', '$nombre1', '$ultimo_numero_foto')");
			}
			$bd = NULL;				
		}

		function ultimo_numero_foto($id_noticia){
			// Fecha: 17 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT MAX(foto) as maximo FROM galeria_noticias WHERE id ="' . $id_noticia . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
		
		function ultimo_numero_foto_seg_e_hig($id_noticia){
			// Fecha: 17 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT MAX(foto) as maximo FROM galeria_seg_e_hig WHERE id ="' . $id_noticia . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
		
		function consultar_noticia($id_noticia){
			// Fecha: 17 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM noticias WHERE id_noticia ="' . $id_noticia . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function consultar_seg_e_hig($id_noticia){
			// Fecha: 17 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM seg_e_hig WHERE id_noticia ="' . $id_noticia . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_noticias(){
			// Fecha: 17 de Enero de 2013
			// Autor: .j
	
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="900" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Titulo</th>';
			echo '<th>Bajada</th>'; 
		    echo '<th>Fecha</th>'; 
		    echo '<th>Texto</th>';
		    
			echo '<th colspan="2">Acciones</th>';
			echo '</tr>';
			
			$q = 'SELECT * FROM noticias ORDER By fecha DESC';
			$r = $this->excecuteQuery($q);

			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],30,'modificacion')){
					$lnkmodificar_noticia = 'form_noticia.php?id_noticia=' . $row['id_noticia'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_noticia = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],30,'baja')){
					$lnkborrar_noticia = 'form_noticia.php?id_noticia=' . $row['id_noticia'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_noticia = '#';				
					$src1='iconos_grises/eliminarg.png';
				}

				echo '<tr class="modo1">';
				
				echo '<td>' . $row['titulo'] . '</td>';
				echo '<td>' . $row['bajada'] . '</td>';
				echo '<td>' . convertir_fecha($row['fecha']) . '</td>';
				echo '<td>' . substr($row['texto'], 0, 150) . '</td>';
				
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_noticia.  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Noticia"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_noticia.  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Noticia"></a></td>';
			}
			echo '</table>';
		}		

		function lista_seg_e_hig(){
			// Fecha: 17 de Enero de 2013
			// Autor: .j
	
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="900" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Titulo</th>';
			echo '<th>Bajada</th>'; 
		    echo '<th>Fecha</th>'; 
		    echo '<th>Texto</th>';
		    
			echo '<th colspan="2">Acciones</th>';
			echo '</tr>';
			
			$q = 'SELECT * FROM seg_e_hig ORDER By fecha DESC';
			$r = $this->excecuteQuery($q);

			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],30,'modificacion')){
					$lnkmodificar_noticia = 'form_seg_hig.php?id_noticia=' . $row['id_noticia'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_noticia = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],30,'baja')){
					$lnkborrar_noticia = 'form_seg_hig.php?id_noticia=' . $row['id_noticia'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_noticia = '#';				
					$src1='iconos_grises/eliminarg.png';
				}

				echo '<tr class="modo1">';
				
				echo '<td>' . $row['titulo'] . '</td>';
				echo '<td>' . $row['bajada'] . '</td>';
				echo '<td>' . convertir_fecha($row['fecha']) . '</td>';
				echo '<td>' . substr($row['texto'], 0, 150) . '</td>';
				
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_noticia.  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Noticia"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_noticia.  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Noticia"></a></td>';
			}
			echo '</table>';
			$bd = NULL;
		}		

		// FIN METODOS RELACIONADOS CON NOTICIAS
		
		// INICIO METODOS RELACIONADOS CON COONVOCATORIAS

		function borrar_archivo_convocatoria($id_convocatoria, $archivo){
			// Fecha: 6 de Marzo de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE convocatoria SET archivo='' WHERE id_convocatoria='$id_convocatoria'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function ultimo_id_convocatoria(){
			// Fecha: 6 de Marzo de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_convocatoria) as maximo FROM convocatoria';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
	
		function agregar_convocatoria($titulo, $texto, $link, $nombre_nuevo, $fecha_desde, $fecha_hasta){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$vfecha=explode("-",$fecha_desde);
			$fecha_desde = $vfecha[2]."-".$vfecha[1]."-".$vfecha[0];
			$vfecha=explode("-",$fecha_hasta);
			$fecha_hasta = $vfecha[2]."-".$vfecha[1]."-".$vfecha[0];
			$this->excecuteQuery("INSERT INTO convocatoria (titulo, texto, link, archivo, fecha_desde, fecha_hasta) VALUES ('$titulo', '$texto', '$link', '$nombre_nuevo','$fecha_desde','$fecha_hasta')");
		}

		function borrar_convocatoria($id_convocatoria, $archivo){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `convocatoria` WHERE `id_convocatoria` = '$id_convocatoria'");
						if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function modificar_convocatoria($id_convocatoria, $titulo, $texto, $link, $nombre_nuevo, $fecha_desde, $fecha_hasta){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$vfecha=explode("-",$fecha_desde);
			$fecha_desde = $vfecha[2]."-".$vfecha[1]."-".$vfecha[0];
			$vfecha=explode("-",$fecha_hasta);
			$fecha_hasta = $vfecha[2]."-".$vfecha[1]."-".$vfecha[0];

			$this->excecuteQuery("UPDATE convocatoria SET titulo='$titulo', texto='$texto', link='$link', archivo='$nombre_nuevo', fecha_desde = '$fecha_desde', fecha_hasta = '$fecha_hasta' WHERE id_convocatoria=$id_convocatoria");
		}
		
		function consultar_convocatoria($id_convocatoria){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM convocatoria WHERE id_convocatoria ="' . $id_convocatoria . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_convocatorias(){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
			
		    echo '<th>T&iacute;tulo</th>';			
		    echo '<th>Texto</th>';
		    echo '<th>Link</th>';			
		    echo '<th>Vigencia</th>';			
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = 'SELECT * FROM convocatoria ORDER By id_convocatoria DESC';
			$r = $this->excecuteQuery($q);
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],27,'modificacion')){
					$lnkmodificar_convocatoria = 'form_convocatoria.php?id_convocatoria=' . $row['id_convocatoria'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_convocatoria = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],27,'baja')){
					$lnkborrar_convocatoria = 'form_convocatoria.php?id_convocatoria=' . $row['id_convocatoria'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_convocatoria = '#';				
					$src1='iconos_grises/eliminarg.png';
				}

				echo '<tr class="modo1">';
				echo '<td>' . $row['titulo'] .'</td>';
				echo '<td>' . $row['texto'] .'</td>';
				echo '<td>' . $row['link'] .'</td>';
				$vfecha=explode("-",$row['fecha_desde']);
				$fecha_desde = $vfecha[2]."-".$vfecha[1]."-".$vfecha[0];
				$vfecha=explode("-",$row['fecha_hasta']);
				$fecha_hasta = $vfecha[2]."-".$vfecha[1]."-".$vfecha[0];

				echo '<td>' . $fecha_desde .' al '. $fecha_hasta .'</td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_convocatoria . '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Convocatoria"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_convocatoria . '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Convocatoria"></a></td>';
			}
			echo '</table>';
		}
		
		// FIN METODOS RELACIONADOS CON CONVOCATORIAS
		
		// INICIO METODOS RELACIONADOS CON VARIOS

		function borrar_archivo_varios($id_varios, $archivo){
			// Fecha: 4 de Marzo de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE varios SET archivo='' WHERE id_varios='$id_varios'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function ultimo_id_varios(){
			// Fecha: 4 de Marzo de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_varios) as maximo FROM varios';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}

		function agregar_varios($titulo, $texto, $link, $nombre_nuevo, $fechaD, $fechaH){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("INSERT INTO varios (titulo, texto, link, archivo, fecha_desde, fecha_hasta) VALUES ('$titulo', '$texto', '$link', '$nombre_nuevo', '$fechaD', '$fechaH')");
		}
		
		function borrar_varios($id_varios, $archivo){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `varios` WHERE `id_varios` = '$id_varios'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}			
		}
		
		function modificar_varios($id_varios, $titulo, $texto, $link, $nombre_nuevo, $fechaD, $fechaH){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE varios SET titulo='$titulo', texto='$texto', link='$link', archivo='$nombre_nuevo', fecha_desde='$fechaD', fecha_hasta='$fechaH' WHERE id_varios='$id_varios'");
		}		

		function consultar_varios($id_varios){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM varios WHERE id_varios ="' . $id_varios . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_varios(){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>T&iacute;tulo</th>';			
		    echo '<th>Texto</th>';
		    echo '<th>Link</th>';			
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = 'SELECT * FROM varios ORDER By id_varios DESC';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],26,'modificacion')){
					$lnkmodificar_varios = 'form_varios.php?id_varios=' . $row['id_varios'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_varios = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],26,'baja')){
					$lnkborrar_varios = 'form_varios.php?id_varios=' . $row['id_varios'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_varios = '#';				
					$src1='iconos_grises/eliminarg.png';
				}

				echo '<tr class="modo1">';
				echo '<td>' . $row['titulo'] .'</td>';
				echo '<td>' . substr($row['texto'], 0, 85) .'</td>';
				echo '<td>' . $row['link'] .'</td>';				
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_varios . '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar varios"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_varios . '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar varios"></a></td>';
				$fila++;
			}
			echo '</table>';
		}
		
		// FIN METODOS RELACIONADOS CON VARIOS	
		
		// INICIO METODOS RELACIONADOS CON PROYECTOS LICITACIONES
	
		function ultimo_id_licitacion(){
			// Fecha: 23 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_licitacion) as maximo FROM licitacion';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
	
		function agregar_licitacion($titulo, $fecha_publicacion, $fecha_apertura, $horario_apertura, $unidad_ejecutora, $numero_licitacion, $comentario, $archivo, $estado){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("INSERT INTO licitacion (titulo, fecha_publicacion, fecha_apertura, horario_apertura, unidad_ejecutora, numero_licitacion, comentario, archivo, estado) VALUES ('$titulo', '$fecha_publicacion', '$fecha_apertura', '$horario_apertura', '$unidad_ejecutora', '$numero_licitacion', '$comentario', '$archivo', '$estado')");
		}

		function borrar_licitacion($id_licitacion, $archivo){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `licitacion` WHERE `id_licitacion` = '$id_licitacion'");
			if ($archivo){			
				unlink('../PDF/'.$archivo);
			}
		}
		
		function borrar_archivo_licitacion($id_licitacion, $archivo){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE licitacion SET archivo='' WHERE id_licitaciion='$id_licitacion'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function modificar_licitacion($id_licitacion, $titulo, $fecha_publicacion, $fecha_apertura, $horario_apertura, $unidad_ejecutora, $numero_licitacion, $comentario, $archivo, $estado){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE licitacion SET titulo='$titulo', fecha_publicacion='$fecha_publicacion', fecha_apertura='$fecha_apertura', horario_apertura='$horario_apertura', unidad_ejecutora='$unidad_ejecutora', numero_licitacion='$numero_licitacion', comentario='$comentario', archivo='$archivo', estado='$estado' WHERE id_licitacion='$id_licitacion'");
		}
		
		function consultar_licitacion($id_licitacion){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM licitacion WHERE id_licitacion="' . $id_licitacion . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
	
		function lista_licitaciones(){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
	
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="900" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Titulo</th>';
			echo '<th>Fecha Publicacion</th>'; 
		    echo '<th>Fecha Apertura</th>'; 
		    echo '<th>Horario Apertura</th>';
		    echo '<th>Unidad Ejecutora</th>';
		    echo '<th>Num. Licitacion</th>';
			echo '<th>Estado</th>';
		    
			echo '<th colspan="2">Acciones</th>';
			echo '</tr>';
			
			$q = 'SELECT * FROM licitacion ORDER By fecha_publicacion DESC';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],24,'modificacion')){
					$lnkmodificar_licitacion = 'form_licitacion.php?id_licitacion=' . $row['id_licitacion'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_licitacion = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],24,'baja')){
					$lnkborrar_licitacion = 'form_licitacion.php?id_licitacion=' . $row['id_licitacion'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_licitacion = '#';				
					$src1='iconos_grises/eliminarg.png';
				}
				
				echo '<tr class="modo1">';
				
				echo '<td>' . $row['titulo'] . '</td>';
				echo '<td>' . $row['fecha_publicacion'] . '</td>';
				echo '<td>' . $row['fecha_apertura'] . '</td>';
				echo '<td>' . $row['horario_apertura'] . '</td>';
				
				$row_unidad_ejecutora = $this->consultar_unidad_ejecutora($row['unidad_ejecutora']);
				echo '<td>' . $row_unidad_ejecutora['nombre'] . '</td>';
								
				echo '<td>' . $row['numero_licitacion'] . '</td>';				
				echo '<td>' . $row['estado'] . '</td>';
				
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_licitacion.  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Licitacion"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_licitacion.  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Licitacion"></a></td>';
				$fila++;
			}
			echo '</table>';
			$bd = NULL;
		}		
	
	// FIN METODOS RELACIONADOS CON PROYECTOS LICITACIONES
		
	// INICIO METODOS RELACIONADOS CON PROYECTOS GESTION UNIDO FORMULARIOS
		function ultimo_id_proyecto_g_u(){
			// Fecha: 21 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_proyecto_g_u) as maximo FROM proyecto_g_u';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}	
	
		function agregar_proyecto_g_u($descripcion, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			//echo  $nombre_usuario . " - " . $contrasenia . " - " . $nombre . " - " . $apellido . " - " . $email . " - " . $nivel_acceso;
			$this->excecuteQuery("INSERT INTO proyecto_g_u (descripcion, archivo) VALUES ('$descripcion', '$archivo')");
		}

		function borrar_proyecto_g_u($id_proyecto_g_u, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `proyecto_g_u` WHERE `id_proyecto_g_u` = '$id_proyecto_g_u'");
			if ($archivo){			
				unlink('../PDF/'.$archivo);
			}
		}
		
		function borrar_archivo_proyecto_g_u($id_proyecto_g_u, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE proyecto_g_u SET archivo='' WHERE id_proyecto_g_u='$id_proyecto_g_u'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function modificar_proyecto_g_u($id_proyecto_g_u, $descripcion, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE proyecto_g_u SET descripcion='$descripcion', archivo='$archivo' WHERE id_proyecto_g_u='$id_proyecto_g_u'");
		}
		
		function consultar_proyecto_g_u($id_proyecto_g_u){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM proyecto_g_u WHERE id_proyecto_g_u ="' . $id_proyecto_g_u . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function get_path_sh_file($id){
			switch((int)$id){
				case 3: //INTEMA
					$ruta = "seg_e_hig/intema/";
				break;
				case 4: //IIB
					$ruta = "seg_e_hig/iib/";
				break;
				case 5: //IFIMAR
					$ruta = "seg_e_hig/ifimar/";
				break;
				case 6: //INBIOTEC
					$ruta = "seg_e_hig/inbiotec/";
				break;
				case 7: //IIMYC
					$ruta = "seg_e_hig/iimyc/";
				break;
				case 10: //UNIHDO
					$ruta = "seg_e_hig/unihdo/";
				break;
				case 11: //CCT-MDP
					$ruta = "seg_e_hig/cct/";
				break;
				default:
				break;
			}
			return $ruta??null;
		}
		function consultar_sh_file($id){
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
	
			$q = 'SELECT * FROM seg_e_hig_files WHERE id = ' . $id;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function agregar_sh_file($instituto, $descripcion, $archivo){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
			
			$sFecha = date("Y-m-d");
			$q = "INSERT INTO seg_e_hig_files VALUES(0,$instituto,'$descripcion','$archivo','$sFecha')";
			$r = $this->excecuteQuery($q);
			$r = $this->excecuteQuery("SELECT LAST_INSERT_ID() as id");
			$row = mysqli_fetch_assoc($r);
			$iLast = (int)$row['id'];
			$r = $this->excecuteQuery("UPDATE seg_e_hig_files SET archivo = '$iLast"."$archivo' WHERE id = $iLast");
			return $iLast;
		}

		function actualizar_sh_file($id, $instituto, $descripcion, $archivo){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$sFecha = date("Y-m-d");
			if($archivo){
				$sArch = " archivo = '$id"."$archivo',";
			}else{
				$sArch = "";
			}
			
			$q = "UPDATE seg_e_hig_files SET instituto = $instituto, descripcion = '$descripcion', $sArch fecha = '$sFecha' WHERE id = $id";
			$r = $this->excecuteQuery($q);
			return $id;
		}

		function quitar_sh_file($id, $instituto, $archivo){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = "SELECT archivo, instituto FROM seg_e_hig_files WHERE id = $id";
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_assoc($r);
			$path = $this->get_path_sh_file((int)$row['instituto']);
			unlink("../PDF/".$path.$row['archivo']);

			$q = "UPDATE seg_e_hig_files SET archivo = '' WHERE id = $id";
			$r = $this->excecuteQuery($q);
		}

		function borrar_sh_file($id){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = "SELECT archivo, instituto FROM seg_e_hig_files WHERE id = $id";
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_assoc($r);
			$path = $this->get_path_sh_file((int)$row['instituto']);
			if($row['archivo']){
				unlink("../PDF/".$path.$row['archivo']);
			}
			$q = "DELETE FROM seg_e_hig_files WHERE id = $id";
			$r = $this->excecuteQuery($q);
			return $r;
		}

		function lista_proyectos_g_u(){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Descripcion</th>';
			echo '<th>Archivo</th>'; 
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = '
			SELECT * FROM proyecto_g_u';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],23,'modificacion')){
					$lnkmodificar_proyecto_g_u = 'form_proyecto_g_u.php?id_proyecto_g_u=' . $row['id_proyecto_g_u'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_proyecto_g_u = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],23,'baja')){
					$lnkborrar_proyecto_g_u = 'form_proyecto_g_u.php?id_proyecto_g_u=' . $row['id_proyecto_g_u'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_proyecto_g_u = '#';				
					$src1='iconos_grises/eliminarg.png';
				}
				
				echo '<tr class="modo1">';
					
				echo '<td>' . $row['descripcion'] .'</td>';
				echo '<td>' . $row['archivo'] . '</td>';				
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_proyecto_g_u .  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Proyecto CI"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_proyecto_g_u .  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Proyecto CI"></a></td>';
				$fila++;
			}
			echo '</table>';
		}

		function filtra_sh_files($iUE=null,$sDate=null,$sDesc=null){
			if($iUE){
				$sWhere ="WHERE instituto = $iUE";
			}
			if($sDate){
				if($sWhere){
					$sWhere.=" AND fecha = '$sDate'";
				}else{
					$sWhere ="WHERE fecha = '$sDate'";
				}
			}
			if($sDesc){
				if($sWhere){
					$sWhere.=" AND descripcion LIKE '$sDesc%'";
				}else{
					$sWhere ="WHERE descripcion LIKE '$sDesc%'";
				}
			}
			
			return $sWhere;
		}
		
		function lista_sh_files($oPostData){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			//print_r($oPostData);

			$sWhere = 'WHERE 1';

			if(isset($oPostData['sAction']) && ($oPostData['sAction'] == "filter")){
				$sWhere = $this->filtra_sh_files($oPostData['iUE'],$oPostData['sDate'],$oPostData['sDesc']);
			}
			
			$vData = $this->getUEs();
			echo '<form name="frmFiltro" method="POST" action="lista_sh_files.php">';
			echo '<input type="hidden" name="sAction" value="filter">';
			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="left">';
			//TITULOS
			echo '<tr>';
		    echo '<th colspan="40">Filtros</th>';
			echo '</tr>';
			echo '<tr>';
			//UE
			echo '	<td class="modo1">';
			echo '		<span>U.E</span>';
			echo '	</td>';
			echo '	<td class="modo1" style="width:254px;">';
			echo '		<select name="iUE">';
			echo '			<option value="0">---</option>';
			foreach($vData as $oUE){
				if (isset($oPostData['iUE'])){
					if((int)$oPostData['iUE'] == (int)$oUE['id_unidad_ejecutora']){
						echo '		<option value="'.$oUE['id_unidad_ejecutora'].'" selected>'.$oUE['nombre'].'</option>';
					}else{
						echo '		<option value="'.$oUE['id_unidad_ejecutora'].'">'.$oUE['nombre'].'</option>';
					}
				}
			}
			echo '		</select>';
			echo '	</td>';
			//FECHA
			echo '	<td class="modo1">';
			echo '		<span>Fecha</span>';
			echo '	</td>';
			echo '	<td class="modo1" align="left">';
			echo '		<select name="sDate">';
			echo '			<option value="0">---</option>';
			$vData = $this->getSHFilesDates();
			foreach($vData as $sDates){
				if (isset($oPostData['sDate'])){
					if($oPostData['sDate'] == $sDates['fecha']){
						echo '		<option value="'.$sDates['fecha'].'" selected>'.convertir_fecha($sDates['fecha']).'</option>';
					}else{
						echo '		<option value="'.$sDates['fecha'].'">'.convertir_fecha($sDates['fecha']).'</option>';
					}
				}
			}
			echo '		</select>';
			echo '	</td>';
			echo '</tr>';
			//DESCRIPCION
			echo '<tr>';
			echo '	<td class="modo1" style="width:239px">';
			echo '		<span>Descripci&oacute;n</span>';
			echo '	</td>';
			if (isset($oPostData['sDesc'])){
				echo '	<td class="modo1" align="left" colspan="2">';
				echo '		<input type="text" name ="sDesc" value="'.$oPostData['sDesc'].'" style="width:311px"></input>';
				echo '	</td>';
			}
			echo '	<td class="modo1">';
			echo '		<input type="submit" value="Filtrar"/>';
			echo '	</td>';
			echo '</tr>';	
			echo '<tr style="height:20px"><td></td></tr>';
			echo '</table>';
			echo '</form>';

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th style="width:60px">Fecha</th>';
		    echo '<th>Descripcion</th>';
			echo '<th>Archivo</th>'; 
			echo '<th>Unidad Ejecutora</th>'; 
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = 'SELECT * FROM seg_e_hig_files '.$sWhere.' ORDER BY instituto, fecha DESC';
			
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],30,'modificacion')){
					$lnkmodificar_proyecto_c_i = 'form_sh_files.php?id=' . $row['id'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_proyecto_c_i = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],30,'baja')){
					$lnkborrar_proyecto_c_i = 'form_sh_files.php?id=' . $row['id'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_proyecto_c_i = '#';				
					$src1='iconos_grises/eliminarg.png';
				}
				
				echo '<tr class="modo1">';
					
				echo '<td>' . convertir_fecha($row['fecha']) .'</td>';
				echo '<td>' . $row['descripcion'] .'</td>';
				echo '<td>' . $row['archivo'] . '</td>';
				$instituto = $this->getUE((int)$row['instituto']);

				echo '<td>' . $instituto['nombre'] .'</td>';
				
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_proyecto_c_i .  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Proyecto CI"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_proyecto_c_i .  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Proyecto CI"></a></td>';
				$fila++;
				echo "</tr>";
			}
			echo '</table>';
		}

	// FIN METODOS RELACIONADOS CON PROYECTOS GESTION UNIDO FORMULARIOS
	
	
	// INICIO METODOS RELACIONADOS CON PROYECTOS UNIDADES EJECUTORAS
	
		function ultimo_id_proyecto_u_e(){
			// Fecha: 21 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_unidad_ejecutora) as maximo FROM proyecto_u_e';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
	
		function agregar_proyecto_u_e($descripcion, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("INSERT INTO proyecto_u_e (descripcion, archivo) VALUES ('$descripcion', '$archivo')");
		}

		function borrar_proyecto_u_e($id_proyecto_u_e, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `proyecto_u_e` WHERE `id_unidad_ejecutora` = '$id_proyecto_u_e'");
			if ($archivo){
				unlink('../PDF/'.$archivo);
			}
		}
		
		function borrar_archivo_proyecto_u_e($id_proyecto_u_e, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE proyecto_u_e SET archivo='' WHERE id_unidad_ejecutora='$id_proyecto_u_e'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function modificar_proyecto_u_e($id_proyecto_u_e, $descripcion, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE proyecto_u_e SET descripcion='$descripcion', archivo='$archivo' WHERE id_unidad_ejecutora='$id_proyecto_u_e'");
		}
		
		function consultar_proyecto_u_e($id_proyecto_u_e){
			// Fecha: 15 de Enero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * FROM proyecto_u_e WHERE id_unidad_ejecutora="' . $id_proyecto_u_e . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_proyectos_u_e($nombre_usuario){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Descripcion</th>';
			echo '<th>Archivo</th>'; 
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = '
			SELECT * FROM proyecto_u_e';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_proyecto_u_e = 'form_proyecto_u_e.php?id_unidad_ejecutora=' . $row['id_unidad_ejecutora'] . '&opcion=3';
				$lnkborrar_proyecto_u_e = 'form_proyecto_u_e.php?id_unidad_ejecutora=' . $row['id_unidad_ejecutora'] . '&opcion=2';				
				
				echo '<tr class="modo1">';
				echo '<td>' . $row['descripcion'] .'</td>';
				echo '<td>' . $row['archivo'] . '</td>';

				if($this->checkPerm($_SESSION["id_usuario"],22,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_proyecto_u_e .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
				if($this->checkPerm($_SESSION["id_usuario"],22,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_proyecto_u_e .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				$fila++;
			}
			echo '</table>';
		}

	// FIN METODOS RELACIONADOS CON PROYECTOS UNIDADES EJECUTORAS
	
	// INICIO METODOS RELACIONADOS CON PROYECTOS COOP INTERNACIONAL
	
		function ultimo_id_proyecto_c_i(){
			// Fecha: 21 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT Max(id_proyecto_c_i) as maximo FROM proyecto_c_i';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
		
		function agregar_proyecto_c_i($descripcion, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("INSERT INTO proyecto_c_i (descripcion, archivo) VALUES ('$descripcion', '$archivo')");
		}

		function borrar_proyecto_c_i($id_proyecto_c_i, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("DELETE FROM `proyecto_c_i` WHERE `id_proyecto_c_i` = '$id_proyecto_c_i'");
			if ($archivo){
				unlink('../PDF/'.$archivo);
			}
		}
		
		function borrar_archivo_proyecto_c_i($id_proyecto_c_i, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE proyecto_c_i SET archivo='' WHERE id_proyecto_c_i='$id_proyecto_c_i'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function modificar_proyecto_c_i($id_proyecto_c_i, $descripcion, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE proyecto_c_i SET descripcion='$descripcion', archivo='$archivo' WHERE id_proyecto_c_i='$id_proyecto_c_i'");
		}
		
		function consultar_proyecto_c_i($id_proyecto_c_i){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$q = 'SELECT * FROM proyecto_c_i WHERE id_proyecto_c_i ="' . $id_proyecto_c_i . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_proyectos_c_i(){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Descripcion</th>';
			echo '<th>Archivo</th>'; 
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = '
			SELECT * FROM proyecto_c_i';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],21,'modificacion')){
					$lnkmodificar_proyecto_c_i = 'form_proyecto_c_i.php?id_proyecto_c_i=' . $row['id_proyecto_c_i'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_proyecto_c_i = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],21,'baja')){
					$lnkborrar_proyecto_c_i = 'form_proyecto_c_i.php?id_proyecto_c_i=' . $row['id_proyecto_c_i'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_proyecto_c_i = '#';				
					$src1='iconos_grises/eliminarg.png';
				}
				
				echo '<tr class="modo1">';
					
				echo '<td>' . $row['descripcion'] .'</td>';
				echo '<td>' . $row['archivo'] . '</td>';
				
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_proyecto_c_i .  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Proyecto CI"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_proyecto_c_i .  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Proyecto CI"></a></td>';
				$fila++;
			}
			echo '</table>';
		}

	// FIN METODOS RELACIONADOS CON PROYECTOS COOP INTERNACIONAL
			
	// INICIO METODOS RELACIONADOS CON PROYECTOS PIP 
	
		function ultimo_id_proyecto_pip(){
			// Fecha: 21 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
		
			$q = 'SELECT Max(id_proyecto_pip) as maximo FROM proyecto_pip';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
	
		function agregar_proyecto_pip($descripcion, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
		
			$this->excecuteQuery("INSERT INTO proyecto_pip (descripcion, archivo) VALUES ('$descripcion', '$archivo')");
		}

		function borrar_proyecto_pip($id_proyecto_pip, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
		
			$this->excecuteQuery("DELETE FROM `proyecto_pip` WHERE `id_proyecto_pip` = '$id_proyecto_pip'");
			if ($archivo){
				unlink('../PDF/'.$archivo);
			}
		}
		
		function borrar_archivo_proyecto_pip($id_proyecto_pip, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
		
			$this->excecuteQuery("UPDATE proyecto_pip SET archivo='' WHERE id_proyecto_pip='$id_proyecto_pip'");
			if ($archivo){
				unlink('../PDF/'.$archivo);					
			}
		}

		function modificar_proyecto_pip($id_proyecto_pip, $descripcion, $archivo){
			// Fecha: 15 de Enero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
		
			$this->excecuteQuery("UPDATE proyecto_pip SET descripcion='$descripcion', archivo='$archivo' WHERE id_proyecto_pip='$id_proyecto_pip'");
		}
		
		function consultar_proyecto_pip($id_proyecto_pip){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022
		
			$q = '
				SELECT * FROM proyecto_pip
				WHERE id_proyecto_pip ="' . $id_proyecto_pip . '"
				 ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_proyectos_pip(){
			// Fecha: 15 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022
		
			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Descripcion</th>';
			echo '<th>Archivo</th>'; 
		    echo '<th colspan="2">Acciones</tr>';			
			
			$q = '
			SELECT * FROM proyecto_pip';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				if($this->checkPerm($_SESSION["id_usuario"],20,'modificacion')){
					$lnkmodificar_proyecto_pip = 'form_proyecto_pip.php?id_proyecto_pip=' . $row['id_proyecto_pip'] . '&opcion=3';
					$src = 'actualizar_datos.png';
				}else{
					$lnkmodificar_proyecto_pip = '#';
					$src = 'iconos_grises/actualizar_datosg.png';
				}
				
				if($this->checkPerm($_SESSION["id_usuario"],20,'baja')){
					$lnkborrar_proyecto_pip = 'form_proyecto_pip.php?id_proyecto_pip=' . $row['id_proyecto_pip'] . '&opcion=2';				
					$src1='eliminar.png';
				}else{
					$lnkborrar_proyecto_pip = '#';				
					$src1='iconos_grises/eliminarg.png';
				}
				
				echo '<tr class="modo1">';
					
				echo '<td>' . $row['descripcion'] .'</td>';
				echo '<td>' . $row['archivo'] . '</td>'; 
				echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_proyecto_pip .  '><img src="'.$src1.'" width="30" height="30" border="0" alt="Borrar Proyecto PIP"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_proyecto_pip .  '><img src="'.$src.'" width="30" height="30" border="0" alt="Modificar Proyecto PIP"></a></td>';
				$fila++;
			}
			echo '</table>';
		}

	// FIN METODOS RELACIONADOS CON PROYECTOS PIP
	
	
	// FIN METODOS RELACIONADOS CON WEB
	//-------------------------------------------------------------------------------------------
	
	// INICIO METODOS RELACIONADOS CON EL PROGRAMA ADMINISTRATIVO



	//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON EL MODULO DE NOTAS

		function borrar_nota($id_nota, $anio){
			// Fecha: 7 de Febrero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$this->excecuteQuery("DELETE FROM `nota` WHERE `id_nota` = '$id_nota'");
		}
	
		function lista_notas($nombre_usuario,$vData){
			// Fecha: 11 de Enero de 2013
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$iMaxRows = 15;
			
			$sHtml = 'B&uacute;squeda:
			<form name="frmBus" action="lista_notas.php" method="POST">
			<input type="checkbox" name="chkBus" value="n.fecha" {n.fecha}><label style="font-size:12px">Fecha</label></input>
			<input type="text" name="dateBus" value="{dateBus}" style="width:200px"/><label style="font-size:12px"> (AAAA-MM-DD)</label><br/>
			
			<input type="radio" name="opBus" value="n.numero_nota"{n.numero_nota}><label style="font-size:12px">Num. Nota/A&ntilde;o</label>
			<input type="radio" name="opBus" value="n.destinatario"{n.destinatario}><label style="font-size:12px">Destinatarios</label>
			<input type="radio" name="opBus" value="n.texto"{n.texto}><label style="font-size:12px">Texto</label>
			<input type="text" name="txtBus" value="{txtBus}" style="width:200px"/>
			<input type="submit" name="btnBus" value="Buscar"/>
			</form>';
			
			if (array_key_exists('txtBus', $vData))
				$varTxtBus = $vData['txtBus'];
			else
				$varTxtBus = "";

			if (array_key_exists('opBus', $vData))
				$varOpBus = $vData['opBus'];
			else
				$varOpBus = "";

			if (array_key_exists('chkBus', $vData))
				$varChkBus = $vData['chkBus'];
			else
				$varChkBus = "";

			if (array_key_exists('dateBus', $vData))
				$varDateBus = $vData['dateBus'];
			else
				$varDateBus = "";			
			
			
			if(isset($vData['opBus']) && $vData['opBus']){
				$sHtml = str_replace("{".$vData['opBus']."}","checked='checked'",$sHtml);
			}else{
				$sHtml = str_replace("{n.numero_nota}","checked='checked'",$sHtml);
			}
			if(isset($vData['chkBus']) && $vData['chkBus']){
				$sHtml = str_replace("{n.fecha}","checked='checked'",$sHtml);
			}else{
				$sHtml = str_replace("{n.fecha}","",$sHtml);
			}

			if(isset($vData['dateBus']) && $vData['dateBus']){
				$sHtml = str_replace("{dateBus}",$vData['dateBus'],$sHtml);
			}else{
				$sHtml = str_replace("{dateBus}","",$sHtml);
			}

			$vTemp = array("{n.numero_nota}","{n.destinatario}","{n.texto}");
			$sHtml = str_replace($vTemp,"",$sHtml);
			$sHtml = str_replace("{txtBus}",$varTxtBus,$sHtml);
			$sHtml = str_replace("{dateBus}",(isset($vData['dateBus'])) ? $vData['dateBus'] : "",$sHtml);
			echo $sHtml;

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>N&uacute;m._Nota/A&ntilde;o</th>';
			echo '<th>Fecha_Nota</th>'; 
		    echo '<th>Destinatario_Nota</th>'; 
		    echo '<th>Lugar Trabajo</th>';
		    echo '<th>Texto</th>';
		    echo '<th colspan="3">Acciones</th></tr>';			

			$sWhere = '';
			if(isset($vData['txtBus']) && $vData['txtBus']!=""){
				if($vData['opBus'] != 'n.numero_nota'){
					$sWhere = 'WHERE '.$vData['opBus'].' LIKE "%'.$vData['txtBus'].'%"';
				}else{
					$nNota = explode("/",$vData['txtBus']);
					
					$sWhere = 'WHERE n.numero_nota = '.$nNota[0];
					if(isset($nNota[1])){
						$sWhere.=' AND n.anio_numero_nota = '.$nNota[1];
					}
				}
			}
			
			if(isset($vData['dateBus']) && $vData['dateBus']!=""){
				if($sWhere ==''){
					$sWhere = 'WHERE n.fecha LIKE "'.$vData['dateBus'].'%"';
				}else{
					$sWhere .= ' AND n.fecha LIKE "'.$vData['dateBus'].'%"';
				}
			}

			$iPagActual = (isset($_GET['pag']))?$_GET['pag']:1;
			$iLimit = ($iPagActual -1) * $iMaxRows;
			
			$q = 'SELECT SQL_CALC_FOUND_ROWS * FROM nota n '.$sWhere.' ORDER By n.id_nota DESC LIMIT '.$iLimit.','.$iMaxRows;
			$r = $this->excecuteQuery($q);

			$p = $this->excecuteQuery("SELECT CEIL(FOUND_ROWS()/$iMaxRows) as total");
			$results = mysqli_fetch_assoc($p);
			$iTotalPag = $results['total'];
			
			if($r){
				while ( $row = mysqli_fetch_array($r) ){
					$lnkmodificar_nota = 'form_nota.php?id_nota=' . $row['id_nota'] . '&opcion=3';
					$lnkborrar_nota = 'form_nota.php?id_nota=' . $row['id_nota'] . '&opcion=2';
					$lnkimprimir_nota = 'nota_pdf.php?id_nota='.$row['id_nota'].'&fecha='.convertir_fecha($row['fecha']).'&confecciono='.rawurlencode($row['confecciono']);
				
					echo '<tr class="modo1">';
					echo '<td>' . $row['numero_nota'] .'/'. $row['anio_numero_nota'].'</td>';
					echo '<td>' . convertir_fecha($row['fecha']) . '</td>';
					echo '<td>' . $row['destinatario'] . '</td>';
					echo '<td>' . $row['lugar_trabajo'] . '</td>';				
					echo '<td>' . substr($row['texto'], 0, 55)	. '...</td>';
					
					if($this->checkPerm($_SESSION["id_usuario"],6,'baja')){				
						echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_nota .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
					if($this->checkPerm($_SESSION["id_usuario"],6,'modificacion')){	
						echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_nota .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					echo '<td align="center"><font color="#333333"><a href=' . $lnkimprimir_nota.  '><img src="acrobat.png" width="30" height="30" border="0" alt="Imprimir Nota"></a></td>';
				}
			}
			$iNextPage = ($iPagActual<$iTotalPag)?($iPagActual+1):$iPagActual;
			$iPrevPage = ($iPagActual > 1)?($iPagActual-1):$iPagActual;
			
			echo "<tr>
					<td colspan='10'>
						<a href='lista_notas.php?pag=$iPrevPage&opBus=".$varOpBus."&txtBus=".$varTxtBus."&chkBus=".$varChkBus."&dateBus=".$varDateBus."'>Prev </a>
						$iPagActual / $iTotalPag
						<a href='lista_notas.php?pag=$iNextPage&opBus=".$varOpBus."&txtBus=".$varTxtBus."&chkBus=".$varChkBus."&dateBus=".$varDateBus."' value='sig'>Sig</a>
					</td>
				</tr>";
			echo '</table>';
		}
		
		function ultimo_numero_nota(){
			// Fecha: 11 de Enero de 2013
			// Autor: .j
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$q = '
				SELECT Max(numero_nota) as maximo
				FROM nota
			     ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
		
		function agregar_nota($anio_numero_nota, $fecha, $destinatario, $lugar_trabajo, $texto, $referencia, $firmante, $firmante1, $CC, $confecciono, $firma_digital){
			// Fecha: 13 de Enero de 2013
			// Autor: .j
			// Modificado por Vanina 2017 para obtener el numero de nota al grabar
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$destinatario = addslashes($destinatario);
			$lugar_trabajo = addslashes($lugar_trabajo);
			$texto = addslashes(nl2br($texto));
			if(!$firmante or $firmante==''){$firmante='null';}
			if(!$firmante1 or $firmante1==''){$firmante1='null';}		
			$this->excecuteQuery("INSERT INTO nota 
						(numero_nota, 
						anio_numero_nota, 
						fecha, 
						destinatario, 
						lugar_trabajo, 
						texto, 
						referencia, 
						firmante, 
						firmante1, 
						CC, 
						confecciono, 
						firma_digital) 
					SELECT
						IFNULL(MAX(numero_nota), 0)+1,
						'$anio_numero_nota' as anio_numero_nota,
						'$fecha' as fecha,
						'$destinatario' as destinatario,
						'$lugar_trabajo' as lugar_trabajo, 
						'$texto' as texto, 
						'$referencia' as referencia, 
						$firmante as firmante, 
						$firmante1 as firmante1, 
						'$CC' as CC, 
						'$confecciono' as confecciono, 
						'$firma_digital' as firma_digital					
					FROM
						nota
					WHERE anio_numero_nota = '$anio_numero_nota'");

		}
		
		function modificar_nota($numero_nota, $anio_numero_nota, $fecha, $destinatario, $lugar_trabajo, $texto, $referencia, $firmante, $firmante1, $CC, $firma_digital, $id_nota){
			// Fecha: 13 de Enero de 2013
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$destinatario = addslashes($destinatario);
			$lugar_trabajo = addslashes($lugar_trabajo);
			$texto = addslashes(nl2br($texto));
			if(!$firmante or $firmante==''){$firmante='null';}
			if(!$firmante1 or $firmante1==''){$firmante1='null';}	

			$this->excecuteQuery("UPDATE nota SET 
					numero_nota='$numero_nota', 
					anio_numero_nota='$anio_numero_nota', 
					fecha='$fecha', 
					destinatario='$destinatario', 
					lugar_trabajo='$lugar_trabajo', 
					texto='$texto', 
					referencia='$referencia', 
					firmante=$firmante, 
					firmante1=$firmante1, 
					CC='$CC', 
					firma_digital='$firma_digital' 
					WHERE 
						id_nota = $id_nota");
		}
		
		function consultar_nota($id_nota, $anio){
			// Fecha: 13 de Enero de 2013
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			
			$q = '
				SELECT * FROM nota
				WHERE id_nota ="' . $id_nota . '"
				 ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		
	// FIN METODOS RELACIONADOS CON NOTAS

	//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON ORDENES DE COMPRA

		function agregar_orden_compra_encabezado($numero_orden_compra, $anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $confecciono, $firmante, $firma_digital,$id_unidad){
			// Fecha: 18 de Febrero de 2013
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = "INSERT INTO orden_compra 
					(numero_orden_compra, 
					anio_numero_orden_compra, 
					fecha, 
					contacto, 
					proveedor, 
					procedimiento_seleccion, 
					objeto, 
					referencia, 
					confecciono, 
					firmante, 
					firma_digital,
					id_unidad_ejecutora) 
				VALUES ('$numero_orden_compra', 
					'$anio_numero_orden_compra', 
					'$fecha', 
					'$contacto', 
					'$proveedor', 
					'$procedimiento_seleccion',
					'$objeto', 
					'$referencia', 
					'$confecciono', 
					'$firmante', 
					'$firma_digital',
					$id_unidad)";

			$this->excecuteQuery($q);
		}
	
		function modificar_orden_compra_encabezado($numero_orden_compra, $anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $confecciono, $firmante, $firma_digital,$id_unidad){
			// Fecha: 18 de Febrero de 2013
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$this->excecuteQuery("UPDATE orden_compra SET 
					anio_numero_orden_compra=$anio_numero_orden_compra, 
					fecha='$fecha', 
					contacto='$contacto', 
					proveedor='$proveedor', 
					procedimiento_seleccion='$procedimiento_seleccion', 
					objeto='$objeto', 
					referencia='$referencia', 
					confecciono='$confecciono', 
					firmante='$firmante', 
					firma_digital='$firma_digital',
					id_unidad_ejecutora=$id_unidad
				WHERE numero_orden_compra='$numero_orden_compra' AND 
					anio_numero_orden_compra = $anio_numero_orden_compra");
		}
	
		function consultar_numero_orden_compra($numero_orden_compra, $iYear){
			// Fecha: 5 de Febrero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = 'SELECT * FROM orden_compra WHERE numero_orden_compra ="' . $numero_orden_compra . '" AND anio_numero_orden_compra = '.$iYear;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
		
		function consultar_orden_compra($numero_orden_compra, $numero_item, $iYear){
			// Fecha: 31 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			if ($numero_item != 0){
				$q = 'SELECT * FROM orden_compra WHERE numero_orden_compra ="' . $numero_orden_compra . '" AND numero_item ="' . $numero_item . '" AND anio_numero_orden_compra = '.$iYear;
			}else
				$q = 'SELECT * FROM orden_compra WHERE numero_orden_compra ="' . $numero_orden_compra . '" AND anio_numero_orden_compra = '.$iYear;

			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
	
		function ultimo_numero_item_orden_compra($numero_orden_compra, $iYear){
			// Fecha: 31 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
					
			$q = '
				SELECT Max(numero_item) as maximo
				FROM orden_compra WHERE numero_orden_compra ="'. $numero_orden_compra . '" AND anio_numero_orden_compra = '.$iYear;
				
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		

		}

		function modificar_orden_compra($numero_orden_compra, $anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $numero_item, $descripcion_componente, $cantidad, $unidad, $precio_unitario, $signo_moneda, $subtotal, $confecciono, $firmante, $firma_digital, $id_unidad){
			// Fecha: 31 de Diciembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022

			$this->excecuteQuery("UPDATE orden_compra 
					SET anio_numero_orden_compra=$anio_numero_orden_compra, 
					fecha='$fecha', 
					contacto=$contacto, 
					proveedor=$proveedor, 
					procedimiento_seleccion=$procedimiento_seleccion, 
					objeto='$objeto', 
					referencia='$referencia', 
					numero_item=$numero_item, 
					descripcion_componente='$descripcion_componente', 
					cantidad=$cantidad, 
					unidad='$unidad', 
					precio_unitario=$precio_unitario, 
					signo_moneda=$signo_moneda, 
					subtotal=$precio_unitario*$cantidad, 
					confecciono='$confecciono', 
					firmante=$firmante, 
					firma_digital=$firma_digital,
					id_unidad_ejecutora=$id_unidad
					WHERE numero_orden_compra=$numero_orden_compra AND 
						numero_item=$numero_item AND 
						anio_numero_orden_compra = $anio_numero_orden_compra");
		}

		function borrar_movimiento_orden_compra($numero_orden_compra, $numero_item, $iYear){
			// Fecha: 31 de Diciembre de 2012
			// Autor: .j
			//Modificacion Vani: si es el ultimo item no borrar la orden de compra
			$q = 'SELECT count(*) as cantItems
				FROM orden_compra WHERE numero_orden_compra ="'. $numero_orden_compra . '" AND anio_numero_orden_compra = '.$iYear;				
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			$cantItems = $row['cantItems'];
		
			if ($cantItems > 1) {
				$this->excecuteQuery("DELETE FROM `orden_compra` WHERE `numero_orden_compra` = '$numero_orden_compra' AND `numero_item` = '$numero_item' AND anio_numero_orden_compra = $iYear"); }
			else {
				$this->excecuteQuery("UPDATE `orden_compra` SET 
					numero_item = 0,
					descripcion_componente = null,
					cantidad = null,
					unidad = null,
					precio_unitario = null,
					signo_moneda = null,
					subtotal = null
					WHERE `numero_orden_compra` = '$numero_orden_compra' AND `numero_item` = '$numero_item' AND anio_numero_orden_compra = $iYear"); 
			}
		}

		function borrar_orden_compra($numero_orden_compra){
			// Fecha: 31 de Diciembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$this->excecuteQuery("DELETE FROM `orden_compra` WHERE `numero_orden_compra` = '$numero_orden_compra'");
		}

		function agregar_orden_compra($anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $numero_item, $descripcion_componente, $cantidad, $unidad, $precio_unitario, $signo_moneda, $subtotal, $confecciono, $firmante, $firma_digital, $id_unidad){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$this->excecuteQuery("INSERT INTO orden_compra 
						(numero_orden_compra, 
						anio_numero_orden_compra, 
						fecha, 
						contacto, 
						proveedor, 
						procedimiento_seleccion, 
						objeto, 
						referencia, 
						numero_item, 
						descripcion_componente, 
						cantidad, 
						unidad, 
						precio_unitario, 
						signo_moneda, 
						subtotal, 
						confecciono, 
						firmante, 
						firma_digital,
						id_unidad_ejecutora) 
				     SELECT 
						IFNULL(MAX(numero_orden_compra), 0)+1, 
						$anio_numero_orden_compra, 
						'$fecha', 
						$contacto, 
						$proveedor, 
						$procedimiento_seleccion, 
						'$objeto', 
						'$referencia', 
						$numero_item, 
						'$descripcion_componente', 
						$cantidad, 
						'$unidad', 
						$precio_unitario, 
						$signo_moneda, 
						$subtotal, 
						'$confecciono', 
						$firmante, 
						$firma_digital,
						$id_unidad
					FROM orden_compra
					WHERE anio_numero_orden_compra = '$anio_numero_orden_compra'");
					
			$r = $this->excecuteQuery("SELECT 
						MAX(numero_orden_compra) as numero_orden_compra
					FROM orden_compra
					WHERE anio_numero_orden_compra = $anio_numero_orden_compra");		
			$row = mysqli_fetch_array($r);
			echo $row['numero_orden_compra'];
			return $row['numero_orden_compra'];
					
		}

		
		function agregar_orden_compra_item($numero_orden_compra, $anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $numero_item, $descripcion_componente, $cantidad, $unidad, $precio_unitario, $signo_moneda, $subtotal, $confecciono, $firmante, $firma_digital, $id_unidad){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$this->excecuteQuery("INSERT INTO orden_compra 
						(numero_orden_compra, 
						anio_numero_orden_compra, 
						fecha, 
						contacto, 
						proveedor, 
						procedimiento_seleccion, 
						objeto, 
						referencia, 
						numero_item, 
						descripcion_componente, 
						cantidad, 
						unidad, 
						precio_unitario, 
						signo_moneda, 
						subtotal, 
						confecciono, 
						firmante, 
						firma_digital,
						id_unidad_ejecutora) 
				     VALUES 
						($numero_orden_compra, 
						$anio_numero_orden_compra, 
						'$fecha', 
						$contacto, 
						$proveedor, 
						$procedimiento_seleccion, 
						'$objeto', 
						'$referencia', 
						$numero_item, 
						'$descripcion_componente', 
						$cantidad, 
						'$unidad', 
						$precio_unitario, 
						$signo_moneda, 
						$subtotal, 
						'$confecciono', 
						$firmante, 
						$firma_digital,
						$id_unidad)");
		}

		function lista_orden_compra_por_item($numero_orden_compra, $opcion, $iYear){
			// Fecha: 18 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			
			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			$total = 0;
			
			$q = 'SELECT * FROM orden_compra WHERE numero_orden_compra ='. $numero_orden_compra . ' AND anio_numero_orden_compra = '.$iYear.' ORDER By numero_orden_compra, numero_item';
			$r = $this->excecuteQuery($q);
			$cant = 0;
			while ($r and $row = mysqli_fetch_array($r) ){
				if ($row['numero_item'] != '0'){
					if ($cant == 0) { //Imprimo encabezados
						echo '<tr>';
							echo '<th>N&uacute;m. Item</th>';
						echo '<th>Desc. Componente</th>'; 
						echo '<th>Cantidad</th>'; 
						echo '<th>Unidad</th>'; 
						echo '<th>Moneda</th>'; 
						echo '<th>Precio Unitario</th>'; 
						echo '<th>Sub.Total</th>';
						if ($opcion != 1 and $opcion != 2) { // NO MUESTRO LAS ACCIONES PORQUE ELIGIO ALTA O BAJA
								echo '<th colspan="2">Acciones</th>';
						}
						echo '</tr>';						
					}
					$cant++;
					$lnkmodificar_orden_compra = 'form_orden_compra.php?numero_orden_compra='.$row['numero_orden_compra'].'&numero_item='.$row['numero_item'].'&anio='.$iYear.'&opcion=3';
					$lnkborrar_orden_compra = 'form_orden_compra.php?numero_orden_compra='.$row['numero_orden_compra'].'&numero_item='.$row['numero_item'].'&anio='.$iYear.'&opcion=5'; // ACA SE ELIJE ELIMINAR UN REGISTRO DE LA ORDEN DE COMPRA

					echo '<tr class="modo1">';
				
					echo '<td>' . $row['numero_item'] .'</td>';
					echo '<td>' . $row['descripcion_componente'] .'</td>';				
					echo '<td>' . $row['cantidad'] . '</td>';
					echo '<td>' . $row['unidad']. '</td>';
					$row_moneda = $this->consultar_moneda($row['signo_moneda']);				
					echo '<td>' . $row_moneda['signo'] . '</td>';
					echo '<td>' . number_format($row['precio_unitario'], 2, ',' , '.') . '</td>';
					echo '<td>' . number_format($row['subtotal'], 2, ',' , '.') . '</td>';
					$total = $total + $row['subtotal'];
					if ($opcion != 1 and $opcion != 2) { // NO MUESTRO LAS ACCIONES PORQUE ELIGIO ALTA O BAJA
						echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_orden_compra .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';
						echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_orden_compra .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					}
					echo '</tr>';
				}				
			}//end while
			

			echo '<tr class="modo2">';
			echo '<td colspan="9">Total: ' . number_format($total, 2, ',' , '.') . ' </td>';
			echo '</tr>';
			
			
			
			if ($opcion != 2) { // NO MUESTRO el boton de grabar PORQUE ELIGIO BAJA
				echo '<tr><td colspan="9" align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar_encabezado(this.form)" alt="Grabar datos"><img src="grabar_datos.png" width="25" heigth="20" border="0"></button></td></tr>';
			}

			echo '</table>';
			$bd = NULL;	
			return $numero_orden_compra != '' ? $cant : 0;		
		}

		function ultimo_numero_orden_compra(){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j
				
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			
			$q = '
				SELECT Max(numero_orden_compra) as maximo
				FROM orden_compra
			     ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		
		}
	
		function lista_ordenes_compra($nombre_usuario){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$iMaxRows = 15;
			
			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>N&uacute;m.O_C</th>';
		    echo '<th>Fecha_O_C</th>';
			echo '<th>Procedimiento</th>'; 
			echo '<th style="width: 75px;">Monto</th>'; 
			echo '<th>Referencia</th>'; 
		    echo '<th colspan="3">Acciones</tr>';			

			$iPagActual = (isset($_GET['pag']))?$_GET['pag']:1;
			$iLimit = ($iPagActual -1) * $iMaxRows;
			
			//Nota Vani: este group by es porque la tabla guarda header e items todo junto!
			//Ver de normalizarla cuando pueda
			$q = 'SELECT SQL_CALC_FOUND_ROWS 
					anio_numero_orden_compra,
					numero_orden_compra,
					fecha,
					confecciono,
					contacto,
					proveedor,
					procedimiento_seleccion,
					referencia
				FROM 
					orden_compra 
				GROUP BY 
					anio_numero_orden_compra,
					numero_orden_compra, 
					fecha, 
					confecciono, 
					contacto, 
					proveedor, 
					procedimiento_seleccion, 
					referencia
				ORDER By 
					anio_numero_orden_compra DESC, 
					numero_orden_compra DESC 
				LIMIT '.
					$iLimit.','.$iMaxRows;
			$r = $this->excecuteQuery($q);
			$fila = 1;
			$ant_numero_orden_compra = 0;
						
			$p = $this->excecuteQuery("SELECT CEIL(FOUND_ROWS()/$iMaxRows) as total");
			$results = mysqli_fetch_assoc($p);
			$iTotalPag = $results['total'];
			
			while ( $row = mysqli_fetch_array($r) ){
				$act_numero_orden_compra = $row['numero_orden_compra'];
				$lnkmodificar_orden_compra = 'form_orden_compra.php?numero_orden_compra=' . $row['numero_orden_compra'] . '&anio='. $row['anio_numero_orden_compra'] .'&opcion=4';
				$lnkborrar_orden_compra   = 'form_orden_compra.php?numero_orden_compra='  . $row['numero_orden_compra'] . '&anio='. $row['anio_numero_orden_compra'] .'&opcion=2';
				$lnkimprimir_orden_compra = 'orden_compra_pdf.php?numero_orden_compra='   . $row['numero_orden_compra'] . '&fecha='.convertir_fecha($row['fecha']).'&anio_numero_orden_compra='.$row['anio_numero_orden_compra'].'&confecciono='.rawurlencode($row['confecciono']).'&contacto='.$row['contacto'].'&proveedor='.$row['proveedor'];

				if ($act_numero_orden_compra != $ant_numero_orden_compra){
					echo '<tr class="modo1">';
					echo '<td>' . $row['numero_orden_compra'] .'/'. $row['anio_numero_orden_compra'] .'</td>';
					echo '<td>' . convertir_fecha($row['fecha']) .'</td>';					
					$row_procedimiento_seleccion = $this->consultar_procedimiento_seleccion($row['procedimiento_seleccion']);				
					echo '<td>' . $row_procedimiento_seleccion['descripcion'] . '</td>';
					
					//Si no tiene items grabados devuelvo $0
					$q1 = 'SELECT 
							FORMAT(SUM(IFNULL(oc.subtotal,0)),2) as fTotal, 
							IFNULL(m.signo,\'$\') as signo
						FROM orden_compra oc 
						LEFT OUTER JOIN moneda m ON m.id_moneda = oc.signo_moneda 
						WHERE oc.numero_orden_compra = '.$act_numero_orden_compra.' AND oc.anio_numero_orden_compra = '.$row['anio_numero_orden_compra'].'
						      and oc.numero_item is not null
						GROUP BY m.signo';
					$r1 = $this->excecuteQuery($q1);
					$numResults = mysqli_num_rows($r1);
					if ($numResults > 0) {
						$row1 = mysqli_fetch_assoc($r1);
						$monto = $row1['signo'].' '. $row1['fTotal'];
					} else {
						$monto = '$ 0.00';
					}
					
					echo '<td>' . $monto . '</td>';
					echo '<td>' . $row['referencia'] . '</td>';
					if($this->checkPerm($_SESSION["id_usuario"],3,'baja')){				
						echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_orden_compra .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
					if($this->checkPerm($_SESSION["id_usuario"],3,'modificacion')){	
						echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_orden_compra .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';					
					$ant_numero_orden_compra = $act_numero_orden_compra;
					echo '<td align="center"><font color="#333333"><a target="_blank" href=' . $lnkimprimir_orden_compra.  '><img src="acrobat.png" width="30" height="30" border="0" alt="Imprimir Orden Compra"></a></td>';
					echo '</tr>';

				}
				$fila++;
			}
			$iNextPage = ($iPagActual<$iTotalPag)?($iPagActual+1):$iPagActual;
			$iPrevPage = ($iPagActual > 1)?($iPagActual-1):$iPagActual;
			
			echo "<tr>
					<td colspan='10'>
						<a href='lista_ordenes_compra.php?pag=$iPrevPage'>Prev </a>
						$iPagActual / $iTotalPag
						<a href='lista_ordenes_compra.php?pag=$iNextPage' value='sig'>Sig</a>
					</td>
				</tr>";
			
			echo '</table>';
			$bd = NULL;			
		}
		
	// FIN METODOS RELACIONADOS CON ORDENES DE COMPRA

	//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON PROCEDIMIENTOS

		function agregar_procedimiento($descripcion){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("INSERT INTO 
						procedimiento (descripcion, baja) 
					VALUES 
						('$descripcion',0)");
		}

		function borrar_procedimiento($id_procedimiento){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j. Modificado por Vanina por baja logica		

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE `procedimiento` 
					SET baja = 1
					WHERE `id_procedimiento` = '$id_procedimiento'");
					
		}
		
		function modificar_procedimiento($id_procedimiento, $descripcion){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE procedimiento 
					SET descripcion='$descripcion' 
					WHERE id_procedimiento='$id_procedimiento'");
		}
		
		function consultar_procedimiento_seleccion($id_procedimiento){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = 'SELECT * FROM procedimiento
				WHERE id_procedimiento ="' . $id_procedimiento . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_procedimientos($nombre_usuario){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Descripci&oacute;n</th>';
		    echo '<th colspan="3">Acciones</tr>';			
			
			$q = 'SELECT * 
				FROM procedimiento 
				WHERE baja=0
				ORDER By descripcion';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_procedimiento = 'form_procedimiento.php?id_procedimiento=' . $row['id_procedimiento'] . '&opcion=3';
				$lnkborrar_procedimiento = 'form_procedimiento.php?id_procedimiento=' . $row['id_procedimiento'] . '&opcion=2';
				$lnkvisualizar_procedimiento = 'form_procedimiento_ver.php?id_procedimiento=' . $row['id_procedimiento'];				

				echo '<tr class="modo1">';
				echo '<td>' . $row['descripcion'] .'</td>';
				if($this->checkPerm($_SESSION["id_usuario"],18,'baja')){
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_procedimiento .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
				if($this->checkPerm($_SESSION["id_usuario"],18,'modificacion')){
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_procedimiento .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';					
				echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_procedimiento .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				$fila++;
			}
			echo '</table>';
		}
		
		function listar_procedimientos($id_procedimiento=0){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = 'SELECT 
					* 
				FROM 
					procedimiento
				WHERE 
					baja = 0 or 
					id_procedimiento = '.$id_procedimiento. ' 
				ORDER BY 
					id_procedimiento';
					
			$r = $this->excecuteQuery($q);
			echo '<td>';
			echo '<select name="procedimiento">';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id_procedimiento'] == $id_procedimiento){
					echo '<option selected value='. $row['id_procedimiento'] .'>'.  $row['descripcion'] .'</option>';
				}else{
					echo '<option value='. $row['id_procedimiento'] .'>'. $row['descripcion'] .'</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}

		function getProcedures($id_procedimiento=0){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$q = 'SELECT 
					* 
				FROM 
					procedimiento
				WHERE 
					baja = 0 or 
					id_procedimiento = '.$id_procedimiento. ' 
				ORDER BY 
					id_procedimiento';
			$r = $this->excecuteQuery($q);
			
			while ( $row = mysqli_fetch_assoc($r) ){
				$vData[] = $row;
			}
			return $vData;
		}

		// FIN METODOS RELACIONADOS CON PROCEDIMIENTOS

		//-------------------------------------------------------------------------------------------
		// INICIO METODOS RELACIONADOS CON ORDENES DE PAGO

		function borrar_orden_pago($numero_orden_pago, $iYear){
			// Fecha: 06 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			$q = "DELETE FROM `orden_pago` WHERE `numero_orden_pago` = '$numero_orden_pago' AND anio_numero_orden_pago = $iYear";
			$this->excecuteQuery($q);
		}

		function modificar_orden_pago($numero_orden_pago, $fecha, $anio_numero_orden_pago, $confeccionador, $proveedor, $usa_banco, 
					      $factura, $objeto, $asignacion_rendicion, $id_moneda, $importe, $aclaraciones, $firmante, 
					      $cm, $iva, $alicuota, $id_unidad_ejecutora, $cert_ret, $cuenta, $firmante2,
					      $forma_pago, $no_enviar_aviso_pago_adicional, $id_titular_aviso_pago, $condicion_venta_ce){
			// Fecha: 06 de Diciembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			if(is_null($id_titular_aviso_pago) or $id_titular_aviso_pago=="")
				$id_titular_aviso_pago = "NULL";

			$sSQL = "UPDATE orden_pago 
				SET 	fecha='$fecha', 
					anio_numero_orden_pago='$anio_numero_orden_pago', 
					confeccionador='$confeccionador', 
					proveedor='$proveedor', 
					usa_banco=$usa_banco, 
					factura='$factura', 
					objeto='$objeto', 
					asignacion_rendicion='$asignacion_rendicion', 
					id_moneda=$id_moneda, 
					importe='$importe', 
					aclaraciones='$aclaraciones', 
					firmante='$firmante', 
					cm = $cm, 
					iva = $iva, 
					alicuota = $alicuota, 
					cert_ret = $cert_ret, 
					cuenta = $cuenta,
					firmante2='$firmante2',
					forma_pago=$forma_pago,
					no_enviar_aviso_pago_adicional=$no_enviar_aviso_pago_adicional,
					id_titular_aviso_pago=$id_titular_aviso_pago,
					condicion_venta_ce=$condicion_venta_ce
				WHERE numero_orden_pago='$numero_orden_pago' AND 
					anio_numero_orden_pago='$anio_numero_orden_pago'";
			$this->excecuteQuery($sSQL);
		}

		function agregar_orden_pago($numero_orden_pago, $fecha, $anio_numero_orden_pago, $confeccionador, $proveedor, $usa_banco, 
					    $factura, $objeto, $asignacion_rendicion, $id_moneda, $importe, $aclaraciones, $confecciono, 
					    $firmante, $cm, $iva, $alicuota, $id_unidad_ejecutora, $cert_ret, $cuenta, $firmante2,
					    $forma_pago, $no_enviar_aviso_pago_adicional, $id_titular_aviso_pago, $condicion_venta_ce){
			// Fecha: 06 de Diciembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			if(is_null($id_titular_aviso_pago) or $id_titular_aviso_pago=="")
				$id_titular_aviso_pago = "NULL";

			$sSQL = "INSERT INTO orden_pago 
					(numero_orden_pago, 
					fecha, 
					anio_numero_orden_pago, 
					confeccionador, 
					proveedor, 
					usa_banco, 
					factura, 
					objeto, 
					asignacion_rendicion, 
					id_moneda, 
					importe, 
					aclaraciones, 
					confecciono, 
					firmante, 
					cm, 
					iva, 
					alicuota, 
					id_unidad_ejecutora, 
					cert_ret, 
					cuenta,
					firmante2,
					forma_pago,
					no_enviar_aviso_pago_adicional,
					id_titular_aviso_pago,
					condicion_venta_ce) 
				VALUES 
					('$numero_orden_pago', 
					'$fecha', 
					'$anio_numero_orden_pago', 
					'$confeccionador', 
					'$proveedor', 
					$usa_banco, 
					'$factura', 
					'$objeto', 
					'$asignacion_rendicion', 
					$id_moneda, 
					'$importe', 
					'$aclaraciones', 
					'$confecciono', 
					$firmante, 
					$cm, 
					$iva, 
					$alicuota, 
					$id_unidad_ejecutora, 
					$cert_ret, 
					$cuenta,
					$firmante2,
					$forma_pago,
					$no_enviar_aviso_pago_adicional,
					$id_titular_aviso_pago,
					$condicion_venta_ce)";
			//echo $sSQL; exit;
			return $this->excecuteQuery($sSQL);
		}
		
		function consultar_orden_pago($numero_orden_pago, $iYear){
			// Fecha: 06 de Diciembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = 'SELECT 
					op.*,
					case op.forma_pago
						when 1 then \'Cheque\'
						when 2 then \'Transferencia\'
						when 3 then \'Pago de servicios\'
					end as forma_pago_descripcion,
					CONCAT(t.apellido,\' \',t.nombre) as titular_aviso_pago_nombre,
					t.email as titular_aviso_pago_email,
					m.signo as signo_moneda
				FROM orden_pago op
					LEFT OUTER JOIN titular t ON
					op.id_titular_aviso_pago = t.id_titular
					LEFT OUTER JOIN moneda m ON
					op.id_moneda = m.id_moneda
				WHERE numero_orden_pago ="' . $numero_orden_pago . '" AND anio_numero_orden_pago = "'.$iYear.'"';
			
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_ordenes_pago($nombre_usuario,$vData){
			// Fecha: 06 de Diciembre de 2012
			// Autor: .j
			// Modificado por Sebasti&aacute;n Salerno

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$iMaxRows = 15;	

			$numero_orden_pago = isset($vData['numero_orden_pago'])?$vData['numero_orden_pago']:'';
			$fecha = isset($vData['fecha'])?$vData['fecha']:'';
			$anio = isset($vData['anio'])?$vData['anio']:'';
			$factura = isset($vData['factura'])?$vData['factura']:'';
			$importe = isset($vData['importe'])?$vData['importe']:'';
			$emisor = isset($vData['emisor'])?$vData['emisor']:'';
			$proveedor = isset($vData['proveedor'])?$vData['proveedor']:'';
			$cuit = isset($vData['cuit'])?$vData['cuit']:'';
			$cert_ret = isset($vData['cert_ret'])?$vData['cert_ret']:'';

			$sHtml = '<div class="title">B&uacute;squeda: </div>
			<form name="frmBus" action="lista_ordenes_pago.php" method="POST">
			<div class="row">
				<div class="input-content small">
					<label>Num O.P</label>
					<input type="text" name="numero_orden_pago" value='.$numero_orden_pago .'>
				</div>
				<div class="input-content small">
					<label>A&ntilde;o</label>
					<input type="number" name="anio"  value='.$anio.'>
				</div>
				<div class="input-content small">
					<label>Importe</label>
					<input type="number" name="importe"  value='.$importe.'>
				</div>
				<div class="input-content">
					<label>Emisor</label>
					<input type="text" name="emisor" value='.$emisor.'>
				</div>
				<div class="input-content">
					<label>Proveedor</label>
					<input type="text" name="proveedor" value='.$proveedor.'>
				</div>
				<div class="input-content medium">
					<label>CUIT</label>
					<input type="text" name="cuit"  value='.$cuit.' >
				</div>
			</div>
  		<input type="submit" value="Buscar">
			</form>
			';
			
		
			echo $sHtml; 
			
			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>N&uacute;m_O_P</th>';
			echo '<th>Fecha_O_P</th>'; 
			echo '<th>Emisor</th>';
			echo '<th>Proveedor</th>';
			echo '<th>Factura</th>'; 
			echo '<th>Importe</th>';						
			echo '<th>Aclaraciones</th>';			
		    echo '<th colspan="6">Acciones</tr>';			

			//	echo
			if(isset($_GET['pag'])) 
				$iPagActual = ($_GET['pag']);
			else 
				$iPagActual = 1;
			$iLimit = ($iPagActual -1) * $iMaxRows;
			$sWhere = 'WHERE 1';
			$varBus = '';

			if (array_key_exists('numero_orden_pago', $vData) and $vData['numero_orden_pago']!="") {
				$vTmp = explode("/",$vData['numero_orden_pago']);
				if(isset($vTmp[1])){
					$anio = $vTmp[1];
				}
				$nro = $vTmp[0];
				$sWhere .= ' AND op.numero_orden_pago LIKE "%'.$nro.'%" AND op.anio_numero_orden_pago LIKE"'.$anio.'%"';
				$varBus .= '&numero_orden_pago='.$vData['numero_orden_pago'];
			}

			if (array_key_exists('anio', $vData) and $vData['anio']!="") {
				$anio = $vData['anio'];
				$sWhere .= ' AND op.anio_numero_orden_pago LIKE"'.$anio.'%"';
				$varBus .= '&anio='.$vData['anio'];
			}
			
			if (array_key_exists('importe', $vData) and $vData['importe']!="") {
				$importe = $vData['importe'];
				$sWhere .= ' AND op.importe LIKE"'.$importe.'%"';
				$varBus .= '&importe='.$vData['importe'];
			}
			
			if (array_key_exists('emisor', $vData) and $vData['emisor']!="") {
				$emisor = $vData['emisor'];
				$sWhere .= ' AND u.nombre LIKE"'.$emisor.'%"';
				$varBus .= '&emisor='.$vData['emisor'];
			}
			
			if (array_key_exists('proveedor', $vData) and $vData['proveedor']!="") {
				$proveedor = $vData['proveedor'];
				$sWhere .= ' AND p.razon_social LIKE"'.$proveedor.'%"';
				$varBus .= '&proveedor='.$vData['proveedor'];
			}
			
			if (array_key_exists('cuit', $vData) and $vData['cuit']!="") {
				$cuit = $vData['cuit'];
				$sWhere .= ' AND p.cuit LIKE"'.$cuit.'%"';
				$varBus .= '&cuit='.$vData['cuit'];
			}

			$q = 'SELECT SQL_CALC_FOUND_ROWS * 
				FROM orden_pago op LEFT JOIN 
					unidad_ejecutora u ON u.id_unidad_ejecutora = op.id_unidad_ejecutora LEFT JOIN 
					proveedor p ON p.id_proveedor = op.proveedor '.
				$sWhere.
				' ORDER By 
					anio_numero_orden_pago DESC, 
					numero_orden_pago DESC 
				LIMIT '.$iLimit.','.$iMaxRows;

			$r = $this->excecuteQuery($q);

			$fila = 1;

			$p = $this->excecuteQuery("SELECT CEIL(FOUND_ROWS()/$iMaxRows) as total");
			$results = mysqli_fetch_assoc($p);
			$iTotalPag = $results['total'];
			
			
			while ( $row = mysqli_fetch_array($r) ){
				$nroOP = $row['numero_orden_pago'];
				$yearOP= $row['anio_numero_orden_pago'];
				//Calculo si debe mostrar la fecha de envio de aviso de pago: solo si la forma de pago es Cheque o Transferencia
				if ($row['forma_pago'] != 3) 
					$showDate = "true";
				else
					$showDate = "false";
				
				if((int)$row['estado'] === 1){
					$sChecked = '';
					$sDisabled = '';
					$lnkmodificar_orden_pago = 'form_orden_pago.php?numero_orden_pago=' . $nroOP . '&anio='.$yearOP.'&opcion=3';
					$lnkborrar_orden_pago = 'form_orden_pago.php?numero_orden_pago=' . $nroOP . '&anio='.$yearOP.'&opcion=2';	
				}else{
					$sChecked = 'checked="checked"';
					$sDisabled = 'disabled="disabled"';
					$lnkmodificar_orden_pago = '#';
					$lnkborrar_orden_pago = '#';
				}
				$fecha = new DateTime($row['fecha']);
				$fecha_base = new DateTime('2023-07-01');
				$lnkimprimir_orden_pago = 'orden_pago_pdf.php?numero_orden_pago='   . $nroOP . '&fecha='.convertir_fecha($row['fecha']).'&anio_numero_orden_pago='.$yearOP.'&confecciono='.rawurlencode($row['confecciono']).'&proveedor='.$row['proveedor'];
				if(($this->isRetentionAgent($row['id_unidad_ejecutora']) || ($fecha < $fecha_base))  && (int)$row['cert_ret'] != 0){//&& $this->availRetention($row['proveedor']) 
					$lnkimprimir_retenciones = 'recibo_retencion_pdf.php?numero_orden_pago='   . $nroOP .'&anio_numero_orden_pago='.$yearOP.'&confecciono='.rawurlencode($row['confecciono']);
				}else{
					$lnkimprimir_retenciones = "#";
				}
				$lnkvisualizar_orden_pago = 'form_orden_pago.php?numero_orden_pago=' . $nroOP . '&anio='.$yearOP.'&opcion=4';

				echo '<tr class="modo1">';
				
				echo '<td>' . $row['numero_orden_pago'] . '/' . $row['anio_numero_orden_pago'] .'</td>';
				echo '<td>' . convertir_fecha($row['fecha']) . '</td>';
				echo '<td>' . $row['nombre'] . '</td>';
				echo '<td>' . $row['razon_social'] . '</td>';
				echo '<td>' . $row['factura'] . '</td>';
				echo '<td>' . number_format($row['importe'], 2, ',' , '.') . '</td>';
				echo '<td>' . $row['aclaraciones'] . '</td>';
				if($this->checkPerm($_SESSION["id_usuario"],4,'baja') && (int)$row['estado'] === 1){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_orden_pago .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';	
				
				if($this->checkPerm($_SESSION["id_usuario"],4,'modificacion') && (int)$row['estado'] === 1){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_orden_pago .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" title="Modificar Registro"></a></td>';
				
				if($this->checkPerm($_SESSION["id_usuario"],4,'consulta')){
					echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_orden_pago .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/previsualizarg.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';

				echo '<td align="center"><font color="#333333"><a target="_blank" href=' . $lnkimprimir_orden_pago.  '><img src="acrobat.png" width="30" height="30" border="0" title="Imprimir Orden Pago"></a></td>';

				if(($this->isRetentionAgent($row['id_unidad_ejecutora']) || ($fecha < $fecha_base))  && $this->availRetention($row['proveedor']) && (int)$row['cert_ret'] != 0){
					echo '<td align="center"><font color="#333333"><a target="_blank" href=' . $lnkimprimir_retenciones.  '><img src="arba.png" width="30" height="30" border="0" title="Imprimir Recibo de Retenciones"></a></td>';
				}else{
					echo '<td align="center"><font color="#333333"><a href=' . $lnkimprimir_retenciones.  '><img src="iconos_grises/no_arba.png" width="30" height="30" border="0" title="Imprimir Recibo de Retenciones"></a></td>';
				}
				if($this->checkPerm($_SESSION["id_usuario"],4,'especial')){
					echo '<td align="center"><font color="#333333"><input type="checkbox" name="closeOP_'.$nroOP.$yearOP.'" id="closeOP_'.$nroOP.$yearOP.'" onclick="closeOP(\''.$nroOP.'\', \''.$yearOP.'\',\''.$showDate.'\', this);" '.$sChecked.' '.$sDisabled.'</td>';
				}else{
					echo '<td align="center"><font color="#333333"></td>';
				}
				$fila++;
			}
			$iNextPage = ($iPagActual<$iTotalPag)?($iPagActual+1):$iPagActual;
			$iPrevPage = ($iPagActual > 1)?($iPagActual-1):$iPagActual;
			
			echo "<tr>
					<td colspan='10'>
						<a href='lista_ordenes_pago.php?pag=$iPrevPage".$varBus."'>Prev </a>
						$iPagActual / $iTotalPag
						<a href='lista_ordenes_pago.php?pag=$iNextPage".$varBus."' value='sig'>Sig</a>
					</td>
				</tr>";

			echo '</table>';
			$bd = NULL;			
		}
		
		function getCertRet($opID, $iYear){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$sSQL = "SELECT cert_ret FROM orden_pago WHERE numero_orden_pago = $opID AND anio_numero_orden_pago = $iYear";
			$r = $this->excecuteQuery($sSQL);
			$row = mysqli_fetch_assoc($r);
			
			return (int)$row['cert_ret'];
		}
		
		function closeOP($opID, $iYear, $fecha_aviso_pago){
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$sSQL = "UPDATE orden_pago SET estado = 0, fecha_aviso_pago = '$fecha_aviso_pago'
				 WHERE numero_orden_pago = $opID AND anio_numero_orden_pago = $iYear";
			return $this->excecuteQuery($sSQL);
		}
		
		function get_next_numero_orden_pago($iYear){
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = 'SELECT IFNULL(MAX(numero_orden_pago), 0)+1 AS nextNumeroOrden FROM orden_pago WHERE anio_numero_orden_pago = '.$iYear;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['nextNumeroOrden'];		
		}		
		
		function consultar_orden_pago_avisos_pendientes(){
			// Fecha: 09 de Abril de 2019
			// Autor: Vanina
			// Las ordenes de pago con forma de pago 'Pago de Servicios' no deben ser enviadas

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = 'SELECT 
					op.*,
					case op.forma_pago
						when 1 then \'cheque\'
						when 2 then \'transferencia\'
						when 3 then \'pago de servicios\'
					end as forma_pago_descripcion,
					CONCAT(t.apellido,\' \',t.nombre) as titular_aviso_pago_nombre,
					t.email as titular_aviso_pago_email,
					p.contacto as proveedor_contacto,
					p.email as proveedor_email,
					p.razon_social as proveedor_razon_social,
					m.signo as signoMoneda
				FROM 
					orden_pago op
					LEFT OUTER JOIN titular t ON
					op.id_titular_aviso_pago = t.id_titular
					LEFT OUTER JOIN proveedor p ON
					op.proveedor = p.id_proveedor 
					LEFT OUTER JOIN moneda m
					ON m.id_moneda = op.id_moneda
				WHERE 
					op.forma_pago != 3 and
					op.aviso_pago_enviado = 0 and
					op.fecha_aviso_pago <= curdate()';
			
			$filas = $this->excecuteQuery($q);
			return $filas;
		}

		function modificar_op_aviso_pago_enviado ($numero_orden_pago, $anio_numero_orden_pago, $aviso_pago_enviado, &$error){
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$error = "";
			try {
				$this->excecuteQuery("UPDATE `orden_pago` 
					SET aviso_pago_enviado = $aviso_pago_enviado
					WHERE numero_orden_pago='$numero_orden_pago' AND 
						anio_numero_orden_pago='$anio_numero_orden_pago'");
				return true;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}
		}

		// FIN METODOS RELACIONADOS CON ORDEN DE PAGO
		
		
		//-------------------------------------------------------------------------------------------		
		// INICIO METODOS RELACIONADOS CON MESA DE SALIDA

		function agregar_mesa_salida_encabezado($numero_remito, $fecha, $confecciono, $firmante){
			// Fecha: 21 de Febrero de 2013
			// Autor: .j
			//echo  $nombre_usuario . " - " . $contrasenia . " - " . $nombre . " - " . $apellido . " - " . $email . " - " . $nivel_acceso;

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022
			
			$vFecha = explode("-",$fecha);
			$anio = (int)$vFecha[0];
			$q = "INSERT INTO mesa_salida (numero_remito, fecha, documento, destinatario, copias, cantidad_hojas, confecciono, firmante,anio_numero_tramite) VALUES ('$numero_remito', '$fecha',  '$confecciono', '$firmante',$anio)";
			$this->excecuteQuery($q);
		}

		function modificar_mesa_salida_encabezado($numero_remito, $fecha, $confecciono, $firmante, $anio){
			// Fecha: 21 de Febrero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022

			$q = "UPDATE mesa_salida SET fecha='$fecha', confecciono='$confecciono', firmante='$firmante' WHERE numero_remito='$numero_remito' AND anio_numero_tramite = $anio";

			$this->excecuteQuery($q);
		}		

		function modificar_mesa_salida($numero_remito, $fecha, $numero_orden, $numero_tramite, $remitente, $documento, $destinatario, $copias, $cantidad_hojas, $firmante){
			// Fecha: 06 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022

			$vFecha = explode("-",$fecha);
			$anio = (int)$vFecha[0];
			$q = "UPDATE mesa_salida SET fecha='$fecha', numero_tramite='$numero_tramite', anio_numero_tramite=$anio, remitente='$remitente', documento='$documento', destinatario='$destinatario', copias='$copias', cantidad_hojas='$cantidad_hojas', firmante='$firmante' WHERE numero_remito='$numero_remito' AND numero_orden='$numero_orden' AND anio_numero_tramite = $anio";
			$this->excecuteQuery($q);
		}		

		function borrar_movimiento_mesa_salida($numero_remito, $numero_orden, $anio){
			// Fecha: 05 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022

			$q = "DELETE FROM `mesa_salida` WHERE `numero_remito` = '$numero_remito' AND `numero_orden` = '$numero_orden' AND anio_numero_tramite = $anio";
		
			$this->excecuteQuery($q);
		}

		function borrar_mesa_salida($numero_remito, $anio){
			// Fecha: 05 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022

			$q="DELETE FROM `mesa_salida` WHERE `numero_remito` = '$numero_remito' AND anio_numero_tramite = $anio";
			$this->excecuteQuery($q);
		}

		function agregar_mesa_salida($numero_remito, $fecha, $numero_orden, $numero_tramite, $remitente, $documento, $destinatario, $copias, $cantidad_hojas, $confecciono, $firmante){
			// Fecha: 05 de Diciembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$vFecha = explode("-",$fecha);
			$anio = (int)$vFecha[0];
			$this->excecuteQuery("INSERT INTO mesa_salida (fecha, numero_remito, numero_orden, numero_tramite, anio_numero_tramite, remitente, documento, destinatario, copias, cantidad_hojas, confecciono, firmante) VALUES ('$fecha', '$numero_remito', '$numero_orden', '$numero_tramite', $anio, '$remitente', '$documento', '$destinatario', '$copias', '$cantidad_hojas', '$confecciono', '$firmante')");
		}
		
		function ultimo_numero_orden_mesa_salida($numero_remito,$iYear){
			// Fecha: 3 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022
				
			$q = '
				SELECT Max(numero_orden) as maximo
				FROM mesa_salida WHERE numero_remito ="'. $numero_remito . '" AND anio_numero_tramite = '.$iYear;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['maximo'];		

		}
		
		function consultar_mesa_salida($numero_remito, $numero_orden, $iYear){
			// Fecha: 03 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022
			
			if ($numero_orden != 0){
				$q = 'SELECT * FROM mesa_salida	WHERE numero_remito ="' . $numero_remito . '" AND numero_orden ="' . $numero_orden . '" AND anio_numero_tramite = '.$iYear;
			}else
				$q = 'SELECT * FROM mesa_salida	WHERE numero_remito ="' . $numero_remito . '" AND anio_numero_tramite = '.$iYear;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function getDataMesaSalida($numero_tramite){
			// Fecha: 03 de Diciembre de 2012
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
			$vTramite = explode("-",$numero_tramite);
			if($vTramite[0] && $vTramite[1]){
				$q = 'SELECT remitente, documento, cantidad FROM mesa_entrada	WHERE numero_tramite = ' . $vTramite[0] . ' AND anio_numero_tramite = ' . $vTramite[1];
				$r = $this->excecuteQuery($q);
				$row = mysqli_fetch_array($r);
				if($row){
					$row['remitente'] = utf8_encode($row['remitente']);
					$row['documento'] = utf8_encode($row['documento']);
				}
				return $row;
			}else{
				return null;
			}
		}

		function lista_mesa_salida($nombre_usuario,$vData){
			// Fecha: 03 de Diciembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022
			$iMaxRows = 15;
			
			$sHtml = 'B&uacute;squeda:
			<form name="frmBus" action="lista_mesa_salida.php" method="POST">
			<input type="checkbox" name="chkBus" value="ms.fecha" {ms.fecha}><label style="font-size:12px">Fecha</label></input>
			<input type="text" name="dateBus" value="{dateBus}" style="width:200px"/><label style="font-size:12px"> (AAAA-MM-DD)</label><br/>
			
			<input type="radio" name="opBus" value="ms.numero_tramite"{ms.numero_tramite}><label style="font-size:12px">Num. Tr&aacute;mite</label>
			<input type="radio" name="opBus" value="ms.remitente"{ms.remitente}><label style="font-size:12px">Remitente</label>
			<input type="radio" name="opBus" value="ms.documento"{ms.documento}><label style="font-size:12px">Documentos</label>
			<input type="radio" name="opBus" value="d.descripcion"{d.descripcion}><label style="font-size:12px">Destinatarios</label>
			<input type="text" name="txtBus" value="{txtBus}" style="width:200px"/>
			<input type="submit" name="btnBus" value="Buscar"/>
			</form>';
			if(isset($vData['opBus'])){
				$sHtml = str_replace("{".$vData['opBus']."}","checked='checked'",$sHtml);
			}else{
				$sHtml = str_replace("{ms.numero_tramite}","checked='checked'",$sHtml);
			}
			if(isset($vData['chkBus'])){
				$sHtml = str_replace("{ms.fecha}","checked='checked'",$sHtml);
			}else{
				$sHtml = str_replace("{ms.fecha}","",$sHtml);
			}

			if(isset($vData['dateBus'])){
				$sHtml = str_replace("{dateBus}",$vData['dateBus'],$sHtml);
			}else{
				$sHtml = str_replace("{dateBus}","",$sHtml);
			}

			$vTemp = array("{ms.numero_tramite}","{ms.remitente}","{ms.documentos}","{d.descripcion}");
			$sHtml = str_replace($vTemp,"",$sHtml);
			$sHtml = str_replace("{txtBus}",$vData['txtBus']??'',$sHtml);
			$sHtml = str_replace("{dateBus}",$vData['dateBus']??'',$sHtml);
			echo $sHtml;
			
			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>N&uacute;m. Remito</th>';
		    echo '<th>Fecha_Remito</th>';
			echo '<th>Remitente</th>'; 
			echo '<th>Documento</th>'; 
			echo '<th>Destinatario</th>'; 
			echo '<th>Copias</th>'; 
			echo '<th>Cant. Hojas</th>'; 
		    echo '<th colspan="3">Acciones</tr>';			

			$iPagActual = ($_GET['pag'])??1;
			$iLimit = ($iPagActual -1) * $iMaxRows;

			$sWhere = '';
			if(isset($vData['txtBus'])){
				if($vData['opBus'] != 'numero_tramite'){
					$sWhere = 'WHERE '.$vData['opBus'].' LIKE "%'.$vData['txtBus'].'%"';
				}else{
					$sWhere = 'WHERE '.$vData['opBus'].' = '.$vData['txtBus'];
				}
			}
			
			if(isset($vData['dateBus']) && isset($vData['chkBus'])){
				if($sWhere ==''){
					$sWhere = 'WHERE ms.fecha LIKE "'.$vData['dateBus'].'%"';
				}else{
					$sWhere .= ' AND ms.fecha LIKE "'.$vData['dateBus'].'%"';
				}
			}
			
			$q = '
				SELECT SQL_CALC_FOUND_ROWS * FROM mesa_salida ms INNER JOIN destinatario d ON d.id_destinatario = ms.destinatario '.$sWhere.' GROUP BY numero_remito, anio_numero_tramite ORDER By ms.id DESC LIMIT '.$iLimit.','.$iMaxRows;
			$r = $this->excecuteQuery($q);
			$fila = 1;

			$p = $this->excecuteQuery("SELECT CEIL(FOUND_ROWS()/$iMaxRows) as total");
			$results = mysqli_fetch_assoc($p);
			$iTotalPag = $results['total'];
			
			
			$ant_numero_remito = 0;
			while ( $row = mysqli_fetch_assoc($r) ){
				$act_numero_remito = $row['numero_remito'];
				$lnkmodificar_mesa_salida = 'form_mesa_salida.php?numero_remito=' . $row['numero_remito'] . '&anio='.$row['anio_numero_tramite'].'&opcion=4';
				$lnkborrar_mesa_salida = 'form_mesa_salida.php?numero_remito=' . $row['numero_remito'] . '&anio='.$row['anio_numero_tramite'].'&opcion=2';
				$lnkvisualizar_mesa_salida = 'form_mesa_salida_ver.php?numero_remito=' . $row['numero_remito'].'&anio='.$row['anio_numero_tramite'];

				if ($act_numero_remito != $ant_numero_remito){
					echo '<tr class="modo1">';				
					echo '<td>' . $row['numero_remito'] .'/'. $row['anio_numero_tramite'] .'</td>';
					echo '<td>' . convertir_fecha($row['fecha']) .'</td>';					
					echo '<td>' . $row['remitente'] . '</td>';
					echo '<td>' . substr($row['documento'], 0, 45) .'...'. '</td>';
					echo '<td>' . $row['descripcion'] . '</td>';
					echo '<td>' . $row['copias'] . '</td>';
					echo '<td>' . $row['cantidad_hojas'] . '</td>';
					if($this->checkPerm($_SESSION["id_usuario"],2,'baja')){				
						echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_mesa_salida .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
					if($this->checkPerm($_SESSION["id_usuario"],2,'modificacion')){	
						echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_mesa_salida .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_mesa_salida .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
					$ant_numero_remito = $act_numero_remito;
					echo "</tr>";
				}
				$fila++;
			}
			$iNextPage = ($iPagActual<$iTotalPag)?($iPagActual+1):$iPagActual;
			$iPrevPage = ($iPagActual > 1)?($iPagActual-1):$iPagActual;
			
			echo "<tr>
					<td colspan='10'>
						<a href='lista_mesa_salida.php?pag=$iPrevPage'>Prev </a>
						$iPagActual / $iTotalPag
						<a href='lista_mesa_salida.php?pag=$iNextPage' value='sig'>Sig</a>
					</td>
				</tr>";
			
			echo '</table>';
			$bd = NULL;			
		}

		function lista_mesa_salida_por_remito($numero_remito, $opcion, $iYear){
			// Fecha: 03 de Diciembre de 2012
			// Autor: .j
			//	echo "nr " . $numero_remito;

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022
			
			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla">';
			echo '<tr>';
			echo '<th>N&uacute;m. Orden</th>';
			echo '<th>N&uacute;m. Tr&aacute;mite</th>'; 
			echo '<th>Remitente</th>'; 
			echo '<th>Documento</th>'; 
			echo '<th>Destinatario</th>'; 
			echo '<th>Copias</th>'; 
			echo '<th>Cant. Hojas</th>';
			echo '<th>Detalle</th>';
			if ($opcion != 1) // NO MUESTRO LAS ACCIONES PORQUE ELIGIO ELIMINAR
			    echo '<th colspan="2">Acciones</tr>';			
			$q = 'SELECT * FROM mesa_salida WHERE numero_remito ='. $numero_remito . ' AND anio_numero_tramite = '.$iYear.' ORDER By numero_remito, numero_orden';
			$r = $this->excecuteQuery($q);
		
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_mesa_salida = 'form_mesa_salida.php?numero_remito='.$row['numero_remito'].'&numero_orden='.$row['numero_orden'].'&anio='.$iYear.'&opcion=3';
				$lnkborrar_mesa_salida = 'form_mesa_salida.php?numero_remito='.$row['numero_remito'].'&numero_orden='.$row['numero_orden'].'&anio='.$iYear.'&opcion=5'; // ACA SE ELIJE ELIMINAR UN REGISTRO DE UN REMITO
				$lnkdetalle_mesa_salida = 'detalle_mesa_salida.php?numero_remito='.$row['numero_remito'].'&numero_orden='.$row['numero_orden'].'&anio='.$iYear;
	
				echo '<tr class="modo1">';
				
				echo '<td>' . $row['numero_orden'] .'</td>';
				echo '<td>' . $row['numero_tramite'] .'</td>';				
				echo '<td>' . $row['remitente'] . '</td>';
				echo '<td>' . substr($row['documento'], 0, 45) .'...'. '</td>';
				$row_destinatario = $this->consultar_destinatario($row['destinatario']);				
				echo '<td>' . $row_destinatario['descripcion'] . '</td>';
				echo '<td>' . $row['copias'] . '</td>';
				echo '<td>' . $row['cantidad_hojas'] . '</td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkdetalle_mesa_salida .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver detalle"></a></td>';
				if ($opcion != 1){ // NO MUESTRO LAS ACCIONES PORQUE ELIGIO ELIMINAR				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_mesa_salida .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_mesa_salida .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}
			}
			echo '</table>';
			$bd = NULL;			
		}

		function lista_item_mesa_salida($numero_remito, $iYear, $numero_orden){
			// Fecha: 03 de Diciembre de 2012
			// Autor: .j
			//	echo "nr " . $numero_remito;

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022
			
			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla">';
			echo '<tr>';
		    echo '<th>N&uacute;m. Orden</th>';
			echo '<th>N&uacute;m. Tr&aacute;mite</th>'; 
			echo '<th>Remitente</th>'; 
			echo '<th>Documento</th>'; 
			echo '<th>Destinatario</th>'; 
			echo '<th>Copias</th>'; 
			echo '<th>Cant. Hojas</th>';

			$q = 'SELECT * FROM mesa_salida WHERE numero_remito ='. $numero_remito . ' AND anio_numero_tramite = '.$iYear.' AND numero_orden = '.$numero_orden;
			$r = $this->excecuteQuery($q);
		
			while ( $row = mysqli_fetch_array($r) ){
				echo '<tr class="modo1">';
				
				echo '<td>' . $row['numero_orden'] .'</td>';
				echo '<td>' . $row['numero_tramite'] .'</td>';				
				echo '<td>' . $row['remitente'] . '</td>';
				echo '<td>' . $row['documento']. '</td>';
				$row_destinatario = $this->consultar_destinatario($row['destinatario']);				
				echo '<td>' . $row_destinatario['descripcion'] . '</td>';
				echo '<td>' . $row['copias'] . '</td>';
				echo '<td>' . $row['cantidad_hojas'] . '</td>';
			}
			echo '</table>';
			$bd = NULL;			
		}

		// FIN METODOS RELACIONADOS CON MESA DE SALIDA
					
		//-------------------------------------------------------------------------------------------
		// INICIO METODOS RELACIONADOS CON PROVINCIAS

		function consultar_provincia($id_provincia){
			// Fecha: 27 de Octubre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = '
				SELECT * FROM provincia
				WHERE id_provincia ="' . $id_provincia . '"
				 ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
		
		function listar_provincias($provincia, $textoReadOnly){
			// Fecha: 1 de Diciembre de 2012
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022
			
			$q = '
			SELECT * FROM provincia Order By nombre
			    ';
			$r = $this->excecuteQuery($q);
			echo '<td class="modo2" style="text-align:left">';
			echo '<select name="provincia" id="provincia" '.$textoReadOnly. '>';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id_provincia'] == $provincia){
					echo '<option selected value='. $row['id_provincia'] .'>'.  $row['nombre'] .'</option>';
				}else{
					echo '<option value='. $row['id_provincia'] .'>'. $row['nombre'] . '</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}
		
		function listar_condicion_iva($condicion_iva, $textoReadOnly){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022

			$q = '
			SELECT * FROM condicion_iva Order By id
			    ';
			$r = $this->excecuteQuery($q);
			echo '<td class="modo2" style="text-align:left">';
			echo '<select name="condicion_iva" onchange="showIIBB(this);" '.$textoReadOnly. '>';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id'] == $condicion_iva){
					echo '<option selected value='. $row['id'] .'>'.  $row['nombre'] .'</option>';
				}else{
					echo '<option value='. $row['id'] .'>'. $row['nombre'] . '</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}
		
		function listar_iibb($IIBB, $textoReadOnly){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022
			
			$q = '
			SELECT * FROM iibb Order By id
			    ';
			$r = $this->excecuteQuery($q);
			echo '<td class="modo2" style="text-align:left">';
			echo '<select name="IIBB" id="IIBB" onchange="showIIBB(this);" '.$textoReadOnly. '>';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id'] == $IIBB){
					echo '<option selected value='. $row['id'] .'>'.  $row['desc'] .'</option>';
				}else{
					echo '<option value='. $row['id'] .'>'. $row['desc'] . '</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}
		
		function getIIBBName($IIBB){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			if($IIBB){
				$q = "SELECT `desc` FROM iibb WHERE id = $IIBB";
				//echo $q;exit;
				$r = $this->excecuteQuery($q);
				$row = mysqli_fetch_array($r);
				if($row){
					return $row['desc'];
				}else{
					return "";
				}
			}else{
				return "";
			}
		}
		
		// FIN METODOS RELACIONADOS CON PROVINCIAS

		// --------------------------------------------------------------------------------------------------------------------------------
		// INICIO METODOS RELACIONADOS CON PROVEEDORES
		
		//ojo tambien esta listar_proveedores...
		function getSuppliers($idProveedorSel){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$q = 'SELECT id_proveedor, razon_social 
				FROM proveedor 
				WHERE baja=0 or id_proveedor = '.$idProveedorSel
				.' ORDER BY razon_social';
			$r = $this->excecuteQuery($q);
			
			while ( $row = mysqli_fetch_assoc($r) ){
				$vData[] = $row;
			}
			return $vData;
		}
		
		function getCMPercent($idProveedor){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = 'SELECT cm_porciento FROM proveedor WHERE id_proveedor = '.$idProveedor;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_assoc($r);
			return $row['cm_porciento'];
			
		}
		
		function listar_proveedores($proveedor=0, $textoReadOnly=0, $enForm=0,$mult_id=0){
			// Fecha: 6 de Diciembre de 2012
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX
			//Modificada por Vanina para incluir la baja logica
			//el parametro proveedor es el id proveedor preseleccionado, 0 indica ninguno

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			//Incluyo el proveedor seleccionado aunque este dado de baja
			//si es cero no incluye nada porque no existe
			$q = 'SELECT * FROM proveedor 
				WHERE baja = 0 or id_proveedor = '.$proveedor.
				' ORDER BY razon_social';
			$r = $this->excecuteQuery($q);
			if (!$enForm){
				echo "<td>";
				$selectWidth = "style=\"width: 383px;\"";
				$selectId = "id=\"proveedor\"";
				$selectName = "name=\"proveedor\"";
			} else {
				echo "<td colspan=\"2\" class=\"left-align\">Proveedor: ";
				$selectWidth = "style=\"width: 280px;margin-right: 10px;\"";
				$selectId = "";
				$selectName = "name=\"proveedor".$mult_id."\"";
			}
			echo "<select $selectId $selectName onchange=\"buscarBancos(this);\" $selectWidth $textoReadOnly>";
			echo "<option value=\"null\"  selected email=\"\">----</option>";
			while ( $row = mysqli_fetch_array($r) ){
				if(!$row['cm_porciento']){
					$cm_porciento = 0;
				}else{
					$cm_porciento = $row['cm_porciento'];
				}
				if ($row['id_proveedor'] == $proveedor){
					echo '<option email="'.trim($row['email']).'" c_iibb="'.$row['iibb'].'" nroiibb="'.$row['nro_iibb'].'" nrocuit="'.$row['cuit'].'" c_iva="'.$row['condicion_iva'].'" cm="'.$cm_porciento.'" selected id="opprov_'.$row['id_proveedor'].'" value='. $row['id_proveedor'] .'>'. $row['razon_social'] .'</option>';
				}else{
					echo '<option email="'.trim($row['email']).'" c_iibb="'.$row['iibb'].'" nroiibb="'.$row['nro_iibb'].'" nrocuit="'.$row['cuit'].'" c_iva="'.$row['condicion_iva'].'" cm="'.$cm_porciento.'"          id="opprov_'.$row['id_proveedor'].'" value='. $row['id_proveedor'] .'>'. $row['razon_social'] .'</option>';	
				}
			}
			echo '</select>';
			echo "<input type=\"button\" name=\"opSearchProv\" value=\"CUIT\" onclick=\"buscarPorCUIT();\" $textoReadOnly />";
			echo '</td>';
		}


		function agregar_proveedor($cuit, $razon_social, $IIBB, $nroIIBB, $condicion_iva, $domicilio, $provincia, $contacto, $telefono, $email, $contacto2, $email2, $banco1, $titular_cuenta1, $cuit1, $tipo_cuenta1, $numero_cuenta1, $cbu1, $banco2, $titular_cuenta2, $cuit2, $tipo_cuenta2, $numero_cuenta2, $cbu2, $cm){
			// Fecha: 3 de Noviembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022

			$this->excecuteQuery("INSERT INTO proveedor (
						cuit, 
						razon_social, 
						iibb, 
						nro_iibb, 
						condicion_iva, 
						domicilio, 
						provincia, 
						contacto, 
						telefono, 
						email, 
						contacto2, 
						email2, 
						banco1, 
						titular_cuenta1, 
						cuit_cuenta1, 
						tipo_cuenta1, 
						numero_cuenta1, 
						cbu1, 
						banco2, 
						titular_cuenta2, 
						cuit_cuenta2, 
						tipo_cuenta2, 
						numero_cuenta2, 
						cbu2, 
						cm_porciento,
						baja) 
			VALUES (
						'$cuit', 
						'$razon_social', 
						'$IIBB', 
						'$nroIIBB', 
						$condicion_iva, 
						'$domicilio', 
						'$provincia', 
						'$contacto', 
						'$telefono', 
						'$email', 
						'$contacto2', 
						'$email2', 
						'$banco1', 
						'$titular_cuenta1', 
						'$cuit1', 
						'$tipo_cuenta1', 
						'$numero_cuenta1', 
						'$cbu1', 
						'$banco2', 
						'$titular_cuenta2', 
						'$cuit2', 
						'$tipo_cuenta2', 
						'$numero_cuenta2', 
						'$cbu2', 
						$cm,
						0)");
		}


		function borrar_proveedor($id_proveedor){
			// Fecha: 26/07/2017
			// Autor: .j, reescrito por Vanina para baja logica

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022

			$this->excecuteQuery("UPDATE `proveedor` 
					SET baja = 1
					WHERE `id_proveedor` = '$id_proveedor'");
		}
		
		
		function check_cuit_proveedor($cuit, $id_proveedor){
			// Fecha: 19-07-2017
			// Autor: Vanina
			// Devuelve true si no existe un proveedor con cuit igual al
			//parametro 1. No considera el proveedor pasado en parametro 2.
			//En el alta esta viniendo en cero el idproveedor

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = '	SELECT count(*) as cant FROM proveedor
				WHERE cuit ="' . $cuit . '"
				      and id_proveedor != "'.$id_proveedor. '"
				      and baja = 0';
			$r = $this->excecuteQuery($q);			
			$row = mysqli_fetch_array($r);
			$cant = $row['cant'];
			mysqli_free_result($r);
			return $cant==0;
		}
				
		function consultar_proveedor($cuit){
			// Fecha: 3 de Noviembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022

			$q = '
				SELECT * FROM proveedor
				WHERE cuit="' . $cuit . '"
				 and baja=0';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
		
		function consultar_proveedor_por_id($id_proveedor){
			// Fecha: 3 de Febrero de 2013
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = '
				SELECT * FROM proveedor
				WHERE id_proveedor="' . $id_proveedor . '"
				 ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}
		
		function getCondicionIva($c_iva){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = '
				SELECT nombre FROM condicion_iva
				WHERE id=' . $c_iva;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row['nombre'];		
		}
		
		function availRetention($idSupplier){
			$vData = $this->consultar_proveedor_por_id($idSupplier);
			$c_iva = (int)$vData['condicion_iva'];
			$c_iibb = (int)$vData['iibb'];
			//Condicion IVA Responsable Inscripto o Monotributo. IIBB distinto de Exento
			return (bool)($c_iva == 1 || $c_iva == 2) && $c_iibb != 3;
		}
		
		function getSupplierName($idSupplier){
			$vData = consultar_proveedor_por_id($idSupplier);
			return $vData['razon_social'];
		}
		
		function modificar_proveedor($id_proveedor, $cuit, $razon_social, $IIBB, $nroIIBB, $condicion_iva, $domicilio, $provincia, $contacto, $telefono, $email, $contacto2, $email2, $banco1, $titular_cuenta1, $cuit1, $tipo_cuenta1, $numero_cuenta1, $cbu1, $banco2, $titular_cuenta2, $cuit2, $tipo_cuenta2, $numero_cuenta2, $cbu2, $cm){
			// Fecha: 3 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022
			
			$sSQL = "UPDATE proveedor 
			SET 
				cuit='$cuit', 
				razon_social='$razon_social', 
				iibb='$IIBB', 
				nro_iibb='$nroIIBB', 
				condicion_iva=$condicion_iva, 
				domicilio='$domicilio', 
				provincia='$provincia', 
				contacto='$contacto', 
				telefono='$telefono', 
				email='$email', 
				contacto2='$contacto2', 
				email2='$email2', 
				banco1='$banco1', 
				titular_cuenta1='$titular_cuenta1', 
				cuit_cuenta1='$cuit1', 
				tipo_cuenta1='$tipo_cuenta1', 
				numero_cuenta1='$numero_cuenta1', 
				cbu1='$cbu1', 
				banco2='$banco2', 
				titular_cuenta2='$titular_cuenta2', 
				cuit_cuenta2='$cuit2', 
				tipo_cuenta2='$tipo_cuenta2', 
				numero_cuenta2='$numero_cuenta2', 
				cbu2='$cbu2', 
				cm_porciento=$cm 
				WHERE id_proveedor='$id_proveedor'";
			
			$this->excecuteQuery($sSQL);
		}

		function lista_proveedores($nombre_usuario){
			// Fecha: 3 de Noviembre de 2012
			// Autor: .j
		
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022

			echo '<table id="tab" width="900" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
			echo '<th><input type="button" value="C_U_I_T_" onclick="selecciona(this,0)" onkeypress="selecciona(this,2)" /></th>';
			echo '<th><input type="button" value="Raz&oacute;n_Social" onclick="selecciona(this,1)" onkeypress="selecciona(this,2)" /></th>';			
		    echo '<th>Domiclio</th>'; 
		    echo '<th>Contacto</th>';
		    echo '<th>Tel&eacute;fono</th>';
			echo '<th>E-Mail</th>';
		    
			echo '<th colspan="3">Acciones</th>';
			echo '</tr>';
			
			$q = 'SELECT * FROM proveedor WHERE baja=0 ORDER BY razon_social';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_proveedor = 'form_proveedor.php?id_proveedor=' . $row['id_proveedor'] . '&opcion=3';
				$lnkborrar_proveedor = 'form_proveedor.php?id_proveedor=' . $row['id_proveedor'] . '&opcion=2';				
				$lnkvisulizar_proveedor = 'form_proveedor.php?id_proveedor=' . $row['id_proveedor']. '&opcion=4';	;

				echo '<tr class="modo1">';
				echo '<td>' . $row['cuit'] . '   </td>';
				echo '<td>' . $row['razon_social'] . '</td>';
				echo '<td>' . $row['domicilio'] . '</td>';
				echo '<td>' . $row['contacto'] . '</td>';
				echo '<td>' . $row['telefono'] . '</td>';
				echo '<td>' . $row['email'].'</td>';
				if($this->checkPerm($_SESSION["id_usuario"],12,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_proveedor .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
				if($this->checkPerm($_SESSION["id_usuario"],12,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_proveedor .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				if($this->checkPerm($_SESSION["id_usuario"],12,'consulta')){
					echo '<td align="center"><font color="#333333"><a href=' . $lnkvisulizar_proveedor .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/previsualizarg.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				$fila++;
			}
			echo '</table>';

		}		
		// FIN METODOS RELACIONADOS CON PROVEEDORES

		// INICIO METODOS RELACIONADOS CON UNIDADES EJECUTORAS
				
		function listar_unidades_ejecutoras($id_unidad_ejecutora=0){
			// Fecha: 16 de Enero de 2013
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022

			$q = 'SELECT * 
				FROM unidad_ejecutora 
				WHERE baja = 0 or id_unidad_ejecutora = '.$id_unidad_ejecutora.' 
				ORDER BY nombre';
			$r = $this->excecuteQuery($q);
			echo '<td>';
			echo '<select name="unidad_ejecutora">';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id_unidad_ejecutora'] == $id_unidad_ejecutora){
					echo '<option selected value='. $row['id_unidad_ejecutora'] .'>'.  $row['nombre'] .'</option>';
				}else{
					echo '<option value='. $row['id_unidad_ejecutora'] .'>'. $row['nombre'] .'</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}
			
		function getUE($iID){
			// Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = "SELECT * 
				FROM unidad_ejecutora 
				WHERE id_unidad_ejecutora = $iID";
			$r = $this->excecuteQuery($q);
			$vData = mysqli_fetch_assoc($r);
			return $vData;
		}
						
		function getUEs($iID=0){
			// Fecha: 08/04/2014
			// Autor: Sebasti&aacute;n Salerno

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			if (is_null($iID) or ($iID == ""))
				$iID = 0;
			
			$q = "SELECT 
					* 
				FROM 
					unidad_ejecutora 
				WHERE 
				 	id_unidad_ejecutora = $iID or
				 	baja = 0
				ORDER BY 
				 	nombre";
				 	
			$r = $this->excecuteQuery($q);
			
			while ( $row = mysqli_fetch_assoc($r) ){
				$vData[] = $row;
			}
		
			return $vData;
		}
		
		//Carga todas las cuentas incluyendo el id de parametro aunque este de baja	
			//Sirve para cargar combos programas
		function getCuentasUnidades($iID=0){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = "SELECT 
					* 
				FROM 
					unidad_cuentas
				WHERE 
				 	id = $iID or
					baja = 0 
				ORDER BY 
				 	nro_cuenta";
				 	
			$r = $this->excecuteQuery($q);
			$vData = array();
			
			while ( $row = mysqli_fetch_assoc($r) ){
				$vData[] = $row;
			}
			return $vData;
		}
						
		//Carga solo las de una unidad ejecutora parametro, descartando todas las bajas
		function getCuentasUnidadesPorUnidad($iID=0){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$q = "SELECT 
					* 
				FROM 
					unidad_cuentas
				WHERE 
				 	id_unidad_ejecutora = $iID and
					baja = 0 
				ORDER BY 
				 	nro_cuenta";
				 	
			$r = $this->excecuteQuery($q);
			$vData = array();
			
			while ( $row = mysqli_fetch_assoc($r) ){
				$vData[] = $row;
			}
			return $vData;
		}
		
		function agregar_unidad_ejecutora($nombre, $nombre_completo, $cuit, $domicilio, $telefono, $referente, $mail_referente, $director, $mail_director, $agente_retencion, $iibb){
			// Fecha: 7 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("INSERT INTO unidad_ejecutora 
						(nombre, 
						nombre_completo,
						cuit, 
						domicilio, 
						telefono, 
						referente, 
						mail_referente, 
						director, 
						mail_director,
						agente_retencion,
						iibb) 
					VALUES ('$nombre',
						'$nombre_completo',
						'$cuit', 
						'$domicilio', 
						'$telefono', 
						'$referente', 
						'$mail_referente', 
						'$director', 
						'$mail_director',
						$agente_retencion,
						'$iibb')");
		}
				
		function agregar_cta_unidad_ejecutora($id_unidad_ejecutora, $nro_cuenta){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			return $this->excecuteQuery("INSERT INTO unidad_cuentas
						(nro_cuenta, 
						id_unidad_ejecutora) 
					VALUES ('$nro_cuenta', 
						$id_unidad_ejecutora)");
		}

		function eliminar_cta_unidad_ejecutora($idCuenta){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			return $this->excecuteQuery("UPDATE `unidad_cuentas` 
					SET baja = 1
					WHERE `id` = $idCuenta");
		}
		
		function consultar_cta_unidad_ejecutora($id_cuenta){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = 'SELECT * FROM unidad_cuentas
				WHERE id ='. $id_cuenta ;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function modificar_cta_unidad_ejecutora($idCuenta, $nroCuenta){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			return $this->excecuteQuery("UPDATE `unidad_cuentas` 
					SET nro_cuenta = '$nroCuenta'
					WHERE `id` = $idCuenta");
		}

		function borrar_unidad_ejecutora($id_unidad_ejecutora){
			// Fecha: 7 de Noviembre de 2012
			// Autor: .j. Modificado por Vanina para baja logica
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE `unidad_ejecutora` 
					SET baja = 1
					WHERE `id_unidad_ejecutora` = '$id_unidad_ejecutora'");
			
		}

		function modificar_unidad_ejecutora($id_unidad_ejecutora, $nombre, $nombre_completo, $cuit, $domicilio, $telefono, $referente, $mail_referente, $director, $mail_director, $agente_retencion, $iibb){
			// Fecha: 7 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE unidad_ejecutora 
				SET nombre='$nombre',
				nombre_completo='$nombre_completo',
				cuit='$cuit', 
				domicilio='$domicilio', 
				telefono='$telefono', 
				referente='$referente', 
				mail_referente='$mail_referente', 
				director='$director', 
				mail_director='$mail_director',
				agente_retencion=$agente_retencion,
				iibb='$iibb'
				WHERE id_unidad_ejecutora='$id_unidad_ejecutora'");
		}
		
		function consultar_unidad_ejecutora($id_unidad_ejecutora){
			// Fecha: 3 de Noviembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = 'SELECT * FROM unidad_ejecutora
				WHERE id_unidad_ejecutora ="' . $id_unidad_ejecutora . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_unidades_ejecutoras($nombre_usuario){
			// Fecha: 27 de Octubre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022
		
			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Nombre Unidad</th>';
			echo '<th>CUIT</th>'; 
		    echo '<th>Domicilio</th>'; 
		    echo '<th>Tel&eacute;fono</th>'; 			
		    echo '<th>Contacto Adm.</th>';
		    echo '<th>Mail Contacto Adm.</th>';		
		    echo '<th colspan="3">Acciones</tr>';			
			
			$q = 'SELECT * 
				FROM unidad_ejecutora
				WHERE baja = 0
				ORDER BY nombre';
			$r = $this->excecuteQuery($q);
			$fila = 1;
		
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_unidad_ejecutora = 'form_unidad_ejecutora.php?id_unidad_ejecutora=' . $row['id_unidad_ejecutora'] . '&opcion=3';
				$lnkborrar_unidad_ejecutora = 'form_unidad_ejecutora.php?id_unidad_ejecutora=' . $row['id_unidad_ejecutora'] . '&opcion=2';				
				$lnkvisualizar_unidad_ejecutora = 'form_unidad_ejecutora.php?id_unidad_ejecutora=' . $row['id_unidad_ejecutora'] . '&opcion=4';	
				echo '<tr class="modo1">';
				
				echo '<td>' . $row['nombre'] .'</td>';
				echo '<td>' . $row['cuit'] . '</td>';
				echo '<td>' . $row['domicilio'] . '</td>';
				echo '<td>' . $row['telefono'] . '</td>';
				echo '<td>' . $row['referente'] . '</td>';
				echo '<td>' . $row['mail_referente'] . '</td>';
				if($this->checkPerm($_SESSION["id_usuario"],15,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_unidad_ejecutora .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
				if($this->checkPerm($_SESSION["id_usuario"],15,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_unidad_ejecutora .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';												
				echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_unidad_ejecutora .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				$fila++;
			}
			echo '</table>';
		}
		
		// FIN METODOS RELACIONADOS CON UNIDADES EJECUTORAS		
				
		function getSHFilesDates(){
			// Fecha: 26/05/2014
			// Autor: Sebasti&aacute;n Salerno
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 19/10/2022
			$q = "SELECT DISTINCT fecha 
				FROM seg_e_hig_files 
				ORDER BY fecha DESC";
			$r = $this->excecuteQuery($q);
			
			while ( $row = mysqli_fetch_assoc($r) ){
				$vData[] = $row;
			}
			return $vData;
		}	
//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON FIRMANTE

		function listar_firmantes($id_firmante="", $nombre_select="firmante", $acceptNull=false, $textoReadOnly=""){
			// Fecha: 24 de Enero de 2013
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022
			if ($id_firmante == "") $id_firmante = 0;
			$q = 'SELECT 
					id_firmante,
					titulo_apellido_nombre 
				FROM firmante
				WHERE (baja = 0) OR
					(id_firmante = '.$id_firmante. ')
				ORDER BY
					titulo_apellido_nombre'; 
			
			$r = $this->excecuteQuery($q);
			echo '<td>';
			echo "<select name=\"$nombre_select\" $textoReadOnly>";
			if ($acceptNull) {
				echo "<option value=''";
				if ($id_firmante =='') echo " selected ";
				echo ">Sin firmante</option>";
			}
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id_firmante'] == $id_firmante){
					echo '<option selected value='. $row['id_firmante'] .'>'.  $row['titulo_apellido_nombre'] .'</option>';
				}else{
					echo '<option value='. $row['id_firmante'] .'>'. $row['titulo_apellido_nombre'] .'</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}

		function getSignatures($id_firmante){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$q = 'SELECT * 
				FROM firmante 				
				WHERE (baja = 0) OR
					(id_firmante = '.$id_firmante. ')
				ORDER BY id_firmante'; 
			$r = $this->excecuteQuery($q);
			
			while ( $row = mysqli_fetch_assoc($r) ){
				$vData[] = $row;
			}
			return $vData;
		}
				
		function agregar_firmante($titulo_apellido_nombre, $cargo, $lugar, $firma){
			// Fecha: 12 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("INSERT INTO firmante 
					(titulo_apellido_nombre, 
					cargo, 
					lugar, 
					firma,
					baja) 
				VALUES ('$titulo_apellido_nombre', 
					'$cargo', 
					'$lugar', 
					'$firma',
					0)");
		}

		function borrar_firmante($id_firmante){
			// Fecha: 12 de Noviembre de 2012
			// Autor: .j. Modificado por Vanina para implementar baja logica
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			//Nota Vanina: No doy de baja el archivo de la firma para que si hubiera PDF asociados 
			//se generen ok
			$this->excecuteQuery("UPDATE `firmante` 
					SET baja = 1
					WHERE `id_firmante` = '$id_firmante'");
			
		}
		
		function borrar_firma_firmante($id_firmante, $firma){
			// Fecha: 14 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE firmante 
					SET firma='' 
					WHERE id_firmante='$id_firmante'");
			if ($firma){			
				unlink($firma);					
			}
		}

		function modificar_firmante($id_firmante, $titulo_apellido_nombre, $cargo, $lugar, $firma){
			// Fecha: 12 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE firmante 
					SET titulo_apellido_nombre='$titulo_apellido_nombre', 
					cargo='$cargo', 
					lugar='$lugar', 
					firma='$firma' 
				     WHERE 
					id_firmante='$id_firmante'");
		}
		
		function consultar_firmante($id_firmante){
			// Fecha: 12 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 21/09/2022
			
			$q = '
				SELECT * FROM firmante
				WHERE id_firmante ="' . $id_firmante . '"
				 ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_firmantes(){
			// Fecha: 12 de Noviembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Firmante</th>';
			echo '<th>Cargo</th>'; 
		    echo '<th>Lugar</th>'; 
		    echo '<th>Firma</th>';
		    echo '<th colspan="3">Acciones</tr>';			
			
			$q = 'SELECT * 
				FROM firmante 
				WHERE baja = 0
				Order By titulo_apellido_nombre';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_firmante = 'form_firmante.php?id_firmante=' . $row['id_firmante'] . '&opcion=3';
				$lnkborrar_firmante = 'form_firmante.php?id_firmante=' . $row['id_firmante'] . '&opcion=2';				
				$lnkvisualizar_firmante = 'form_firmante_ver.php?id_firmante=' . $row['id_firmante'];

				echo '<tr class="modo1">';
					
				echo '<td>' . $row['titulo_apellido_nombre'] .'</td>';
				echo '<td>' . $row['cargo'] . '</td>';
				echo '<td>' . $row['lugar'] . '</td>';
				echo '<td><img src="'. $row['firma'] . '" width="75" height="35" border="0"></td>';
				if($this->checkPerm($_SESSION["id_usuario"],16,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_firmante .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';
				
				if($this->checkPerm($_SESSION["id_usuario"],16,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_firmante .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_firmante .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				$fila++;
			}
			echo '</table>';
		}

	// FIN METODOS RELACIONADOS CON FIRMANTES
	
	//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON BANCOS

		function agregar_banco($nombre){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("INSERT INTO banco (nombre) VALUES ('$nombre')");
		}

		function borrar_banco($id_banco){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022
		
			$this->excecuteQuery("DELETE FROM `banco` WHERE `id_banco` = '$id_banco'");
		}
		
		function modificar_banco($id_banco, $nombre){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE banco SET nombre='$nombre' WHERE id_banco='$id_banco'");
		}
		
		function consultar_banco($id_banco){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022
			
			$q = '
				SELECT * FROM banco
				WHERE id_banco =' . $id_banco;
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_bancos($nombre_usuario){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 18/10/2022
		
			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Nombre</th>';
		    echo '<th colspan="3">Acciones</tr>';			
			
			$q = '
			SELECT * FROM banco ORDER By nombre
			     ';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_banco = 'form_banco.php?id_banco=' . $row['id_banco'] . '&opcion=3';
				$lnkborrar_banco = 'form_banco.php?id_banco=' . $row['id_banco'] . '&opcion=2';				
				$lnkvisualizar_banco = 'form_banco_ver.php?id_banco=' . $row['id_banco'];

				echo '<tr class="modo1">';
				echo '<td>' . $row['nombre'] .'</td>';
				if($this->checkPerm($_SESSION["id_usuario"],13,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_banco .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
				if($this->checkPerm($_SESSION["id_usuario"],13,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_banco .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_banco .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';					
				$fila++;
			}
			echo '</table>';
		}
		
		function listar_bancos1($banco, $textoReadOnly){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022
			
			$q = '
			SELECT * FROM banco Order By nombre
			    ';
			$r = $this->excecuteQuery($q);
			echo '<td>';
			echo '<select name="banco1"  '.$textoReadOnly. '>';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id_banco'] == $banco){
					echo '<option selected value='. $row['id_banco'] .'>'.  $row['nombre'] .'</option>';
				}else{
					echo '<option value='. $row['id_banco'] .'>'. $row['nombre'] .'</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}

		function listar_bancos2($banco, $textoReadOnly){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX
				
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 14/10/2022

			$q = '
			SELECT * FROM banco Order By nombre
			    ';
			$r = $this->excecuteQuery($q);
			echo '<td>';
			echo '<select name="banco2" '.$textoReadOnly. '>';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id_banco'] == $banco){
					echo '<option selected value='. $row['id_banco'] .'>'.  $row['nombre'] .'</option>';
				}else{
					echo '<option value='. $row['id_banco'] .'>'. $row['nombre'].'</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}
	// FIN METODOS RELACIONADOS CON BANCOS
	
	//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON DESTINATARIOS

		function agregar_destinatario($mesa, $descripcion){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("INSERT INTO destinatario 
						(mesa, 
						descripcion,
						baja) 
					VALUES (
						'$mesa', 
						'$descripcion',
						0)");
		}

		function borrar_destinatario($id_destinatario){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j. Modificado por Vanina agrego baja logica
		
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE `destinatario` 
					SET baja = 1
					WHERE `id_destinatario` = '$id_destinatario'");			
		}
		
		function modificar_destinatario($id_destinatario, $mesa, $descripcion){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE destinatario 
					SET mesa='$mesa', 
					descripcion='$descripcion' 
					WHERE id_destinatario='$id_destinatario'");
		}
		
		function consultar_destinatario($id_destinatario){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022
			
			$q = '	SELECT * FROM destinatario
				WHERE id_destinatario ="' . $id_destinatario . '"';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_destinatarios($nombre_usuario){
			// Fecha: 15 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

		
			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Mesa</th>';
			echo '<th>Descripcion</th>'; 			
		    echo '<th colspan="3">Acciones</tr>';			
			
			$q = 'SELECT * FROM destinatario 
				WHERE baja=0 
				ORDER By descripcion';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_destinatario = 'form_destinatario.php?id_destinatario=' . $row['id_destinatario'] . '&opcion=3';
				$lnkborrar_destinatario = 'form_destinatario.php?id_destinatario=' . $row['id_destinatario'] . '&opcion=2';
				$lnkvisualizar_destinatario = 'form_destinatario_ver.php?id_destinatario=' . $row['id_destinatario'];				
				echo '<tr class="modo1">';
				echo '<td>' . $row['mesa'] .'</td>';
				echo '<td style="text-align:left">' . $row['descripcion'] . '</td>';
				if($this->checkPerm($_SESSION["id_usuario"],17,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_destinatario .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
				if($this->checkPerm($_SESSION["id_usuario"],17,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_destinatario .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_destinatario .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';					
				$fila++;
			}
			echo '</table>';
		}
	
		function listar_destinatarios($id_destinatario, $mesa){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$q = 'SELECT 
					* 
				FROM 
					destinatario 
				WHERE  
					mesa = "'.$mesa.'" and
					(baja = 0 or 
					id_destinatario = '.$id_destinatario .') 
				ORDER BY 
					descripcion';

			$r = $this->excecuteQuery($q);
			echo '<td>';
			echo '<select name="destinatario" style="width: 350px;">';
			while ( $row = mysqli_fetch_array($r) ){
				if ($row['id_destinatario'] == $id_destinatario){
					echo '<option selected value='. $row['id_destinatario'] .'>'.  $row['descripcion'] .'</option>';
				}else{
					echo '<option value='. $row['id_destinatario'] .'>'. $row['descripcion'] .'</option>';	
				}
			}
			echo '</select>';
			echo '</td>';
		}
		
	// FIN METODOS RELACIONADOS CON DESTINATARIOS
	
	//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON MONEDAS
		function getCoins(){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$q = 'SELECT * FROM moneda Order By descripcion';
			$r = $this->excecuteQuery($q);
			
			while ( $row = mysqli_fetch_assoc($r) ){
				$vData[] = $row;
			}
			return $vData;
		}

		function getCoinSymbol($iID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			
			$q = 'SELECT signo FROM moneda WHERE id_moneda = '.$iID;
			$r = $this->excecuteQuery($q);
			
			$row = mysqli_fetch_assoc($r);
			return $row['signo'];
		}

		function agregar_moneda($signo, $descripcion){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("INSERT INTO moneda (signo, descripcion) VALUES ('$signo', '$descripcion')");
		}

		function check_uso_moneda ($id_moneda) {		
			//Vanina
			// No permito borrar una moneda si esta siendo utilizada en una Orden de Pago, 
			//Actas Compras, Anexo donacion items, Orden de Compra		

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$r = $this->excecuteQuery("SELECT
						exists(SELECT 1 FROM `actas_compras` WHERE moneda = ".$id_moneda.") as ExisteMonedaActasCompras,
						exists(SELECT 1 FROM `orden_pago` WHERE id_moneda = ".$id_moneda.") as ExisteMonedaOrdenPago,
						exists(SELECT 1 FROM `anexo_donacion_items` WHERE moneda = ".$id_moneda.") as ExisteMonedaAnexoDonacion,
						exists(SELECT 1 FROM `orden_compra` WHERE signo_moneda = ".$id_moneda.") as ExisteMonedaOrdenCompra
					FROM
						moneda
					WHERE
						id_moneda = ".$id_moneda);
			$row = mysqli_fetch_array($r);
			$mensaje = "";
			if ($row['ExisteMonedaActasCompras'] == 1) $mensaje = "Actas de Compras, ";
			if ($row['ExisteMonedaOrdenPago'] == 1) $mensaje = $mensaje."Ordenes de Pago, ";
			if ($row['ExisteMonedaAnexoDonacion'] == 1) $mensaje = $mensaje."Items de Anexo de Donacion, ";
			if ($row['ExisteMonedaOrdenCompra'] == 1) $mensaje = $mensaje."Ordenes de Compra, ";
			$mensaje = substr($mensaje,0,-2);
			return $mensaje;
		}
		
		
		function borrar_moneda($id_moneda){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j. 			
			
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("DELETE FROM `moneda` WHERE `id_moneda` = '$id_moneda'");
		}
		
		function modificar_moneda($id_moneda, $signo, $descripcion){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE moneda SET signo='$signo', descripcion='$descripcion' WHERE id_moneda='$id_moneda'");
		}
		
		function consultar_moneda($id_moneda){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			$q = '
				SELECT * FROM moneda
				WHERE id_moneda ="' . $id_moneda . '"
				 ';
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_monedas($nombre_usuario){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022
		
			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Signo</th>';
			echo '<th>Descripci&oacute;n</th>'; 
		    echo '<th colspan="3">Acciones</tr>';			
			
			$q = 'SELECT * 
					FROM moneda 
					ORDER BY descripcion';
			$r = $this->excecuteQuery($q);
			$fila = 1;
			
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_moneda = 'form_moneda.php?id_moneda=' . $row['id_moneda'] . '&opcion=3';
				$lnkborrar_moneda = 'form_moneda.php?id_moneda=' . $row['id_moneda'] . '&opcion=2';				
				$lnkvisualizar_moneda = 'form_moneda_ver.php?id_moneda=' . $row['id_moneda'];				

				echo '<tr class="modo1">';
				echo '<td>' . $row['signo'] .'</td>';
				echo '<td>' . $row['descripcion'] . '</td>';
				if($this->checkPerm($_SESSION["id_usuario"],14,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_moneda .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
				if($this->checkPerm($_SESSION["id_usuario"],14,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_moneda .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_moneda .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				$fila++;
			}
			echo '</table>';
		}

		//$signo_moneda: muestra el combo con ese valor selected (0=default que es peso)
		//si enabled es false, no muestra mas opciones en la lista
		//si enabled es true, muestra el resto de las opciones en la lista
		function listar_monedas($signo_moneda=0, $enabled=true, $name='signo_moneda'){
			// Fecha: 10 de Diciembre de 2012
			// Autor: .j
			// ESTA FUNCION ESTA HECHA PARA RELLENAR LOS LISTBOX
			//Si signo moneda es distinto de cero, muestro ese solo ya que toda la orden de compra debe tener el mismo signo y a partir del 2do item se queda fijo

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			if ($enabled) {
				$where = "";}
			else {
				if ($signo_moneda == 0) $signo_moneda = 1;
				$where = " WHERE id_moneda = $signo_moneda ";
			}
			
			
			$q = "SELECT * 
					FROM moneda ".
					$where.
					" ORDER BY descripcion";
			$r = $this->excecuteQuery($q);
			echo "<select name='$name' >";
			while ( $row = mysqli_fetch_array($r) ){
				echo '<option ';
				if ($row['id_moneda'] == $signo_moneda){
					echo 'selected';
				}
				echo ' value='. $row['id_moneda'] .'>'.  $row['signo'] .'</option>';
			}
			echo '</select>';
		}

		//$signo_moneda: muestra el combo con ese valor selected (0=default que es peso)
		//si enabled es false, no muestra mas opciones en la lista
		//si enabled es true, muestra el resto de las opciones en la lista
		
		
	// FIN METODOS RELACIONADOS CON MONEDAS
	
	//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON MESA DE ENTRADA

		function agregar_mesa_entrada($numero_tramite, $anio_numero_tramite, $fecha, $remitente, $documento, $destinatario, $cantidad, $observaciones, $confecciono, $firmante){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$q = "INSERT INTO mesa_entrada (numero_tramite, anio_numero_tramite, fecha, remitente, documento, destinatario, cantidad, observaciones, confecciono, firmante) 
									VALUES ('$numero_tramite', '$anio_numero_tramite', '$fecha', '$remitente', '$documento', '$destinatario', '$cantidad', '$observaciones', '$confecciono', '$firmante')";
			
			$this->excecuteQuery($q);
		}

		function borrar_mesa_entrada($numero_orden){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$q = "DELETE FROM `mesa_entrada` WHERE `numero_orden` = '$numero_orden'";
		
			$this->excecuteQuery($q);
		}
		
		function modificar_mesa_entrada($numero_orden, $numero_tramite, $anio_numero_tramite, $fecha, $remitente, $documento, $destinatario, $cantidad, $observaciones, $firmante){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$q = "UPDATE mesa_entrada SET numero_tramite='$numero_tramite', anio_numero_tramite='$anio_numero_tramite', fecha='$fecha', remitente='$remitente', documento='$documento', destinatario='$destinatario', cantidad='$cantidad', observaciones='$observaciones', firmante='$firmante' WHERE numero_orden='$numero_orden'";

			$this->excecuteQuery($q);
			
		}
		
		function consultar_mesa_entrada($numero_orden){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022
			
			$q = 'SELECT * FROM mesa_entrada WHERE numero_orden = ' . $numero_orden ;
			$r = $this->excecuteQuery($q);
			if($r){
				$row = mysqli_fetch_array($r);
				return $row;
			}else{
				return false;
			}
		}
		
		function getConfig($sField){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022
			$sSQL = "SELECT valor FROM config WHERE id = '$sField'";
			$sResult = $this->excecuteQuery($sSQL);
			$row = mysqli_fetch_assoc($sResult);
			return (int)$row['valor'];
		}
		
		function setConfig($sField){
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

			$sSQL = "UPDATE config SET valor = valor+1 WHERE id = '$sField'";
			$sResult = $this->excecuteQuery($sSQL);
		}

		function lista_mesa_entrada($nombre_usuario,$vData){
			// Fecha: 19 de Noviembre de 2012
			// Autor: .j
			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022


			$iMaxRows = 15;

			$sHtml = 'B&uacute;squeda:
			<form name="frmBus" action="lista_mesa_entrada.php" method="POST">
			<input type="radio" name="opBus" value="me.numero_tramite"{me.numero_tramite}><label style="font-size:12px">Num. Tr&aacute;mite</label>
			<input type="radio" name="opBus" value="me.fecha"{me.fecha}><label style="font-size:12px">Fecha</label>
			<input type="radio" name="opBus" value="me.remitente"{me.remitente}><label style="font-size:12px">Remitente</label>
			<input type="radio" name="opBus" value="me.documento"{me.documento}><label style="font-size:12px">Documentos</label>
			<input type="radio" name="opBus" value="d.descripcion"{d.descripcion}><label style="font-size:12px">Destinatarios</label>
			<input type="text" name="txtBus" value="{txtBus}" style="width:200px"/>
			<input type="submit" name="btnBus" value="Buscar"/>
			</form>';
			if(isset($vData['opBus'])){
				$sHtml = str_replace("{".$vData['opBus']."}","checked='checked'",$sHtml);
			}else{
				$sHtml = str_replace("{me.numero_tramite}","checked='checked'",$sHtml);
			}
			$vTemp = array("{me.numero_tramite}","{me.fecha}","{me.remitente}","{me.documentos}","{d.descripcion}");
			$sHtml = str_replace($vTemp,"",$sHtml);
			$sHtml = str_replace("{txtBus}",$vData['txtBus'] ?? '',$sHtml);
			echo $sHtml;

			
			echo '<div align="center">
					<form method="post" enctype="multipart/form-data" name="filtro_fechas" id="filtro_fechas" action="mesa_entrada_pdf.php">';//action="../tcpdf/examples/example_006.php"
			echo '		<label for="fecha_desde">
							<font font-size="12" font-family:"Verdana, Geneva, sans-serif" color="#333">
								<strong>Desde: </strong>
							</font>
							<input name="fecha_desde" type="text" id="fecha_desde" size="12" maxlength="10">
						</label>';			
			
			echo '		<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Inicial" id="lanzador_desde">';
			echo '		<label for="fecha_hasta">
							<font font-size="12" font-family:"Verdana, Geneva, sans-serif" color="#333">
								<strong>Hasta: </strong>
							</font>
							<input name="fecha_hasta" type="text" id="fecha_hasta" size="12" maxlength="10">
						</label>';			
			echo '		<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Final" id="lanzador_hasta">';
			?>
			<script type="text/javascript"> 
				Calendar.setup({ 
				inputField     :    "fecha_desde",     // id del campo de texto 
				ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
				button     :    "lanzador_desde"     // el id del bot&oacute;n que lanzar&aacute; el calendario 
			}); 
			</script>
			<script type="text/javascript"> 
				Calendar.setup({ 
				inputField     :    "fecha_hasta",     // id del campo de texto 
				ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
				button     :    "lanzador_hasta"     // el id del bot&oacute;n que lanzar&aacute; el calendario 
			}); 
			</script>
			<style type="text/css">
				.desdehasta {
					font-family: Verdana, Geneva, sans-serif;
					font-size: 11px;
					color: #333;
					font-variant: normal;
					font-weight: normal;
					text-align: center;
					padding-left: 5px;
					padding-right: 30px;
					vertical-align: bottom;
					padding-bottom: 10px;
				}
			</style>			
			<?php
			echo '<input name="image" type="image" src="acrobat.png" width="20" height="20" oversrc="acrobat.png">';
			echo '</form></div>';
								
			echo '<table id="tab" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
			echo '<th>N&uacute;m. Tr&aacute;mite</th>'; 
			echo '<th><!--<input type="button" value="Fecha" onclick="selecciona(this,1)" onkeypress="selecciona(this,2)" />-->Fecha</th>';
			echo '<th>Remitente</th>'; 
			echo '<th><!--<input type="button" value="Documentos" onclick="selecciona(this,3)" onkeypress="selecciona(this,4)" />-->Documentos</th>';
			echo '<th><!--<input type="button" value="Destinatario" onclick="selecciona(this,4)" onkeypress="selecciona(this,5)" />-->Destinatario</th>';
			echo '<th>Cantidad</th>'; 
			echo '<th>Observaciones</th>'; 						
		    echo '<th colspan="3">Acciones</tr>';			
			
			$iPagActual = ($_GET['pag'])??1;
			$iLimit = ($iPagActual -1) * $iMaxRows;

			$sWhere = '';
			if(isset($vData['txtBus'])){
				if($vData['opBus'] != 'numero_tramite'){
					$sWhere = 'WHERE '.$vData['opBus'].' LIKE "%'.$vData['txtBus'].'%"';
				}else{
					$sWhere = 'WHERE '.$vData['opBus'].' = '.$vData['txtBus'];
				}
			}

			$q = '
			SELECT SQL_CALC_FOUND_ROWS * FROM mesa_entrada me INNER JOIN destinatario d ON d.id_destinatario = me.destinatario '. $sWhere .' ORDER BY me.numero_orden DESC LIMIT '.$iLimit.','.$iMaxRows;
			$r = $this->excecuteQuery($q);
			$fila = 1;
			$p = $this->excecuteQuery("SELECT CEIL(FOUND_ROWS()/$iMaxRows) as total");
			$results = mysqli_fetch_assoc($p);
			$iTotalPag = $results['total'];
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar_mesa_entrada = 'form_mesa_entrada.php?numero_orden=' . $row['numero_orden'] . '&opcion=3';
				$lnkborrar_mesa_entrada = 'form_mesa_entrada.php?numero_orden=' . $row['numero_orden'] . '&opcion=2';
				$lnkvisualizar_mesa_entrada = 'form_mesa_entrada_ver.php?numero_orden=' . $row['numero_orden'];

				echo '<tr class="modo1">';
				echo '<td>' . $row['numero_tramite'] . '/' . $row['anio_numero_tramite'] .'</td>';				
				echo '<td>' . convertir_fecha($row['fecha']) . '</td>';
				echo '<td>' . $row['remitente'] . '</td>';
				echo '<td>' . substr($row['documento'], 0, 45) .'...'. '</td>';
				$row_destinatario = $this->consultar_destinatario($row['destinatario']);				
				echo '<td>' . $row_destinatario['descripcion'] . '</td>';
				echo '<td>' . $row['cantidad'] . '</td>';
				echo '<td>' . $row['observaciones'] . '</td>';

				if($this->checkPerm($_SESSION["id_usuario"],1,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar_mesa_entrada .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';
				if($this->checkPerm($_SESSION["id_usuario"],1,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar_mesa_entrada .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar_mesa_entrada .  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';		
				$fila++;
			}
			$iNextPage = ($iPagActual<$iTotalPag)?($iPagActual+1):$iPagActual;
			$iPrevPage = ($iPagActual > 1)?($iPagActual-1):$iPagActual;
			
			echo "<tr>
					<td colspan='10'>
						<a href='lista_mesa_entrada.php?pag=$iPrevPage'>Prev </a>
						$iPagActual / $iTotalPag
						<a href='lista_mesa_entrada.php?pag=$iNextPage' value='sig'>Sig</a>
					</td>
				</tr>";
			echo '</table>';
			$bd = NULL;			
			echo '</table>';
		}
		
	// FIN METODOS RELACIONADOS CON MESA DE ENTRADA

	//-------------------------------------------------------------------------------------------	
	//-------------------------------------------------------------------------------------------

	// INICIO METODOS RELACIONADOS CON TITULAR

		
		public function getTitular($iID){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$sSQL = "SELECT * 
				FROM titular 
				WHERE id_titular = $iID";
			$res = $this->excecuteQuery($sSQL);

			$vData = mysqli_fetch_assoc($res);
			
			return $vData;
		}

		function listar_titulares($id_titular="0"){ 
			// Fecha: 31 oct 2017
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 22/09/2022

			if (is_null($id_titular) or ($id_titular == ""))
				$id_titular = 0;

			$q = "SELECT 
					id_titular,
					apellido,
					nombre,
					email 
				FROM titular
				WHERE (baja = 0) OR
					(id_titular = $id_titular)
				ORDER BY
					apellido"; 
			
			$r = $this->excecuteQuery($q);
			
			while($row = mysqli_fetch_assoc($r)){
				$vData[] = $row;
			}		
			
			return $vData;
		}

				
		function agregar_titular($apellido, $nombre, $dni, $email){
			// Fecha: 31 oct 2017
			// Autor: Vanina

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("INSERT INTO titular 
					(apellido, 
					nombre, 
					dni,
					email,
					baja) 
				VALUES ('$apellido', 
					'$nombre', 
					'$dni',
					'$email',  
					0)");
		}

		function borrar_titular($id_titular){
			// Fecha: 31 oct 2017
			// Autor: Vanina		

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE `titular` 
					SET baja = 1
					WHERE `id_titular` = $id_titular");
			
		}

		function modificar_titular($id_titular, $apellido, $nombre, $dni, $email){
			// Fecha: 31 oct 2017
			// Autor: Vanina

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$this->excecuteQuery("UPDATE titular 
					SET apellido='$apellido', 
					nombre='$nombre', 
					dni='$dni',
					email='$email'  
				     WHERE 
					id_titular='$id_titular'");
		}
		
		function consultar_titular($id){
			// Fecha: 31 oct 2017
			// Autor: Vanina			

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			$q = "SELECT * FROM titular
				WHERE id_titular = $id";
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;		
		}

		function lista_titulares($vData){
			// Fecha: 31 oct 2017

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			// busqueda
			$sHtml = '
			<form name="frmBus" action="lista_titulares.php" method="POST">
			<table class="tabla">
				<tr><th colspan="6">B&uacute;squeda</th></tr>
				<tr class="modo1">
					<td>
						Apellido
					</td>
					<td>
						<input type="text" name="apellidoBus" value="{apellidoBus}" style="width:150px"/>
					</td>
					<td>
						Nombre
					</td>
					<td>
						<input type="text" name="nombreBus" value="{nombreBus}" style="width:150px"/>
					</td>
					<td>
						<input type="submit" name="btnBus" value="Buscar"/>
					</td>
				</tr>
			</table>
			</form>';
			
			if (array_key_exists('apellidoBus', $vData))
				$apellidoBus = $vData['apellidoBus'];
			else
				$apellidoBus = "";

			if (array_key_exists('nombreBus', $vData))
				$nombreBus = $vData['nombreBus'];
			else
				$nombreBus = "";
			
			$sHtml = str_replace("{apellidoBus}",$apellidoBus,$sHtml);
			$sHtml = str_replace("{nombreBus}",$nombreBus,$sHtml);

			echo $sHtml;	

			$sWhere = '';

			if ($apellidoBus != "") {				
				$sWhere .= ' AND apellido LIKE "'.$apellidoBus.'%"';
			} 
			if ($nombreBus != "") {				
				$sWhere .= ' AND nombre LIKE "'.$nombreBus.'%" ';
			}

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" align="center" class="tabla table-autosort table-autofilter">';
			echo '<thead><tr>';
		    	echo '<th class="table-sortable:default">Apellido</th>';
			echo '<th class="table-sortable:default">Nombre</th>'; 
		    	echo '<th class="table-sortable:default">Email</th>'; 
		    	echo '<th class="table-sortable:default" colspan="3">Acciones</th>';			
			echo '</tr></thead>';
			$q = "SELECT * 
				FROM titular 
				WHERE baja = 0 $sWhere
				Order By apellido";

			$r = $this->excecuteQuery($q);
			$fila = 1;

			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar = 'form_titular.php?id=' . $row['id_titular'] . '&opcion=3';
				$lnkborrar = 'form_titular.php?id=' . $row['id_titular'] . '&opcion=2';				
				$lnkvisualizar = 'form_titular.php?id=' . $row['id_titular'] . '&opcion=4';

				echo '<tr class="modo1">';
					
				echo '<td>' . $row['apellido'] .'</td>';
				echo '<td>' . $row['nombre'] . '</td>';
				echo '<td>' . $row['email'] . '</td>';
				if($this->checkPerm($_SESSION["id_usuario"],31,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';
				
				if($this->checkPerm($_SESSION["id_usuario"],31,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar.  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';

				if($this->checkPerm($_SESSION["id_usuario"],31,'consulta')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar.  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/previsualizarg.png" width="30" height="30" border="0" alt="Ver Registro"></a></td>';
				
				$fila++;
			}
			echo '</table>';
		}

		
		function check_dni_titular($dni, $id_titular=""){
			// Fecha: 26-10-2017
			// Autor: Vanina
			// Devuelve true si no existe un titular con dni igual al
			//parametro 1. No considera el usuario pasado en parametro 2.

			//Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 18/10/2022

			if (trim($dni) != "") {
				
				$q = '	SELECT 
						count(*) as cant 
					FROM 
						titular
					WHERE 
						dni ="' . $dni . '" and
						id_titular != "'.$id_titular. '" and
						baja = 0';

				$r = $this->excecuteQuery($q);			
				$row = mysqli_fetch_array($r);
				$cant = $row['cant'];
				mysqli_free_result($r);
				return $cant==0;
				}
			else
				return true;
		}
	// FIN METODOS RELACIONADOS CON TITULAR
	
//-------------------------------------------------------------------------------------------
	//INICIO METODOS RELACIONADOS CON CERTIFICADOS

		function getTiposCertificados(){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$q = "SELECT 
					* 
				FROM 
					certificado_tipo
				ORDER BY 
				 	nombre";
				 	
			$r = $this->excecuteQuery($q);
			return $r;
		}
	

		function getTitulosPersonas($tipo_titulo = "1"){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$q = "SELECT 
					* 
				FROM 
					certificado_titulo_persona
				WHERE
					tipo_titulo IN ($tipo_titulo)";
				 	
			$r = $this->excecuteQuery($q);
			return $r;
		}

		function lista_certificados($vData){
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$iMaxRows = 15;
			
			$sHtml = '
			<form name="frmBus" action="lista_certificados.php" method="POST">
			<table class="tabla">
				<tr><th colspan="6">B&uacute;squeda</th></tr>
				<tr class="modo1">
					<td>
						Fecha<br/> (dd-mm-aaaa)
					</td>
					<td>
						<input type="text" name="dateBus" value="{dateBus}" style="width:150px"/>
					</td>
					<td>
						Tipo Certificado
					</td>
					<td>
						<select name="tipoCertificado"><option value=>Todos</option>';
						$arrayTiposCertificados = $this->getTiposCertificados();
						while ( $row = mysqli_fetch_array($arrayTiposCertificados) ){
							if (array_key_exists('tipoCertificado', $vData) and $row['id_tipo_certificado'] == $vData['tipoCertificado']){
								$sHtml = $sHtml.'<option selected value='. $row['id_tipo_certificado'] .'>'.  $row['nombre'] .'</option>';
							}else{
								$sHtml = $sHtml.'<option value='. $row['id_tipo_certificado'] .'>'. $row['nombre'] .'</option>';	
							}
						}
						$sHtml = $sHtml.'</select>';
					$sHtml = $sHtml.'	
					</td>		
					<td>
						Certif. N&uacute;m./A&ntilde;o
					</td>
					<td>
						<input type="text" name="txtNroCertificado" value="{txtNroCertificado}" style="width:70px"/>
					</td>
				</td></tr>
				<tr class="modo1">
					<td>
						Solicitante Apellido
					</td>
					<td>
						<input type="text" name="txtSolicitanteApellido" value="{txtSolicitanteApellido}" style="width:150px"/>
					</td>
					<td>
						Solicitante Nombre
					</td>
					<td align="left">
						<input type="text" name="txtSolicitanteNombre" value="{txtSolicitanteNombre}" style="width:150px"/>
					</td>
					<td colspan="2">
						<input type="submit" name="btnBus" value="Buscar"/>
					</td>
					</td>
				</tr>
			</table>
			</form>';
			
			if (array_key_exists('txtNroCertificado', $vData))
				$vartxtNroCertificado = $vData['txtNroCertificado'];
			else
				$vartxtNroCertificado = "";

			if (array_key_exists('txtSolicitanteApellido', $vData))
				$vartxtSolicitanteApellido = $vData['txtSolicitanteApellido'];
			else
				$vartxtSolicitanteApellido = "";

			if (array_key_exists('txtSolicitanteNombre', $vData))
				$vartxtSolicitanteNombre = $vData['txtSolicitanteNombre'];
			else
				$vartxtSolicitanteNombre = "";

			if (array_key_exists('dateBus', $vData))
				$varDateBus = $vData['dateBus'];
			else
				$varDateBus = "";

			if (array_key_exists('tipoCertificado', $vData))
				$varTipoCertificado = $vData['tipoCertificado'];
			else
				$varTipoCertificado = "";			
			
			$sHtml = str_replace("{txtNroCertificado}",$vartxtNroCertificado,$sHtml);
			$sHtml = str_replace("{txtSolicitanteApellido}",$vartxtSolicitanteApellido,$sHtml);
			$sHtml = str_replace("{txtSolicitanteNombre}",$vartxtSolicitanteNombre,$sHtml);
			$sHtml = str_replace("{dateBus}",$varDateBus,$sHtml);

			echo $sHtml;

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    	echo '<th>N&uacute;m. Certif./A&ntilde;o</th>';
			echo '<th>Fecha</th>'; 
		    	echo '<th>Tipo</th>'; 
		    	echo '<th>Solicitante</th>';
		    	echo '<th colspan="3">Acciones</th></tr>';			

			$sWhere = '';

			if ($varDateBus != "") {
				$arrayDateBus = explode("-",$varDateBus);				
				$sWhere .= ' AND c.fecha_certificado = "'.$arrayDateBus[2].'-'.$arrayDateBus[1].'-'.$arrayDateBus[0].'"';
			} 
			if ($varTipoCertificado != "") {				
				$sWhere .= ' AND c.id_tipo_certificado = '.$varTipoCertificado.' ';
			}
			if ($vartxtNroCertificado != "") {
				$arrayNroCertificado = explode("/",$vartxtNroCertificado);
				$sWhere .= 'AND c.numero = '.$arrayNroCertificado[0];
				if(isset($arrayNroCertificado[1])){
					$sWhere.=' AND c.anio = '.$arrayNroCertificado[1];
				}
			}
			if ($vartxtSolicitanteApellido != "") {				
				$sWhere .= " AND c.apellido LIKE '%$vartxtSolicitanteApellido%'";
			}
			if ($vartxtSolicitanteNombre != "") {				
				$sWhere .= " AND c.nombre LIKE '%$vartxtSolicitanteNombre%'";
			}
			

			$iPagActual = (isset($_GET['pag']))?$_GET['pag']:1;
			$iLimit = ($iPagActual -1) * $iMaxRows;
			
			$q = 'SELECT SQL_CALC_FOUND_ROWS 
					c.id_certificado,
					c.numero, 
					c.anio, 
					c.fecha_certificado, 
					c.apellido, 
					c.nombre,
					t.nombre as tipo_certificado 
				FROM 
					certificado c, 
					certificado_tipo t 
				WHERE 
					c.id_tipo_certificado=t.id_tipo_certificado and 
					c.baja = 0 '.
					$sWhere.' 
				ORDER By 
					id_certificado DESC 
				LIMIT '.
					$iLimit.','.$iMaxRows;
			$r = $this->excecuteQuery($q);

			$p = $this->excecuteQuery("SELECT CEIL(FOUND_ROWS()/$iMaxRows) as total");
			$results = mysqli_fetch_assoc($p);
			$iTotalPag = $results['total'];
			
			if($r){
				while ( $row = mysqli_fetch_array($r) ){
					$lnkmodificar = 'form_certificado.php?id_certificado=' . $row['id_certificado'] . '&opcion=3';
					$lnkborrar = 'form_certificado.php?id_certificado=' . $row['id_certificado'] . '&opcion=2';
					$lnkimprimir = 'certificado_pdf.php?id_certificado='.$row['id_certificado'];			
				
					echo '<tr class="modo1">';
					echo '<td>' . $row['numero'] .'/'. $row['anio'].'</td>';
					echo '<td>' . convertir_fecha($row['fecha_certificado']) . '</td>';
					echo '<td>' . $row['tipo_certificado'] . '</td>';				
					echo '<td>' . $row['apellido'].', '. $row['nombre'] . '</td>';
					
					if($this->checkPerm($_SESSION["id_usuario"],32,'baja')){				
						echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';					
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro"></a></td>';				
					if($this->checkPerm($_SESSION["id_usuario"],32,'modificacion')){	
						echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar .  '><img src="actualizar_datos.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" alt="Modificar Registro"></a></td>';
					echo '<td align="center"><font color="#333333"><a href=' . $lnkimprimir .  '><img src="acrobat.png" width="30" height="30" border="0" alt="Imprimir Nota"></a></td>';
				}
			}
			$iNextPage = ($iPagActual<$iTotalPag)?($iPagActual+1):$iPagActual;
			$iPrevPage = ($iPagActual > 1)?($iPagActual-1):$iPagActual;
			
			echo "<tr>
					<td colspan='10'>
						<a href='lista_certificados.php?pag=$iPrevPage.&tipoCertificado=".$varTipoCertificado."&txtNroCertificado=".$vartxtNroCertificado."&dateBus=".$varDateBus."&txtSolicitanteApellido=".$vartxtSolicitanteApellido."&txtSolicitanteNombre=".$vartxtSolicitanteNombre."'>Prev </a>
						$iPagActual / $iTotalPag
						<a href='lista_certificados.php?pag=$iNextPage.&tipoCertificado=".$varTipoCertificado."&txtNroCertificado=".$vartxtNroCertificado."&dateBus=".$varDateBus."&txtSolicitanteApellido=".$vartxtSolicitanteApellido."&txtSolicitanteNombre=".$vartxtSolicitanteNombre."' value='sig'>Sig</a>
					</td>
				</tr>";
			echo '</table>';
		}

						
		function agregar_certificado_unificacion_aportes ($anio, $id_tipo_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, &$error){
			// Fecha: 1 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$bd = new Bd;
			$con = $bd->AbrirBd();
			try {
				$this->excecuteQuery("START TRANSACTION");
				$this->excecuteQuery("INSERT INTO `certificado`
							(`anio`, 
							`numero`, 					
							`fecha_certificado`, 
							`id_tipo_certificado`, 
							`apellido`, 
							`nombre`, 
							`DNI`) 
						SELECT
							$anio,
							IFNULL(MAX(numero), 0)+1,
							'$fecha_certificado',
							$id_tipo_certificado,
							'$apellido', 
							'$nombre', 
							'$DNI'
						FROM
							certificado
						WHERE 
							anio = $anio");
				$id_certificado = $this->lastId(); 
				$this->excecuteQuery("INSERT INTO `certificado_unificacion_aportes`
							(`id_certificado`, 
							`id_titulo_persona`, 
							`CUIL`, 
							`fecha_ingreso`)  
						VALUES 
							($id_certificado,
							$id_titulo_persona,
							'$CUIL',
							'$fecha_ingreso')");
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}

		}

						
		function agregar_certificado_obra_social ($anio, $id_tipo_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $id_escalafon_categoria, &$error){
			// Fecha: 1 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$bd = new Bd;
			$con = $bd->AbrirBd();
			try {
				$this->excecuteQuery("START TRANSACTION");
				$this->excecuteQuery("INSERT INTO `certificado`
							(`anio`, 
							`numero`, 					
							`fecha_certificado`, 
							`id_tipo_certificado`, 
							`apellido`, 
							`nombre`, 
							`DNI`) 
						SELECT
							$anio,
							IFNULL(MAX(numero), 0)+1,
							'$fecha_certificado',
							$id_tipo_certificado,
							'$apellido', 
							'$nombre', 
							'$DNI'
						FROM
							certificado
						WHERE 
							anio = $anio");
				$id_certificado = $this->lastId(); 
				$this->excecuteQuery("INSERT INTO `certificado_obra_social`
							(`id_certificado`, 
							`id_titulo_persona`, 
							`CUIL`, 
							`fecha_ingreso`,
							`id_escalafon_categoria`
							)  
						VALUES 
							($id_certificado,
							$id_titulo_persona,
							'$CUIL',
							'$fecha_ingreso',
							$id_escalafon_categoria)");
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}

		}
						
		function agregar_certificado_antiguedad ($anio, $id_tipo_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $id_escalafon_categoria, $fecha_egreso, $goce_licencia, &$error){
			// Fecha: 1 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$bd = new Bd;
			$con = $bd->AbrirBd();
			try {
				$this->excecuteQuery("START TRANSACTION");
				$sql = "INSERT INTO `certificado`
				(`anio`, 
				`numero`, 					
				`fecha_certificado`, 
				`id_tipo_certificado`, 
				`apellido`, 
				`nombre`, 
				`DNI`) 
			SELECT
				$anio,
				IFNULL(MAX(numero), 0)+1,
				'$fecha_certificado',
				$id_tipo_certificado,
				'$apellido', 
				'$nombre', 
				'$DNI'
			FROM
				certificado
			WHERE 
				anio = $anio";
				$this->excecuteQuery($sql);
				$id_certificado = $this->lastId(); 
				if ($fecha_egreso != '') $fecha_egreso = "'".$fecha_egreso."'";
				else $fecha_egreso = "NULL";
				$sql = "INSERT INTO `certificado_antiguedad`
				(`id_certificado`, 
				`id_titulo_persona`, 
				`CUIL`, 
				`fecha_ingreso`,
				`id_escalafon_categoria`,
				`fecha_egreso`,
				`goce_licencia`
				)  
			VALUES 
				($id_certificado,
				$id_titulo_persona,
				'$CUIL',
				'$fecha_ingreso',
				$id_escalafon_categoria,
				$fecha_egreso,
				$goce_licencia
				)";
				$this->excecuteQuery($sql);
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}

		}

		function agregar_certificado_beca($anio, $id_tipo_certificado, 
						$fecha_certificado, $apellido, $nombre, $DNI, $id_escalafon_categoria,
						$resolucion, $fecha_resolucion, $fecha_ini_beca, $fecha_fin_beca, $tema, 
						$id_titulo_persona, $apellido_direccion, $nombre_direccion, 
						$articulo_lugar, $lugar_beca, &$error) {
			// Fecha: 27 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$bd = new Bd;
			$con = $bd->AbrirBd();
			try {
				$this->excecuteQuery("START TRANSACTION");
				$this->excecuteQuery("INSERT INTO `certificado`
							(`anio`, 
							`numero`, 					
							`fecha_certificado`, 
							`id_tipo_certificado`, 
							`apellido`, 
							`nombre`, 
							`DNI`) 
						SELECT
							$anio,
							IFNULL(MAX(numero), 0)+1,
							'$fecha_certificado',
							$id_tipo_certificado,
							'$apellido', 
							'$nombre', 
							'$DNI'
						FROM
							certificado
						WHERE 
							anio = $anio");
				$id_certificado = $this->lastId(); 
				if ($fecha_fin_beca != '') $fecha_fin_beca = "'".$fecha_fin_beca."'";
				else $fecha_fin_beca = "NULL";
				$this->excecuteQuery("INSERT INTO `certificado_beca`
							(`id_certificado`, 
							`id_escalafon_categoria`,
							`resolucion`,
							`fecha_resolucion`,
							`fecha_ini_beca`,
							`fecha_fin_beca`,
							`tema`,
							`id_titulo_persona`, 
							`apellido_direccion`,
							`nombre_direccion`,  
							`articulo_lugar`,
							`lugar_beca`
							)  
						VALUES 
							($id_certificado,
							$id_escalafon_categoria,
							'$resolucion',
							'$fecha_resolucion',
							'$fecha_ini_beca',
							$fecha_fin_beca,
							'$tema',
							$id_titulo_persona,
							'$apellido_direccion',
							'$nombre_direccion',
							'$articulo_lugar',
							'$lugar_beca'
							)");
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}

		}


		function agregar_certificado_horario($anio, $id_tipo_certificado, 
						$fecha_certificado, $apellido, $nombre, $DNI, $id_escalafon_categoria,
						$fecha_ini, $tema, $id_titulo_persona, $articulo_lugar, $lugar, $id_unidad_ejecutora,
						$hora_ini_lunes, $hora_fin_lunes, $hora_ini_martes, $hora_fin_martes,
						$hora_ini_miercoles, $hora_fin_miercoles, $hora_ini_jueves, $hora_fin_jueves,
						$hora_ini_viernes, $hora_fin_viernes, &$error) {
			// Fecha: 27 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$bd = new Bd;
			$con = $bd->AbrirBd();
			try {
				$this->excecuteQuery("START TRANSACTION");
				$this->excecuteQuery("INSERT INTO `certificado`
							(`anio`, 
							`numero`, 					
							`fecha_certificado`, 
							`id_tipo_certificado`, 
							`apellido`, 
							`nombre`, 
							`DNI`) 
						SELECT
							$anio,
							IFNULL(MAX(numero), 0)+1,
							'$fecha_certificado',
							$id_tipo_certificado,
							'$apellido', 
							'$nombre', 
							'$DNI'
						FROM
							certificado
						WHERE 
							anio = $anio");
				$id_certificado = $this->lastId(); 

				if ($hora_ini_lunes != '') $hora_ini_lunes = "'".$hora_ini_lunes."'";
				else $hora_ini_lunes = "NULL";
				if ($hora_fin_lunes != '') $hora_fin_lunes = "'".$hora_fin_lunes."'";
				else $hora_fin_lunes = "NULL";
				if ($hora_ini_martes != '') $hora_ini_martes = "'".$hora_ini_martes."'";
				else $hora_ini_martes = "NULL";
				if ($hora_fin_martes != '') $hora_fin_martes = "'".$hora_fin_martes."'";
				else $hora_fin_martes = "NULL";
				if ($hora_ini_miercoles != '') $hora_ini_miercoles = "'".$hora_ini_miercoles."'";
				else $hora_ini_miercoles = "NULL";
				if ($hora_fin_miercoles != '') $hora_fin_miercoles = "'".$hora_fin_miercoles."'";
				else $hora_fin_miercoles = "NULL";
				if ($hora_ini_jueves != '') $hora_ini_jueves = "'".$hora_ini_jueves."'";
				else $hora_ini_jueves = "NULL";
				if ($hora_fin_jueves != '') $hora_fin_jueves = "'".$hora_fin_jueves."'";
				else $hora_fin_jueves = "NULL";
				if ($hora_ini_viernes != '') $hora_ini_viernes = "'".$hora_ini_viernes."'";
				else $hora_ini_viernes = "NULL";
				if ($hora_fin_viernes != '') $hora_fin_viernes = "'".$hora_fin_viernes."'";
				else $hora_fin_viernes = "NULL";

				$this->excecuteQuery("INSERT INTO `certificado_horario`
							(`id_certificado`, 
							`id_titulo_persona`, 
							`fecha_ini`,
							`id_escalafon_categoria`,
							`id_unidad_ejecutora`,
							`tema`,
							`articulo_lugar`,							
							`lugar`,
							`hora_ini_lunes`,
							`hora_fin_lunes`,
							`hora_ini_martes`,
							`hora_fin_martes`,
							`hora_ini_miercoles`,
							`hora_fin_miercoles`,
							`hora_ini_jueves`,
							`hora_fin_jueves`,
							`hora_ini_viernes`,
							`hora_fin_viernes`
							)  
						VALUES 
							($id_certificado,
							$id_titulo_persona,
							'$fecha_ini',
							$id_escalafon_categoria,
							$id_unidad_ejecutora,
							'$tema',
							'$articulo_lugar',
							'$lugar',
							$hora_ini_lunes,
							$hora_fin_lunes,
							$hora_ini_martes,
							$hora_fin_martes,
							$hora_ini_miercoles,
							$hora_fin_miercoles,
							$hora_ini_jueves,
							$hora_fin_jueves,
							$hora_ini_viernes,
							$hora_fin_viernes
							)");
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}

		}

		function borrar_certificado($id_certificado, &$error){
			// Fecha: 1 feb 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			try {
				$this->excecuteQuery("UPDATE `certificado` 
					SET baja = 1
					WHERE `id_certificado` = $id_certificado");
				return true;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}
			
		}

		function consultar_tipo_certificado($id){
			// Fecha: 11 feb 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			try {
				$r = $this->excecuteQuery("SELECT 
							c.id_tipo_certificado
						FROM 
							certificado c
						WHERE 
							c.id_certificado = $id");
				$row = mysqli_fetch_array($r);
				return $row;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}			
		}


		function consultar_certificado_obra_social($id){
			// Fecha: 11 feb 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			try {
				$r = $this->excecuteQuery("SELECT 
							c.*,
							cos.CUIL,
							cos.fecha_ingreso,
							cos.id_titulo_persona,
							ctp.titulo_persona,
							cos.id_escalafon_categoria,
							CONCAT(e.nombre, case ec.nombre
									when '--' then ''
									else CONCAT(' Categor&iacute;a ', ec.nombre)
								end) as escalafon_categoria_nombre
						FROM 
							certificado c 

							LEFT OUTER JOIN certificado_obra_social cos
								ON c.id_certificado = cos.id_certificado
							LEFT OUTER JOIN certificado_titulo_persona ctp
								ON cos.id_titulo_persona = ctp.id_titulo_persona
							LEFT OUTER JOIN escalafon_categoria ec
								ON cos.id_escalafon_categoria = ec.id_escalafon_categoria
							LEFT OUTER JOIN escalafon e
								ON ec.id_escalafon = e.id_escalafon
						WHERE 
							c.id_certificado = $id");

				$row = mysqli_fetch_array($r);
				return $row;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}			
		}

		//Unificacion Aportes
		function consultar_certificado_unificacion_aportes($id){
			// Fecha: 11 feb 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			try {
				$r = $this->excecuteQuery("SELECT 
							c.*,
							cua.CUIL,
							cua.fecha_ingreso,
							cua.id_titulo_persona,
							ctp.titulo_persona
						FROM 
							certificado c 

							LEFT OUTER JOIN	certificado_unificacion_aportes cua
						    		ON c.id_certificado = cua.id_certificado
							LEFT OUTER JOIN certificado_titulo_persona ctp
								ON cua.id_titulo_persona = ctp.id_titulo_persona
						WHERE 
							c.id_certificado = $id");
				$row = mysqli_fetch_array($r);
				return $row;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}			
		}


		function consultar_certificado_antiguedad($id){
			// Fecha: 23 feb 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			try {
				$r = $this->excecuteQuery("SELECT 
							c.*,
							ca.CUIL,
							ca.fecha_ingreso,
							ca.fecha_egreso,
							ca.id_titulo_persona,
							ctp.titulo_persona,
							ca.id_escalafon_categoria,
							CONCAT(e.nombre, case ec.nombre
									when '--' then ''
									else CONCAT(' Categor&iacute;a ', ec.nombre)
								end) as escalafon_categoria_nombre,
							ca.goce_licencia
						FROM 
							certificado c 

							LEFT OUTER JOIN certificado_antiguedad ca
								ON c.id_certificado = ca.id_certificado
							LEFT OUTER JOIN certificado_titulo_persona ctp
								ON ca.id_titulo_persona = ctp.id_titulo_persona
							LEFT OUTER JOIN escalafon_categoria ec
								ON ca.id_escalafon_categoria = ec.id_escalafon_categoria
							LEFT OUTER JOIN escalafon e
								ON ec.id_escalafon = e.id_escalafon
						WHERE 
							c.id_certificado = $id");

				$row = mysqli_fetch_array($r);
				return $row;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}			
		}


		function consultar_certificado_beca($id){
			// Fecha: 27 feb 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			try {
				$r = $this->excecuteQuery("SELECT 
							c.*,
							cb.id_escalafon_categoria,
							CONCAT(e.nombre, case ec.nombre
									when '--' then ''
									else 
										case ec.id_escalafon
											when 5 then CONCAT(' ', ec.nombre)
											else CONCAT(' Categor&iacute;a ', ec.nombre)
										end
								end) as escalafon_categoria_nombre,
							cb.resolucion,
							cb.fecha_resolucion,
							cb.fecha_ini_beca,
							cb.fecha_fin_beca,
							cb.tema,
							cb.id_titulo_persona,
							ctp.titulo_persona,
							cb.apellido_direccion,
							cb.nombre_direccion,
							cb.articulo_lugar,
							cb.lugar_beca
						FROM 
							certificado c 

							LEFT OUTER JOIN certificado_beca cb
								ON c.id_certificado = cb.id_certificado
							LEFT OUTER JOIN certificado_titulo_persona ctp
								ON cb.id_titulo_persona = ctp.id_titulo_persona
							LEFT OUTER JOIN escalafon_categoria ec
								ON cb.id_escalafon_categoria = ec.id_escalafon_categoria
							LEFT OUTER JOIN escalafon e
								ON ec.id_escalafon = e.id_escalafon
						WHERE 
							c.id_certificado = $id");

				$row = mysqli_fetch_array($r);
				return $row;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}			
		}


		function consultar_certificado_horario($id){
			// Fecha: 13 mar 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			try {
				$r = $this->excecuteQuery("SELECT 
							c.*,
							e.id_escalafon,
							ch.id_escalafon_categoria,
							e.nombre as escalafon_nombre, 
							ec.nombre as escalafon_categoria_nombre,
							ch.fecha_ini,
							ch.tema,
							ch.id_titulo_persona,
							ctp.titulo_persona,
							ch.articulo_lugar,
							ch.lugar,
							ch.id_unidad_ejecutora,
							u.nombre as unidad_nombre,
							u.nombre_completo as unidad_nombre_completo,
							ch.hora_ini_lunes,
							ch.hora_fin_lunes,
							ch.hora_ini_martes,
							ch.hora_fin_martes,
							ch.hora_ini_miercoles,
							ch.hora_fin_miercoles,
							ch.hora_ini_jueves,
							ch.hora_fin_jueves,
							ch.hora_ini_viernes,
							ch.hora_fin_viernes
						FROM 
							certificado c 

							LEFT OUTER JOIN certificado_horario ch
								ON c.id_certificado = ch.id_certificado
							LEFT OUTER JOIN certificado_titulo_persona ctp
								ON ch.id_titulo_persona = ctp.id_titulo_persona
							LEFT OUTER JOIN escalafon_categoria ec
								ON ch.id_escalafon_categoria = ec.id_escalafon_categoria
							LEFT OUTER JOIN escalafon e
								ON ec.id_escalafon = e.id_escalafon
							LEFT OUTER JOIN unidad_ejecutora u
								ON ch.id_unidad_ejecutora = u.id_unidad_ejecutora
						WHERE 
							c.id_certificado = $id");

				$row = mysqli_fetch_array($r);
				return $row;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}			
		}


		function modificar_certificado_unificacion_aportes($id_certificado,  
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, &$error){
			// Fecha: 1 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$this->excecuteQuery("START TRANSACTION");
			try {
				$this->excecuteQuery("UPDATE `certificado`
						SET 
							`fecha_certificado` = '$fecha_certificado', 
							`apellido`='$apellido', 
							`nombre`='$nombre', 
							`DNI`='$DNI'
						WHERE 
							id_certificado = $id_certificado");

				$this->excecuteQuery("UPDATE `certificado_unificacion_aportes`
						SET	`id_titulo_persona` = $id_titulo_persona, 
							`CUIL` = '$CUIL', 
							`fecha_ingreso` = '$fecha_ingreso'
						WHERE 
							id_certificado = $id_certificado");
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}
			
		}


		function modificar_certificado_obra_social($id_certificado,  
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $id_escalafon_categoria, &$error){
			// Fecha: 1 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$this->excecuteQuery("START TRANSACTION");
			try {
				$this->excecuteQuery("UPDATE `certificado`
						SET 
							`fecha_certificado` = '$fecha_certificado', 
							`apellido`='$apellido', 
							`nombre`='$nombre', 
							`DNI`='$DNI'
						WHERE 
							id_certificado = $id_certificado");

				$this->excecuteQuery("UPDATE `certificado_obra_social`
						SET	`id_titulo_persona` = $id_titulo_persona, 
							`CUIL` = '$CUIL', 
							`fecha_ingreso` = '$fecha_ingreso',
							id_escalafon_categoria = $id_escalafon_categoria
						WHERE 
							id_certificado = $id_certificado");

				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}
			
		}



		function modificar_certificado_antiguedad($id_certificado,  
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $id_escalafon_categoria, $fecha_egreso, $goce_licencia, &$error){
			// Fecha: 26 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$this->excecuteQuery("START TRANSACTION");
			try {
				$this->excecuteQuery("UPDATE `certificado`
						SET 
							`fecha_certificado` = '$fecha_certificado', 
							`apellido`='$apellido', 
							`nombre`='$nombre', 
							`DNI`='$DNI'
						WHERE 
							id_certificado = $id_certificado");
				if ($fecha_egreso != '') $fecha_egreso = "'".$fecha_egreso."'";
				else $fecha_egreso = "NULL";

				$this->excecuteQuery("UPDATE `certificado_antiguedad`
						SET	`id_titulo_persona` = $id_titulo_persona, 
							`CUIL` = '$CUIL', 
							`fecha_ingreso` = '$fecha_ingreso',
							id_escalafon_categoria = $id_escalafon_categoria,
							`fecha_egreso` = $fecha_egreso,
							goce_licencia = $goce_licencia
						WHERE 
							id_certificado = $id_certificado");

				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}
			
		}


		function modificar_certificado_beca($id_certificado, 
						$fecha_certificado, $apellido, $nombre, $DNI, $id_escalafon_categoria,
						$resolucion, $fecha_resolucion, $fecha_ini_beca, $fecha_fin_beca, $tema, 
						$id_titulo_persona, $apellido_direccion, $nombre_direccion, 
						$articulo_lugar, $lugar_beca, &$error){
			// Fecha: 27 feb 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$this->excecuteQuery("START TRANSACTION");
			try {
				$this->excecuteQuery("UPDATE `certificado`
						SET 
							`fecha_certificado` = '$fecha_certificado', 
							`apellido`='$apellido', 
							`nombre`='$nombre', 
							`DNI`='$DNI'
						WHERE 
							id_certificado = $id_certificado");
				if ($fecha_fin_beca != '') $fecha_fin_beca = "'".$fecha_fin_beca."'";
				else $fecha_fin_beca = "NULL";

				$this->excecuteQuery("UPDATE `certificado_beca`
						SET	
							`id_escalafon_categoria` = $id_escalafon_categoria,
							`resolucion` = '$resolucion',
							`fecha_resolucion` = '$fecha_resolucion',
							`fecha_ini_beca` = '$fecha_ini_beca',
							`fecha_fin_beca` = $fecha_fin_beca,
							`tema` = '$tema',
							`id_titulo_persona`= $id_titulo_persona,  
							`apellido_direccion` = '$apellido_direccion',
							`nombre_direccion` = '$nombre_direccion',
							`articulo_lugar` = '$articulo_lugar',
							`lugar_beca` = '$lugar_beca'
						WHERE 
							id_certificado = $id_certificado");

				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}
			
		}

		function modificar_certificado_horario($id_certificado,
						$fecha_certificado, $apellido, $nombre, $DNI, $id_escalafon_categoria,
						$fecha_ini, $tema, $id_titulo_persona, $articulo_lugar, $lugar, $id_unidad_ejecutora,
						$hora_ini_lunes, $hora_fin_lunes, $hora_ini_martes, $hora_fin_martes,
						$hora_ini_miercoles, $hora_fin_miercoles, $hora_ini_jueves, $hora_fin_jueves,
						$hora_ini_viernes, $hora_fin_viernes, &$error){
			// Fecha: 19 mar 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$this->excecuteQuery("START TRANSACTION");
			try {
				$this->excecuteQuery("UPDATE `certificado`
						SET 
							`fecha_certificado` = '$fecha_certificado', 
							`apellido`='$apellido', 
							`nombre`='$nombre', 
							`DNI`='$DNI'
						WHERE 
							id_certificado = $id_certificado");

				if ($hora_ini_lunes != '') $hora_ini_lunes = "'".$hora_ini_lunes."'";
				else $hora_ini_lunes = "NULL";
				if ($hora_fin_lunes != '') $hora_fin_lunes = "'".$hora_fin_lunes."'";
				else $hora_fin_lunes = "NULL";
				if ($hora_ini_martes != '') $hora_ini_martes = "'".$hora_ini_martes."'";
				else $hora_ini_martes = "NULL";
				if ($hora_fin_martes != '') $hora_fin_martes = "'".$hora_fin_martes."'";
				else $hora_fin_martes = "NULL";
				if ($hora_ini_miercoles != '') $hora_ini_miercoles = "'".$hora_ini_miercoles."'";
				else $hora_ini_miercoles = "NULL";
				if ($hora_fin_miercoles != '') $hora_fin_miercoles = "'".$hora_fin_miercoles."'";
				else $hora_fin_miercoles = "NULL";
				if ($hora_ini_jueves != '') $hora_ini_jueves = "'".$hora_ini_jueves."'";
				else $hora_ini_jueves = "NULL";
				if ($hora_fin_jueves != '') $hora_fin_jueves = "'".$hora_fin_jueves."'";
				else $hora_fin_jueves = "NULL";
				if ($hora_ini_viernes != '') $hora_ini_viernes = "'".$hora_ini_viernes."'";
				else $hora_ini_viernes = "NULL";
				if ($hora_fin_viernes != '') $hora_fin_viernes = "'".$hora_fin_viernes."'";
				else $hora_fin_viernes = "NULL";


				$this->excecuteQuery("UPDATE `certificado_horario`
						SET	
							`id_titulo_persona`= $id_titulo_persona,  
							`fecha_ini` = '$fecha_ini',
							`id_escalafon_categoria` = $id_escalafon_categoria,
							`id_unidad_ejecutora` = $id_unidad_ejecutora,
							`tema` = '$tema',
							`articulo_lugar` = '$articulo_lugar',
							`lugar` = '$lugar',
							`hora_ini_lunes` = $hora_ini_lunes,
							`hora_fin_lunes` = $hora_fin_lunes,
							`hora_ini_martes` = $hora_ini_martes,
							`hora_fin_martes` = $hora_fin_martes,
							`hora_ini_miercoles` = $hora_ini_miercoles,
							`hora_fin_miercoles` = $hora_fin_miercoles,
							`hora_ini_jueves` = $hora_ini_jueves,
							`hora_fin_jueves` = $hora_fin_jueves,
							`hora_ini_viernes` = $hora_ini_viernes,
							`hora_fin_viernes` = $hora_fin_viernes
						WHERE 
							id_certificado = $id_certificado");

				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}
			
		}

		//id_escalafones son los escalafones a incluir
		//1 CPA, 2 CIC, 3 SINEP, 4 art 9, 5 Beca
		function getEscalafonCategorias($id_escalafones){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$q = "SELECT 
					ec.id_escalafon_categoria,
					CONCAT(e.nombre, case ec.nombre
						when '--' then ''
						else 
							case ec.id_escalafon
								when 5 then CONCAT(' ', ec.nombre)
								else CONCAT(' Categor&iacute;a ', ec.nombre)
							end
					end) as nombre
				FROM 
					escalafon_categoria ec,
					escalafon e
				WHERE 
					ec.id_escalafon = e.id_escalafon and
					e.id_escalafon IN ($id_escalafones)
				ORDER BY
					nombre";
				 	
			$r = $this->excecuteQuery($q);
			return $r;
		}


		//id_escalafones son los escalafones a incluir
		//1 CPA, 2 CIC, 3 SINEP, 4 art 9, 5 Beca
		function getEscalafonCategorias2($id_escalafones){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$q = "SELECT 
					e.id_escalafon,
					ec.id_escalafon_categoria,
					ec.nombre
				FROM 
					escalafon_categoria ec,
					escalafon e
				WHERE 
					ec.id_escalafon = e.id_escalafon and
					e.id_escalafon IN ($id_escalafones)
				ORDER BY
					nombre";
				 	
			$r = $this->excecuteQuery($q);
			return $r;
		}


		//id_escalafones son los escalafones a incluir
		//1 CPA, 2 CIC, 3 SINEP, 4 art 9, 5 Beca
		function getEscalafon($id_escalafones){
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			
			$q = "SELECT 
					e.id_escalafon,
					e.nombre
				FROM 
					escalafon e
				WHERE 
					e.id_escalafon IN ($id_escalafones)";
				 	
			$r = $this->excecuteQuery($q);
			return $r;
		}

//-------------------------------------------------------------------------------------------
	//INICIO METODOS RELACIONADOS CON TRAMITES = SEGUIMIENTO RENDICION ADMINISTRACION
		

		function lista_tramites($vData){
			// Autor: Vanina: no acuerdo con el html en esta instancia pero ya lo mejoraré (en algunas cosas continuo el esquema utilizado previamente)
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$iMaxRows = 15;
			
			// busqueda
			$sHtml = '
			<form name="frmBus" action="lista_tramites.php" method="POST">
			<table class="tabla">
				<tr><th colspan="6">B&uacute;squeda</th></tr>
				<tr class="modo1">
					<td>
						Tramite N&uacute;m./A&ntilde;o
					</td>
					<td>
						<input type="text" name="nroTramiteBus" value="{nroTramiteBus}" style="width:150px"/>
					</td>
					<td>
						Titular
					</td>
					<td>
						<select name="titularBus"><option value="">Todos</option>';
							$arrayTitulares = $this->listar_titulares();
							foreach($arrayTitulares as $row){
								if (array_key_exists('titularBus', $vData) and $row['id_titular'] == $vData['titularBus']){
									$sHtml = $sHtml. '<option selected value="';
								}else{
									$sHtml = $sHtml. '<option value="';
								}
								$sHtml = $sHtml. $row['id_titular'] .'">'. $row['apellido'].', '.$row['nombre'].'</option>';
							}
						$sHtml = $sHtml.'</select>';
					$sHtml = $sHtml.'	
					</td>
					<td>
						Estado
					</td>
					<td align="left">';
					$estadoBusSel1 = "";
					$estadoBusSel2 = "";
					$estadoBusSel3 = "";
					if (array_key_exists('estadoBus', $vData)) {
						switch ($vData['estadoBus']){
							case 1: $estadoBusSel1= " selected ";
								break;
							case 2: $estadoBusSel2= " selected ";
								break;
							case 3: $estadoBusSel3= " selected ";
								break;
						}
					}
					$sHtml = $sHtml.'<select name="estadoBus">
							<option value=>Todos</option>
							<option '.$estadoBusSel1.' value=1>Inicio</option>
							<option '.$estadoBusSel2.' value=2>En curso</option>
							<option '.$estadoBusSel3.' value=3>Finalizado</option>
						</select>
					</td>
		
				</tr>
				<tr class="modo1">
					<td>
						Fecha<br/> (dd-mm-aaaa)
					</td>
					<td>
						<input type="text" name="fechaBus" value="{fechaBus}" style="width:150px"/>
					</td>
					<td>
						Administrador
					</td>
					<td>
						<select name="administradorBus"><option value="">Todos</option>';
							$arrayTitulares = $this->listar_titulares();
							foreach($arrayTitulares as $row){
								if (array_key_exists('administradorBus', $vData) and $row['id_titular'] == $vData['administradorBus']){
									$sHtml = $sHtml. '<option selected value="';
								}else{
									$sHtml = $sHtml. '<option value="';
								}
								$sHtml = $sHtml. $row['id_titular'] .'">'. $row['apellido'].', '.$row['nombre'].'</option>';
							}
						$sHtml = $sHtml.'</select>';
					$sHtml = $sHtml.'	
					</td>		
					<td colspan="2">
						<input type="submit" name="btnBus" value="Buscar"/>
					</td>
					</td>
				</tr>
			</table>
			</form>';
			
			if (array_key_exists('nroTramiteBus', $vData))
				$nroTramiteBus = $vData['nroTramiteBus'];
			else
				$nroTramiteBus = "";

			if (array_key_exists('titularBus', $vData))
				$titularBus = $vData['titularBus'];
			else
				$titularBus = "";

			if (array_key_exists('estadoBus', $vData))
				$estadoBus = $vData['estadoBus'];
			else
				$estadoBus = "";

			if (array_key_exists('fechaBus', $vData))
				$fechaBus = $vData['fechaBus'];
			else
				$fechaBus = "";

			if (array_key_exists('administradorBus', $vData))
				$administradorBus = $vData['administradorBus'];
			else
				$administradorBus = "";			
			
			$sHtml = str_replace("{nroTramiteBus}",$nroTramiteBus,$sHtml);
			$sHtml = str_replace("{titularBus}",$titularBus,$sHtml);
			$sHtml = str_replace("{estadoBus}",$estadoBus,$sHtml);
			$sHtml = str_replace("{fechaBus}",$fechaBus,$sHtml);
			$sHtml = str_replace("{administradorBus}",$administradorBus,$sHtml);

			echo $sHtml;

			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">';
			echo '<tr>';
		    echo '<th>Tr&aacute;mite N&uacute;m./A&ntilde;o</th>';
			echo '<th>Fecha</th>'; 
		    echo '<th>Titular Proyecto</th>'; 
		    echo '<th>Administrador Proyecto</th>';
		    echo '<th>Estado</th>';
		    echo '<th colspan="4">Acciones</th></tr>';			

			$sWhere = '';
			$sHaving = '';

			if ($nroTramiteBus != "") {
				$arrayNroTramite = explode("/",$nroTramiteBus);
				$sWhere .= 'AND t.numero = '.$arrayNroTramite[0];
				if(isset($arrayNroTramite[1])){
					$sWhere.=' AND t.anio = '.$arrayNroTramite[1];
				}
			}
			if ($fechaBus != "") {
				$arrayFechaBus = explode("-",$fechaBus);				
				$sWhere .= ' AND t.fecha_inicio = "'.$arrayFechaBus[2].'-'.$arrayFechaBus[1].'-'.$arrayFechaBus[0].'"';
			} 
			if ($titularBus != "") {				
				$sWhere .= ' AND t.id_titular_proyecto = '.$titularBus.' ';
			}
			if ($administradorBus != "") {				
				$sWhere .= ' AND t.id_titular_adm_proyecto = '.$administradorBus.' ';
			}

			if ($estadoBus != "") {				
				$sHaving .= " AND id_estado = ".$estadoBus;
			}
			

			$iPagActual = (isset($_GET['pag']))?$_GET['pag']:1;
			$iLimit = ($iPagActual -1) * $iMaxRows;
			
			//estados (solo se muestran en pantalla de acuerdo al ultimo movimiento)
			//Inicio (ultimo movimiento en 1)
			//En curso (ultimo movimiento en 2 o 3)
			//Finalizado (ultimo movimiento en 4)
			$q = 'SELECT SQL_CALC_FOUND_ROWS 
					t.id_tramite,
					t.anio, 
					t.numero, 
					t.fecha_inicio, 
					t.rendicion,
					t.id_titular_proyecto,
					CONCAT(t1.apellido,", ", t1.nombre) as titular_proyecto,
					t.id_titular_adm_proyecto,
					CONCAT(t2.apellido,", ", t2.nombre) as titular_adm_proyecto,
					case (SELECT tm2.id_tramite_movimiento_tipo
					      FROM tramite_movimiento tm2
					      WHERE tm2.id_tramite_movimiento = 
					      	(SELECT max(tm1.id_tramite_movimiento)
					     	FROM tramite_movimiento tm1
					     	WHERE tm1.id_tramite = t.id_tramite)) 
						when 1 then \'Inicio\'
						when 2 then \'En curso\'
						when 3 then \'En curso\'
						when 4 then \'Finalizado\'
					end as estado,
					case (SELECT tm2.id_tramite_movimiento_tipo
					      FROM tramite_movimiento tm2
					      WHERE tm2.id_tramite_movimiento = 
					      	(SELECT max(tm1.id_tramite_movimiento)
					     	FROM tramite_movimiento tm1
					     	WHERE tm1.id_tramite = t.id_tramite)) 
						when 1 then 1
						when 2 then 2
						when 3 then 2
						when 4 then 3
					end as id_estado
				FROM 
					tramite t 
					    LEFT OUTER JOIN
					titular t1 ON
						t.id_titular_proyecto = t1.id_titular
					LEFT OUTER JOIN
					titular t2 ON
						t.id_titular_adm_proyecto = t2.id_titular
				WHERE   1 = 1 '.
					$sWhere.' 
				HAVING   1 = 1 '.
					$sHaving.' 
				ORDER BY 
					id_tramite DESC 
				LIMIT '.
					$iLimit.','.$iMaxRows;
			$r = $this->excecuteQuery($q);

			$p = $this->excecuteQuery("SELECT CEIL(FOUND_ROWS()/$iMaxRows) as total");
			$results = mysqli_fetch_assoc($p);
			$iTotalPag = $results['total'];
			
			if($r){
				while ( $row = mysqli_fetch_array($r) ){
					$lnkmodificar = 'form_tramite.php?id_tramite=' . $row['id_tramite'] . '&opcion=3';
					$lnkborrar = 'form_tramite.php?id_tramite=' . $row['id_tramite'] . '&opcion=2';
					$lnkmovimientos = 'lista_tramites_movimientos.php?id_tramite='.$row['id_tramite'];
					$lnkvisualizar = 'form_tramite.php?id_tramite=' . $row['id_tramite'] . '&opcion=4';			
				
					echo '<tr class="modo1">';
					echo '<td>' . $row['numero'] .'/'. $row['anio'].'</td>';
					echo '<td>' . convertir_fecha($row['fecha_inicio']) . '</td>';
					echo '<td>' . $row['titular_proyecto'] . '</td>';				
					echo '<td>' . $row['titular_adm_proyecto']. '</td>';
					echo '<td>' . $row['estado']. '</td>';
					
					if($this->checkPerm($_SESSION["id_usuario"],33,'baja')  and $row['id_estado'] == 1){				
						echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar .  '><img src="eliminar.png" width="30" height="30" border="0" title="Borrar Registro"></a></td>';					
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" title="No permitido: Borrar Registro (reclamo ya enviado por email)"></a></td>';				
					if($this->checkPerm($_SESSION["id_usuario"],33,'modificacion') and $row['id_estado'] != 3){	
						echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar .  '><img src="actualizar_datos.png" width="30" height="30" border="0" title="Modificar Registro"></a></td>';
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" title="No permitido: Modificar Registro (reclamo finalizado o falta de permiso)"></a></td>';
					if($this->checkPerm($_SESSION["id_usuario"],33,'consulta')){
						echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar .  '><img src="previsualizar.png" width="30" height="30" border="0" title="Ver Registro"></a></td>';
					}else
						echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/previsualizarg.png" width="30" height="30" border="0" title="Ver Registro"></a></td>';

					echo '<td align="center"><font color="#333333"><a href=' . $lnkmovimientos .  '><img src="iconos/movimientos.png" width="25" height="25" border="0" title="Movimientos"></a></td>';
				}
			}
			$iNextPage = ($iPagActual<$iTotalPag)?($iPagActual+1):$iPagActual;
			$iPrevPage = ($iPagActual > 1)?($iPagActual-1):$iPagActual;
			
			echo "<tr>
					<td colspan='10'>
						<a href='lista_tramites.php?pag=$iPrevPage&nroTramiteBus=$nroTramiteBus&fechaBus=$fechaBus&titularBus=$titularBus&administradorBus=$administradorBus&estadoBus=$estadoBus' >Prev </a> 
						$iPagActual / $iTotalPag
						<a href='lista_tramites.php?pag=$iNextPage&nroTramiteBus=$nroTramiteBus&fechaBus=$fechaBus&titularBus=$titularBus&administradorBus=$administradorBus&estadoBus=$estadoBus' value='sig'>Sig</a>
					</td>
				</tr>";
			echo '</table>';
		}
		

		function getTiposReclamos(){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$q = "SELECT 
					* 
				FROM 
					tramite_reclamo_tipo
				ORDER BY 
				 	id_tramite_reclamo_tipo";
				 	
			$r = $this->excecuteQuery($q);
			return $r;
		}


		function getTramiteComprobantes($id_tramite, $soloNoPresentados = false){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$where = "";
			if ($soloNoPresentados) {
				$where = " and (select count(*) 
						from tramite_reclamo tr 
						where tr.id_tramite_comprobante = tc.id_tramite_comprobante and
							presentado = 0) ";
			}		
	
			$q = "SELECT 
					tc.*,
					m.signo as moneda 
				FROM 
					tramite_comprobante tc,
					moneda m
				WHERE
					tc.id_tramite = $id_tramite and
					tc.id_moneda = m.id_moneda
					$where
				ORDER BY 
				 	tc.id_tramite_comprobante";
				 	
			$r = $this->excecuteQuery($q);
			return $r;
		}


		function agregar_tramite ($anio, $fecha_inicio, $rendicion, $id_titular_proyecto,
					$id_titular_adm_proyecto, $motivo_tramite, $rendicion_codigo, $observaciones,
					$comprobantes, $id_usuario, &$id_tramite, &$error) {
			// Fecha: 24 jun 2019
			// Autor: Vanina
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$bd = new Bd;
			$con = $bd->AbrirBd();
			try {
				$this->excecuteQuery("START TRANSACTION");
				$sql = "INSERT INTO `tramite`
				(`anio`, 
				`numero`, 					
				`fecha_inicio`, 
				`rendicion`, 
				`id_titular_proyecto`, 
				`id_titular_adm_proyecto`,
				`motivo_tramite`, 
				`rendicion_codigo`,
				`observaciones`) 
			SELECT
				$anio,
				IFNULL(MAX(numero), 0)+1,
				'$fecha_inicio',
				'$rendicion', 
				$id_titular_proyecto, 
				$id_titular_adm_proyecto,
				$motivo_tramite,
				'$rendicion_codigo',
				'$observaciones'
			FROM
				tramite
			WHERE 
				anio = $anio";
			
			error_log($sql);
			$this->excecuteQuery($sql);
		
			 $id_tramite = $this->lastId(); 

				//1 tramite tiene muchos comprobantes
				foreach($comprobantes as $comprobante) {
					$fechaComprobante = $comprobante['fechacomprobante'];
					if (is_Null($fechaComprobante) or $fechaComprobante == "")
						{$fechaComprobante = "NULL";}
					else
						{$fechaComprobante = "'".convertir_fecha_sql($fechaComprobante)."'";}
					$sql = "INSERT INTO `tramite_comprobante`
					(`id_tramite`, 
					`comprobante`,
					`id_moneda`,
					`monto`,
					`fecha`,
					`proveedor`,
					`proveedor_id`
					)  
				VALUES 
					($id_tramite,
					'".$comprobante['comprobante']."',".
					$comprobante['monedacomprobante'].",".
					$comprobante['montocomprobante'].",".
					$fechaComprobante.",".
					"'".$comprobante['proveedorcomprobante']."',".
					$comprobante['proveedorid'].")";
					
					error_log($sql);
					$this->excecuteQuery($sql);

					$id_tramite_comprobante = $this->lastId(); 
					//y 1 comprobante tiene muchos reclamos
					foreach($comprobante['reclamos'] as $reclamo) {
						$descripcion = 'NULL';
						if ($reclamo == 9) {
							//Inserta con destino
							$descripcion = "'".$comprobante['destino']."'";
						} else if ($reclamo == 11) {
							//Inserta con monto
							$descripcion = "'".$comprobante['monto']."'";
						} else if ($reclamo == 12) {
							//Inserta con motivo
							$descripcion = "'".$comprobante['motivo']."'";
						} 
						$sql = "INSERT INTO `tramite_reclamo`
						(`id_tramite_comprobante`, 
						`id_tramite_reclamo_tipo`,
						`descripcion`
						)  
					VALUES 
						($id_tramite_comprobante,
						$reclamo,
						$descripcion
						)";
						$this->excecuteQuery($sql);
					}
				}
				//Primer movimiento
				$sql = "INSERT INTO `tramite_movimiento`
				(`id_tramite`, 
				`id_tramite_movimiento_tipo`, 					
				`id_usuario`) 
			VALUES
				($id_tramite,
				1,
				$id_usuario)";
				error_log($sql);
				$this->excecuteQuery($sql);
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}

		}


		function consultar_tramite($id){
			// Fecha: 26 jun 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			try {
				$r = $this->excecuteQuery("SELECT
							t.id_tramite,
							t.anio, 
							t.numero, 
							t.fecha_inicio, 
							t.rendicion,
							t.id_titular_proyecto,
							t.id_titular_adm_proyecto,
							t.motivo_tramite,
							t.rendicion_codigo,
							t.observaciones,
							case (SELECT tm2.id_tramite_movimiento_tipo
							      FROM tramite_movimiento tm2
							      WHERE tm2.id_tramite_movimiento = 
							      	(SELECT max(tm1.id_tramite_movimiento)
							     	FROM tramite_movimiento tm1
							     	WHERE tm1.id_tramite = t.id_tramite)) 
								when 1 then 'Inicio'
								when 2 then 'En curso'
								when 3 then 'En curso'
								when 4 then 'Finalizado'
							end as estado,
							case (SELECT tm2.id_tramite_movimiento_tipo
							      FROM tramite_movimiento tm2
							      WHERE tm2.id_tramite_movimiento = 
							      	(SELECT max(tm1.id_tramite_movimiento)
							     	FROM tramite_movimiento tm1
							     	WHERE tm1.id_tramite = t.id_tramite)) 
								when 1 then 1
								when 2 then 2
								when 3 then 2
								when 4 then 3
							end as id_estado,
							(select count(*) 
							 from tramite_comprobante tc 
							 where tc.id_tramite = t.id_tramite) as cant_comprobantes
						FROM 
							tramite t
						WHERE 
							t.id_tramite = $id");
				$row = mysqli_fetch_array($r);
				return $row;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}			
		}


		function modificar_tramite($id_tramite, $rendicion, $id_titular_proyecto,
					$id_titular_adm_proyecto, $rendicion_codigo, $observaciones, $comprobantes, $id_usuario, &$error) {
			// Fecha: 28 jun 2019
			// Autor: Vanina
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$bd = new Bd;
			$con = $bd->AbrirBd();
			try {
				$this->excecuteQuery("START TRANSACTION");
				$this->excecuteQuery("UPDATE `tramite`
					     SET
							`rendicion`= '$rendicion', 
							`id_titular_proyecto` = $id_titular_proyecto, 
							`id_titular_adm_proyecto` = $id_titular_adm_proyecto,
							`rendicion_codigo`= '$rendicion_codigo',
							`observaciones`= '$observaciones' 
						WHERE 
							id_tramite = $id_tramite");

				//1 tramite tiene muchos comprobantes
				//borro todo e inserto de nuevo

				$this->excecuteQuery("DELETE FROM tramite_reclamo WHERE id_tramite_comprobante IN (select id_tramite_comprobante from tramite_comprobante where id_tramite = $id_tramite)");
				$this->excecuteQuery("DELETE FROM tramite_comprobante WHERE id_tramite = $id_tramite");

		
				foreach($comprobantes as $comprobante) {
					$fechaComprobante = $comprobante['fechacomprobante'];

					if (is_Null($fechaComprobante) or $fechaComprobante == "")
						{$fechaComprobante = "NULL";}
					else
						{$fechaComprobante = "'".convertir_fecha_sql($fechaComprobante)."'";}

					$this->excecuteQuery("INSERT INTO `tramite_comprobante`
								(`id_tramite`, 
								`comprobante`,
								`id_moneda`,
								`monto`,
								`fecha`,
								`proveedor`,
								`proveedor_id`
								)  
							VALUES 
								($id_tramite,
								'".$comprobante['comprobante']."',".
								$comprobante['monedacomprobante'].",".
								$comprobante['montocomprobante'].",".
								$fechaComprobante.",".
								"'".$comprobante['proveedorcomprobante']."',".
								$comprobante['proveedorid'].")");

					$id_tramite_comprobante = $this->lastId(); 
					//y 1 comprobante tiene muchos reclamos
					foreach($comprobante['reclamos'] as $reclamo) {
						$descripcion = 'NULL';
						if ($reclamo == 9) {
							//Inserta con destino
							$descripcion = "'".$comprobante['destino']."'";
						} else if ($reclamo == 11) {
							//Inserta con monto
							$descripcion = "'".$comprobante['monto']."'";
						} else if ($reclamo == 12) {
							//Inserta con motivo
							$descripcion = "'".$comprobante['motivo']."'";
						} 
						$this->excecuteQuery("INSERT INTO `tramite_reclamo`
									(`id_tramite_comprobante`, 
									`id_tramite_reclamo_tipo`,
									`descripcion`
									)  
								VALUES 
									($id_tramite_comprobante,
									$reclamo,
									$descripcion
									)");
					}
				}
				//Movimiento: esta modificacion de datos antes del envio no genera movimientos
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
			    	//$con->rollback();
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}

		}

		function getTramiteComprobante($id_tramite_comprobante){			
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$q = "SELECT 
					* 
				FROM 
					tramite_comprobante
				WHERE
					id_tramite_comprobante = $id_tramite_comprobante";
				
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;
		}


		//Modificacion del tramite luego de que ya fue enviado un email
		function modificar_tramite2($id_tramite, $comprobantes, $firma_realizada, $id_usuario, &$error) {
			// Fecha: 30 jun 2019
			// Autor: Vanina
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			
			$rowTramite = $this->consultar_tramite($id_tramite);
			try {
				$this->excecuteQuery("START TRANSACTION");
				if ($rowTramite["motivo_tramite"]==1) {
					$detalle = "Documentación presentada:<br> ";
					$cantPresentados = 0;
					foreach($comprobantes as $comprobante) {
						//El primer elemento viene vacio
						if (count($comprobante)>0) {
							$id_tramite_comprobante = $comprobante['id_tramite_comprobante']; 
							$rowComprobante = $this->getTramiteComprobante($id_tramite_comprobante);
							$detalle = $detalle." <u>Comprobante ".$rowComprobante['comprobante'].":</u><br>";
							//y 1 comprobante tiene muchos reclamos
							foreach($comprobante['reclamos'] as $reclamo) {
								$this->excecuteQuery("UPDATE `tramite_reclamo`
									     SET presentado = 1
									     WHERE 
										`id_tramite_comprobante` = $id_tramite_comprobante and
										`id_tramite_reclamo_tipo` =". $reclamo);
								//texto de cuales papeles fueron presentados
								$rowReclamo = $this->getTramiteReclamo($id_tramite_comprobante,$reclamo);
								$detalle = $detalle."&#8226; ".$rowReclamo['reclamo_nombre']. " ".$rowReclamo['descripcion']." <br>";
								$cantPresentados = $cantPresentados + 1;
							}
						}
					}
					//Movimiento: modificacion de datos luego del envio genera movimientos
					//Solo si presento algun reclamo
					if ($cantPresentados > 0) {
						$this->excecuteQuery("INSERT INTO `tramite_movimiento`
									(`id_tramite`, 
									`id_tramite_movimiento_tipo`, 					
									`id_usuario`,
									`detalle`) 
								VALUES
									($id_tramite,
									3,
									$id_usuario,
									'$detalle')");
						}
					//Si se marcaron todos los comprobantes como entregados, pasa a estado finalizado e inserta movimiento finalizado
					$rowsTramiteComprobantes = $this->getTramiteComprobantes ($id_tramite, true);
					if (mysqli_num_rows($rowsTramiteComprobantes) == 0) {
						$this->excecuteQuery("INSERT INTO `tramite_movimiento`
									(`id_tramite`, 
									`id_tramite_movimiento_tipo`, 					
									`id_usuario`) 
								VALUES
									($id_tramite,
									4,
									$id_usuario)");
					}
				} else {
					//motivo es 2
					if ($firma_realizada == "1") {
						$this->excecuteQuery("INSERT INTO `tramite_movimiento`
									(`id_tramite`, 
									`id_tramite_movimiento_tipo`, 					
									`id_usuario`,
									`detalle`) 
								VALUES
									($id_tramite,
									3,
									$id_usuario,
									'Se presentó el titular a firmar el cierre de rendición')");
						$this->excecuteQuery("INSERT INTO `tramite_movimiento`
									(`id_tramite`, 
									`id_tramite_movimiento_tipo`, 					
									`id_usuario`) 
								VALUES
									($id_tramite,
									4,
									$id_usuario)");	
					}
				}
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
				$this->excecuteQuery("ROLLBACK");
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}

		}


		//Obtiene la lista de reclamos dado un comprobante de un tramite
		function getTramiteReclamos($id_tramite_comprobante, $soloNoPresentados = false){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022
			$where = "";
			if ($soloNoPresentados) {
				$where = " and presentado = 0 ";
			}
			$q = "SELECT 
					tr.*,
					trt.nombre as reclamo_nombre
				FROM 
					tramite_reclamo tr,
					tramite_reclamo_tipo trt
				WHERE
					tr.id_tramite_reclamo_tipo = trt.id_tramite_reclamo_tipo and
					id_tramite_comprobante = $id_tramite_comprobante 
					$where
				ORDER BY 
				 	tr.id_tramite_reclamo_tipo";
				 	
			$r = $this->excecuteQuery($q);
			return $r;
		}

		//Obtiene la descripcion del reclamos dado un reclamo de un comprobante de un tramite
		function getTramiteReclamo($id_tramite_comprobante, $id_tramite_reclamo_tipo){
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$q = "SELECT 
					tr.*,
					trt.nombre as reclamo_nombre
				FROM 
					tramite_reclamo tr,
					tramite_reclamo_tipo trt
				WHERE
					tr.id_tramite_reclamo_tipo = trt.id_tramite_reclamo_tipo and
					id_tramite_comprobante = $id_tramite_comprobante and
					tr.id_tramite_reclamo_tipo = $id_tramite_reclamo_tipo";
				 	
			$r = $this->excecuteQuery($q);
			$row = mysqli_fetch_array($r);
			return $row;
		}
	
		//Envia email de aviso a los administradores y con copia al usuario
		//Si es el primer envio, y es exitoso, cambia el estado del tramite para que no pueda ser modificado
		//Genera movimiento de tipo 2
		function enviar_email_tramite($id_tramite, $id_usuario, &$error2){
			
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			$rowTramite = $this->consultar_tramite($id_tramite);
			$rowTitularProyecto = $this->consultar_titular($rowTramite['id_titular_proyecto']);
			if (!is_null($rowTramite['id_titular_adm_proyecto']))
				$rowTitularAdmProyecto = $this->consultar_titular($rowTramite['id_titular_adm_proyecto']);
			$rowUsuario = $this->consultar_usuario($id_usuario);

			if ($rowTramite['motivo_tramite'] == 1) {
				$subject = utf8_decode("CONICET MAR DEL PLATA - Documentación pendiente de presentación"); 
			} else {
				$subject = utf8_decode("CONICET MAR DEL PLATA - Solicitud de firma para cierre de rendición"); 
			}
			
			$cc = "";
			$to = "";
			if (!is_null($rowTramite['id_titular_adm_proyecto'])) 
				if (!($rowTramite['id_titular_proyecto'] == $rowTramite['id_titular_adm_proyecto'])) {
					//Si son diferentes, el administrador recibe el correo
					$to = $rowTitularAdmProyecto['email'].","; 
				}

			$cc = $rowUsuario['email'].",mibello@conicet.gov.ar"; 
			$cco = ""; 
			$to = $to.$rowTitularProyecto['email'];
			$textoTemporalEmails = "";

			separaFecha(date('Y-m-d'),$anio, $mes, $dia, $nombreMes);

			
			$message = 
			"<html>
			<head>
			<title>Documentaci&oacute;n pendiente de presentaci&oacute;n</title>
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
					<td colspan=2 style=\"text-align: center;font-weight: bold;font-size: 16px;\">
						<br>
						<br>
						[Este es un email autom&aacute;tico del sistema. Por favor no responder a este remitente.]
					</td>
				</tr>
				<tr>
					<td colspan=2 style=\"text-align: center;\">
						<br>
						<br>
						$textoTemporalEmails
					</td>
				</tr>		
				<tr>
					<td style=\"padding-left:0px;\" colspan=2>
						<img src='conicet170px.jpg' />
					</td>
				</tr>
				<tr>
					<td style=\"text-align: right;margin-right: 1em;\" colspan=2>
						Mar del Plata, $dia de $nombreMes de $anio
						<br>
						<br>
					</td>
				</tr>";
				if ($rowTramite['motivo_tramite'] == 1) {
					$message = $message."<tr>
						<td colspan=2>
						Estimado/a ".htmlentities($rowTitularProyecto['apellido']).", ".htmlentities($rowTitularProyecto['nombre'])."<br>
						Por medio del presente, se informa que se encuentra pendiente de presentaci&oacute;n la siguiente documentaci&oacute;n relacionada a:
						<br>
						<br>
						</td>
					</tr>
					<tr>
						<td colspan=2>";
					//Comprobantes que tienen al menos un reclamo sin presentar
					$rowsComprobantes = $this->getTramiteComprobantes($id_tramite, true);
					while ($rc = mysqli_fetch_assoc($rowsComprobantes)){
						$soloNoPresentados = true;
						$prov = $this->consultar_proveedor_por_id($rc['proveedor_id']);
						$rowsReclamos = $this->getTramiteReclamos($rc['id_tramite_comprobante'],$soloNoPresentados);
						//Si no tiene reclamos pendientes de presentacion, no debe figurar en el email
						if (mysqli_num_rows($rowsReclamos) > 0) {
							$masTextoComprobante = "&nbsp;&nbsp;&nbsp;&nbsp;<b>Monto:</b> ".$rc['moneda']." ".$rc['monto'];
							if (!is_null($rc['fecha']) || !is_null($rc['proveedor']))
								$masTextoComprobante = $masTextoComprobante."<br>";
							if (!is_null($rc['fecha']))
								$masTextoComprobante = $masTextoComprobante. "<b>Fecha:</b> ".convertir_fecha($rc['fecha']);
							if (!is_null($rc['proveedor']) and $rc['proveedor'] != "")
								$masTextoComprobante = $masTextoComprobante. "&nbsp;&nbsp;&nbsp;&nbsp;<b>Proveedor:</b> ".htmlentities($rc['proveedor']);
							else 
								$masTextoComprobante = $masTextoComprobante. "&nbsp;&nbsp;&nbsp;&nbsp;<b>Proveedor:</b> ".htmlentities($prov['razon_social']);

							$message = $message."<table style=\"font-family:'Terminal Dosis', Arial, sans-serif;border:2px rgb(89, 146, 196) solid;\">
								<tr>
									<td colspan=2 style=\"background-color:rgb(89, 146, 196);color:white;text-align:center;font-size:17px;padding: 2px 10px;\">
										<b>Comprobante:</b> ".htmlentities($rc['comprobante']).$masTextoComprobante."
									</td>
								</tr>
								<tr>
									<td colspan=2>
										<ul>";
							while ($rr = mysqli_fetch_assoc($rowsReclamos)){
								$reclamo = $rr['reclamo_nombre'].htmlentities(" ".$rr['descripcion']);
								$message = $message."<li type=\"circle\">".$reclamo."</li>";
							}
							$message = $message."	</ul>
									</td>
								</tr>
							</table><br>";
							} 
						}
				} else {
					//motivo es firma de cierre de rendicion
					$message = $message."<tr>
						<td colspan=2>
						Estimado/a ".htmlentities($rowTitularProyecto['apellido']).", ".htmlentities($rowTitularProyecto['nombre'])."<br>
						Por medio del presente, se informa que hemos procedido al cierre de la rendici&oacute;n ".$rowTramite['rendicion_codigo'].". <br>
						Por tal motivo, lo convocamos a firmar la misma, a las oficinas de la UAT del CCT CONICET Mar del Plata, sito en calle Moreno 3527 piso 3, los d&iacute;as martes y jueves de 9 a 13hs.
						<br>
						<br>
						</td>
					</tr>
					<tr>
						<td colspan=2>";				
				}

				$firmaUsuario = "";
				if (!(is_null($rowUsuario["titulo"])) and ($rowUsuario["titulo"] != "")) 
					$firmaUsuario = $rowUsuario['titulo']." ";
				$firmaUsuario = htmlentities($firmaUsuario.$rowUsuario['nombre']." ".$rowUsuario['apellido']);
				$texto_solo_motivo_1 = "";
				if ($rowTramite['motivo_tramite'] == 1) $texto_solo_motivo_1 = "Se solicita su presentaci&oacute;n a la brevedad.";
				$message=$message."</td>
				</tr>
				<!--<tr>
					<td  colspan=2 style=\"background-color:rgb(89, 146, 196);\" >
					</td>
				</tr>-->
				
				<tr>
					<td colspan=2 style=\"text-align: left;margin-right: 1em;\">
						<br>
						$texto_solo_motivo_1<br>
						Cordialmente,<br>
						<br>
						<br>
					</td>
				</tr>
				<tr>
					<td colspan=2 style=\"text-align: right;margin-right: 1em;\">".
						$firmaUsuario."<br>".
						$rowUsuario['email']."<br>".
						"CCT Conicet Mar delPlata
						<br>
						<br>
					</td>			
				</tr>
			</table>
			</body>
			</html>";

			if (send_email ($to,$cc,$cco,$subject,$message,dirname( __FILE__ )."/images/conicet170px.jpg","")){
				//correo enviado
				$this->excecuteQuery("INSERT INTO `tramite_movimiento`
							(`id_tramite`, 
							`id_tramite_movimiento_tipo`, 					
							`id_usuario`) 
						VALUES
							($id_tramite,
							2,
							$id_usuario)");
				return true;
			}
			else {
				//correo no enviado
				return false;
			}			
		}


		function lista_tramites_movimientos($id_tramite, $iMaxRows, $iLimit){

			//estados (solo se muestran en pantalla de acuerdo al ultimo movimiento)
			//Inicio (ultimo movimiento en 1)
			//En curso (ultimo movimiento en 2 o 3)
			//Finalizado (ultimo movimiento en 4)

			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 23/09/2022

			$q = "SELECT SQL_CALC_FOUND_ROWS 
					tm.id_tramite_movimiento_tipo,
					case (tm.id_tramite_movimiento_tipo)
						when 1 then 'Apertura'
						when 2 then 'Envio de email de reclamo'
						when 3 then 'Presentacion de comprobantes faltantes'
						when 4 then 'Finalizacion'
					end as tramite_movimiento_tipo_nombre,
					DATE_FORMAT(tm.fecha,'%d/%m/%Y %H:%i') as fecha, 
					tm.id_usuario,
					u.nombre,
					u.apellido,
					tm.detalle
				FROM 
					tramite_movimiento tm 
					    LEFT OUTER JOIN
					usuario u ON
						tm.id_usuario = u.id_usuario
				WHERE
					tm.id_tramite = $id_tramite
				ORDER BY
					tm.id_tramite_movimiento DESC 
				LIMIT ".
					$iLimit.','.$iMaxRows;
			$r = $this->excecuteQuery($q);

			return $r;

		}

		function borrar_tramite($id_tramite, &$error){
			// Fecha: 15 jul 2019
			// Autor: Vanina	
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 19/10/2022
			
			try {
				$this->excecuteQuery("START TRANSACTION");
				$this->excecuteQuery("DELETE FROM `tramite_movimiento` 
					WHERE `id_tramite` = $id_tramite");
				$this->excecuteQuery("DELETE FROM `tramite_reclamo` 
					WHERE `id_tramite_comprobante` in (select c.id_tramite_comprobante 
									   from tramite_comprobante c
									   where c.id_tramite = $id_tramite)");
				$this->excecuteQuery("DELETE FROM `tramite_comprobante` 
					WHERE `id_tramite` = $id_tramite");
				$this->excecuteQuery("DELETE FROM `tramite` 
					WHERE `id_tramite` = $id_tramite");
				$this->excecuteQuery("COMMIT");
				return true;
			} catch (Exception $e) {
			    	$error = 'Algo fallo: '. $e->getMessage(). "\n";
				return false;
			}
			
		}

    //************************************** MËTODOS DE ACCESO A BD *********************************************************************/
    // SOLO MÉTODOS RELACIONADOS CON EL ACCESO A LA BD - refactor

    //Nota Vani: no se utiliza el parametro de nombre de la base de datos a abrir
    function AbrirBd(){
      //Actualizado a PHP > 7 
      //Victoria Ganuza
      //Fecha: 20/09/2022

      switch($_SERVER['SERVER_NAME']){
          case "localhost":
          case "servidor":
          case "192.168.0.6":
              $uName = "root";
              $uPass = "";
              $uBD   = "cctmar_cct";
          break;
          default:
              $uName = "conicet";
              $uPass = ")OD4]N_Of_,q";
              $uBD   = "conicet_cctmar_cct";
          break;	
      }
      $this->conn = mysqli_connect('localhost', $uName,$uPass); // CONEXION ONLINE
      $select_bd = mysqli_select_db($this->conn, $uBD);
    }

    function excecuteQuery($q) {
			/*
			* Fecha: Noviembre 2020
			* Autor: Victoria
			* Maneja lo básico de realizar una consulta a la BD
			*/
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 20/09/2022

			$r = mysqli_query($this->conn,$q);
			if (mysqli_errno($this->conn)) {
				$error = mysqli_errno($this->conn) . ": " . mysqli_error($this->conn) . "\n";
				error_log($error);
				throw new Exception($error);
			}
			if ($r) {
				return $r;
			} else {
				return false;
			}
		}

		function realEscapeString($string){
			//Actualizado a PHP > 7 
			//Victoria Ganuza
			//Fecha: 20/09/2022
			return mysqli_real_escape_string($this->conn,$string);
		}

		function lastId(){
			return mysqli_insert_id($this->conn);
		}
	} // FIN CLASS BD
?>
