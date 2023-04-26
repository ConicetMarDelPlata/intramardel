<?php

	require_once('tcpdf/config/lang/spa.php');
	require_once('tcpdf/tcpdf.php');
	include "seguridad_bd.php";

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

	$vTrimestre = explode("-", $_POST['sTrimestre']);
	$iYear		= $_POST['iYear'];
	$iUE		= (int)$_POST['iUE'];
	$vUE		= $bd->getUE($iUE);
	
class MYPDF extends TCPDF {
	
    //Page header
    public function Header() {
		global $vUE, $iUE, $vTrimestre, $iYear;
		
		if($iUE === 0){
			$sUENombre 		= "TODAS";
			$sUEDomicilio 	= "------";
			$sUETelefono  	= "------";
		}else{
			$sUENombre 		= ($vUE['nombre']);
			$sUEDomicilio 	= ($vUE['domicilio']);
			$sUETelefono 	= $vUE['telefono'];
		}

		switch((int)$vTrimestre[0]){
			case 1:
				$sTrim = "Enero - Febrero - Marzo";
			break;
			case 4:
				$sTrim = "Abril - Mayo - Junio";
			break;
			case 7:
				$sTrim = "Julio - Agosto - Septiembre";
			break;
			case 10:
				$sTrim = "Octubre - Noviembre - Diciembre";
			break;
		}
        // Logo
        $image_file = K_PATH_IMAGES.'escudo_arg.jpg';
        $logo_file 	= K_PATH_IMAGES.'conicet.jpg';
		
		$w_Rect1 = 275/3;
		$w_Rect2 = 275/2;
		$w_Rect3 = 275/3;
		
		$this->SetDrawColor(0);
		$this->Rect(15,17,275,20,'D');
		//$this->Rect(85,5,$w_Rect2,20,'',array(255,255,255));
		//$this->Rect(220,5,$w_Rect3,20,'',array(255,255,255));
        // Set font
        $this->SetFont('helvetica', 'B', 7);

        // Title
		$this->Ln(1);
		//LINEA 1
		//RECUADRO 1
        $this->Cell($w_Rect1, 3, 'CONSEJO NACIONAL DE INVESTIGACIONES', 0, 0, 'C');
		$this->Cell(10);
		//RECUADRO 2
		$this->Cell(10);
        $this->Cell($w_Rect2/2+20, 3, 'DOCUMENTO DE CARGO PATRIMONIAL', 0, 0, 'L');
		$this->Cell(10);
		$this->SetFont('helvetica', 'B', 7);
		//RECUADRO 3
        $this->Cell(20, 3, 'TRIMESTRE: ', 0, 0, 'L');
		$this->SetFont('helvetica', '', 7);
        $this->Cell($w_Rect3/2, 3, $sTrim, 0, 1, 'L');
		$this->SetFont('helvetica', 'B', 7);

		//LINEA 2
		//RECUADRO 1
        $this->Cell($w_Rect1, 3, 'CIENT�FICAS Y T�CNICAS', 0, 0, 'C');
		$this->Cell(10);
		$this->SetFont('helvetica', 'B', 7);
		//RECUADRO 2
		$this->Cell(10);
        $this->Cell(20, 3, 'DEPENDENCIA: ', 0, 0, 'L');
		$this->SetFont('helvetica', '', 7);
        $this->Cell($w_Rect2/2, 3, $sUENombre, 0, 0, 'L');
		$this->SetFont('helvetica', 'B', 7);
		$this->Cell(10);
		//RECUADRO 3
        $this->Cell(20, 3, 'A�O: ', 0, 0, 'L');
		$this->SetFont('helvetica', '', 7);
        $this->Cell($w_Rect3/2, 3, $iYear, 0, 1, 'L');

		//LINEA 3
		//RECUADRO 1
        $this->Cell($w_Rect1, 5, 'DEPARTAMENTO PATRIMONIO', 0, 0, 'C');
		$this->Cell(10);
        $this->SetFont('helvetica', 'B', 7);
		//RECUADRO 2
		$this->Cell(10);
        $this->Cell(20, 3, 'DIRECCI�N: ', 0, 0, 'L');
        $this->SetFont('helvetica', '', 7);
        $this->Cell($w_Rect2/2, 3, $sUEDomicilio, 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 7);
		$this->Cell(10);
		//RECUADRO 3
        $this->Cell($w_Rect3, 3, "", 0, 1, 'C');

		//LINEA 4
		//RECUADRO 1
        $this->Cell($w_Rect1, 5, '', 0, 0, 'C');
		$this->Cell(10);
        $this->SetFont('helvetica', 'B', 7);
		//RECUADRO 2
		$this->Cell(10);
        $this->Cell(20, 3, 'TEL�FONO: ', 0, 0, 'L');
        $this->SetFont('helvetica', '', 7);
        $this->Cell($w_Rect2/2, 3, $sUETelefono, 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 7);
		$this->Cell(10);
		//RECUADRO 3
        $this->Cell($w_Rect3, 3, "", 0, 1, 'C');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom

		//Parámetros rectámgulos inferiores
		$w_Rect1 = 70;
		$w_Rect2 = 70;
		$w_Rect3 = 125;
		$y_Rects = 19;
		$h_Rects = 20;

        $this->SetY(-33);

		//Textos en cuadrados
        $this->SetFont('helvetica', '', 7);
		
        $this->Cell($w_Rect1, 3, 'V� B� Departamento Patrimonio', 0, 0, 'C');
		$this->Cell(5);
        $this->Cell($w_Rect2, 3, 'V� B� Delegado Patrimonial', 0, 0, 'C');
		$this->Cell(5);
        $this->Cell($w_Rect3, 3, 'Conformidad del Responsable Directo por la Tenencia, Uso y Conservaci�n de los Bienes Inventariados', 0, 1, 'C');

        $this->Cell($w_Rect1, 3, '', 0, 0, 'C');
		$this->Cell(5);
        $this->Cell($w_Rect2, 3, '', 0, 0, 'C');
		$this->Cell(5);
        $this->Cell($w_Rect3, 3, '', 0, 1, 'C');

		$this->Ln(4);
		
        $this->Cell($w_Rect1, 3, '------------------------------------------', 0, 0, 'C');
		$this->Cell(5);
        $this->Cell($w_Rect2, 3, '------------------------------------------', 0, 0, 'C');
		$this->Cell(5);
        $this->Cell($w_Rect3, 3, '------------------------------------------', 0, 1, 'C');

        $this->Cell($w_Rect1, 3, 'Firma, Sello y Fecha', 0, 0, 'C');
		$this->Cell(5);
        $this->Cell($w_Rect2, 3, 'Firma, Sello y Fecha', 0, 0, 'C');
		$this->Cell(5);
        $this->Cell($w_Rect3, 3, 'Firma, Sello y Fecha', 0, 1, 'C');

        $this->SetY(-15);

				$this->SetDrawColor(0);
		
		$this->Rect(15,  $this->getY()-$y_Rects, $w_Rect1, $h_Rects,'D');
		$this->Rect(90, $this->getY()-$y_Rects, $w_Rect2, $h_Rects,'D');
		$this->Rect(165, $this->getY()-$y_Rects, $w_Rect3, $h_Rects,'D');
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'R', 'M');
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
//$pdf->SetHeaderData("escudo_arg.jpg", 8, "MINISTERIO DE CIENCIA, TECNOLOG�A E INNOVACI�N PRODUCTIVA","CONSEJO NACIONAL DE INVESTIGACIONES CIENT�FICAS Y T�CNICAS");
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+20, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER+15);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM+30);

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

$vData		= $bd->getDCPTData($vTrimestre[0],$vTrimestre[1],$iYear,$iUE);
//echo "<pre>";
//var_dump($vData);
//if($vData){
	// add a page
	$pdf->AddPage('L');
	$pdf->Ln(7);
	$pdf->SetFont('Helvetica', '', 7);
	$pdf->Ln();
	$pdf->SetFillColor(192,192,192);
	$w = 40;

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
	   vertical-align: middle;
	}
	td {
	   color: #000000;
	   padding-bottom: 2px;
	   padding-top: 2px;
	   border: 1px solid #CCCCCC;
	   vertical-align: middle;
	}
	</style>

	<table style="border: 1px solid #CCCCCC" valign="middle">
		<tr>
			<th style="width:50px;text-align:center" valign="middle">Ord.</th>
			<th style="width:50px;text-align:center">N� de Inv</th>
			<th style="width:50px;text-align:center">Cant.</th>
			<th style="width:230px;text-align:center">Descripci�n</th>
			<th style="width:115px;text-align:center">Serie</th>
			<th style="width:70px;text-align:center">F.Compra</th>
			<th style="width:80px;text-align:center">Cod. Pres. Clase Gen.</th>
			<th style="width:80px;text-align:right">Importe</th>
			<th style="width:120px;text-align:center">Motivo Alta</th>
			<th style="width:130px;text-align:center">Res. Otorgamiento</th>
		</tr>';

	$tbl_footer="</table>";

	if($vData){
		foreach($vData as $Item=>$Valor){
			if($iUE === 0){
				$sUE = $Valor['nombre'];
			}
			
			$cant 			= $Valor['cant'];
			$descripcion 	= $Valor['descripcion'];
			$serie 			= $Valor['serie'];
			$fechaC			= $Valor['fecha_compra'];
			$moneda			= $Valor['moneda'];
			$importe		= $Valor['importe'];
			$res_oto		= $Valor['res_oto'];

			$i++;
			if($i%2==0){
				$sStyle = 'style="background-color:#DDDDDD"';
			}else{
				$sStyle = '';
			} 
			if($sLastUE !== $sUE){
				$sTR ='<tr><td colspan="10" align="center" style="background-color:#777777; color:#FFFFFF">'.$sUE.'</td></tr>';
				$sLastUE = $sUE;
			}else{
				$sTR ='';
			}
			
			$tbl_content.=$sTR.'
			<tr '.$sStyle.'>
				<td align="center">'.$i.'</td>
				<td align="center"></td>
				<td align="center">'.$cant.'</td>
				<td>'.$descripcion.'</td>
				<td>'.$serie.'</td>
				<td>'.convertir_fecha($fechaC).'</td>
				<td></td>
				<td style="text-align:right;">'.$bd->getCoinSymbol((int)$moneda).' '.number_format ($importe , 2, ',', '.').'</td>
				<td>Donaci&oacute;n al CONICET</td>
				<td>'.$res_oto.'</td>
				<!--<hr>-->
			</tr>';
			$fTotal += $Item['importe'];
		}
		$pdf->writeHTML($tbl_header . ($tbl_content) . $tbl_footer, true, false, true, false, '');
	}else{
		$pdf->writeHTML($tbl_header . ('') . $tbl_footer, true, false, true, false, '');
	}

	$pdf->Output('anexo_donacion.pdf');
//}
?>
