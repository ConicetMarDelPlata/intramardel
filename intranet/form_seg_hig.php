<?php
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
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_NOT');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],26,''); //26-otros temas
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
<title>Seguridad e Higiene</title>
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
	//alert("Llega");
	//alert(document.form3.opcion.value);
	if (document.form3.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if (document.form3.titulo.value == "") 
		{
			alert("El título de la noticia es obligatorio.");
			return (false);
		}
		else
			//enviar = window.confirm('Se enviarán todos los datos del formulario');
			//(enviar)?form.submit():'return false';
			document.form3.submit();
	}
	else //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		document.form3.submit();
}

function enviar1(form){
	document.form3.opcion.value = 4;
	document.form3.submit();
}
function enviar2(form){
	document.form3.opcion.value = 5;
	document.form3.submit();
}
function enviar3(form){
	document.form3.opcion.value = 6;
	document.form3.submit();
}
function enviar4(form){
	document.form3.opcion.value = 7;
	document.form3.submit();
}
function enviar5(form){
	document.form3.opcion.value = 8;
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
.TITULO {	font-family: Verdana, Geneva, sans-serif;
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
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      Seguridad e Higiene ::</span>
	  <?php
		$webNotAlta = $bd->checkPerm($_SESSION["id_usuario"],26,'alta');
		echo '<a href="'; 
		echo ($webNotAlta)?"form_seg_hig.php?opcion=1":"#"; 
		echo '"><img src="';
		echo ($webNotAlta)?"agregar.png":"iconos_grises/agregarg.png";
		echo '" width="25" height="25" border="0"></a>'; 
	  ?>
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-web.php");?>
	</td>
    <td width="722" valign="top"> <p> 
        <?php
		switch ($opcion){
			case 1: // OPCION ALTA 
				$id_noticia=0;
				$titulo = "";
				$bajada = "";
				$fecha = "";
				$texto = "";
				$foto1 = "";				
				$foto2 = "";				
				$foto3 = "";				
				$foto4 = "";				
				$foto5 = "";
				$mostrar = 0;																
				break;
			case 2: // OPCION BAJA 
						//$codigo_articulo = $HTTP_GET_VARS['codigo_articulo'];
				//break;
			case 3: // OPCION MODIFICACION
				$id_noticia = $_GET['id_noticia'];
				$row = $bd->consultar_seg_e_hig($id_noticia);				
				$titulo = $row["titulo"];
				$bajada = $row["bajada"];
				$fecha = $row["fecha"];
				$texto = $row["texto"];
				$mostrar = $row["mostrar"];				
				$foto1 = $row["foto1"];
				$foto2 = $row["foto2"];
				$foto3 = $row["foto3"];
				$foto4 = $row["foto4"];	
				$foto5 = $row["foto5"];	
				break;			
		} // FIN SWITCH
?>
      </p>
      <form action="abm_seg_hig.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <tr> 
            <td width="245"> </td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Título:*</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="titulo" type="text" id="titulo" value="' . $titulo .'"' . 'size="55" maxlength="75"></td>';
			?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Bajada:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="bajada" type="text" id="bajada" value="' . $bajada .'"' . 'size="55" maxlength="75"></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Fecha:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="fecha" type="text" id="fecha" value="' . $fecha .'"' . 'size="25" maxlength="25">';
				echo '<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha" id="lanzador_fecha"></td>';
			?>
		<script type="text/javascript"> 
			Calendar.setup({ 
			inputField     :    "fecha",     // id del campo de texto 
			ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
			button     :    "lanzador_fecha"     // el id del botón que lanzará el calendario 
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
          </tr>
			<tr> 
            <td class="modo1"><div align="left"><font color="#000099"><strong>Texto*:</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><textarea name="texto" id="texto" cols="45" rows="15">'. $texto .'</textarea></td>';					
			?>
          </tr>
		  <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Atributos:</strong></font></div></td>
				<td class="modo2"><div align="left"><input type="button" value="Comienza Negrita" onclick="insertAtCaret('texto','<b>');"><input type="button" value="Cierra Negrita" onclick="insertAtCaret('texto','</b>');"></td>
          </tr>		  		  
		  <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Mostrar (1=SI, 0=NO):</strong></font></div></td>
            <?php 
				echo '<td class="modo2"><div align="left"><input name="mostrar" type="text" id="mostrar" value="' . $mostrar .'"' . 'size="55" maxlength="1"></td>';			
			?>
          </tr>		  		  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Foto 1:</strong></font></div></td>
            <?php 
			echo '<td class="modo1"><input name="archivo1" id="archivo1" type="file" value=""></td>';
			if ( ($opcion ==3) && ($foto1) ){
				echo '<td><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar1(form)" alt="Eliminar Foto"><img src="eliminar.png" width="30" heigth="30" border="0"></button></td>';
			}
				echo '<td><img src="../fotos_seg_e_hig/'. $foto1 . '" width="60" height="60" border="0"></td>';
			?>
          </tr>
		<tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Foto 2:</strong></font></div></td>
            <?php 
			echo '<td class="modo1"><input name="archivo2" id="archivo2" type="file" value=""></td>';
			if ( ($opcion ==3) && ($foto2) ){
				echo '<td><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar2(form)" alt="Eliminar Foto"><img src="eliminar.png" width="30" heigth="30" border="0"></button></td>';
				echo '<td><img src="../fotos_seg_e_hig/'. $foto2 . '" width="60" height="60" border="0"></td>';
			}else{
				if($foto2){
					echo '<td><img src="../fotos_seg_e_hig/'. $foto2 . '" width="60" height="60" border="0"></td>';
				}else{
					echo '<td></td>';
				}
			}
				
			?>
          </tr>
		<tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Foto 3:</strong></font></div></td>
            <?php 
			echo '<td class="modo1"><input name="archivo3" id="archivo3" type="file" value=""></td>';
			if ( ($opcion ==3) && ($foto3) ){
				echo '<td><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar3(form)" alt="Eliminar Foto"><img src="eliminar.png" width="30" heigth="30" border="0"></button></td>';
				echo '<td><img src="../fotos_seg_e_hig/'. $foto3 . '" width="60" height="60" border="0"></td>';
			}else{
				if($foto3){
					echo '<td><img src="../fotos_seg_e_hig/'. $foto3 . '" width="60" height="60" border="0"></td>';
				}else{
					echo '<td></td>';
				}
			}
			?>
          </tr>		  
		<tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Foto 4:</strong></font></div></td>
            <?php 
			echo '<td class="modo1"><input name="archivo4" id="archivo4" type="file" value=""></td>';
			if ( ($opcion ==3) && ($foto4) ){
				echo '<td><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar4(form)" alt="Eliminar Foto"><img src="eliminar.png" width="30" heigth="30" border="0"></button></td>';
				echo '<td><img src="../fotos_seg_e_hig/'. $foto4 . '" width="60" height="60" border="0"></td>';
			}else{
				if($foto4){
					echo '<td><img src="../fotos_seg_e_hig/'. $foto4 . '" width="60" height="60" border="0"></td>';
				}else{
					echo '<td></td>';
				}
			}
			?>
          </tr>		  
		<tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Foto 5:</strong></font></div></td>
            <?php 
			echo '<td class="modo1"><input name="archivo5" id="archivo5" type="file" value=""></td>';
			if ( ($opcion ==3) && ($foto5) ){
				echo '<td><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar5(form)" alt="Eliminar Foto"><img src="eliminar.png" width="30" heigth="30" border="0"></button></td>';
				echo '<td><img src="../fotos_seg_e_hig/'. $foto5 . '" width="60" height="60" border="0"></td>';
			}else{
				if($foto5){
					echo '<td><img src="../fotos_seg_e_hig/'. $foto5 . '" width="60" height="60" border="0"></td>';
				}else{
					echo '<td></td>';
				}
			}
			?>
          </tr>	  
          <tr> 
            <td colspan="2" class="modo1" align="center"><font color="#000099">* 
              Datos obligatorios.</font></td>
          </tr>
        </table>
        <?php		
		switch ($opcion){
			case 1: // ALTA  
				echo '<input type="hidden" name="opcion" id="opcion" value="1">';
				echo '<input type="hidden" name="id_noticia" id="id_noticia" value="'.$id_noticia.'">';				
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<input type="hidden" name="id_noticia" id="id_noticia" value="'.$id_noticia.'">';				
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Noticia"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<input type="hidden" name="id_noticia" id="id_noticia" value="'.$id_noticia.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;
	?>
      </form>
      <script>
	document.form3.titulo.focus();
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
