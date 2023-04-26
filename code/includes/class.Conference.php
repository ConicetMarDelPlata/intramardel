<?php
    include_once("class.Feriados.php");

	// CLASS CONFERENCE
	Class Conference{
        protected $feriados;
        protected $bd;
		
		function __construct($bd){
            $this->bd=$bd;
            $this->feriados	= new Feriados($bd); 
        }

        public function isSalaAvailable($sDate, $sTime,$sSala, $sTimeFin = NULL, $reservaId = NULL){

            /*
            *   Si el día en cuestion no es feriado, obtenemos las reservas para esa sala y ese día tales que la hora de inicio, o de fin,
            *   de la nueva reserva quede incluida dentro del intervalo de la reserva previa. Si no exiten tales 
            *   reservas sabemos que la sala está disponible.
            */

            // se asume el formato de sDate en "$a-$m-$d"
            list($a,$m,$d) = explode("-",$sDate);
            if($this->feriados->esFeriado($d,$m,$a)) return false;

            // Todas las reservas de la sala en esa fecha
            $sSQL = "SELECT * FROM sala_conferencia_reservas WHERE fecha='$sDate' AND sala_id=$sSala AND (";
    
            // con inicio <= sTime < fin
            $sSQL .= " (inicio <= '$sTime' AND fin >'$sTime' )";
    
            if (!is_null($sTimeFin))  {
                // o inicio < sTimeFin <= sTimeFin
                $sSQL .= " OR (inicio < '$sTimeFin' AND fin >= '$sTimeFin') OR (inicio >= '$sTime' and fin < '$sTimeFin')";
            }
    
            $sSQL .= " ) AND NOT deleted ";

            if (!is_null($reservaId))  {
                // este es en el caso en que quiero preguntar para hacer el update de la reserva misma no deberia tener en cuanta el id al comparar
                // sino puede que siempre me de ocupada
                $sSQL .= " AND id <> '$reservaId' ";
            }

            $sSQL .= "ORDER BY fecha, inicio ASC LIMIT 1";

            try {
                $r = $this->bd->excecuteQuery($sSQL);
            } catch (exception $e) {
                error_log($e->getMessage() . " al ver disponibilidad de sala.");
                $error = "Error al ver disponibilidad de sala ";
                new Exception($error);
            }
            
            if($r){
                $row = mysqli_fetch_assoc($r);
                if($row){
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        }
        
        public function getLimitTimeToBlock($sDate, $sTime,$sSala){
            $r = false;
			$sSQL = "SELECT * FROM sala_conferencia_reservas WHERE fecha='$sDate' AND inicio > '$sTime'  AND sala_id = $sSala ORDER BY fecha, inicio ASC LIMIT 1 AND NOT deleted";
            
            try {
                $r = $this->bd->excecuteQuery($sSQL);
            } catch (exception $e) {
                error_log($e->getMessage() . " al obtener tiempo de sala.");
                $error = "Error al obtener tiempo de sala ";
                new Exception($error);
            }
            
			if($r){
				$row = mysqli_fetch_assoc($r);
				if($row){
					return $row['inicio'];
				}else{
					return false;
				}
			}else{
				return false;
			}
			
		}

        public function getEquipmentBySala($salaId){
            // Fecha: 30 Sep 2020
            // Autor: Victoria
            // Devuelve la lista de equipos por sala.
        
            $eq = array();
            $q = "SELECT equipos_x_salas.id, asignada AS checked, nombre";
            $q.= " FROM equipos_x_salas LEFT JOIN equipos ON equipos.id = equipos_x_salas.key_equipos_id WHERE";
            $q.= " equipos_x_salas.baja = 0 AND asignada = 1 AND key_salas_id = $salaId";
            
            try {
                $r = $this->bd->excecuteQuery($q);
            } catch (exception $e) {
                error_log($e->getMessage() . " al obtener equipo de sala.");
                $error = "Error al obtener equipo de sala ";
                new Exception($error);
            }

            while ($row = mysqli_fetch_array($r)){
                array_push($eq,$row);
            }
            return $eq;
        }

        public function reservarEquipos($Equipos, $reserva) {
            // Fecha: Octubre 2020
            // Autor: Victoria
            // Crea una reservación de los equipos seleccionados para la nueva reserva.
        
            try {
                foreach($Equipos as $equipoId) {
                    $q = "INSERT INTO `sala_conferencia_equipo_x_reserva` (`equipo_x_sala`, `sala_conferencia_reserva`) VALUES ($equipoId, $reserva);";
                    $r = $this->bd->excecuteQuery($q);
                }
                return true;
            } catch (exception $e) {
                error_log($e->getMessage() . " reservar equipos.");
                $error = "Error al reserva equipos ";
                throw new Exception($error);
            }
        }

        public function updateEquipos($Equipos, $reserva) {
            // Fecha: Octubre 2020
            // Autor: Victoria
            // Crea una reservación de los equipos seleccionados para la nueva reserva.
            
            try {
                //Obtengo los equipos para esa reserva
                $q="SELECT ER.equipo_x_sala as equipo, ER.deleted FROM sala_conferencia_equipo_x_reserva ER where ER.sala_conferencia_reserva = $reserva;";
                
                $r = $this->bd->excecuteQuery($q);
                $recuperar = [];
                $all = [];

                if($r) {
                    while( $row = mysqli_fetch_assoc( $r)){
                        $equipoId = $row['equipo'];
                        $all[] = $equipoId;
                        if (in_array($equipoId, $Equipos)) {
                            if ($row['deleted']) {
                                $recuperar[] = $equipoId;
                            }
                        } else {
                            $delete[] = $equipoId;
                        }
                    }

                    //delete equipos
                    if (!empty($delete)) {
                        $delete = implode("','", $delete);
                        $q = "UPDATE sala_conferencia_equipo_x_reserva SET deleted=true";
                        $q.=" WHERE equipo_x_sala in  ('".$delete."')  and sala_conferencia_reserva=$reserva";
                        $r = $this->bd->excecuteQuery($q);
                    }

                    //recuperar equipo reserva
                    if (!empty($recuperar)) {
                        $recuperar = implode("','", $recuperar);
                        $q = "UPDATE sala_conferencia_equipo_x_reserva SET deleted=false";
                        $q.=" WHERE equipo_x_sala in  ('".$recuperar."')  and sala_conferencia_reserva=$reserva";
                        $r = $this->bd->excecuteQuery($q);
                    }

                    //agregar nuevos
                    
                    $result = array_diff($Equipos, $all);
                    if (isset($result)) {
                        foreach($result as $equipoId) {
                            $q = "INSERT INTO `sala_conferencia_equipo_x_reserva` (`equipo_x_sala`, `sala_conferencia_reserva`) VALUES ($equipoId, $reserva);";
                            $r = $this->bd->excecuteQuery($q);
                        }
                    }
                }
                return true;
            } catch (exception $e) {
                error_log($e->getMessage() . " reservar equipos.");
                $error = "Error al reserva equipos ";
                throw new Exception($error);
            }
        }
    
        public function createReservation($sUsuario, $sDate, $sTimeIni, $sTimeFin, $Titulo, $Titular, $Motivo, $SalaId) {
            // Fecha: 13 Octubre 2020
            // Autor: Victoria
            // Crea una reservación de la sala con id salaId, retorna el ID de la nueva reserva si no hubo errores.

            list($d,$m,$a) = explode("-",$sDate);
            $q = "INSERT INTO sala_conferencia_reservas (`fecha`, `inicio`, `fin`, `sala_id`, `titulo`, `usuario_id`, `nombre_solicitante`, `detalles`) VALUES ('$a-$m-$d', '$sTimeIni', '$sTimeFin', '$SalaId', '$Titulo', '$sUsuario', '$Titular', '$Motivo')";

            try {
                $r = $this->bd->excecuteQuery($q);
                if ($r) {
                    return $this->bd->lastId();
                }
                return $r;
            } catch (exception $e) {
                error_log($e->getMessage() . " al crear reserva de sala.");
                $error = "Error al reserva la sala ";
                new Exception($error);
            }
        }

        public function updateReservation($data, $id) {
            // Fecha: Octubre 2020
            // Autor: Victoria
            // Actualiza una reservación.

            $q = "UPDATE sala_conferencia_reservas SET ";
            $q.= $this->mapped_implode(',', $data);
            $q.=" WHERE id = $id";
            try {
                $r = $this->bd->excecuteQuery($q);
                return true;
            } catch (exception $e) {
                error_log($e->getMessage() . " al crear reserva de sala.");
                $error = "Error al reserva la sala ";
                new Exception($error);
            }
        }


        public function getAllReservations($dia, $mes, $año, $sSala = null) {
            // Fecha: Octubre 2020
            // Autor: Victoria
            // Dado un día, un mes, un año y el id de una sala se retornan todas las reservaciones que coinciden con esos datos.
            $result = [];
            if ($sSala) {
                $q  = "SELECT `id`,`inicio` as horaI,`fin` as horaF, titulo FROM `sala_conferencia_reservas` WHERE  sala_id = $sSala and ";
                $q .= "fecha = '$año-$mes-$dia' and not deleted ORDER BY horaI;";
            } else {
                $q  = "SELECT sala_conferencia_reservas.id, inicio as horaI, fin as horaF, titulo, usuario_id as creator, salas_conferencia.nombre as sala ";
                $q .= "FROM sala_conferencia_reservas LEFT JOIN  salas_conferencia ON salas_conferencia.id = sala_conferencia_reservas.sala_id ";
                $q .= "WHERE fecha = '$año-$mes-$dia' and not deleted and not mail_sent ORDER BY horaI";
            }

            try {
                $r = $this->bd->excecuteQuery($q); 
                if($r){
                    while( $row = mysqli_fetch_assoc( $r)){
                        $result[] = $row; 
                    }
                    return $result;
                } else {
                    return [];
                }
            } catch (exception $e) {
                error_log($e->getMessage() . " getAllReservations.");
                $error = "Error al obtener las reservas ";
                new Exception($error);
            }
        }
		
		public function mail_sent($tasks) {
            foreach ($tasks as $t) {
                $q = "UPDATE sala_conferencia_reservas SET mail_sent = 1 where id = ". $t['id'];
                try {
                    $r = $this->bd->excecuteQuery($q);
                    return true;
                } catch (exception $e) {
                    error_log($e->getMessage() . " al crear reserva de sala.");
                    $error = "Error al actualizar el estado de envio de mails en reservas ";
                    new Exception($error);
                }
            }
        }

        public function getReservationById($id) {
            // Fecha: Octubre 2020
            // Autor: Victoria
            // Dado un id de una reserva se retornan la reserva.

            $q = "SELECT SCR.usuario_id as usuarioId, SCR.fecha as sDate, SCR.inicio as sTimeIni, SCR.fin as sTimeFin, SCR.titulo as sTitulo, ";
            $q.= "SCR.nombre_solicitante as sTitular, SCR.detalles as sMotivo, SCR.sala_id as sala, GROUP_CONCAT(CER.equipo_x_sala) as Equipos, "; 
            $q.= "SCR.deleted, SCR.deleted_by ";
            $q.= "FROM  `sala_conferencia_reservas` SCR ";
            $q.= "LEFT JOIN  `sala_conferencia_equipo_x_reserva` CER on CER.sala_conferencia_reserva = SCR.id and NOT CER.deleted ";
            $q.= "WHERE SCR.id = $id;";

            try {
                $r = $this->bd->excecuteQuery($q); 
                if($r){
                    $row = mysqli_fetch_assoc($r);

                    //Estas  lineas mapean el valor de 'Equipos' que viene como un string de ids concatenados por , a un array de ids integers
                    if(empty($row['Equipos'])){
                        $row['Equipos'] = [];
                    } else {
                        $row['Equipos'] = array_map('intval', explode(",",$row['Equipos']));
                    }

                    return $row;
                } else {
                    $error = "No se encontro la reserva ";
                    throw new Exception($error);
                } 
            }catch (exception $e) {
                error_log($e->getMessage() . " getAllReservations.");
                $error = "Error obtener la reservas ";
                new Exception($error);
            }
        }

        public function deleteReservation($usuario_id,$id) {
            // Fecha: Noviembre 2020
            // Autor: Victoria
            // Se da de baja una reserva dado el id de la misma.

            $q = "UPDATE sala_conferencia_reservas SET ";
            $q.= "deleted = true, deleted_by = $usuario_id ";
            $q.="WHERE id = $id";

            try {
                $r = $this->bd->excecuteQuery($q);
                return true;
            } catch (exception $e) {
                error_log($e->getMessage() . " al eliminar la reserva de sala.");
                $error = "Error al eliminar la reserva de sala ";
                new Exception($error);
            }
        }

        // Esto se puede mover a algun utils si se necesita en otro lado
        private function mapped_implode($glue, $array, $symbol = '=') {
            return implode($glue, array_map(
                function($k, $v) use($symbol) {
                    return $k . $symbol . "'".$v."'";
                },
                array_keys($array),
                array_values($array)
                )
            );
        }


    }
?>