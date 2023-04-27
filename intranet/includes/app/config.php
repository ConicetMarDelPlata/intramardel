<?php
	if (!isset($_SESSION)){
		session_start();
	}
	
	// Mapeo de keys de $_SESSION
	define('autentificado', 'autentificado');
	define('usuario', 'usuario');
	define('contrasenia','contrasenia');
	define('id_usuario','id_usuario');

	if(is_file("includes/class.Tpl.php")){
		$sBACK = "";
	} else{
		if(is_file("../includes/class.Tpl.php")){
			$sBACK = "../";
		} else {
			$sBACK = "../../../";//Para cuando llamo desde app/carpeta/
		}
   	}
  
  	$usuraio_id = $_SESSION[id_usuario];

  

	include_once($sBACK."includes/class.Tpl.php");
	include_once($sBACK."includes/class.User.php");
	include_once($sBACK."includes/class.Email.php");
	include_once($sBACK."includes/class.Conference.php");
	include_once($sBACK."includes/class.Utils.php");
    include_once($sBACK."seguridad_bd.php");
    
	$bd = new Bd;
	$bd->AbrirBd();

	$oUser		= new User($bd);
	$conference	= new Conference($bd);
	$oUtils		= new Utils();
	$oEmail		= new Email();

?>