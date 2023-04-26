<?php
//include "seguridad_bd.php";
require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');
include "seguridad_bd.php";

$sMes = array("0","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");	
		exit();
	}
	
	global $autenticado, $nombre_usuario, $contrasenia, $bd, $cnx;
	
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia = $_SESSION["contrasenia"];
	
	$sesion = NULL;	
	
	$bd = new Bd;
	$cnx = $bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_DON');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],7,''); //7=Anexo donacion
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'escudo_arg.jpg';
        $logo_file = K_PATH_IMAGES.'conicet2.jpg';

		
				$this->SetDrawColor(0);
				//$this->Rect(5,5,285,24,'D',array(255,255,255));
				$this->Rect(5, 5, 285, 24, 'D');
        $this->Image($image_file, 10, 10, 8, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
				$this->Ln(6);
				$this->Cell(10);
        // Set font
        $this->SetFont('helvetica', '', 9);
        // Title
        $this->Cell(10, 7, 'MINISTERIO DE CIENCIA, TECNOLOGÍA E INNOVACIÓN PRODUCTIVA', 0, 1, 'L', 0, '', 0, false, 'M', 'm');

		$this->Cell(10);
		// Set font
        $this->SetFont('helvetica', 'B', 9);
        // Sub Title
        $this->Cell(10, 5, 'CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS', 0, false, 'L', 0, '', 0, false, 'M', 'm');
        //$this->Image($logo_file, 10, 8, 40, '', 'JPG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
	//Linea orginal de logo antes de los 10 años
	$this->Image($logo_file, 10, 10, 20, '', 'JPG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'R', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sebastian Salerno');
$pdf->SetTitle('Envio de items de AD');
$pdf->SetSubject('CCT-CONICET-MDP');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
//$pdf->SetHeaderData("escudo_arg.jpg", 8, "MINISTERIO DE CIENCIA, TECNOLOGÍA E INNOVACIÓN PRODUCTIVA","CONSEJO NACIONAL DE INVESTIGACIONES CIENTÍFICAS Y TÉCNICAS");
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ------------------------------------------------------------------------------------------
// -- SI EL ANEXO SE ENCUENTRA ABIERTO DEBE MOSTRAR LOS ITEMS ENVIADOS Y NO, CON SU ESTADO --
// -- SI EL ANEXO SE ENCUANTRA CERRADO DEBE MOSTRAR LA PLANILLA PARA PRESENTAR             --
// ------------------------------------------------------------------------------------------

// set font
$pdf->SetFont('Helvetica', 'B', 12);
$iID = (int)$_REQUEST['id'];
$vAnexoHeader 	= $bd->getAnexoHeader($iID);
if($vAnexoHeader['estado'] == 1){ 	// Abierto
	$vData		= $bd->getAnexoItems($iID);
}else{
	$vData		= $bd->getAnexoItems($iID,'fecha_compra');
}
//$vUE 			= $bd->getUE($vAnexoHeader['ue']);
//$vUEAdm			= $bd->getUE(13); //La unidad de administracion es siempre
//la 13 CCT CONICET Mar del Plata fija, ver email de mib del 31 oct 2017
$vTitular		= $bd->getTitular($vAnexoHeader['titular']);

// add a page
$pdf->AddPage('L');
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
if($vAnexoHeader['estado'] == 1){ 	// Abierto
	$pdf->SetTextColor(0);
	$pdf->SetAlpha(0.07);

	// Start Transformation
	$pdf->SetFont('Helvetica', 'B', 45); 
	$pdf->StartTransform();
	// Rotate 20 degrees counter-clockwise centered by (70,110) which is the lower left corner of the rectangle
	$pdf->Rotate(30, 200, 200);
	$pdf->Text(80, 80, 'ANEXO DONACIÓN ABIERTO');
	// Stop Transformation
	$pdf->StopTransform();

	$pdf->SetTextColor(0);
	$pdf->SetFont('Helvetica', 'B', 12);
	$pdf->SetY(10);
	$pdf->SetX(10);
	$pdf->SetAlpha(1); 
}

$pdf->Ln(14);
if($vAnexoHeader['estado'] == 1){ 	// Abierto
	$bSumarizar = false;
	$pdf->Write(0, 'Anexo Donación - ABIERTO', '', 0, 'C', true, 0, false, false, 0);
}else{								// Cerrado
	$bSumarizar = true;
	$pdf->Write(0, 'Anexo Donación', '', 0, 'C', true, 0, false, false, 0);
}
$iFontSize = 8;

$pdf->SetFont('Helvetica', '', $iFontSize);
$pdf->Ln();
$pdf->Cell(60, 5, 'Donación de bienes inventariables al CONICET (Resolución 2667/99).', 0, 1, 'L');
$pdf->Ln(3);
$pdf->SetFillColor(192,192,192);
$wTitulo = 40;
$wDatos  = 80;
$w = 0;

$pdf->Cell($wTitulo, 5, 'NÚMERO ID', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', $iFontSize);
$pdf->Cell($w, 5, $vAnexoHeader['id'], 0, 0, 'L');
$pdf->Cell(20);
$pdf->SetFont('Helvetica', '', $iFontSize);
$pdf->Cell($wTitulo, 5, 'TIPO DE RENDICIÓN', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', $iFontSize);
$pdf->Cell($w, 5, $vAnexoHeader['subsidio'], 0, 0, 'L');
$pdf->Ln(4);


$pdf->SetFont('Helvetica', '', $iFontSize);
$pdf->Cell($wTitulo, 5, 'TITULAR', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', $iFontSize);
	$tempTitular = ($vTitular['apellido']).", ".($vTitular['nombre']);
	if ($vTitular['dni'] != "") $tempTitular = $tempTitular." (".($vTitular['dni']).")";
$pdf->Cell($wDatos, 5, $tempTitular, 0, 0, 'L');
$pdf->Cell(20);
$pdf->SetFont('Helvetica', '', $iFontSize);
$pdf->Cell($wTitulo, 5, 'FECHA DE APERTURA', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', $iFontSize);
$pdf->Cell($wDatos, 5, convertir_fecha($vAnexoHeader['fecha']), 0, 0, 'L');
$pdf->Ln(4);

$pdf->SetFont('Helvetica', '', $iFontSize);
$pdf->Cell($wTitulo, 5, 'UNIDAD', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', $iFontSize);
$pdf->Cell($wDatos, 5, $vAnexoHeader['ue_inv_nombre'], 0, 0, 'L');
$pdf->Cell(20);
$pdf->SetFont('Helvetica', '', $iFontSize);
$pdf->Cell($wTitulo, 5, 'RES. DE OTORGAMIENTO', 0, 0, 'L');
$pdf->SetFont('Helvetica', 'B', $iFontSize);
$pdf->MultiCell(90, 5, $vAnexoHeader['res_oto'], 0,'L', 0, 0);
$pdf->Ln(4);
$pdf->Ln(4);

$pdf->Ln(7);
$pdf->SetFont('Helvetica', '', 8);
$pdf->MultiCell(275, 5, 'Por el presente dono al CONICET los siguientes bienes inventariables, los que solicito queden afectados al uso de la Unidad indicada precedentemente.', 0,'L', 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Helvetica', '', 8);
$tbl_header='
<style>
table {
   border-collapse: collapse;
   border-spacing: 0;
   padding-bottom: 2px;
   padding-left: 5px;
   padding-top: 2px;
}
tr {
   padding-bottom: 5px;
   padding-left: 0px;
   padding-top: 2px;
   border:1px;
   border-color:#000000;
}
th {
   background-color: #AAAAAA;
   color: #000000;
   font-family: Helvetica;
   font-weight: bold;
   padding-bottom: 2px;
   padding-top: 2px;
   text-align: left;
   border: 1px solid #CCCCCC;
}
td {
   color: #000000;
   padding-bottom: 2px;
   padding-top: 2px;
   border: 1px solid #CCCCCC;
}
</style>

<table>
	<tr>
		<th style="width:50px;text-align:center">Ord.</th>
		<th style="width:50px;text-align:center">Cant.</th>
		<th style="width:220px">Descripción</th>
		<th style="width:100px">Marca</th>
		<th style="width:100px">Modelo</th>
		<th style="width:150px">Serie</th>
		<th style="width:50px">Unidad</th>
		<th style="width:70px">F.Compra</th>
		<th style="width:80px;">Importe</th>
		<th style="width:80px;">Estado</th>
	</tr>';

$tbl_footer="</table>";

if($vData){
	$i=0;
	$tbl_content="";
	$fTotal = 0;
	foreach($vData as $Item){
		$i++;
		if($i%2==0){
			$sStyle = 'style="background-color:#DDDDDD"';
		}else{
			$sStyle = '';
		} 
		$sEstado = ($Item['enviado'])?'Enviado':'Pendiente';

		if((int)$Item['ui'] !== 0){
			$sUE = substr($Item['ui_nombre'],0,3);
		}else{
			$sUE = substr($vAnexoHeader['ue_inv_nombre'],0,3);
		}
					
		$tbl_content.='
		<tr '.$sStyle.'>
			<td align="center">'.$i.'</td>
			<td align="center">'.$Item['cant'].'</td>
			<td>'.$Item['descripcion'].'</td>
			<td>'.$Item['marca'].'</td>
			<td>'.$Item['modelo'].'</td>
			<td>'.$Item['serie'].'</td>
			<td>'.$sUE.'</td>
			<td>'.convertir_fecha($Item['fecha_compra']).'</td>
			<td style="text-align:right;">'.$bd->getCoinSymbol((int)$Item['moneda']).' '.number_format ($Item['importe'] , 2, ',', '.').'</td>
			<td>'.$sEstado.'</td>
			<!--<hr>-->
		</tr>';
		$fTotal += $Item['importe'];
	}
	if($bSumarizar){
		$tbl_content .='<tr>
			<th colspan="8" align="right">TOTAL</th>
			<th align="right">'.$bd->getCoinSymbol((int)$Item['moneda']).' '.number_format ($fTotal , 2, ',', '.').'</th>
			<th>&nbsp;</th>
		</tr>';
	}
	$fecha_pie = explode("-",convertir_fecha($vAnexoHeader['fecha_cierre']??'0000-00-00'));
	$tbl_content .='
	<tr><td colspan="8" style="border:none;"><br/></td></tr>
	<tr>
		<td colspan="8" style="border:none;">
			Mar del Plata, '.$fecha_pie[0].' de '.$sMes[(int)$fecha_pie[1]].' de '.$fecha_pie[2].'
		</td>
	</tr>
	';
	
	$pdf->writeHTML($tbl_header . ($tbl_content) . $tbl_footer, true, false, true, false, '');
	
	$pdf->Ln(10);
	if($pdf->getY() > 177.12){
		$pdf->AddPage();
		$pdf->Ln(20);
	}
	$pdf->Cell(285/3, 1, '------------------------------------------', 0, 0, 'C');
	$pdf->Cell(285/3, 1, '------------------------------------------', 0, 0, 'C');
	$pdf->Cell(285/3, 1, '------------------------------------------', 0, 0, 'C');
	$pdf->Ln(4);
	$pdf->Cell(285/3, 3, 'Firma Administrador', 0, 0, 'C');
	$pdf->Cell(285/3, 3, 'Firma Coordinador', 0, 0, 'C');
	$pdf->Cell(285/3, 3, 'Firma Titular', 0, 1, 'C');
	$pdf->Ln(4);

	$pdf->Cell(285, 3, 'Domicilio Laboral Unidad: '.$vAnexoHeader['ue_inv_domicilio'], 0, 1, 'L');
	$pdf->Cell(285, 3, 'Domicilio Laboral Administrador: '.$vAnexoHeader['ue_adm_domicilio'], 0, 1, 'L');
	//Moreno 3527, piso 3, Mar del Plata - Buenos Aires
	
	
	//echo $tbl_header . $tbl_content . $tbl_footer;
}

$pdf->Output('anexo_donacion.pdf');
?>
