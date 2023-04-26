<?php
    include "./includes/header.php";
    include "seguridad_bd.php";
    $sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");	
		exit();
	}
    
    include_once("./includes/app/config.php");
    include_once("./includes/class.Tpl.php");
    include_once("./includes/class.Feriados.php");
    include_once("./includes/class.Salas.php");
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    //echo date("Y-m-d h:iA", $date->format('U'));

    $tpl = new tpl("templates/reserva_salas/calendar.html");

    $feriados = new Feriados($bd);
    $sala = new Salas($bd);
	
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
    $usuario_id = $_SESSION["id_usuario"];
    $contrasenia = $_SESSION["contrasenia"];

    $sesion = NULL;	

    $id_permiso = 37;//37-Reserva de salas de conferencia.
	
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],$id_permiso,''); 
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
    }	

	if ($bd->nivel_acceso($nombre_usuario) > 1){
        $puede_agregar = $bd->checkAccess($_SESSION["id_usuario"],$id_permiso,'alta'); 
    } else {
        $puede_agregar = true;
    }
    //=======================================================================
    $tpl->setVar("user",$nombre_usuario);
    $tpl->setVar("userId",$usuario_id);
	$tpl->setVar("sBack","Volver");
	$tpl->setVar("linkBack","panel_control_modulos.php");

    $oPanelCtrl = $bd->getPanel("panel_control_modulos");
	if($oPanelCtrl){
		$sBlockData = $tpl->beginBlock("PC");
		foreach($oPanelCtrl as $Item){
			if ($bd->checkAccess($_SESSION["id_usuario"],$Item['id_permiso'],$Item['acceso'])){
				$vVars = array(
				'linkPC'=>$Item['link'],
				'iconoPC'=>'iconos/'.$Item['icono'],
				'nombrePC'=>$Item['nombre']
				);
				$tpl->addToBlock($sBlockData,$vVars);
			}
		}
		$tpl->endBlock();
	}

    $iWeekOffset = (int)(($_GET['m'])??'0');
    $iSala = (int)(($_GET['s'])??'0');

    if($iWeekOffset > 0){
        $tpl->setVar("prevOffset",$iWeekOffset-14);
    }else{
        $tpl->setVar("prevOffset",0);
    }
    $tpl->setVar("nextOffset",$iWeekOffset+14);
    $primerDia = $oUtils->getFirstDateOfWeek();
    $diaDesde = $oUtils->getDateHeader($primerDia, $iWeekOffset);
    $tpl->setVar("firstDay",$diaDesde);
    $ultimoDia = $oUtils->getDateHeader($primerDia, $iWeekOffset+11);
    $tpl->setVar("lastDay",$ultimoDia);
    $salas = $sala->obtener_salas();
    $oBlock = $tpl->beginBlock("SALAS");

    foreach((array)$salas as $Item){
        if ((int)$Item['id'] == $iSala){
            $sSelected = "selected='selected'";
        }else{
            $sSelected = "";
        }
        $vVars = array(
            'id'=>$Item['id'],
            'nombre'=>$Item['nombre'],
            'sSelected'=>$sSelected
        );
        $tpl->addToBlock($oBlock,$vVars);
    }
    $tpl->endBlock();

    $oBlock = $tpl->beginBlock("SALASMODAL");
    foreach((array)$salas as $Item){
        $tpl->addToBlock($oBlock,$Item);
    }
    $tpl->endBlock();
    $z=0;
    $iOffset = 0;
    if((int)$iWeekOffset === 0){
        $isPast = true;				
    }else{
        $isPast = false;				
    }

    $hBlock = $tpl->beginBlock("CALENDAR-HOURS");
    for ($k=0;$k<20;$k++){
        $kSuma = 30 * $k;
        $sHora = date("H:i",strtotime('8:00:00 +'.$kSuma.' minute'));
        $vVars = array(
            'hour'=>$sHora
        );
        $tpl->addToBlock($hBlock,$vVars);
    }
    $tpl->endBlock();

    $wBlock = $tpl->beginBlock("CALENDAR-WEEK");
    for ($w=0;$w<2;$w++){
        $wHtml = '<div class="calendar-header">';
        for ($d=0;$d<5;$d++){
            $j = ($w*7+$d);
            if($oUtils->taskIsToday($primerDia, $j + $iWeekOffset)){
                $wHtml .= '<div class="calendar-day hoy">'.$oUtils->getDayName($d,true).'<br><span class="calendar-header--cell-span">'.$oUtils->getTaskDateHeader($primerDia, $j + $iWeekOffset).'</span></div>';
            } else {
                $wHtml .= '<div class="calendar-day">'.$oUtils->getDayName($d,true).'<br><span class="calendar-header--cell-span">'.$oUtils->getTaskDateHeader($primerDia, $j + $iWeekOffset).'</span></div>';
            }
        }
        $wHtml .= '</div><div class="calendar-body">';
        for ($d=0;$d<5;$d++){
            $j = ($w*7+$d);
            $sNow = date("Y-m-d",strtotime ( '+'.$j + $iWeekOffset.' day' , strtotime ($primerDia) ));
            $ahora = date("H:i:s");

            if($oUtils->taskIsToday($primerDia, $j + $iWeekOffset)){
                $isPast = false;
                $isToday = true;
            } else {
                $isToday = false;

            }
            $dia = date('d',strtotime($sNow));
            $mes = date('m',strtotime($sNow));
            $year = date('Y',strtotime($sNow));
            $esFeriado = $feriados->esFeriado($dia, $mes, $year);

            if (!$esFeriado){
                $reservaDia = $conference->getAllReservations($dia, $mes, $year,$iSala);
            }

            $sHoraActual = date("H:i:s",strtotime('8:00:00'));
            $sHoraFinDia = date("H:i:s",strtotime('18:00:00'));
            if ($isPast){
                $wHtml .= '<div class="calendar-day past">';
            } else {
                if ($esFeriado) {
                    $feriadoNombre = $feriados->datosFeriado($dia, $mes, $year);
                    $wHtml .= '<div class="calendar-day holiday">';
                } else {
                    $wHtml .= '<div class="calendar-day">';
                }
            }
            foreach ((array)$reservaDia as $r){
                $horaInicio = date("H:i:s",strtotime($r['horaI']));
                $horaFin = date("H:i:s",strtotime($r['horaF']));
                if ($sHoraActual === $horaInicio) {
                    while ($sHoraActual < $horaFin) {
                        if ($isToday){
                            if ($sHoraActual < $ahora) {
                                $wHtml .= '<div class="calendar-cell calendar-cell--disabled hasReservation" data-title="'.$r['horaI'].'-'.$r['horaF'].': '.$r['titulo'].'" onclick=editReservation('.$r['id'].')></div>';
                            } else {
                                $wHtml .= '<div class="calendar-cell hasReservation" data-title="'.$r['horaI'].'-'.$r['horaF'].': '.$r['titulo'].'" onclick=editReservation('.$r['id'].')></div>';
                            }
                        } else {
                            $wHtml .= '<div class="calendar-cell hasReservation" data-title="'.$r['horaI'].'-'.$r['horaF'].': '.$r['titulo'].'" onclick=editReservation('.$r['id'].')></div>';
                        }                        
                        $sHoraActual = date("H:i:s",strtotime($sHoraActual.' + 30 minute'));
                    }
                } else {
                    while ($sHoraActual < $horaInicio) {
                        if (($puede_agregar) && !$esFeriado && !$isPast){
                            if ($isToday){
                                if ($sHoraActual < $ahora) {
                                    $wHtml .=  "<div class='calendar-cell calendar-cell--disabled'></div>";
                                } else {
                                    $wHtml .=  "<div class='calendar-cell' onclick='Block(\"".$sHoraActual."\",\"".$sNow."\");'></div>";
                                }
                            } else {
                                $wHtml .=  "<div class='calendar-cell' onclick='Block(\"".$sHoraActual."\",\"".$sNow."\");'></div>";
                            }                           

                        } else {
                            if ($esFeriado){
                                $wHtml .=  "<div class='calendar-cell' data-title='$feriadoNombre'></div>";

                            } else {
                                $wHtml .=  "<div class='calendar-cell'></div>";
                            }
                        }
                        $sHoraActual = date("H:i:s",strtotime($sHoraActual.' + 30 minute'));
                    }
                    while ($sHoraActual < $horaFin) {
                        $wHtml .= '<div class="calendar-cell hasReservation"  data-title="'.date('H:i',strtotime($r['horaI'])).'-'.date('H:i',strtotime($r['horaF'])).': '.$r['titulo'].'" onclick=editReservation('.$r['id'].')></div>';
                        $sHoraActual = date("H:i:s",strtotime($sHoraActual.' + 30 minute'));
                    }
                }
            }
            while ($sHoraActual < $sHoraFinDia){
                if (($puede_agregar) && !$esFeriado && !$isPast){
                    if ($isToday){
                        if ($sHoraActual < $ahora) {
                            $wHtml .=  "<div class='calendar-cell calendar-cell--disabled'></div>";
                        } else {
                            $wHtml .=  "<div class='calendar-cell' onclick='Block(\"".$sHoraActual."\",\"".$sNow."\");'></div>";
                        }
                    } else  {
                        $wHtml .=  "<div class='calendar-cell' onclick='Block(\"".$sHoraActual."\",\"".$sNow."\");'></div>";
                    }                    
                } else{
                    if ($esFeriado){
                        $wHtml .=  "<div class='calendar-cell' data-title='$feriadoNombre'></div>";

                    } else {
                        $wHtml .=  "<div class='calendar-cell'></div>";
                    }
                }
                $sHoraActual = date("H:i:s",strtotime($sHoraActual.' + 30 minute'));
            }

            $wHtml .= '</div>';
        }
        $wHtml .= '</div>';
        $vVars = array(
            'row'=>$wHtml
        );
        $tpl->addToBlock($wBlock,$vVars);
    }
    $tpl->endBlock();
    
	$tpl->printTpl();

?>