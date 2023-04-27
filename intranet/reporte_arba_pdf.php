<?php
	//include "seguridad_bd.php";
	require_once('tcpdf/config/lang/spa.php');
	require_once('tcpdf/tcpdf.php');
	include "seguridad_bd.php";
	ini_set('max_execution_time',0);
	ini_set('memory_limit','512M');

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
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_ARBA');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],11,''); ///11- Reportes arba
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}

	$sFIni = $_POST['sFIni'];
	$sFFin = $_POST['sFFin'];
	
	$iUE		= (int)$_POST['iUE'];
	$vUE		= $bd->getUE($iUE);
	
class MYPDF extends TCPDF {
	
    //Page header
    public function Header() {
		global $vUE, $iUE, $sFIni, $sFFin;
		
		if($iUE === 0){
			$sUENombre 		= "TODAS";
			$sUEDomicilio 	= "------";
			$sUETelefono  	= "------";
			$sUECUIT  	= "------";//nuevo Vani
		}else{
			$sUENombre 		= $vUE['nombre'];
			$sUEDomicilio 	= $vUE['domicilio'];
			$sUETelefono 	= $vUE['telefono'];
			$sUECUIT  	= $vUE['cuit'];//nuevo Vani
		}

        // Logo
        $image_file = K_PATH_IMAGES.'escudo_arg.jpg';
        $logo_file 	= K_PATH_IMAGES.'conicet2.jpg';
		
		$w_Rect1 = 60;
		$w_Rect2 = 275/2;
		$w_Rect3 = 275/3;
		
		//Original antes de 10a
		$this->Image($logo_file, 20, 20, 16, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		//$this->Image($logo_file, 20, 19, 35, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->Rect(15,17,275,20,'D');

        // Set font
        $this->SetFont('helvetica', 'B', 7);

        // Title
		$this->Ln(1);
		//LINEA 1
		//RECUADRO 1
		$this->Cell(20);
        //$this->Cell($w_Rect1, 3, 'CENTRO CIENTÍFICO TECNOLÓGICO', 0, 0, 'C');
	$this->Cell($w_Rect1, 3, '', 0, 0, 'C');
		$this->Cell(10);
		//RECUADRO 2
		$this->Cell(10);
        $this->Cell($w_Rect2/2+20, 3, 'INFORME AGENTE DE RETENCIÓN', 0, 0, 'L');
		$this->Cell(10);
		$this->SetFont('helvetica', 'B', 7);
		//RECUADRO 3
        $this->Cell(20, 3, 'PERÍODO: ', 0, 0, 'L');
		$this->SetFont('helvetica', '', 7);
        $sStr = '';
		if($sFIni && $sFFin){
			$sStr = "Del $sFIni al $sFFin inclusive";
		}
		if($sFIni && !$sFFin){
			$sStr = "Desde el $sFIni al ".date("d-m-Y");
		}
		if(!$sFIni && $sFFin){
			$sStr = "Hasta el $sFFin";
		}
        $this->Cell($w_Rect3/2, 3, $sStr, 0, 1, 'L');
		$this->SetFont('helvetica', 'B', 7);

		//LINEA 2
		//RECUADRO 1
		$this->Cell(20);
        //$this->Cell($w_Rect1, 3, 'CONICET MAR DEL PLATA', 0, 0, 'C');
	$this->Cell($w_Rect1, 3, '', 0, 0, 'C');
		$this->Cell(10);
		$this->SetFont('helvetica', 'B', 7);
		//RECUADRO 2
		$this->Cell(10);
        $this->Cell(30, 3, 'UNIDAD: ', 0, 0, 'L');
		$this->SetFont('helvetica', '', 7);
        $this->Cell($w_Rect2/2, 3, $sUENombre." (CUIT $sUECUIT)", 0, 0, 'L');//modifico Vani
		$this->SetFont('helvetica', 'B', 7);
		$this->Cell(10);
		//RECUADRO 3
        $this->Cell(20, 3, '', 0, 0, 'L');
		$this->SetFont('helvetica', '', 7);
        $this->Cell($w_Rect3/2, 3, '', 0, 1, 'L');

		//LINEA 3
		//RECUADRO 1
		$this->Cell(20);
        $this->Cell($w_Rect1, 5, '', 0, 0, 'C');
		$this->Cell(10);
        $this->SetFont('helvetica', 'B', 7);
		//RECUADRO 2
		$this->Cell(10);
        $this->Cell(30, 3, 'DIRECCIÓN: ', 0, 0, 'L');
        $this->SetFont('helvetica', '', 7);
        $this->Cell($w_Rect2/2, 3, $sUEDomicilio, 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 7);
		$this->Cell(10);
		//RECUADRO 3
        $this->Cell($w_Rect3, 3, "", 0, 1, 'C');

		//LINEA 4
		//RECUADRO 1
		$this->Cell(20);
        $this->Cell($w_Rect1, 5, '', 0, 0, 'C');
		$this->Cell(10);
        $this->SetFont('helvetica', 'B', 7);
		//RECUADRO 2
		$this->Cell(10);
        $this->Cell(30, 3, 'TELÉFONO: ', 0, 0, 'L');
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

		//Par?tros rect?ulos inferiores
		$w_Rect1 = 70;
		$w_Rect2 = 70;
		$w_Rect3 = 125;
		$y_Rects = 19;
		$h_Rects = 20;

        $this->SetY(-33);

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
$pdf->SetTitle('Reporte Retenciones');
$pdf->SetSubject('CCT-CONICET-MDP');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
//$pdf->SetHeaderData("escudo_arg.jpg", 8, "MINISTERIO DE CIENCIA, TECNOLOG? E INNOVACI? PRODUCTIVA","CONSEJO NACIONAL DE INVESTIGACIONES CIENT?ICAS Y T?NICAS");
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

// set font
$pdf->SetFont('Helvetica', 'B', 12);

$vData		= $bd->getOpRetention($sFIni,$sFFin,$vUE['cuit']);
//print_r($vData);
//echo "<pre>";
//var_dump($vData);

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
			<th style="width:30px;text-align:center" valign="middle">Ord.</th>
			<th style="width:80px;text-align:center">CUIT</th>
			<th style="width:200px;text-align:center">Razón Social</th>
			<th style="width:100px;text-align:center">Nro. Cuenta.</th>
            <th style="width:70px;text-align:center">Nro. De Orden de Pago</th>
            <th style="width:105px;text-align:center">Total Factura</th>
			<th style="width:80px;text-align:center">Fecha Cert.</th>
			<th style="width:100px;text-align:center">Asignación Retención.</th>
			<th style="width:120px;text-align:center">Nro. Cert.</th>
			<th style="width:90px;text-align:center">Importe Ret.</th>
		</tr>';

	$tbl_footer="</table>";
	$sLastUE = ""; //TODO VER SI ANDAAAAAAA
	$tbl_content = "";
	$fTotal = 0;
    $i=0;
    if($vData){
        foreach($vData as $Item=>$Valor){
            if(!isset($Consecutivo)){
                $Consecutivo = (int)$Valor['op_cert_ret'];
            }else{
                $Consecutivo ++;
            }
            // echo "<pre>";
            // var_dump($Valor);
            // exit;
            if($iUE === 0){
                $sUE = $Valor['unidad_nombre'];
            } else {$sUE = "";} //TODO VER SI ANDAAAAAAAAAA}
            
            
            if($Consecutivo == (int)$Valor['op_cert_ret']){
                $CUIT 			= $Valor['proveedor_cuit'];
                $Razon_Social 	= $Valor['proveedor_razon_social'];
                $Cuenta		 	= $Valor['cuenta'];
                $Nro_Orden_Pago		= $Valor['op_numero'];//nuevo Vani
                $Fecha_Cert	 	= $Valor['op_fecha'];
                $asignacion	 	= $Valor['asignacion_rendicion'];
                $Cert_Ret	 	= '0001-' . str_pad((int)$Valor['op_cert_ret'], 8, "0", STR_PAD_LEFT);

                $fMonto 		= (float)$Valor['op_importe'];
                $fCM_Porciento 	= (float)$Valor['op_cm'];
                $fIVA			= (float)$Valor['op_iva'];
                $fAlicuota		= (float)$Valor['op_alicuota'];

                $fCM_Monto			= ($fMonto * $fCM_Porciento / 100);
                $fBase_Imponible	= $fCM_Monto - (($fIVA * $fCM_Monto) / (100+$fIVA));
                $fMonto_a_Retener	= round($fAlicuota * $fBase_Imponible / 100,2);
                $fMonto_a_Pagar		= $fMonto - $fMonto_a_Retener;

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
                //echo "$cant $descripcion $marca	$modelo	$serie $fechaC $moneda $importe $titular";

                //$tbl_content.=$sTR.'
                $tbl_content.='
                <tr '.$sStyle.'>
                    <td align="center">'.$i.'</td>
                    <td align="center">'.$CUIT.'</td>
                    <td>'.$Razon_Social.'</td>
                    <td>'.$Cuenta.'</td>				
                    <td align="right">'.$Nro_Orden_Pago.'</td>
                    <td align="right">'.number_format($fMonto, 2, ',', '.').'</td>
                    <td align="right">'.convertir_fecha($Fecha_Cert).'</td>
                    <td>'.$asignacion.'</td>
                    <td align="right">'.$Cert_Ret.'</td>
                    <td align="right">'.number_format($fMonto_a_Retener, 2, ',' , '.').'</td>
                    <!--<hr>-->
                </tr>';
                $fTotal += $fMonto_a_Retener;
            }else{
                while($Consecutivo != (int)$Valor['op_cert_ret']){
                    $CUIT 			= "------------";
                    $Razon_Social 		= "------------";
                    $Cuenta		 	= "------------";
                    $Nro_Orden_Pago		= "------------";//nuevo Vani
                    $fMonto = 0;//nuevo Victoria
                    $Fecha_Cert	 	= "------------";
                    $asignacion	 	= "------------";
                    $Cert_Ret	 	= '0001-' . str_pad($Consecutivo, 8, "0", STR_PAD_LEFT);
                    $fMonto_a_Retener	= 0;
                
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
                    //echo "$cant $descripcion $marca	$modelo	$serie $fechaC $moneda $importe $titular";

                    //$tbl_content.=$sTR.'
                    $tbl_content.='
                    <tr '.$sStyle.'>
                        <td align="center">'.$i.'</td>
                        <td align="center">'.$CUIT.'</td>
                        <td>'.$Razon_Social.'</td>
                        <td>'.$Cuenta.'</td>
                        <td align="right">'.$Nro_Orden_Pago.'</td>
                        <td align="right">'.$fMonto.'</td>
                        <td align="right">'.convertir_fecha($Fecha_Cert).'</td>
                        <td>'.$asignacion.'</td>
                        <td align="right">'.$Cert_Ret.'</td>
                        <td align="right">'.number_format($fMonto_a_Retener, 2, ',' , '.').'</td>
                        <!--<hr>-->
                    </tr>';
                    $fTotal += $fMonto_a_Retener;
                    $Consecutivo++;
                }
                
                $CUIT 			= $Valor['proveedor_cuit'];
                $Razon_Social 	= $Valor['proveedor_razon_social'];
                $Cuenta		 	= $Valor['cuenta'];
                $Nro_Orden_Pago		= $Valor['op_numero'];//nuevo Vani			
                $Fecha_Cert	 	= $Valor['op_fecha'];
                $asignacion	 	= $Valor['asignacion_rendicion'];
                $Cert_Ret	 	= '0001-' . str_pad((int)$Valor['op_cert_ret'], 8, "0", STR_PAD_LEFT);

                $fMonto 		= (float)$Valor['op_importe'];
                $fCM_Porciento 	= (float)$Valor['op_cm'];
                $fIVA			= (float)$Valor['op_iva'];
                $fAlicuota		= (float)$Valor['op_alicuota'];

                $fCM_Monto			= ($fMonto * $fCM_Porciento / 100);
                $fBase_Imponible	= $fCM_Monto - (($fIVA * $fCM_Monto) / (100+$fIVA));
                $fMonto_a_Retener	= round($fAlicuota * $fBase_Imponible / 100,2);
                $fMonto_a_Pagar		= $fMonto - $fMonto_a_Retener;

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
                //echo "$cant $descripcion $marca	$modelo	$serie $fechaC $moneda $importe $titular";

                //$tbl_content.=$sTR.'
                $tbl_content.='
                <tr '.$sStyle.'>
                    <td align="center">'.$i.'</td>
                    <td align="center">'.$CUIT.'</td>
                    <td>'.$Razon_Social.'</td>
                    <td>'.$Cuenta.'</td>
                    <td align="right">'.$Nro_Orden_Pago.'</td>
                    <td align="right">'.number_format($fMonto, 2, ',', '.').'</td>
                    <td align="right">'.convertir_fecha($Fecha_Cert).'</td>
                    <td>'.$asignacion.'</td>
                    <td align="right">'.$Cert_Ret.'</td>
                    <td align="right">'.number_format($fMonto_a_Retener, 2, ',' , '.').'</td>
                    <!--<hr>-->
                </tr>';
                $fTotal += $fMonto_a_Retener;

            }

        }
    }
    $tbl_content.='
        <tr>
            <td align="right" colspan="9"><b>Total</b></td>
            <td align="right"><b>'.number_format($fTotal, 2, ',' , '.').'</b></td>
            <!--<hr>-->
        </tr>';
    $pdf->writeHTML($tbl_header . utf8_encode($tbl_content) . $tbl_footer, true, false, true, false, '');

    $pdf->Output('Reporte_Retenciones.pdf');

?>
