<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_GET["opcion"] ?? 0;
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_MS');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],2,''); //2=Mesa de salida
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
</style>
<script language="javascript" >
function enviar(form){
	document.form3.submit();
}
</script>
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>
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
    <td align="left" valign="middle"><span class="TITULO">:: 
      Mesa de Salida ::</span><a href="form_mesa_salida.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> <p>
        <?php
				$numero_remito = $_GET['numero_remito'];
				$anio = $_GET['anio'];
				$row = $bd->consultar_mesa_salida($numero_remito, 0, $anio);				
				$fecha = convertir_fecha($row["fecha"]);
				$firmante = $row["firmante"];				
				$numero_orden = $bd->ultimo_numero_orden_mesa_salida($numero_remito, $anio);
				++$numero_orden;
				$numero_tramite = "";
				$remitente = "";
				$documento = "";
				$destinatario = 0;
				$copias = 0;
				$cantidad_hojas = 0;						
?>
      </p>
      <form action="abm_mesa_salida.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>N&uacute;mero 
                de Remito:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="numero_remito" type="text" id="numero_remito" value="' . $numero_remito .'"' . 'size="25" maxlength="25" disabled></td>';
   	  ?>
            <td class="modo1"><div align="right"><font color="#000099"><strong>Fecha:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><input name="fecha" type="text" id="fecha" value="' . $fecha .'"' . 'size="25" maxlength="25" disabled>';
//		echo '<td><input name="image" type="button" src="acrobat.png" width="25" height="25" onClick="enviar(acrobat)" oversrc="acrobat.png"></td>';
		echo '<td class="modo2"><input type="hidden" name="opcion" id="opcion" value="6"></td>';
		echo '<td class="modo2"><input type="hidden" name="numero_remito" id="numero_remito" value="' . $numero_remito .'"></td>';
		echo '<td class="modo2"><input type="hidden" name="fecha" id="fecha" value="' . $fecha .'"></td>';		
		echo '<td class="modo2"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar()" alt="Grabar datos"><img src="acrobat.png" width="15" heigth="20" border="0"></button></td>';
	?>
          </tr>
		  <tr>
            <td class="modo1"><div align="right"><font color="#000099"><strong>Firmante :</strong></font></div></td>
            <?php 
				$bd->listar_firmantes($firmante);
			?>
		  </tr>
        </table>
        <table align="center" class="tabla_form">
        </table>
      </form>
      <?php
		if ($opcion != 2){
			$bd->lista_mesa_salida_por_remito($numero_remito, 1, $anio);  
		}
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
<p>&nbsp;</p>
</body>
</html>
