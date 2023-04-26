<?php
include "seguridad_bd.php";
require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');

$id_certificado = $_REQUEST['id_certificado'];
$bd = new Bd;
$bd->AbrirBd();


class MYPDF extends TCPDF {
    public function Header() {
        // Logo comun
        $image_file = K_PATH_IMAGES.'logoCertificado.jpg';
	$this->Image($image_file, 26, 10, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Logo 10 años
        //$image_file = K_PATH_IMAGES.'conicetLogin10aIntranet.jpg';
	//$this->Image($image_file, 26, 18, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

	$this->SetFont('helvetica', 'C', 9);
	$this->Cell(0, 5, 'Centro Científico Tecnológico CONICET Mar del Plata  ', 0, true, 'R', 0, '', 0, false, 'T', 'M');
    }
    public function Footer() {
	/* dibujamos una linea delimitadora del pie de página */
	$this->Line(15,275,195,275);
        $this->SetY(276);
        $this->SetFont('helvetica', 'C', 9);
	//$this->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
	//$this->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
	$this->Cell(0, 5, 'Moreno 3527 3º Piso - CP 7600 - Mar del Plata - Argentina - Tel: +54 (223) 495-2233/4466', 0, true, 'C', 0, '', 0, false, 'T', 'M');
        //$this->Cell(0, 5, date("m/d/Y H\hi:s"), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}

}


// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Conicet Mar del Plata');
$pdf->SetTitle('Certificado');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);
//$pdf->SetHeaderData(PDF_HEADER_LOGO, 180);


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins (left, top, rigth)
$pdf->SetMargins(25, 45, 25);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 40);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'C', 11);
//var_dump($pdf->getMargins());
// add a page
$pdf->AddPage();
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
$pdf->setCellHeightRatio(1.8);


$rowTC = $bd->consultar_tipo_certificado($id_certificado);
$id_tipo_certificado = $rowTC["id_tipo_certificado"];
		
switch ($id_tipo_certificado){
	case 1: //Obra Social
		$row_certificado = $bd->consultar_certificado_obra_social($id_certificado);
		switch ($row_certificado['id_escalafon_categoria']) {
			case 19: $prefijo_escalafon = "";
				break;
			default: $prefijo_escalafon = "de la ";
				break;
			}

		separaFecha($row_certificado['fecha_ingreso'],$anio, $mes, $dia, $nombreMes);

		$DNI_formateado = number_format($row_certificado['DNI'],0,'','.');
		$CUIL_formateado = substr($row_certificado['CUIL'],0,2)."-".substr($row_certificado['CUIL'],2,strlen($row_certificado['CUIL'])-3)."-".substr($row_certificado['CUIL'],-1,1);

		$parrafo = 'CERTIFICO por intermedio del presente que '.$row_certificado['titulo_persona'].' '.strtoupper($row_certificado['apellido']).' '.strtoupper($row_certificado['nombre']).', con Documento Nacional de Identidad Nº '.$DNI_formateado.', Clave Única de Identificación Laboral Nº '.$CUIL_formateado.' reviste como miembro '.$prefijo_escalafon.html_entity_decode($row_certificado['escalafon_categoria_nombre']).' de este Consejo Nacional de Investigaciones Científicas y Técnicas (CONICET). De acuerdo a los registros obrantes en nuestras bases de datos la fecha de ingreso al organismo data del día '.$dia.' de '.$nombreMes.' del año '.$anio.".\n";
		//$pdf->Write(0, '    '.$parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);

		$parrafo = 'Se llevan a cabo los correspondientes aportes de Ley. En cuanto a la Obra Social, los aportes se realizan a la Obra Social de la Unión del Personal Civil de la Nación N° de código 12570-7.'."\n";
		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);

		separaFecha($row_certificado['fecha_certificado'],$anio, $mes, $dia, $nombreMes);
		
		$parrafo = 'A solicitud del interesado y a sólo efecto de ser presentado ante quien corresponda, se extiende el presente certificado en la ciudad de Mar del Plata el '. $dia.' de '.$nombreMes.' del '.$anio.".\n";
		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);
		
		$pdf->SetY(180);
		$pdf->Write(0, 'Firma y sello', '', 0, 'R', true, 0, false, false, 0);		
		break; 
	case 2: //Unificacion de aportes
		$row_certificado = $bd->consultar_certificado_unificacion_aportes($id_certificado);
		separaFecha($row_certificado['fecha_certificado'],$anio, $mes, $dia, $nombreMes);
		
		$pdf->Write(0, 'Mar del Plata, '. $dia . ' de ' . $nombreMes . ' de ' . $anio, '', 0, 'R', true, 0, false, false, 0);
		$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
		$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
		$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

		$pdf->Write(0, 'SEÑORES', '', 0, 'L', true, 0, false, false, 0);
		$pdf->Write(0, 'SUPERINTENDENCIA DE SERVICIOS DE SALUD', '', 0, 'L', true, 0, false, false, 0);
		$pdf->SetFont('helvetica', 'U', 11);
		$pdf->Write(0, 'PRESENTE', '', 0, 'L', true, 0, false, false, 0);
		$pdf->SetFont('helvetica', '', 11);

		$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
		$pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

		separaFecha($row_certificado['fecha_ingreso'],$anio, $mes, $dia, $nombreMes);		
		
		$DNI_formateado = number_format($row_certificado['DNI'],0,'','.');
		$CUIL_formateado = substr($row_certificado['CUIL'],0,2)."-".substr($row_certificado['CUIL'],2,strlen($row_certificado['CUIL'])-3)."-".substr($row_certificado['CUIL'],-1,1);

		$parrafo = 'CERTIFICO por intermedio del presente que '.$row_certificado['titulo_persona'].' '.strtoupper($row_certificado['apellido']).' '.strtoupper($row_certificado['nombre']).', con Documento Nacional de Identidad Nº '.$DNI_formateado.', Clave Única de Identificación Laboral Nº '.$CUIL_formateado.' es personal del Consejo Nacional de Investigaciones Científicas y Técnicas (CONICET). De acuerdo a los registros obrantes en nuestras bases de datos la fecha de ingreso al organismo data del día '.$dia.' de '.$nombreMes.' del año '.$anio.".\n";

		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);

		$parrafo = "Por dicha actividad le corresponde la Obra Social de la Unión del Personal Civil de la Nación CODIGO RNOS (125707 - UP - UNION PERSONAL).\n";
		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);
		$pdf->SetY(210);
		$pdf->Write(0, 'Firma y sello', '', 0, 'R', true, 0, false, false, 0);
		break;
	case 3: //Antiguedad
		$row_certificado = $bd->consultar_certificado_antiguedad($id_certificado);
		switch ($row_certificado['id_escalafon_categoria']) {
			case 19: $prefijo_escalafon = "";
				break;
			default: $prefijo_escalafon = "de la ";
				break;
			}
		separaFecha($row_certificado['fecha_ingreso'],$anio, $mes, $dia, $nombreMes);

		$DNI_formateado = number_format($row_certificado['DNI'],0,'','.');
		$CUIL_formateado = substr($row_certificado['CUIL'],0,2)."-".substr($row_certificado['CUIL'],2,strlen($row_certificado['CUIL'])-3)."-".substr($row_certificado['CUIL'],-1,1);

		$parrafo = "CERTIFICO por intermedio del presente que ".$row_certificado['titulo_persona'].' '.strtoupper($row_certificado['apellido']).' '.strtoupper($row_certificado['nombre']).', con Documento Nacional de Identidad Nº '.$DNI_formateado.', Clave Única de Identificación Laboral Nº '.$CUIL_formateado.' reviste como miembro '.$prefijo_escalafon.html_entity_decode($row_certificado['escalafon_categoria_nombre'])." en este Consejo Nacional de Investigaciones Científicas y Técnicas (CONICET).\n";
		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);

		$parrafo = "De acuerdo a los registros obrantes en nuestras bases de datos la fecha de ingreso al organismo data del día ".$dia.' de '.$nombreMes.' del año '.$anio;
		if (is_null($row_certificado['fecha_egreso'])) 
			$parrafo = $parrafo.".\n";
		else 
			{separaFecha($row_certificado['fecha_egreso'],$anio, $mes, $dia, $nombreMes);
			$parrafo = $parrafo.' y la fecha de egreso corresponde al día '.$dia.' de '.$nombreMes.' del año '.$anio.".\n";}

		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);

		if($row_certificado['goce_licencia'] == 1) {
			$parrafo = "Así mismo, en función de lo que se desprende del Sistema Integral de Gestión de Recursos Humanos, la persona citada no ha gozado de licencias sin goce de haberes durante el período laborado.\n";
			//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
			//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
			$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
			$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);
		}

		separaFecha($row_certificado['fecha_certificado'],$anio, $mes, $dia, $nombreMes);
		
		$parrafo = "El presente se extiende a solicitud del interesado y al sólo efecto de ser presentado ante quien corresponda, en la ciudad de Mar del Plata el ". $dia.' de '.$nombreMes.' del '.$anio.".\n";
		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);
		
		$pdf->SetY(190);
		$pdf->Write(0, 'Firma y sello', '', 0, 'R', true, 0, false, false, 0);		
		break; 
	case 4: //Beca
		$row_certificado = $bd->consultar_certificado_beca($id_certificado);

		if (is_null($row_certificado['fecha_fin_beca']))
			$tiempo_pasado = false;
		else
			$tiempo_pasado = strtotime($row_certificado['fecha_fin_beca']) < strtotime("now");
		if ($tiempo_pasado){
			$fue_es = "fue";
			$desarrolla_desarrollo = "desarrolló";
			}
		else {
			$fue_es = "es";
			$desarrolla_desarrollo = "desarrolla";
			}
	
		separaFecha($row_certificado['fecha_resolucion'],$anio, $mes, $dia, $nombreMes);

		$DNI_formateado = number_format($row_certificado['DNI'],0,'','.');

		$parrafo = "CERTIFICO que ".strtoupper($row_certificado['apellido']).' '.strtoupper($row_certificado['nombre']).' (DNI '.$DNI_formateado.") ". $fue_es. " Becaria/o de este Consejo Nacional de Investigaciones Científicas y Técnicas, en la categoría de ".html_entity_decode($row_certificado['escalafon_categoria_nombre']).", otorgada por Resolución D Nº ".$row_certificado['resolucion']." de fecha ".$dia.' de '.$nombreMes.' de '.$anio.", desde el ";
		separaFecha($row_certificado['fecha_ini_beca'],$anio, $mes, $dia, $nombreMes);
		$parrafo = $parrafo.$dia.' de '.$nombreMes.' de '.$anio;
		
		if (!is_null($row_certificado['fecha_fin_beca'])) {
			separaFecha($row_certificado['fecha_fin_beca'],$anio, $mes, $dia, $nombreMes);
			$parrafo = $parrafo." hasta el ".$dia.' de '.$nombreMes.' de '.$anio;
			}
		
		$parrafo = $parrafo.", período en el cual ".$desarrolla_desarrollo." tareas de investigación sobre el tema: \"".$row_certificado['tema']."\" bajo la dirección ";
		switch ($row_certificado['id_titulo_persona']) {
			case 4: $prefijo_titulo = "d";
				break;
			default: $prefijo_titulo = "de ";
				break;
			}
		$parrafo = $parrafo.$prefijo_titulo.$row_certificado['titulo_persona']." ".$row_certificado['apellido_direccion'].", ".$row_certificado['nombre_direccion']." en ".$row_certificado['articulo_lugar']." ".$row_certificado['lugar_beca'].".\n";

		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);

		separaFecha($row_certificado['fecha_certificado'],$anio, $mes, $dia, $nombreMes);
		$parrafo = "A solicitud del interesado y al sólo efecto de ser presentado ante quien corresponda, se extiende el presente certificado en la ciudad de Mar del Plata el ". $dia.' de '.$nombreMes.' del '.$anio.".\n";
		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);
		
		$pdf->SetY(190);
		$pdf->Write(0, 'Firma y sello', '', 0, 'R', true, 0, false, false, 0);		
		break; 
	case 5: //Horario
		$row_certificado = $bd->consultar_certificado_horario($id_certificado);

		separaFecha($row_certificado['fecha_ini'],$anio, $mes, $dia, $nombreMes);

		$DNI_formateado = number_format($row_certificado['DNI'],0,'','.');
		$texto_escalafon_categoria = "";
		if ($row_certificado['escalafon_categoria_nombre'] != "--")
			$texto_escalafon_categoria = ", revistando actualmente en la categoría de ".html_entity_decode($row_certificado['escalafon_categoria_nombre']);

		//El lugar es obligatorio si la unidad ejecutora es 11
		if (!is_null($row_certificado['lugar']) and !(trim($row_certificado['lugar'] == ""))) 
			{$texto_lugar = $row_certificado['articulo_lugar']." ".$row_certificado['lugar'].", ";}
		else
			{$texto_lugar = "";}

		if ($row_certificado['id_unidad_ejecutora'] != 11) //Zona de Influencia
			{$texto_lugar = $texto_lugar." ".$row_certificado['unidad_nombre_completo']." ";}
		else
			{$texto_lugar = $texto_lugar;}
		

		$parrafo = "CERTIFICO que ".$row_certificado['titulo_persona']." ".strtoupper($row_certificado['apellido']).' '.strtoupper($row_certificado['nombre']).	
			   ' (DNI '.$DNI_formateado.") es miembro de la ". $row_certificado['escalafon_nombre']. 
			   " de este Consejo Nacional de Investigaciones Científicas y Técnicas desde el ".$dia."/".$mes."/".$anio.$texto_escalafon_categoria.
			   ", con lugar de trabajo autorizado en ".($texto_lugar).
			   " del CENTRO CIENTIFICO TECNOLÓGICO CONICET MAR DEL PLATA (CCT - CONICET - MDP)";
		//si no uso la funcion utf8_encode para unidad_nombre_completo en mi PC no imprime este parrafo, y en conicet si la uso imprime mal el acento.

		if (!is_null($row_certificado['tema']) and !(trim($row_certificado['tema'] == ""))) {
			$parrafo = $parrafo.", desarrollando el tema de investigación: \"".$row_certificado['tema']."\".";
		} else {
			$parrafo = $parrafo.".";
		}

		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);

		if (!is_null($row_certificado['hora_ini_lunes'])) {
			//Muestro parrafo de horarios
			$parrafo = "Según obra en nuestros registros, desempeña sus tareas en los siguientes días y horarios: lunes de ".
				substr($row_certificado['hora_ini_lunes'],0,5)." a ".substr($row_certificado['hora_fin_lunes'],0,5)." hs.".
				", martes de ".substr($row_certificado['hora_ini_martes'],0,5)." a ".substr($row_certificado['hora_fin_martes'],0,5)." hs.".
				", miércoles de ".substr($row_certificado['hora_ini_miercoles'],0,5)." a ".substr($row_certificado['hora_fin_miercoles'],0,5)." hs.".
				", jueves de ".substr($row_certificado['hora_ini_jueves'],0,5)." a ".substr($row_certificado['hora_fin_jueves'],0,5)." hs.".
				", viernes de ".substr($row_certificado['hora_ini_viernes'],0,5)." a ".substr($row_certificado['hora_fin_viernes'],0,5)." hs.";
			$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
			$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);
		}

		separaFecha($row_certificado['fecha_certificado'],$anio, $mes, $dia, $nombreMes);
		$parrafo = "A solicitud del interesado y al sólo efecto de ser presentado ante quien corresponda, se extiende el presente certificado en la ciudad de Mar del Plata el ". $dia.' de '.$nombreMes.' del '.$anio.".\n";

		//$pdf->Write(0, $parrafo, '', 0, 'J', true, 0, false, false, 0);
		//$pdf->writeHTML("<p style=\"text-indent: 10em;\">".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->writeHTML("<p>---- ".$parrafo."</p>", true, false, true, false, 'J');
		$pdf->Write(0, '', '', 0, 'J', true, 0, false, false, 0);
		
		$pdf->SetY(190);
		$pdf->Write(0, 'Firma y sello', '', 0, 'R', true, 0, false, false, 0);		
		break; 

}

//Nro de certificado impreso en el pdf
// -----------------------------------------------------------------------------
$pdf->SetY(230);
$pdf->SetFont('helvetica', 'C', 9);
$pdf->Cell(0, 6, "Certificado ".$row_certificado['numero'].'/'.$row_certificado['anio'], 0, false, 'R', 0, '', 0, false, 'T', 'M');		
// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('certificado'.$row_certificado['numero'].'-'.$row_certificado['anio'].'.pdf', 'D');

//============================================================+
// END OF FILE                                                
//============================================================+
