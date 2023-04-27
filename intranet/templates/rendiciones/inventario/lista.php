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
<script src="js/jquery.js" type="text/javascript"></script>
<script src="tabs/tabcontent.js" type="text/javascript"></script>
<script src="lightbox/jquery.lightbox_me.js" type="text/javascript"></script>
<script type="text/javascript" src="overlib421/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<link href="tabs/template6/tabcontent.css" rel="stylesheet" type="text/css" />
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
<style type="text/css">
table.myTable { border-collapse:collapse; }
table.myTable th { border:1px solid black;padding:5px; font-size:11px; font-family: Arial;}
table.myTable td { border:none; padding:5px; font-size:11px; font-family: Arial;background-color:#D5E8F2;}
</style>
</head>
<body>
	<div width="50" id="popup" style="float:right;padding-right:40px;display:none;overflow: hidden;">
		<img width="32px" src="iconos/flecharoja1.png" />
	</div>
<p align="center"><img src="cabecera.jpg" width="900" height="101" border="0" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
	<tr align="right" valign="top">
		<td colspan="10" width="552">
			<a href="{var_linkBack}" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">{var_sBack}</a>
		</td>
	</tr>
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" />Usuario: {var_User}</strong></p>
      <p>&nbsp;</p></td>
	<td align="left" valign="middle">
		<span class="TITULO">:: Inventario ::</span>
	</td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<!--BEGIN BLOCK PC-->
			<div class="divIcon" style="text-align:center">
				<a href="{var_linkPC}">
					<img src="{var_iconoPC}" class="imgIcon" vspace="4" border="0" />
				</a>
				<br />
				<a href="{var_linkPC}" class="tituloweb2Copia">{var_nombrePC}</a>
			</div>
		<!--END BLOCK PC-->
	</td>
	<!-- CONTENIDO PRINCIPAL -->
	
	<!-- LISTA ACTAS  -->
    <td width="722" valign="top" id="lista_actas">
		<form name="frm_inv" action="inv_pdf.php" method="POST" target="_blank">
			<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
				<tr>
					<th>Per&iacute;odo Desde</th>
					<th>Per&iacute;odo Hasta</th>
					<th>Unidad</th>
				</tr>
				<tr class="modo1">				
					<td style="padding-left:0px; padding-right:0px">
						<input id="sFechaIni" name="sFIni" style="border:none;background-image: url(fondo_tr01.png); width:69px"/>
						<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Inicial" id="lanzador">
					</td>
					<td style="padding-left:0px; padding-right:0px">
						<input id="sFechaFin" name="sFFin" style="border:none;background-image: url(fondo_tr01.png); width:69px"/>
						<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Final" id="lanzador1">
					</td>
					<td>
						<select name="iUE">
							<!--<option value="0" selected>---</option>-->
							<!--BEGIN BLOCK UE-->
							<option value="{var_iUE}">{var_sUE}</option>
							<!--END BLOCK UE-->
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<button type="button" onclick="enviar();">
							<img src="acrobat.png" width="30"/>
						</button>
					</td>
				</tr>
			</table>
		</form>
	</td>
	<!-- FIN LISTA ACTAS  -->
	<!-- FIN CONTENIDO PRINCIPAL -->
  </tr>
</table>

<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
<script type="text/javascript">
Calendar.setup({ 
	inputField     :    "sFechaIni",     // id del campo de texto 
	ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
	button     :    "lanzador"     // el id del bot&oacute;n que lanzar&aacute; el calendario 
});

Calendar.setup({ 
	inputField     :    "sFechaFin",     // id del campo de texto 
	ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
	button     :    "lanzador1"     // el id del bot&oacute;n que lanzar&aacute; el calendario 
});

$( document ).ready(function() {
	DetectaBloqueoPops();
});

function DetectaBloqueoPops()
{
  var popup
  try
  {
    //Se crea una nueva ventana para probar si esta o no activo
    // el bloqueador de ventanas emergentes.
    //Si esta activo, se lanza el error, de lo contrario s&oacute;lo se cierra la ventana creada
    if(!(popup = window.open('about:blank','_blank','width=1,height=1')))
      throw "ErrPop"
    msj = "OK"
    popup.close()
  }
  catch(err)
  {
    //Se captura el error, si fue por motivo de bloqueo, se muestra el mensaje de advertencia
    //Si no fue por bloqueo, entonces se muestra la descripci&oacute;n del error ocurrido.
    if(err=="ErrPop")
      msj = "POPUP"
    else
    {
      msj="Hubo un erro en la pagina.\n\n"
      msj+="Descripcion del error: " + err.description + "\n\n"
     }
  }
  if(msj == "POPUP"){
	$( "#popup" ).fadeIn();
	blink();

/*	$( "#popup" ).fadeIn( "slow", function() {
		alert("ATENCION\n\nDebe habilitar las ventanas emergentes.");
		$( "#popup" ).fadeOut( "slow");
	});*/
  }else{
	if(msj != "OK"){
		alert(msj);
	}
  }
 
}

function blink(){
	if(!$( "#popup" ).css("opacity") || $( "#popup" ).css("opacity") == 1){
		var valor = 0.1;
	}else{
		var valor = 1;
	}

	$( "#popup" ).animate({
	opacity: valor
	}, 2000, function() {
		blink();
	});
}

function enviar(){
	document.forms.frm_inv.submit();
	$("#sFechaIni").val("");
	$("#sFechaFin").val("");
}

</script>
</html>
