<?php
	include "./includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_GET["opcion"];
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_CONV');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],17,''); //17=destinatarios
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
	//alert(document.form3.opcion.value);
	if (document.form3.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		//alert(document.form3.mesa[0].checked);
		//alert(document.form3.mesa[1].checked);
		//return false;
		if ( (!document.form3.mesa[0].checked) && (!document.form3.mesa[1].checked) ) {
			alert("Se debe elegir mesa de entrada o mesa de salida");
			return false;
		}
		/*if (document.form3.mesa[0].checked){
			if ( (!document.form3.interna_externa[0].checked) && (!document.form3.interna_externa[1].checked) ) {
				alert("Se debe elegir interna o externa");
				return false;
			}
		}*/
		if (document.form3.descripcion.value == "") 
		{
			alert("La descripcion de la Mesa es obligatorio.");
			return false;
		}
		else
			//enviar = window.confirm('Se enviarán todos los datos del formulario');
			//(enviar)?form.submit():'return false';
			document.form3.submit();
	}
	else //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		document.form3.submit();
		
}
/* 
function cambiarDisplay(id) {
	if (!document.getElementById) return false;
		fila = document.getElementById(id);
	  	if (fila.style.display != "none") {
			fila.style.display = "none"; //ocultar fila 
		  	} else {
				fila.style.display = ""; //mostrar fila 
			}
}

function mostrarFila(id) {
	if (!document.getElementById) return false;
		fila = document.getElementById(id);
	fila.style.display = ""; //mostrar fila 
}

function ocultarFila(id) {
	if (!document.getElementById) return false;
		fila = document.getElementById(id);
	fila.style.display = "none"; //ocultar fila 
}*/
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
<p align="center"><img src="cabecera.jpg" width="900" height="101"></p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      Destinatarios ::</span><a href="form_banco.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-gral.php");?>
	</td>
    <td width="722" valign="top"> <p> 
        <?php
		switch ($opcion){
			case 1: // OPCION ALTA 
				$id_destinatario=0;
				$mesa = "";
				//$interna_externa = 0;
				$descripcion = "";
				break;
			case 2: // OPCION BAJA 
				//$codigo_articulo = $HTTP_GET_VARS['codigo_articulo'];
				//break;
			case 3: // OPCION MODIFICACION
				$id_destinatario = $_GET['id_destinatario'];
				$row = $bd->consultar_destinatario($id_destinatario);				
				$mesa = $row["mesa"];
				//$interna_externa = $row["interna_externa"];
				$descripcion = $row["descripcion"];				
				break;			
		} // FIN SWITCH
?>
      </p>
      <form action="abm_destinatario.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <tr> 
            <td width="219"> </td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Mesa 
                *</strong></font></div></td>
            <?php 
	  switch ($opcion){
		case 1: // OPCION ALTA. 
			//echo '<td align="center" class="modo1"><input type="radio" name="mesa" id="mesa" value="Entrada" onClick="mostrarFila('."'row2'".')">Entrada';
			echo '<td align="center" class="modo1"><input type="radio" name="mesa" id="mesa" value="Entrada">Entrada';			
			//echo '<input type="radio" name="mesa" id="mesa" onClick="ocultarFila('."'row2'".')" value="Salida">Salida</td>';
			echo '<input type="radio" name="mesa" id="mesa" value="Salida">Salida</td>';			
			break;
		case 2: // OPCION BAJA. 
		case 3: // OPCION MODIFICACION. 
			if($mesa == "Salida"){			
				echo '<td align="center" class="modo1"><input type="radio" name="mesa" id="mesa" value="Entrada">Entrada';
				//echo '<input type="radio" name="mesa" id="mesa" onClick="ocultarFila('."'row2'".')" value="Salida" checked>Salida</td>';
				echo '<input type="radio" name="mesa" id="mesa" value="Salida" checked>Salida</td>';				
			}else{
				echo '<td align="center" class="modo1"><input type="radio" name="mesa" id="mesa" value="Entrada" checked>Entrada';
				//echo '<input type="radio" name="mesa" id="mesa" onClick="ocultarFila('."'row2'".')" value="Salida">Salida</td>';
				echo '<input type="radio" name="mesa" id="mesa" value="Salida">Salida</td>';				
			}
			break;			
		} // FIN SWITCH	
	  ?>
          </tr>
          <?php 
/*      EN MAIL DEL 30-11-12 INES PIDE SACAR OPCION INTERNA/EXTERNA
		switch ($opcion){
			case 1: // OPCION ALTA. POR DEFECTO OCULTAR 
				echo '<tr id="row2" style="display:none">'; 
				break;
			case 2: // OPCION BAJA 
			case 3: // OPCION MODIFICACION. 
				if ($mesa == "Salida"){
					echo '<tr id="row2" style="display:none">';  // OCULTAR
				}else
					echo '<tr id="row2">'; // SI ES MESA DE ENTRADA MOSTRAR
				break;			
		} // FIN SWITCH	*/
	?>
          <?php 
	/*      EN MAIL DEL 30-11-12 INES PIDE SACAR OPCION INTERNA/EXTERNA

		<td class="modo1"><div align="right"><font color="#000099"><strong>Interna / Externa:</strong></font></div></td>
		if ($opcion == 3){
			if ($interna_externa == "Interna"){
				echo '<td align="center" class="modo1"><input type="radio" name="interna_externa" value="Interna" checked>Interna';
	    		echo '<input type="radio" name="interna_externa" value="Externa">Externa</td>';
			}else{
				echo '<td align="center" class="modo1"><input type="radio" name="interna_externa" value="Interna">Interna';
				if ($mesa == "Entrada"){
	    			echo '<input type="radio" name="interna_externa" value="Externa" checked>Externa</td>';
				}else
	    			echo '<input type="radio" name="interna_externa" value="Externa">Externa</td>';
			}		
		}else{	  
			echo '<td align="center" class="modo1"><input type="radio" name="interna_externa" value="Interna">Interna';
    		echo '<input type="radio" name="interna_externa" value="Externa">Externa</td>';
	  	}
    </tr>*/
	  ?>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Descripci&oacute;n:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><input name="descripcion" type="text" id="descripcion" value="' . $descripcion .'"' . 'size="55" maxlength="100"></td>';
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
				echo '<p align="center"><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 2: // BAJA 
			  	echo '<input type="hidden" name="opcion" id="opcion" value="2">';
				echo '<input type="hidden" name="id_destinatario" id="id_destinatario" value="'.$id_destinatario.'">';				
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Destinatario"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<input type="hidden" name="id_destinatario" id="id_destinatario" value="'.$id_destinatario.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;
	?>
      </form>
      <script>
	document.form3.descripcion.focus();
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
