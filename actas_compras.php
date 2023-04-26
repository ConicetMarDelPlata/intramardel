<?php
	include "includes/header.php";
	include "seguridad_bd.php";
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
	$cnx = $bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_AC');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],5,''); //5=Actas de compras
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}
	
	if (isset($_GET['sAction'])) {
		if($_GET['sAction'] == 'PDF'){
			include_once("actas_compras_pdf.php?id=".$_GET['id']);
			//echo "$iProvSel $iP1 $iP2 $iP3";
			//echo "<pre>";
			//var_dump($vActData);
		}
	}
	if (isset($_POST['sAction'])) {	
		if($_POST['sAction'] == 'addActa'){
			$bd->insertAct($_POST,$cnx, $_SESSION["id_usuario"]);
		}
		if($_POST['sAction'] == 'editActa'){
			//echo "<pre>";
			//var_dump($_REQUEST['sel_op_1']." - ".$_REQUEST['sel_op_2']." - ".$_REQUEST['sel_op_3']." - ".$_REQUEST['sel_op_4']);exit;
			//phpinfo();
			$bd->updateAct($_POST,$cnx, $_SESSION["id_usuario"]);
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
   
   <?php
		//if($userData['ac_alta']){
		$acAlta = $bd->checkPerm($_SESSION["id_usuario"],5,'alta');
		$acBaja = $bd->checkPerm($_SESSION["id_usuario"],5,'baja');
		$acModificacion = $bd->checkPerm($_SESSION["id_usuario"],5,'modificacion');
		if ($acAlta){
			echo '<td align="left" valign="middle"><span class="TITULO">:: Actas de Compra ::</span><a href="#" onclick="return newAct();"><img src="agregar.png" width="25" height="25" border="0"></a></td>';
		}else
			echo '<td align="left" valign="middle"><span class="TITULO">:: Actas de Compra ::</span><a href="#"><img src="iconos_grises/agregarg.png" width="25" height="25" border="0"></a></td>';
	?>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
	<!-- CONTENIDO PRINCIPAL -->
	
	<!-- LISTA ACTAS  -->
    <td width="722" valign="top" id="lista_actas">
		<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center">
			<tr>
				<th>Acta</th>
				<th>Fecha</th>
				<th>Procedimiento</th>
				<th>Proveedores</th>
				<th>Firmante</th>
				<th colspan="3">Acciones</th>
			</tr>
			<?php
				$vData = $bd->getActs($cnx);
				if($vData){
					foreach($vData as $Item){
						echo "<tr class=\"modo1\">				
							<td>".$Item['sNum']."</td>
							<td>".$Item['fecha']."</td>
							<td>".$Item['procedimiento']."</td>
							<td>".$Item['proveedores']."</td>
							<td>".$Item['firmante']."</td>
							<td align=\"center\"><font color=\"#333333\"><a href=\"".$bd->getLink(2,$acBaja,$Item['id'])."\"><img src=\"".$bd->getIcon(2,$acBaja)."\" width=\"16\" border=\"0\" alt=\"Borrar Registro\"></a></td>
							<td align=\"center\"><font color=\"#333333\"><a href=\"".$bd->getLink(3,$acModificacion,$Item['id'])."\"><img src=\"".$bd->getIcon(3,$acModificacion)."\" width=\"16\" border=\"0\" alt=\"Modificar Registro\"></a></td>
							<td align=\"center\"><font color=\"#333333\"><a href=\"".$bd->getLink(5,$acModificacion,$Item['id'])."\"><img src=\"acrobat.png\" width=\"16\" border=\"0\" alt=\"Ver Registro\"></a></td>
						</tr>";			
					}
				}
			?>
		</table>
	</td>
	<!-- FIN LISTA ACTAS  -->
	
	<!-- NUEVA ACTA  -->
    <td width="722" valign="top" style="display:none;" id="nueva_acta">
		<div style="margin: 0 auto; padding: 20px 20px 40px;">
			<ul class="tabs" data-persist="true">
				<li id='v1'><a href="#view1">Datos generales</a></li>
				<li id='v2'><a href="#view2">Items</a></li>
				<li id='v3'><a href="#view3">Finalizar</a></li>
			</ul>
			<form name="ac-new-item-form" method="POST" action="actas_compras.php">
				<input type="hidden" name="sAction" value="addActa"/>
				<input type="hidden" name="orden" id="orden" value="0"/>
				<input type="hidden" name="ac_p1_subt" id="ac_p1_subt" value="0"/>
				<input type="hidden" name="ac_p2_subt" id="ac_p2_subt" value="0"/>
				<input type="hidden" name="ac_p3_subt" id="ac_p3_subt" value="0"/>
			
				<!-- TAB 1 Datos generales de la Orden de Compra -->
				<div class="tabcontents" style="padding:0px;">
					<div id="view1">
						<table border="0" cellpadding="1" cellspacing="1" class="tabla_form" style="margin:10px;" align="center">
							<tr>
								<td class="modo1" style="width:190px;">
									Procedimiento de compra
								</td>
								<td class="modo2" style="text-align:left">
									<select type="text" name="ac_procedimiento" id="ac_procedimiento" style="width:460px">
										<?php
											$vData = $bd->getProcedures();
											foreach($vData as $Item){
												echo "<option value='".$Item['id_procedimiento']."'>".$Item['descripcion']."</option>";
											}
										?>
									</select>									
								</td>
							</tr>
							<tr>
								<td class="modo1" style="width:190px;">
									Firmante
								</td>
								<td class="modo2" style="text-align:left">
									<select type="text" name="ac_firmante" id="ac_firmante">
										<?php
											$vData = $bd->getSignatures(0);
											foreach($vData as $Item){
												echo "<option value='".$Item['id_firmante']."'>".$Item['titulo_apellido_nombre']."</option>";
											}
										?>
									</select>								
								</td>
							</tr>
							<tr>
								<td class="modo1" style="width:190px;">
									Moneda
								</td>
								<td class="modo2" style="text-align:left">
									<select type="text" name="ac_moneda" id="ac_moneda" style="width:260px">
										<?php
											$vData = $bd->getCoins();
											foreach($vData as $Item){
												//Default pesos argentinos
												if(1 == (int)$Item['id_moneda']){
													$sSelected = "selected";
												}else{
													$sSelected = "";
												}
												echo "<option value='".$Item['id_moneda']."' $sSelected>".$Item['descripcion']."</option>";
											}
										?>
									</select>								
								</td>
							</tr>
							<tr>
								<td class="modo1" style="width:190px;">
									Objeto
								</td>
								<td class="modo2" style="text-align:left">
									<textarea name="ac_objeto" id="ac_objeto" style="width:360px" rows="4"></textarea>								
								</td>
							</tr>
							<tr>
								<td class="modo1" style="width:190px;">
									Proveedor 1 
								</td>
								<td class="modo2" style="text-align:left">
									<select type="text" name="ac_p1" id="ac_p1" onChange="copyProv(this);" style="width:360px;">
										<option value='-1'>&nbsp;</option>
										<?php
											$vData = $bd->getSuppliers(0);									
											foreach($vData as $Item){
												echo "<option value='".$Item['id_proveedor']."'>".$Item['razon_social']."</option>";
											}
										?>
									</select>									
								</td>
							</tr>
							<tr>
								<td class="modo1" style="width:190px;">
									Proveedor 2
								</td>
								<td class="modo2" style="text-align:left">
									<select type="text" name="ac_p2" id="ac_p2" onChange="copyProv(this);" style="width:360px;">
										<option value='-1'>&nbsp;</option>
										<?php
											foreach($vData as $Item){
												echo "<option value='".$Item['id_proveedor']."'>".$Item['razon_social']."</option>";
											}
										?>
									</select>								
								</td>
							</tr>
							<tr>
								<td class="modo1" style="width:190px;">
									Proveedor 3
								</td>
								<td class="modo2" style="text-align:left">
									<select type="text" name="ac_p3" id="ac_p3" onChange="copyProv(this);" style="width:360px;">
										<option value='-1'>&nbsp;</option>
										<?php
											foreach($vData as $Item){
												echo "<option value='".$Item['id_proveedor']."' rel='".$Item['razon_social']."'>".$Item['razon_social']."</option>";
											}
										?>
									</select>								
								</td>
							</tr>
						</table>

						<p style="text-align:center; margin-top:20px;">
							<button type="button" onclick="checkData(document.forms['ac-new-item-form'],'1');" name="Btn_enviar" id="Btn_enviar" alt="Siguiente">
								<img src="iconos/siguiente.png" width="25" heigth="20" border="0">
							</button>
							<!--<button type="button" onclick="cancelAct();" alt="Cancelar">
								<img src="iconos/arrow-back-1.png" width="25" heigth="20" border="0">
							</button>-->
						</p>
					</div>
					<!-- TAB 2 Items de la Orden de Compra-->
					<div id="view2">
						<table border="0" cellpadding="1" cellspacing="1" class="tabla" align="center" style="margin-top:10px;" >
							<tr>
								<th colspan="12">Nuevo registro</th>
							</tr>
							<tr class="modo1">
								<td>Descripci&oacute;n <span style="color:red">*</span></td>
								<td>Unidad<span style="color:red">*</span></td>
								<td>Cant.<span style="color:red">*</span></td>
								<td id="ac_new_item_p1_td1">Prov. 1<span style="color:red">*</span></td>
								<td id="ac_new_item_p2_td1">Prov. 2<span style="color:red">*</span></td>
								<td id="ac_new_item_p3_td1" style="display:none">Prov. 3<span style="color:red">*</span></td>
								<td>Acciones</td>
							</tr>
							<tr class="modo1">
								<input type='hidden' id="ac-edititem-id" value='-1'/>				
								<td style="padding-left:0px; padding-right:0px">
									<input type="text" value="" name="ac-newitem-descripcion" id="ac-newitem-descripcion" style="width:260px"/>
								</td>
								<td style="padding-left:0px; padding-right:0px">
									<select name="ac-newitem-unidad" id="ac-newitem-unidad">
										<option value="UN">Unidad</option>
										<option value="KG">Kilogramo</option>
										<option value="LT">Litros</option>
										<option value="MT">Metros</option>
									</select>
								</td>
								<td style="padding-left:0px; padding-right:0px">
									<input type="text" style="width:70px" onkeyup="calcAllSubT();"  onBlur="calcAllSubT();" onClick="calcAllSubT();" value="" name="ac-newitem-cant" id="ac-newitem-cant"/>
								</td>
								<td style="padding-left:0px; padding-right:0px">
									<input type="text" style="width:70px" onkeyup="calcSubT(this);"  onBlur="calcSubT(this);" onClick="calcSubT(this);" value="" name="ac-newitem-p1-pu" id="ac-newitem-p1-pu"/>
									<br/>Subt.: <label id="ac-p1-subt"></label>									
								</td>
								<td style="padding-left:0px; padding-right:0px">
									<input type="text" style="width:70px" onkeyup="calcSubT(this);"  onBlur="calcSubT(this);" onClick="calcSubT(this);" value="" name="ac-newitem-p2-pu" id="ac-newitem-p2-pu"/>
									<br/>Subt.: <label id="ac-p2-subt"></label>									
								</td>
								<td id="ac_new_item_p3_td2" style="padding-left:0px;padding-right:0px;display:none">
									<input type="text" style="width:70px" onkeyup="calcSubT(this);"  onBlur="calcSubT(this);" onClick="calcSubT(this);" value="" name="ac-newitem-p3-pu" id="ac-newitem-p3-pu"/>
									<br/>Subt.: <label id="ac-p3-subt"></label>									
								</td>
								<td style="height:30px;text-align:center">
									<button type="button" name="Btn_enviar" id="Btn_enviar" onclick="agregarOeditarItem();" alt="Grabar item">
										<img src="grabar_datos.png" width="16" height="16" border="0">
									</button>
								</td>
							</tr>
						</table>
						<br/>
						<table class="tabla" id="ac_items">
							<tr>
								<td class="libre" colspan="4" style="border:none; background-color:#FFFFFF;">&nbsp;</td>
								<th id="ac_p1_t" colspan="2" style="text-align:center">Prov. 1</th>
								<th id="ac_p2_t" colspan="2" style="text-align:center">Prov. 2</th>
								<th id="ac_p3_t" colspan="2" style="text-align:center;display:none">Prov. 3</th>						
							</tr>
							<tr>
								<th width="20">Ord.</th>
								<th width="700">Descripci&oacute;n</th>
								<th width="20">Un.</th>
								<th width="20">Cant.</th>
								<th width="120">P. Unit.</th>
								<th width="290">Subtotal</th>
								<th width="120">P. Unit.</th>
								<th width="290">Subtotal</th>
								<th id="ac_p3_pu_th" style="display:none" width="120">P. Unit.</th>
								<th id="ac_p3_st_th" style="display:none" width="290">Subtotal</th>
								<!--<td style="border:none"><img src="agregar.png" style="width:16px;cursor: pointer;" onclick="newItem('1');"/></td>-->
							</tr>
						</table>
						<table class="tabla">
							<tr>
								<td align="center" colspan="10">
									<button type="button" onclick="checkData(document.forms['ac-new-item-form'],'2');" name="Btn_enviar" id="Btn_enviar" alt="Siguiente">
										<img src="iconos/siguiente.png" width="25" heigth="20" border="0">
									</button>
									<!--<button type="button" onclick="cancelAct();" alt="Cancelar">
										<img src="iconos/arrow-back-1.png" width="25" heigth="20" border="0">
									</button>-->
								</td>
							</tr>
						</table>
					</div>
					<!--TAB 3 Cierre Orden Compra-->
					<div id="view3">
						<input type="hidden" name="ac_p1_tot" id="ac_p1_tot" value="0"/>
						<input type="hidden" name="ac_p2_tot" id="ac_p2_tot" value="0"/>
						<input type="hidden" name="ac_p3_tot" id="ac_p3_tot" value="0"/>
						<table class="tabla" style="margin-top:20px;">
							<tr>
								<th>
									Proveedor
								</th>
								<th>
									Total
								</th>
								<!--<th style="width:10px">
									Sel.
								</th>-->
							</tr>
							<tr id="ac_p1_tr" class="modo1">
								<td id="ac_p1_3" style="text-align:left;font-weight:bold;">
									Proveedor 1
								</td>
								<td id="ac_p1_total" style="text-align:right;">
									0.00
								</td>
								<!--<td>
									<input name="ac_prov_sel" id="ac_p1_check" type="radio"/>
								</td>-->
							</tr>
							<tr id="ac_p2_tr" class="modo1">
								<td id="ac_p2_3" style="text-align:left;font-weight:bold;">
									Proveedor 2
								</td>
								<td id="ac_p2_total" style="text-align:right;">
									0.00
								</td>
								<!--<td>
									<input name="ac_prov_sel" id="ac_p2_check" type="radio"/>
								</td>-->
							</tr>
							<tr id="ac_p3_tr" class="modo1" style="display:none">
								<td id="ac_p3_3" style="text-align:left;font-weight:bold;">
									Proveedor 3
								</td>
								<td id="ac_p3_total" style="text-align:right;">
									0.00
								</td>
								<!--<td>
									<input name="ac_prov_sel" value="" id="ac_p3_check" type="radio"/>
								</td>-->
							</tr>
						</table>
						<table class="tabla" style="margin-top:20px;" >
							<tr>
								<th>
									Observaciones
								</th>
							</tr>
							<tr class="modo1">
								<td>
									<textarea name="ac_comentario" style="width:500px;height:200px">Ninguno</textarea>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="2">
									<button type="button" onclick="checkData(document.forms['ac-new-item-form'],'3');" name="Btn_enviar" id="Btn_enviar" alt="Grabar datos">
										<img src="grabar_datos.png" width="25" heigth="20" border="0">
									</button>
									<button type="button" onclick="cancelAct();" alt="Cancelar">
										<img src="iconos/arrow-back-1.png" width="25" heigth="20" border="0">
									</button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				
			</form>
		</div>	
    </td>
	<!-- FIN NUEVA ACTA  -->

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
</html>
