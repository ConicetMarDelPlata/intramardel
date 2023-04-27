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
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_LIC');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],24,''); //24=web licitaciones
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
	//alert("Llega");
	//alert(document.form3.opcion.value);
	if (document.form3.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		if ( (!document.form3.estado[0].checked) && (!document.form3.estado[1].checked) ) {
			alert("Se debe elegir Estado En Curso o Finalizada");
			return false;
		}
		if (document.form3.fecha_publicacion.value == ""){
			alert("La fecha de Publicacion es obligatoria.");
			return (false);
		}else
			if (document.form3.fecha_apertura.value == ""){
				alert("La fecha de Apertura es obligatoria.");
				return (false);
		}else
				//enviar = window.confirm('Se enviarán todos los datos del formulario');
				//(enviar)?form.submit():'return false';
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
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      Licitaciones ::</span>
	  <?php
		$webLicAlta = $bd->checkPerm($_SESSION["id_usuario"],24,'alta');
		echo '<a href="'; 
		echo ($webLicAlta)?"form_licitacion.php?opcion=1":"#"; 
		echo '"><img src="';
		echo ($webLicAlta)?"agregar.png":"iconos_grises/agregarg.png";
		echo '" width="25" height="25" border="0"></a>'; 
	  ?>
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-web.php");?>	
	</td>
    <td width="722" valign="top"> 
      <?php
		switch ($opcion){
			case 1: // OPCION ALTA
				$id_licitacion = 0;
				$titulo = "";
				$fecha_publicacion = "";
				$fecha_apertura = "";				
				$horario_aperura = "";
				$unidad_ejecutora = 0;
				$numero_licitacion = "";			
				$comentario = "";			
				$horario_apertura = "";
				$estado = "";
				$archivo = "";				
			break;
			case 2: // OPCION BAJA
				//break;
			case 3: // OPCION MODIFICACION DE 
				$id_licitacion = $_GET['id_licitacion'];
				$row = $bd->consultar_licitacion($id_licitacion);
				$titulo = $row['titulo'];
				$fecha_publicacion = convertir_fecha($row["fecha_publicacion"]);	
				$fecha_apertura = convertir_fecha($row["fecha_apertura"]);
				$horario_apertura = $row["horario_apertura"];
				$unidad_ejecutora = $row["unidad_ejecutora"];
				$numero_licitacion = $row["numero_licitacion"];
				$comentario = $row["comentario"];
				$estado = $row["estado"];
				$archivo = $row["archivo"];				
				break;			
		} // FIN SWITCH
?>
      <form action="abm_licitacion.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <?php 
	  ?>
          <tr> 
            <td width="275" class="modo1"><div align="right"><font color="#000099"><strong>Titulo:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="titulo" type="text" id="titulo" value="' . $titulo .'"' . 'size="65" maxlength="255"></td>';
	  ?>
        </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Fecha Publicacion:*</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="fecha_publicacion" type="text" id="fecha_publicacion" value="' . $fecha_publicacion .'"' . 'size="25" maxlength="25">';
		echo '<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Publicacion" id="lanzador_fecha_publicacion"></td>';
	?>
            <script type="text/javascript"> 
   		Calendar.setup({ 
	    inputField     :    "fecha_publicacion",     // id del campo de texto 
   		ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
    	button     :    "lanzador_fecha_publicacion"     // el id del botón que lanzará el calendario 
	}); 
	</script>
          </tr>
	<tr> 
       <td class="modo1"><div align="right"><font color="#000099"><strong>Fecha Apertura:*</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="fecha_apertura" type="text" id="fecha_apertura" value="' . $fecha_apertura .'"' . 'size="25" maxlength="25">';
		echo '<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Apertura" id="lanzador_fecha_apertura"></td>';
	?>
        <script type="text/javascript"> 
   		Calendar.setup({ 
	    inputField     :    "fecha_apertura",     // id del campo de texto 
   		ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
    	button     :    "lanzador_fecha_apertura"     // el id del botón que lanzará el calendario 
	}); 
	</script>
          </tr>		  
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Horario Apertura:</strong></font></div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="horario_apertura" type="text" id="horario_apertura" value="' . $horario_apertura .'"' . 'size="25" maxlength="25"></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Unidad Ejecutora:</strong></font></div></td>
            <?php 
			if ( ($opcion == 2 ) || ($opcion == 3) ){ // BAJA O MODIFICACION
				$bd->listar_unidades_ejecutoras($unidad_ejecutora);
			}else //ALTA
			{				
				$bd->listar_unidades_ejecutoras(0);
			}
		?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Num. Licitacion:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><div align="left"><input name="numero_licitacion" type="text" id="numero_licitacion" value="' . $numero_licitacion .'"' . 'size="25" maxlength="25"></td>';
	?>
          </tr>
          <tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Comentario:</strong></font></div></td>
            <?php 
			echo '<td class="modo2"><div align="left"><input name="comentario" type="text" id="comentario" value="' . $comentario .'"' . 'size="65" maxlength="255"></td>';
		?>
          </tr>
<tr> 
            <td class="modo1"><div align="right"><font color="#000099"><strong>Archivo:</strong></font></div></td>
            <?php 
		echo '<td class="modo1"><div align="left"><input name="archivo1" id="archivo1" type="file" value="">';
		echo '<br>'.$archivo.'</td>';		
		if ( ($opcion ==3) && ($archivo) ){
			echo '<td><button type="button" name="Btn_enviar" id="Btn_enviar" onClick="enviar2(form)" alt="Eliminar Archivo"><img src="eliminar.png" width="30" heigth="30" border="0"></button></td>';
		}
		// TENDRIA QUE PONER ICONO DE PDF EN LA SIGUIENTE LINEA
		//echo '<td><img src="'. $firma . '" width="60" height="60" border="0"></td>';
		?>
          </tr>
		<tr> 
			<td class="modo1"><div align="right"><font color="#000099"><strong>Estado *</strong></font></div></td>
			<?php 
			  switch ($opcion){
				case 1: // OPCION ALTA. 
					//echo '<td align="center" class="modo1"><input type="radio" name="mesa" id="mesa" value="Entrada" onClick="mostrarFila('."'row2'".')">Entrada';
					echo '<td align="center" class="modo1"><input type="radio" name="estado" id="estado" value="En Curso">En Curso';			
					//echo '<input type="radio" name="mesa" id="mesa" onClick="ocultarFila('."'row2'".')" value="Salida">Salida</td>';
					echo '<input type="radio" name="estado" id="estado" value="Finalizada">Finalizada</td>';			
					break;
				case 2: // OPCION BAJA. 
				case 3: // OPCION MODIFICACION. 
					if($estado == "Finalizada"){			
						echo '<td align="center" class="modo1"><input type="radio" name="estado" id="estado" value="En Curso">En Curso';
						//echo '<input type="radio" name="mesa" id="mesa" onClick="ocultarFila('."'row2'".')" value="Salida" checked>Salida</td>';
						echo '<input type="radio" name="estado" id="estado" value="Finalizada" checked>Finalizada</td>';				
					}else{
						echo '<td align="center" class="modo1"><input type="radio" name="estado" id="estado" value="En Curso" checked>En Curso';
						//echo '<input type="radio" name="mesa" id="mesa" onClick="ocultarFila('."'row2'".')" value="Salida">Salida</td>';
						echo '<input type="radio" name="estado" id="estado" value="Finalizada">Finalizada</td>';				
					}
					break;			
				} // FIN SWITCH	
			  ?>
		 </tr>	  		  
          <tr> 
            <td colspan="2" class="modo1" align="center"><font color="#000099">* 
              Datoso bligatorios.</font></td>
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
				echo '<input type="hidden" name="id_licitacion" id="id_licitacion" value="'.$id_licitacion.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar Registro"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 
				echo '<input type="hidden" name="opcion" id="opcion" value="3">';
				echo '<input type="hidden" name="id_licitacion" id="id_licitacion" value="'.$id_licitacion.'">';
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;				
	?>
      </form>
      <script>
	document.form3.titulo.focus();
</script> <p>&nbsp;</p></td>
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
