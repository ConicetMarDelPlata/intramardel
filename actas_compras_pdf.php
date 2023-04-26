<?php
include "includes/header.php";
include "seguridad_bd.php";
include_once("includes/functions.php");
require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');

$bd = new Bd;
$cnx = $bd->AbrirBd();
//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
$vActHeader = $bd->getAct($_GET['id'],$cnx);
$vActData = $bd->getActData($_GET['id'],$cnx);

$vAux = $bd->consultar_usuario((int)$vActData[0]['auth']);
$sAuth = $vAux['apellido'].", ".$vAux['nombre'];
$iProvSel = (int)$vActData[0]['prov_sel'];
$iP1 = (int)$vActData[0]['p1'];
$iP2 = (int)$vActData[0]['p2'];
$iP3 = (int)$vActData[0]['p3'];



class MYPDF extends TCPDF {
    public function Footer() {
		global $sAuth;
       // $image_file = "img/bg_bottom_releve.jpg";
        //$this->Image($image_file, 11, 241, 189, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->SetY(-15);
        $this->SetFont('helvetica', 'N', 6);
		$this->Cell(0, 5, 'Confeccionada por: ' . ($sAuth), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 6, $this->getAliasNumPage().' / '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');		
        //$this->Cell(0, 5, date("m/d/Y H\hi:s"), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Actas Compras');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
$pdf->SetHeaderData(PDF_HEADER_LOGO, 180);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
//echo PDF_MARGIN_TOP;
$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 20); //PDF_MARGIN_BOTTOM

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('Helvetica', 'B', 10);

// add a page
$pdf->AddPage();
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

$pdf->SetFont('Helvetica', 'C', 10);

//echo $vActHeader['fecha'];exit;

$fecha = explode('-', $vActHeader['fecha']);
$anio = $fecha[0];
$mes = $fecha[1];
if ($mes < 10){
	$mes = substr($mes, 1,1);
}
$dia = $fecha[2];
$meses = array(
    1 => "Enero",
    2 => "Febrero",
    3 => "Marzo",
    4 => "Abril",
    5 => "Mayo",
    6 => "Junio",
    7 => "Julio",
    8 => "Agosto",
    9 => "Septiembre",
    10 => "Octubre",
    11 => "Noviembre",
    12 => "Diciembre",							
);

//$pdf->Write(0, 'Mar del Plata, '. $_GET['fecha'] . ' de  ' . $_GET['fecha'] . ' de ' . $_GET['fecha'], '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, 'Mar del Plata, '. $dia . ' de ' . $meses[$mes] . ' de ' . $anio, '', 0, 'R', true, 0, false, false, 0);
//$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Write(10, 'ACTA DE COMPRA '. $vActHeader['sNum'], '', 0, 'C', true, 0, false, false, 0);
$pdf->SetLineWidth(0.05);
$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());
$pdf->Write(7, '', '', 0, 'C', true, 0, false, false, 0);
//------------------------------------------------------------------------------------------------------
// Procedimiento y objeto de compra
$pdf->Write(7, 'Procedimiento de compra: ','' , 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Write(7, ($vActHeader['procedimiento']),'' , 0, 'L',true);//($vActHeader['procedimiento'])
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Write(7, 'Objeto de compra: ','' , 0, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Write(7, ($vActHeader['objeto']),'' , 0, 'L',true);
$pdf->Write(7, 'Por medio de la presente se detallan los presupuestos presentados por los proveedores del rubro','' , 0, 'L',true);
$pdf->Ln();
//------------------------------------------------------------------------------------------------------
// Tabla con datos
//------------------------------------------------------------------------------------------------------
// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(0, 0, 0, 0);

// set color for background
//$pdf->SetFillColor(176, 176, 217);
$pdf->SetFillColor(204, 204, 204);

// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
//$pdf->MultiCell(55, 5, '[JUSTIFY] '.$txt."\n", 1, 'J', 1, 2, '' ,'', true);

$pdf->SetFont('Helvetica', 'B', 8);
//------------------------------------------------------------------------------------------------------
//Proveedores
//------------------------------------------------------------------------------------------------------
$vProveedores = textFormatProv($vActHeader['P1'],$vActHeader['P2'],$vActHeader['P3'], $pdf);
$iH = 3 * (int)$vProveedores[0];

$pdf->MultiCell(65, $iH, "", 1, 'L', 1, 0, '', '', true);
$pdf->MultiCell(38, $iH, $vProveedores[1], 1, 'C', 1, 0, '', '', true);
if($vProveedores[3]){
	$pdf->MultiCell(38, $iH, $vProveedores[2], 1, 'C', 1, 0, '', '', true);
	$pdf->MultiCell(38, $iH, $vProveedores[3], 1, 'C', 1, 1, '', '', true);
}else{
	$pdf->MultiCell(38, $iH, $vProveedores[2], 1, 'C', 1, 1, '', '', true);
}
//------------------------------------------------------------------------------------------------------
//Headers
//------------------------------------------------------------------------------------------------------
$pdf->setCellPaddings(1, 1, 1, 0);

$pdf->MultiCell(8, 5, 'Ord. ', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell(37, 5, 'Descripción. ', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell(8, 5, 'Un. ', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell(12, 5, 'Cant. ', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell(15, 5, 'P.U. ', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell(23, 5, 'Sub Total. ', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell(15, 5, 'P.U. ', 1, 'C', 1, 0, '', '', true);
if($vProveedores[3]){
	$pdf->MultiCell(23, 5, 'Sub Total. ', 1, 'C', 1, 0, '', '', true);
	$pdf->MultiCell(15, 5, 'P.U. ', 1, 'C', 1, 0, '', '', true);
	$pdf->MultiCell(23, 5, 'Sub Total. ', 1, 'C', 1, 1, '', '', true);
}else{
	$pdf->MultiCell(23, 5, 'Sub Total. ', 1, 'C', 1, 1, '', '', true);
}
// set color for background
$pdf->SetFillColor(183, 242, 177);
$pdf->SetFont('Helvetica', '', 7);

//------------------------------------------------------------------------------------------------------
//Detalle
//------------------------------------------------------------------------------------------------------
$j=1;
$fSt1 = 0;
$fSt2 = 0;
$fSt3 = 0;

for($i=1;$i< count($vActData);$i++){
	$iFill1=0;
	$iFill2=0;
	$iFill3=0;
	
	if((int)$vActData[$i]['st_sel'] == 1){
		$iFill1 = 1;
		$fSt1 += (float)$vActData[$i]['p1_st'];
	}
	if((int)$vActData[$i]['st_sel'] == 2){
		$iFill2 = 1;
		$fSt2 += (float)$vActData[$i]['p2_st'];
	}
	if((int)$vActData[$i]['st_sel'] == 3){
		$iFill3 = 1;
		$fSt3 += (float)$vActData[$i]['p3_st'];
	}
	if(strlen($vActData[$i]['descripcion']) > 40){
		$sDesc = substr(($vActData[$i]['descripcion']),0,40)."...";
		$iCellH = 9;
	}else{
		$sDesc = ($vActData[$i]['descripcion']);
		if(strlen($vActData[$i]['descripcion']) > 25){
			$iCellH = 9;
		}else{
			$iCellH = 9; //5
		}
	}
	$iCellH = 5 * (int)$pdf->getNumLines($sDesc,37);
	
	$pdf->MultiCell(8, $iCellH, $i, 1, 'C', 0, 0, '', '', true,0,false,true,0,'M');
	$pdf->MultiCell(37, $iCellH, $sDesc, 1, 'C', 0, 0, '', '', true,0,false,true,0,'M');
	$pdf->MultiCell(8, $iCellH, $vActData[$i]['unidad'], 1, 'C', 0, 0, '', '', true,0,false,true,0,'M');
	$pdf->MultiCell(12, $iCellH, number_format($vActData[$i]['cant'],0,',','.'), 1, 'C', 0, 0, '', '', true,0,false,true,0,'M');
	$pdf->MultiCell(15, $iCellH, number_format($vActData[$i]['p1_pu'],2,',','.'), 1, 'C', 0, 0, '', '', true,0,false,true,0,'M');
	$pdf->MultiCell(23, $iCellH, $vActHeader['signo_moneda'] . " " . number_format($vActData[$i]['p1_st'],2,',','.'), 1, 'C', $iFill1, 0, '', '', true,0,false,true,0,'M');
	$pdf->MultiCell(15, $iCellH, number_format($vActData[$i]['p2_pu'],2,',','.'), 1, 'C', 0, 0, '', '', true,0,false,true,0,'M');
	if($vProveedores[3]){
		$pdf->MultiCell(23, $iCellH, $vActHeader['signo_moneda'] . " " . number_format($vActData[$i]['p2_st'],2,',','.'), 1, 'C', $iFill2, 0, '', '', true,0,false,true,0,'M');
		$pdf->MultiCell(15, $iCellH, number_format($vActData[$i]['p3_pu'],2,',','.'), 1, 'C', 0, 0, '', '', true,0,false,true,0,'M');
		$pdf->MultiCell(23, $iCellH, $vActHeader['signo_moneda'] . " " . number_format($vActData[$i]['p3_st'],2,',','.'), 1, 'C', $iFill3, 1, '', '', true,0,false,true,0,'M');
	}else{
		$pdf->MultiCell(23, $iCellH, $vActHeader['signo_moneda'] . " " . number_format($vActData[$i]['p2_st'],2,',','.'), 1, 'C', $iFill2, 1, '', '', true,0,false,true,0,'M');
	}
}
//------------------------------------------------------------------------------------------------------
//Total por proveedor
//------------------------------------------------------------------------------------------------------
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->MultiCell(80, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
//$pdf->MultiCell(37, 5, '', 1, 'C', 0, 0, '', '', true);
//$pdf->MultiCell(8, 5, '', 0, 'C', 0, 0, '', '', true);
//$pdf->MultiCell(12, 5, '', 0, 'C', 0, 0, '', '', true);
//$pdf->MultiCell(18, 5, '', 0, 'C', 0, 0, '', '', true);
$pdf->MultiCell(23, 5, $vActHeader['signo_moneda'] . " " . number_format($fSt1,2,',','.'), 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 5, '', 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(23, 5, $vActHeader['signo_moneda'] . " " . number_format($fSt2,2,',','.'), 1, 'C', 0, 0, '', '', true);
if($vProveedores[3]){
	$pdf->MultiCell(15, 5, '', 1, 'C', 0, 0, '', '', true);
	$pdf->MultiCell(23, 5, $vActHeader['signo_moneda'] . " " . number_format($fSt3,2,',','.'), 1, 'C', 0, 1, '', '', true);
}else{
	$pdf->MultiCell(15, 5, '', 0, 'C', 0, 1, '', '', true);
}

$pdf->Ln(5);

//------------------------------------------------------------------------------------------------------
// Observaciones
//------------------------------------------------------------------------------------------------------
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->MultiCell(30, 5, "Observaciones: ", 0, 'C', 0, 0, '', '', true);
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell(149, 15, ( $vActHeader['comentario']), 1, 'L', 0, 1, '', '', true);
$pdf->Ln(10);

//------------------------------------------------------------------------------------------------------
// Proveedores adjudicados
//------------------------------------------------------------------------------------------------------
$pdf->MultiCell(0, 5, "Se deja constancia que la/s oferta/s elegida/s corresponde/n al/los proveedor/es:", 0, 'L', 0, 1, '', '', true);
$pdf->Ln(7);
if($fSt1 > 0){
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->Write(0, "- ".($vActHeader['P1']), '', 0, 'L', false, 0, false, false, 0);
	$pdf->SetFont('Helvetica', '', 10);
	$pdf->Write(0," por un total de ", '', 0, 'L', false, 0, false, false, 0);
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->Write(0, $vActHeader['signo_moneda'] . " " . number_format($fSt1,2,',','.').".- ", '', 0, 'L', false, 0, false, false, 0);
	$pdf->Write(0, "(".strtoupper($vActHeader['nombre_moneda'] . " " .docenumeros($fSt1))."/100). ", '', 0, 'L', true, 0, false, false, 0);
	$pdf->Ln();
}

if($fSt2 > 0){
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->Write(0, "- ".($vActHeader['P2']), '', 0, 'L', false, 0, false, false, 0);
	$pdf->SetFont('Helvetica', '', 10);
	$pdf->Write(0," por un total de ", '', 0, 'L', false, 0, false, false, 0);
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->Write(0, $vActHeader['signo_moneda'] . " " . number_format($fSt2,2,',','.').".- ", '', 0, 'L', false, 0, false, false, 0);
	$pdf->Write(0, "(".strtoupper($vActHeader['nombre_moneda'] . " " .docenumeros($fSt2))."/100). ", '', 0, 'L', true, 0, false, false, 0);
	$pdf->Ln();
}

if($fSt3 > 0){
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->Write(0, "- ".($vActHeader['P3']), '', 0, 'L', false, 0, false, false, 0);
	$pdf->SetFont('Helvetica', '', 10);
	$pdf->Write(0," por un total de ", '', 0, 'L', false, 0, false, false, 0);
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->Write(0, $vActHeader['signo_moneda'] . " " . number_format($fSt3,2,',','.').".- ", '', 0, 'L', false, 0, false, false, 0);
	$pdf->Write(0, "(".strtoupper($vActHeader['nombre_moneda'] . " " .docenumeros($fSt3))."/100). ", '', 0, 'L', true, 0, false, false, 0);
	$pdf->Ln();
}
$pdf->SetFont('Helvetica', '', 10);
$pdf->Write(0, "Y cumplen con las condiciones generales y particulares de lo requerido, además de ser la/s oferta/s que más se adecúa/n al objeto de contratación.", '', 0, 'L', true, 0, false, false, 0);
$pdf->Ln(22);

//------------------------------------------------------------------------------------------------------
// Firma
//------------------------------------------------------------------------------------------------------
if ($pdf->GetY() > 250){
	$pdf->AddPage();
	$pdf->SetY(-45);
}else{ 
	$pdf->SetY(-45);
}

$pdf->Write(0, '----------------------------------------', '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, ($vActHeader['firmante']), '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, ($vActHeader['cargo']), '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0, ($vActHeader['lugar']), '', 0, 'R', true, 0, false, false, 0);

// -----------------------------------------------------------------------------

//Close and output PDF document
$sFileName = 'acta_compra_'.str_replace("/","-",$vActHeader['sNum']).".pdf";
$pdf->Output($sFileName);

//============================================================+
// END OF FILE                                                
//============================================================+
