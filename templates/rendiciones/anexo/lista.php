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
		<span class="TITULO">:: Anexo Donaci&oacute;n ::</span>
		<a href="{var_anexo_alta}"><img src="{var_path_agregar_anexo}" width="25" height="25" border="0"></a>
		<!--<a href="{var_anexo_open_close}"><img src="{var_path_open_close_anexo}" width="27" height="27" border="0" alt="Abrir Anexo Donaci&oacute;n"></a>-->
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
		<form name="frmSearch" action="lista_anexo_donacion.php" method="POST">
			<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
				<tr>
					<th colspan="9">FILTROS</th>
				</tr>
				<tr class="modo1">
					<td width="20">ID</td>
					<td style="text-align:left;width:55px;">
						<input name="searchID" type="text" maxlength="5" style="width:50px;text-align:center"/>
					</td>
					<td width="25">Estado</td>
					<td width="100" style="text-align:left">
						<select name="searchStatus">
							<option value="1"{var_S1}>Abierto</option>
							<option value="0"{var_S0}>Cerrado</option>
							<option value="2"{var_S2}>Todos</option>
						</select>
					</td>
					<td width="130">Unidad</td>
					<td style="text-align:left">
						<select name="searchUI">
							<option value=""{var_sSelAll}>Todos</option>
							<!--BEGIN BLOCK UI-->
							<option value="{var_iUI}" {var_sSelUI}>{var_sUI}</option>
							<!--END BLOCK UI-->
						</select>
					</td>
					<td style="text-align:right;width:40px">
						<input type="submit" value="Buscar"/>
					</td>
				</tr>
			</table>
		</form>
		<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
			<tr>
				<td>
				&nbsp;
				</td>
			</tr>
			<tr>
				<th>ID</th>
				<th>Unidad</th>
				<th>Titular</th>
				<th>Fecha</th>
				<th>Estado</th>
				<th colspan="4">Acciones</th>
			</tr>
			<!--BEGIN BLOCK DESC-->
			<tr class="modo1">				
				<td>{var_ID}</td>
				<td align="left">{var_UE}</td>
				<td align="left">{var_Titular}</td>
				<td>{var_Fecha}</td>
				<td>{var_Estado}</td>
				
				<td align="center"><font color="#333333"><a onclick="return elimina_anexo(this);" href="{var_LinkB}"><img src="{var_ImgB}" width="16" border="0" title="Borrar Anexo"></a></td>
				<td align="center"><font color="#333333"><a href="{var_LinkM}"><img src="{var_ImgM}" width="16" border="0" title="Modificar"></a></td>
				<td align="center"><font color="#333333"><a href="{var_LinkV}" target="_blank"><img src="acrobat.png" width="16" border="0" title="Generar PDF"></a></td>
				<td align="center"><font color="#333333"><a onclick="return fncAbrirCerrarAnexo(this,'{var_sOpenClose}');" href="{var_LinkE}"><img src="iconos/anexo/{var_ImgE}" width="{var_W}" border="0" title="{var_sOpenClose} "></a></td>
			</tr>
			<!--END BLOCK DESC-->	
			<tr>
				<td colspan="10" style="text-align:right;">
					<ul id="pagination-digg">
						<li class="{var_Prev}">
							<!--BEGIN BLOCK AINI-->
							<a href="?page={var_iPrev}">
							<!--END BLOCK AINI-->
								&laquo;Anterior
							<!--BEGIN BLOCK AFIN-->
							</a>
							<!--END BLOCK AFIN-->
						</li>
						
						<!--BEGIN BLOCK PAGES-->
						<li class="{var_sPageActive}">
							{var_sPage}
						</li>
						<!--END BLOCK PAGES-->
						
						<li class="{var_Next}">
							<!--BEGIN BLOCK SINI-->
							<a href="?page={var_iNext}">
							<!--END BLOCK SINI-->
								Siguiente&raquo;
							<!--BEGIN BLOCK SFIN-->
							</a>
							<!--END BLOCK SFIN-->
						</li>
					</ul>					
				</th>
			</tr>
		</table>
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
      msj="Hubo un erro en la p\u00E1gina.\n\n"
      msj+="Descripci\u00F3n del error: " + err.description + "\n\n"
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

function elimina_anexo(anchor){
	if(!anchor.href.endsWith("#")) {
		if(!confirm("Est\u00E1 a punto de ELIMINAR un Anexo Donaci\u00F3n COMPLETO.\n\nEst\u00E1 seguro?")){
			return false;
		}
		}
	else {return false;}
}

function fncAbrirCerrarAnexo(anchor, txt) {
	if(!anchor.href.endsWith("#")) {
		if(!confirm("Est\u00E1 a punto de "+ txt +" un Anexo Donaci\u00F3n.\n\nEst\u00E1 seguro?")){
			return false;
		}
		}
	else {return false;}
}

var message = "{var_message}";
if (message != "")
	alert(message);

</script>

</html>
