<?php
	Class Utils{
		public function getFirstDateOfWeek(){
			$day=date("d");
			$month=date("m");
			$year=date("Y");
			# Obtenemos el día de la semana de la fecha dada
			$diaSemana=date("w",mktime(0,0,0,$month,$day,$year));
			 
			# el 0 equivale al domingo...
			if($diaSemana==0)
			    $diaSemana=7;
			 
			# A la fecha recibida, le restamos el dia de la semana y obtendremos el lunes
			$primerDia=date("Y-m-d",mktime(0,0,0,$month,$day-$diaSemana+1,$year));
			return $primerDia;
		}
		
		public function getLastDayOfMonth($iMonth, $iYear){
			return date("d",(mktime(0,0,0,$iMonth+1,1,$iYear)-1));
		}
		
		public function getDayName($iDayNumber,$sortName){
			if ($sortName) {
				$vDayNames = array("Lun", "Mar", "Mi&eacute;r", "Jue", "Vier");
			} else {
				$vDayNames = array("Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes");
			}
			return $vDayNames[$iDayNumber];
		}	
		
		public function formatDate($sDate){
			list($iY, $iM, $iD) = explode("-",$sDate);
			
			return "$iD-$iM-$iY";
		}

		public function getLastDayOfRange($date)
		{
			$dia = strtotime ( '+2 day' , strtotime ( $date ) ) ;
			return $dia;
			//return date('j',$date);
		}

		public function taskIsToday($dFirstDate, $iOffset){
			if(date("d/m") == date("d/m",strtotime ( '+'.$iOffset.' day' , strtotime ($dFirstDate) ))){
				return true;
			}else {
				return false;
			}
    	}
    
		public function reservationIsInTheFuture($fecha='2020-11-11', $timestamp='00:00:00') {
			return ((date("Y-m-d") < $fecha) or ((date("Y-m-d") == $fecha) and (date("H:i:s") < $timestamp)));
		}
		
		public function getTaskDateHeader($dFirstDate, $iOffset){
			//return $dFirstDate;
			list($dia, $mes) = explode("/", date("d/m",strtotime ( '+'.$iOffset.' day' , strtotime ($dFirstDate) )));
			$MonthNames = array("Ene.", "Feb.", "Mar.", "Abr.", "May.", "Jun.", "Jul.", "Ago.", "Sep.", "Oct.", "Nov.", "Dic.");
			return $dia;
			//return $dia . " de " . $MonthNames[$mes -1];
		}

		public function getDateHeader($dFirstDate,$iOffset){
			list($dia, $mes, $año) = explode("/", date("d/m/Y",strtotime ( '+'.$iOffset.' day' , strtotime ($dFirstDate) )));
			$date = date("d/m/Y",strtotime ( '+'.$iOffset.' day' , strtotime ($dFirstDate) ));
			//$MonthNames = array("Ene.", "Feb.", "Mar.", "Abr.", "May.", "Jun.", "Jul.", "Ago.", "Sep.", "Oct.", "Nov.", "Dic.");
			return $date;
		}
	}
?>