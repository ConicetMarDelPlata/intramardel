<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");	
		exit();
	}

	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_OP');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],4,''); //4-ordenes de pago
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}	

	//Fecha aviso de pago default: viernes siguiente proximo.
	$fecha_aviso_pago_default = date("d-m-Y",strtotime("next Friday"));
	//Pero si hoy es viernes y es antes de las 16 hs, dejar la fecha de hoy
	if ((date("w") == 5) and (date("H") < "21")) {
		$fecha_aviso_pago_default = date("d-m-Y");
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>PANEL CONTROL</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="funciones.js"></script>

<link href="calendario/css/calendario.css" type="text/css" rel="stylesheet">
<script src="calendario/js/calendar.js" type="text/javascript"></script>
<script src="calendario/js/calendar-es.js" type="text/javascript"></script>
<script src="calendario/js/calendar-setup.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript" src="js/misc.js"></script>
<script language="javascript" type="text/javascript" src="js/validaciones.js"></script>

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
.title {
	margin: 10px;
}
.row{
	display: flex;
}
.row .input-content label{
	font-size:12px;
	margin-right:10px;
}
.row .input-content {
	margin-bottom:15px;
	margin-right: 10px;
	margin-left: 5px;
	max-width: 150px;
}
.row .input-content:last-child {
	margin-right: 0px;
}
.row .input-content input {
	max-width: 150px;
}
.row .input-content.small {
	max-width: 80px;
}
.row .input-content.small input {
	max-width: 80px;
}
.row .input-content.medium {
	max-width: 100px;
}
.row .input-content.medium input{
	max-width: 100px;
}
</style>
<link href="tabla.css" rel="stylesheet" type="text/css" />

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
			<a href="panel_control_modulos.php" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">Volver</a>
		</td>
	</tr>
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
   <?php
	   	$permAlta = $bd->checkPerm($_SESSION["id_usuario"],4,'alta');
		if($permAlta) {
			echo '<td align="left" valign="middle"><span class="TITULO">:: &Oacute;rdenes de Pago ::</span><a href="form_orden_pago.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a>';
		}else
			echo '<td align="left" valign="middle"><span class="TITULO">:: &Oacute;rdenes de Pago ::</span><a href="#"><img src="iconos_grises/agregarg.png" width="25" height="25" border="0"></a>';

		$botonLogEnvioAvisosPago = "<a href=envioAvisosPago.log target=\"_blank\"><img src=\"images\docu.png\" width=\"25\" height=\"25\" border=\"0\" alt=\"Log env&iacute;os aviso de pago\"></a>";
		echo $botonLogEnvioAvisosPago."</td>";

	?>
	</tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> 
      <?php
	$bd->lista_ordenes_pago($nombre_usuario,$_REQUEST);
	$bd = NULL;
?>
    </td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<div id="lightbox">
    <div id="content">
	<div id="box">
	<h3>Confirma el pago de la O.P.: <label id="opID">456/1234</label>?</h3>
	<div id="div_fecha_aviso_pago">
		Indique la fecha de aviso de pago
		<input name="fecha_aviso_pago" type="text" id="fecha_aviso_pago" placeholder="DD-MM-YYYY" value="<?php echo $fecha_aviso_pago_default;?>" size="8" maxlength="12">
		<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha ingreso" id="lanzador_FAP">
		<br/><br/>
	</div>
	<button onclick="Aceptar('lightbox');">S&iacute;</button><button onclick="Cancelar('lightbox');">No</button>
	</div>
    </div>
</div>
<script type="text/javascript"> 
	Calendar.setup({ 
		inputField:    "fecha_aviso_pago", // id del campo de texto 
		ifFormat  :    "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
		button    :    "lanzador_FAP"       // el id del botón que lanzará el calendario 
		}); 
</script>
<p>&nbsp;</p>
</body>
</html>
