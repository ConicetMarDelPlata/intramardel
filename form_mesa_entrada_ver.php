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
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_ME');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],1,''); //1=Mesa de entrada
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
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>

<link href="calendario/css/calendario.css" type="text/css" rel="stylesheet">
<script src="calendario/js/calendar.js" type="text/javascript"></script>
<script src="calendario/js/calendar-es.js" type="text/javascript"></script>
<script src="calendario/js/calendar-setup.js" type="text/javascript"></script>
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
      Mesa de Entrada ::</span><a href="form_mesa_entrada.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> 
      <?php
			$numero_orden = $_GET['numero_orden'];
			$row = $bd->consultar_mesa_entrada($numero_orden);
			$numero_tramite = $row['numero_tramite'];
			$anio_numero_tramite = $row['anio_numero_tramite'];
			$fecha = convertir_fecha($row["fecha"]);	
			$remitente = $row["remitente"];
			$documento = $row["documento"];
			$destinatario = $row["destinatario"];
			$cantidad = $row["cantidad"];
			$observaciones = $row["observaciones"];
			$firmante = $row["firmante"];				
	?>
      <form action="" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <?php 
    /*<tr> 
      <td class="modo1"><div align="right"><font color="#000099"><strong>N&uacute;mero 
          de Orden:</strong></font></div></td>

		echo '<td class="modo2"><input name="numero_orden" type="text" id="numero_orden" value="' . $numero_orden .'"' . 'size="25" maxlength="25"></td>';
    </tr>*/
	  ?>
          <tr> 
            <td width="275" class="modo1"><div align="right"><font color="#000099"><strong>N&uacute;mero 
                de tr&aacute;mite:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="numero_tramite" type="text" id="numero_tramite" value="' . $numero_tramite .'"' . 'size="12" maxlength="15" disabled>';
	  ?>
            <?php 
		echo '<input name="anio_numero_tramite" type="text" id="anio_numero_tramite" value="' . $anio_numero_tramite .'"' . 'size="13" maxlength="13" disabled></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Fecha:*</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="fecha" type="text" id="fecha" value="' . $fecha .'"' . 'size="25" maxlength="25" disabled>';
		echo '<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Inicial" id="lanzador"></td>';
	?>
            <script type="text/javascript"> 
   		Calendar.setup({ 
	    inputField     :    "fecha",     // id del campo de texto 
   		ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
    	button     :    "lanzador"     // el id del botón que lanzará el calendario 
	}); 
	</script>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Remitente:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="remitente" type="text" id="remitente" value="' . $remitente .'"' . 'size="65" maxlength="25" disabled></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Documento:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="documento" type="text" id="documento" value="' . $documento .'"' . 'size="65" maxlength="150" disabled></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Destinatario:</strong></font></div></td>
            <?php 
				$bd->listar_destinatarios($destinatario, "Entrada");
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Cantidad:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><div align="left"><input name="cantidad" type="text" id="cantidad" value="' . $cantidad .'"' . 'size="65" maxlength="25" disabled></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Observaciones:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><div align="left"><input name="observaciones" type="text" id="observaciones" value="' . $observaciones .'"' . 'size="65" maxlength="50" disabled></td>';
		?>
          </tr>
	    <tr>
            <td class="modo1"><div align="right"><font color="#000099"><strong>Firmante :</strong></font></div></td>
            <?php 
				$bd->listar_firmantes($firmante);
			?>
          </tr>		  
          <tr> 
            <td colspan="2" class="modo1" align="center"><font color="#000099">* 
              Datoso bligatorios.</font></td>
          </tr>
        </table>
        <?php
		$bd = NULL;				
		?>
      </form>
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
