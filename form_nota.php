<?php
	include "includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_GET['opcion'];
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_NOT_GRAL');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],6,''); //6=Notas
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
<title>Registro de Notas</title>
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
	//alert("Llega");
	//alert(document.form3.opcion.value);
	if (document.form3.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if (document.form3.texto.value == "") 
		{
			alert("El texto de la Nota es obligatorio.");
			return (false);
		}
		else
			//enviar = window.confirm('Se enviarán todos los datos del formulario');
			//(enviar)? form.submit():console.log('false');
			document.form3.submit();
	}
	else //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		document.form3.submit();
}
</script>
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
      M&oacute;dulo de Notas ::</span><a href="form_nota.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> <p> 
        <?php
		switch ($opcion){
			case 1: // OPCION ALTA 
				$id_nota=0;
				//Nota Vani: asigno el numero al momento de grabar
				$numero_nota = "";
				$anio_numero_nota = date('Y');
				$fecha  = date('d-m-Y');
				$destinatario = "";
				$lugar_trabajo = "";
				$texto = "";
				$referencia = "";				
				$firmante = 0;
				$firmante1 = 0;
				$CC = '';
				$firma_digital = 0;				
				break;
			case 2: // OPCION BAJA 
				//$codigo_articulo = $HTTP_GET_VARS['codigo_articulo'];
				//break;
			case 3: // OPCION MODIFICACION
				$id_nota = $_GET['id_nota'];
				$row = $bd->consultar_nota($id_nota, $anio_numero_nota ?? 0);				
				$anio_numero_nota = $row['anio_numero_nota'];
				$numero_nota = $row['numero_nota'];
				$fecha = convertir_fecha($row["fecha"]);
				$destinatario = $row["destinatario"];				
				$lugar_trabajo = $row["lugar_trabajo"];
				$texto = str_replace("<br />","",$row["texto"]);
				$referencia = $row["referencia"];
				$firmante = $row["firmante"];
				$firmante1 = $row["firmante1"];
				$CC = $row["CC"];
				$firma_digital = $row["firma_digital"];				
				break;			
		} // FIN SWITCH
?>
      </p>
      <form action="abm_nota.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <tr> 
            <td width="311"> </td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>N&uacute;mero 
                nota:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="numero_nota" type="text" id="numero_nota" value="' . $numero_nota .'"' . 'size="25" maxlength="25" disabled>';
   		echo '<input name="anio_numero_nota" type="text" id="anio_numero_nota" value="' . $anio_numero_nota .'"' . 'size="13" maxlength="13"></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Fecha:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><div align="left"><input name="fecha" type="text" id="fecha" value="' . $fecha .'"' . 'size="25" maxlength="25">';
			echo '<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Inicial" id="lanzador"></td>';
		  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Destinatario:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><div align="left"><input name="destinatario" type="text" id="destinatario" value="' . $destinatario .'"' . 'size="55" maxlength="55"></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Lugar de trabajo:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><div align="left"><input name="lugar_trabajo" type="text" id="lugar_trabajo" value="' . $lugar_trabajo .'"' . 'size="55" maxlength="255"></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Texto*:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><textarea name="texto" id="texto" cols="45" rows="15" spellcheck="true">'. $texto .'</textarea></td>';					
			?>
          </tr>
		  <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Atributos:</strong></font></div></td>
				<td class="modo2"><div align="left"><input type="button" value="Comienza Negrita" onclick="insertAtCaret('texto','[negrita]');"><input type="button" value="Cierra Negrita" onclick="insertAtCaret('texto','[/negrita]');"></td>
          </tr>		  		  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Referencia:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><div align="left"><input name="referencia" type="text" id="referencia" value="' . $referencia .'"' . 'size="55" maxlength="255"></td>';
	?>
          </tr>
		  <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Firmante de la nota:</strong></font></div></td>
            <?php 
			$bd->listar_firmantes($firmante, "firmante", true);
			?>
          </tr>		  
		  <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Segundo firmante:</strong></font></div></td>
            <?php 
			$bd->listar_firmantes($firmante1, "firmante1", true);
			?>
          </tr>		  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>CC:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="sCC" type="text" id="sCC" value="' . $CC .'"' . 'size="55" maxlength="254">';
	 	 	?>
          </tr>		  		  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Firma_Digital_:1=SI,0=NO:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="firma_digital" type="text" id="firma_digital" value="' . $firma_digital .'"' . 'size="5" maxlength="5">';
	 	 	?>
          </tr>		  		  
          <tr> 
            <td colspan="2" class="modo1" align="center"><font color="#000099">* 
              Datos obligatorios.</font></td>
          </tr>
          <script type="text/javascript"> 
   		Calendar.setup({ 
	    inputField     :    "fecha",     // id del campo de texto 
   		ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
    	button     :    "lanzador"     // el id del botón que lanzará el calendario 
	}); 
		    function insertAtCaret(areaId,text) {
				var txtarea = document.getElementById(areaId);
				var scrollPos = txtarea.scrollTop;
				var strPos = 0;
				var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
					"ff" : (document.selection ? "ie" : false ) );
				if (br == "ie") {
					txtarea.focus();
					var range = document.selection.createRange();
					range.moveStart ('character', -txtarea.value.length);
					strPos = range.text.length;
				}
				else if (br == "ff") strPos = txtarea.selectionStart;
			   
				var front = (txtarea.value).substring(0,strPos);  
				var back = (txtarea.value).substring(strPos,txtarea.value.length);
				txtarea.value=front+text+back;
				strPos = strPos + text.length;
				if (br == "ie") {
					txtarea.focus();
					var range = document.selection.createRange();
					range.moveStart ('character', -txtarea.value.length);
					range.moveStart ('character', strPos);
					range.moveEnd ('character', 0);
					range.select();
				}
				else if (br == "ff") {
					txtarea.selectionStart = strPos;
					txtarea.selectionEnd = strPos;
					txtarea.focus();
				}
				txtarea.scrollTop = scrollPos;
			}
	</script>
        </table>
        <?php		
		switch ($opcion){
			case 1: // ALTA  
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';
				echo '<input type="hidden" name="numero_nota" id="numero_nota" value="'.$numero_nota.'">';
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';				
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<input type="hidden" name="id_nota" id="id_nota" value="'.$id_nota.'">';
				echo '<input type="hidden" name="numero_nota" id="numero_nota" value="'.$numero_nota.'">';								
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Articulo"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<input type="hidden" name="id_nota" id="id_nota" value="'.$id_nota.'">';
				echo '<input type="hidden" name="numero_nota" id="numero_nota" value="'.$numero_nota.'">';				
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;
	?>
      </form>
      <script>
	document.form3.nombre.focus();
</script></td>
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
