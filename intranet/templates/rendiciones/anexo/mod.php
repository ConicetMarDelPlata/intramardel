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
<p align="center"><img src="cabecera.jpg" width="900" height="101" border="0" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" />Usuario: {var_User}</strong></p>
      <p>&nbsp;</p></td>
	<td align="left" valign="middle">
		<span class="TITULO">:: Anexo Donaci&oacute;n ::</span>
		
		<!--<a href="{var_anexo_open_close}"><img src="{var_path_open_close_anexo}" width="27" height="27" border="0" alt="Abrir Anexo Donaci&oacute;n"></a>-->
	</td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		{var_BarraLateral}
	</td>
	<!-- CONTENIDO PRINCIPAL -->
	
	<!-- LISTA ACTAS  -->
    <td width="722" valign="top" id="lista_actas">
		<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
			<tr>
				<td>
					<form name="modAD" method="POST" action="abm_anexo_donacion.php?id={var_iID}">
						<input type="hidden" name="op" id="op" value="5"/>
						<input type="hidden" name="toSend" id="toSend" value=""/>
						<input type="hidden" name="iID" id="iID" value="{var_iID}"/>
						
						<table border="0" cellpadding="1" cellspacing="1" class="tabla_form">
							<tr>
								<td class="modo1" style="width:190px;">ID</td>
								<td class="modo2"><input type="text" name="ID" style="width:99%" disabled value="{var_iID}"/></td>
							</tr>
							<tr>
								<td class="modo1">Subsidio</td>
								<td class="modo2"><input type="text" name="sSubsidio" style="width:99%" {var_HeaderDisabled} value="{var_sSub}"/></td>
							</tr>
							<tr>
								<td class="modo1">Titular</td>
								<td class="modo2">
									<select name="iTitular" style="width:99%" {var_HeaderDisabled}>
										<option value="0">---</option>
										<!--BEGIN BLOCK Titulares-->
										<option value="{var_ID-Tit}" {var_TitSelected}>{var_Desc-Tit}</option>
										<!--END BLOCK Titulares-->
									</select>
								</td>
							</tr>
							<tr>
								<td class="modo1">Unidad:</td>
								<td class="modo2"><!--Actual:<input type="text" style="width:90%" name="sUEActual" disabled value="{var_sUEActual}"/><br/>
									Cambiar a:-->
									<select name="iUE" id="iUE" onchange="masInfo(this.value);" {var_HeaderDisabled}>
										<option value="0" selected>---</option>
										<!--BEGIN BLOCK UE-->
										<option value="{var_ID-UE}" {var_UESelected}>{var_Desc-UE}</option>
										<!--END BLOCK UE-->
									</select>
								</td>
							</tr>
							<tr>
								<td class="modo1" id="lab-txt">Laboratorio</td>
								<td class="modo2" id="lab"><input type="text" style="width:100%" name="lab" value="{var_lab}"/></td> 
							</tr>
							<tr>
								<td class="modo1">Res. de Otorgamiento</td>
								<td class="modo2">
									<textarea name="sResOto" style="width:99%; height:54px; resize:none" {var_HeaderDisabled}>{var_sResOto}</textarea>
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<tr>
				<form name="newReg" action="abm_anexo_donacion.php" method="POST">
					<input type="hidden" name="op" id="op" value="6"/>
					<input type="hidden" name="sAction" id="sAction" value=""/>
					<input type="hidden" name="id" id="id" value="{var_iID}"/>
					<input type="hidden" name="regid" id="regid" value="{var_regID}"/>
					<input type="hidden" name="dFechaCompra" id="dFechaCompra" value=""/>
					
					<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
						<tr>
							<th colspan="12">Nuevo registro</th>
						</tr>
						<tr class="modo1">
							<td>Orden</td>
							<td>Cant.<span style="color:red">*</span></td>
							<td>Descripci&oacute;n <span style="color:red">*</span></td>
							<td>Marca</td>
							<td>Modelo</td>
						</tr>
						<tr class="modo1">				
							<td>{var_iOrden}</td>
							<td style="padding-left:0px; padding-right:0px">
								<input style="width:30px" type="text" name="iCant" id="iCant" value="{var_iCant}"/>
							</td>
							<td style="padding-left:0px; padding-right:0px">
								<input style="width:300px" type="text" name="sDesc" id="sDesc" value="{var_sDesc}"/>
							</td>
							<td style="padding-left:0px; padding-right:0px">
								<input style="width:150px" type="text" name="sMarca" value='{var_sMarca}'/>
							</td>
							<td style="padding-left:0px; padding-right:0px">
								<input style="width:150px" type="text" name="sModelo" value='{var_sModelo}'/>
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
						<tr class="modo1">
							<td>Unidad</td>
							<td>Serie/ISSN/ISBN <span style="color:red">*</span></td>
							<td style="width:80px;">F. Compra <span style="color:red">*</span></td>
							<td style="width:75px;">Importe <span style="color:red">*</span></td>
							<td colspan="4">Acciones</td>					
						</tr>
						<tr class="modo1">
							<td style="padding-left:0px; padding-right:0px">
							<!--BEGIN BLOCK TDUI-->
								<!--Actual:{var_sUINombre}<br>
							Cambiar a:-->
								<select name="iUI" id="iUI" style="width:100px">
									<option value="-1">---</option>
									<!--BEGIN BLOCK UI-->
									<option value="{var_ID-UI}" {var_sSelectedUI}>{var_Desc-UI}</option>
									<!--END BLOCK UI-->
								</select>
							<!--END BLOCK TDUI-->
							</td>
							<td style="padding-left:0px; padding-right:0px">
								<textarea style="width:235px; height:37px; resize:none" type="text" name="sSerie" id="sSerie" value='{var_sSerie}'>{var_sSerie}</textarea>
							</td>
							<td style="padding-left:0px; padding-right:0px">
								<input id="sFecha" style="border:none;background-image: url(fondo_tr01.png); width:79px" value="{var_sFCompra}" disabled/>
								<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha Inicial" id="lanzador">
							</td>
							<!--<td style="text-align:center;padding-left:0px; padding-right:0px">
								<select name="iMoneda">
									<option value="0">---</option>
									BEGIN BLOCK MONEDA
									<option value="{var_idMoneda}">{var_signoMoneda}</option>
									END BLOCK MONEDA
								</select>
							</td>-->
							<td style="text-align:right;padding-left:0px; padding-right:0px">
								$ <input style="width:70px" type="text" name="iImporte" id="iImporte" value="{var_sImporte}"/>
							</td>
							
							<td align="center">
								<!--BEGIN BLOCK ADDITEM-->
								<button type="button" name="Btn_enviar" id="Btn_enviar" onclick="enviar();" alt="Grabar datos">
									<img src="grabar_datos.png" width="16" height="16" border="0">
								</button>
								<!--END BLOCK ADDITEM-->
								<!--BEGIN BLOCK CANCELAR-->
								<button type="button" name="Btn_cancelar" id="Btn_cancelar" onclick="cancelar();" alt="Cancelar Edici&oacute;n" {var_item_alta}>
									<img src="iconos/arrow-back-1.png" width="16" height="16" border="0">
								</button>
								<!--END BLOCK CANCELAR-->
							</td>
						</tr>
					</table>
				</form>
				<br/>
				<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
					<tr>
						<th>Ord.</th>
						<th>Cant.</th>
						<th>Descripci&oacute;n</th>
						<!--<th>Marca</th>
						<th>Modelo</th>-->
						<th>Unidad</th>
						<th style="width:80px;">Serie</th>
						<th style="width:60px;">F. Compra</th>
						<th style="width:75px;">Importe</th>
						<th colspan="3">Acciones</th>
					</tr>
					<!--BEGIN BLOCK REGISTRO-->
					<tr class="modo1">				
						<td style="width:25px;word-break: break-all;">{var_Orden}</td>
						<td style="width:30px;word-break: break-all;">{var_Cant}</td>
						<td style="width:130px;word-break: break-all;">{var_Desc}</td>
						<!--<td>{var_Marca}</td>
						<td>{var_Modelo}</td>-->
						<td>{var_UINombre}</td>
						<td style="width:130px;word-break: break-all;">{var_Serie}</td>
						<td style="width:60px;word-break: break-all;">{var_FCompra}</td>
						<td style="width:75px;text-align:right;">{var_Importe}</td>
						
						<td style="width:20px;" align="center"><font color="#333333"><input type="checkbox" name="toSend" value="{var_itemID}" {var_RegSelected} alt="Enviar Registro" {var_TrDisabled}/></td>
						<td style="width:20px;" align="center"><font color="#333333"><a href="{var_LinkB}" onclick="return preguntar(this);" {var_TrDisabled}><img src="{var_ImgB}" width="16" border="0" alt="Borrar Registro"></a></td>
						<td style="width:20px;" align="center"><font color="#333333"><a href="{var_LinkM}" {var_TrDisabled}><img src="{var_ImgM}" width="16" border="0" alt="Modificar Registro"></a></td>
						<!--<td align="center"><font color="#333333"><a href="{var_LinkV}"><img src="acrobat.png" width="16" border="0" alt="Ver Registro"></a></td>-->
					</tr>
					<!--END BLOCK REGISTRO-->
					<tr>
						<th colspan="6" style="text-align:right;">
							TOTAL &nbsp;
						</th>
						<th style="text-align:right;">
							${var_Total}
						</th>
						<th colspan="3" style="text-align:right;">
						</th>
					</tr>
					<tr>
						<!-- GUARDAR GENERAL -->
						<td colspan="12" align="center">
							<div>
								<button type="button" name="Btn_enviar" id="Btn_enviar" onclick="enviar1();" alt="Grabar datos">
									<img src="grabar_datos.png" width="30" height="30" border="0">
								</button>
								<button type="button" name="Btn_volver" id="Btn_volver" onclick="window.open('lista_anexo_donacion.php','_self');" alt="Volver">
									<img src="iconos/arrow-back-1.png" width="30" height="30" border="0">
								</button>
							</div>				
						</td>
					</tr>
				</table>				
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
	Calendar.setup({ 
		inputField     :    "sFecha",     // id del campo de texto 
		ifFormat     :     "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
		button     :    "lanzador"     // el id del bot&oacute;n que lanzar&aacute; el calendario 
	});
	
	function enviar(){
		$("#dFechaCompra").val($("#sFecha").val());
		var Cant = $("#iCant").val();
		var Desc = $("#sDesc").val();
		var Serie = $("#sSerie").val();
		var Fecha = $("#sFecha").val();
		var Importe = $("#iImporte").val();
		var UI = parseInt($("#iUI").val());
		var UE = parseInt($("#iUE").val());
		var regId = parseInt($("#regid").val());
		var bError = false;
		
		//alert(UI+" - "+UE);
		if(UI === 0){
			if(UE == 13){
				bError = true;
			}
		}
		
		if(Cant && Desc && Serie && Fecha && Importe && !isNaN(Importe) && !bError){
			if(regId){
				$("#sAction").val("modReg");
				$("#op").val("");
			}
			document.forms.newReg.submit();
		}else{
			//alert("Datos obligatorios incompletos.\n");
			if(!Cant){
				$("#iCant").css("border","1px solid red");
			}else{
				$("#iCant").css("border","");
			}
			
			if(!Desc){
				$("#sDesc").css("border","1px solid red");
			}else{
				$("#sDesc").css("border","");
			}
			
			if(!Serie){
				$("#sSerie").css("border","1px solid red");
			}else{
				$("#sSerie").css("border","");
			}
			
			if(!Fecha){
				$("#sFecha").css("border","1px solid red");
			}else{
				$("#sFecha").css("border","none");
			}
			
			if(!Importe || isNaN(Importe)){
				$("#iImporte").css("border","1px solid red");
			}else{
				$("#iImporte").css("border","");
			}
			
			if(bError){
				$("#iUI").css("border","1px solid red");
			}else{
				$("#iUI").css("border","none");
			}

			return false;
		}
	}

	function cancelar(){
		window.open("abm_anexo_donacion.php?op=7&id="+$("#id").val(),"_self");
	}

	function enviar1(){
		var arrID= new Array();
		var arrVal= new Array();
		$('input[type=checkbox]').each(function(){
			//if($(this).attr("checked") && !$(this).attr("disabled")){
				arrID.push($(this).val()); 				//ID
				arrID.push($(this).attr("checked")); 	//Valor
				arrID.push($(this).attr("disabled")); 	//Estado
			//}
		});
		$("#toSend").val(arrID);
		//alert(arrID);
		document.forms.modAD.submit();
	}
	
	function preguntar(obj){
		var sHREF = obj.href;
		var iLEN = parseInt(obj.href.length);
		sHREF = sHREF.substring((iLEN-1),iLEN);
		
		if(sHREF != "#"){
			if(confirm("Est\u00E1 seguro de eliminar este registro?")){
			}else{
				return false;
			}
		}
	}
</script>	
</html>
