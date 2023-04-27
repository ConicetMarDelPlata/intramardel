<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	include_once("./includes/class.Equipos.php");

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
	$equipo = new Equipos($bd);

	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],36,''); //36-Equipo de Salas de Reuniones
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
<link href="tabla.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/table.css" media="all">
<link rel="stylesheet" type="text/css" href="css/base.css" media="all">
<script src="js/table.js" type="text/javascript"></script>

</head>
<body>
<p align="center"><img src="cabecera.jpg" width="900" height="101" border="0" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
	 <?php
		$permAlta = $bd->checkPerm($_SESSION["id_usuario"],36,'alta');
		if($permAlta == 1){
  	  		echo '<td align="left" valign="middle"><span class="TITULO">:: Equipos ::</span><a href="form_equipo_salas.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a></td>';
		}else
			echo '<td align="left" valign="middle"><span class="TITULO">:: Equipos ::</span><a href="#"><img src="iconos_grises/agregarg.png" width="25" height="25" border="0"></a></td>';
	?>  
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-gral.php");?>
	</td>
    <td width="722" valign="top"> 
		<?php
		if($bd->checkPerm($_SESSION["id_usuario"],36,'consulta')){
			$equipo->lista_equipo_salas($_REQUEST);
			$equipo = NULL;
		}

		?>
    </td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
<?php
if (isset($_SESSION["message"])) {
	echo '<script language=javascript>';
	$message=$_SESSION["message"];
	if ($message!="") { 
		echo 'alert("'.$message.'")'; 
		$_SESSION["message"]="";
		}
	echo '</script>';
}
?>
</body>
</html>
