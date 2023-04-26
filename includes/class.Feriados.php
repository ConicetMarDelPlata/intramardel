<?php

	// CLASS FERIADOS
	// Autor: Victoria Ganuza

	Class Feriados {
		public $bd;
			
		function __construct($bd_aux){
			$this->bd=$bd_aux;
		}

        // TODO: Unificar con el metodo para chequear feriados que ya existe
		function esFeriado($dia, $mes, $año) {

            $q = "SELECT count(*) as cant FROM `feriados` WHERE baja=0 AND fecha = '$año-$mes-$dia';";
			try {
                $r = $this->bd->excecuteQuery($q);
                $feriados = mysqli_fetch_array($r);
				return ($feriados['cant'] > 0);
            } catch (exception $e) {
                error_log($e->getMessage() . " al consultar feriado.");
                $error = "Error al consultar feriado ";
                new Exception($error);
            }
		}

        function datosFeriado($dia, $mes, $año) {

            $q = "SELECT descripcion FROM feriados WHERE baja=0 AND fecha = '$año-$mes-$dia';";
			try {
                $r = $this->bd->excecuteQuery($q);
                $feriados = mysqli_fetch_array($r);
				return ($feriados['descripcion']);
            } catch (exception $e) {
                error_log($e->getMessage() . " al consultar feriado.");
                $error = "Error al consultar feriado ";
                new Exception($error);
            }
		}
		function lista_feriados($vData){
			// Fecha: 29 Jun 2020
			// Autor: Victoria Ganuza
	
			// busqueda
			$sHtml = '
			<form name="frmBus" action="lista_feriados.php" method="POST">
			<table class="tabla">
				<tr><th colspan="6">B&uacute;squeda</th></tr>
				<tr class="modo1">
					<td>
						Fecha
					</td>
					<td>
						<input type="date" name="fechaBus" value="{fechaBus}" style="width:150px"/>
					</td>
					<td>
						Descripción
					</td>
					<td>
						<input type="text" name="descripcionBus" value="{descripcionBus}" style="width:150px"/>
					</td>
					<td>
						<input type="submit" name="btnBus" value="Buscar"/>
					</td>
				</tr>
			</table>
			</form>';
			
			if (array_key_exists('fechaBus', $vData))
				$fechaBus = $vData['fechaBus'];
			else
				$fechaBus = "";
	
			if (array_key_exists('descripcionBus', $vData))
				$descripcionBus = $vData['descripcionBus'];
			else
				$descripcionBus = "";
			
			$sHtml = str_replace("{fechaBus}",$fechaBus,$sHtml);
			$sHtml = str_replace("{descripcionBus}",$descripcionBus,$sHtml);
	
			echo $sHtml;	
	
			$sWhere = '';
	
			if ($fechaBus != "") {				
				$sWhere .= " AND fecha='".$fechaBus."'";
			} 
			if ($descripcionBus != "") {				
				$sWhere .= ' AND descripcion LIKE "'.$descripcionBus.'%" ';
			}
	
			echo '<table width="709" border="0" cellpadding="1" cellspacing="1" align="center" class="tabla table-autosort table-autofilter">';
			echo '<thead><tr>';
				echo '<th class="table-sortable:default">Fecha</th>';
			echo '<th class="table-sortable:default">Descripcion</th>'; 
				echo '<th class="table-sortable:default" colspan="3">Acciones</th>';			
			echo '</tr></thead>';
	
			$q = "SELECT * FROM feriados WHERE baja=0 $sWhere Order By fecha";
	
            try {
                $r = $this->bd->excecuteQuery($q);
            } catch (exception $e) {
                error_log($e->getMessage() . " al listar feriado.");
                $error = "Error al listar feriado ";
                new Exception($error);
            }

			$fila = 1;
	
			while ( $row = mysqli_fetch_array($r) ){
				$lnkmodificar = 'form_feriado.php?id=' . $row['id'] . '&opcion=3';
				$lnkborrar = 'form_feriado.php?id=' . $row['id'] . '&opcion=2';				
				$lnkvisualizar = 'form_feriado.php?id=' . $row['id'] . '&opcion=4';
	
				echo '<tr class="modo1">';
					
				echo '<td>' . convertir_fecha($row['fecha']) .'</td>';
				echo '<td>' . $row['descripcion'] . '</td>';
	
				if($this->bd->checkPerm($_SESSION["id_usuario"],34,'baja')){				
					echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro" title="Borrar Registro"></a></td>';					
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro" title="Borrar Registro"></a></td>';
				
				if($this->bd->checkPerm($_SESSION["id_usuario"],34,'modificacion')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar.  '><img src="actualizar_datos.png" width="30" height="30" border="0" title="Modificar Registro" alt="Modificar Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" title="Modificar Registro" alt="Modificar Registro"></a></td>';
	
				if($this->bd->checkPerm($_SESSION["id_usuario"],34,'consulta')){	
					echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar.  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro" title="Ver Registro"></a></td>';
				}else
					echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/previsualizarg.png" width="30" height="30" border="0" alt="Ver Registro" title="Ver Registro"></a></td>';
				
				$fila++;
			}
			echo '</table>';
        }
        
        function agregar_feriado($fecha, $descripcion) {
            // Fecha: 30 jun 2020
            // Autor: Victoria Ganuza
    
            $q = "INSERT INTO feriados (fecha, descripcion, baja) VALUES ('$fecha', '$descripcion',0)";
            echo $q;
            try {
                $r = $this->bd->excecuteQuery($q);
               
            } catch (exception $e) {
                error_log($e->getMessage() . " al agregar feriado.");
                $error = "Error al agregar feriado ";
                new Exception($error);
            }
        }

        function consultar_feriado($id){
            // Fecha: 30 Jun 2020
            // Autor: Victoria Ganuza		
            $q = "SELECT * FROM feriados WHERE id = $id";
            try {
                $r = $this->bd->excecuteQuery($q);
               
            } catch (exception $e) {
                error_log($e->getMessage() . " al consultar feriado.");
                $error = "Error al consultar feriado ";
                new Exception($error);
            }
            $row = mysqli_fetch_array($r);
            return $row;		
        }

        function modificar_feriado($id_feriado, $fecha, $descripcion){
            // Fecha: 30 jun 2020
            // Autor: Victoria Ganuza
            
            $q = "UPDATE feriados SET fecha='$fecha', descripcion='$descripcion' WHERE id='$id_feriado'";
            try {
                $r = $this->bd->excecuteQuery($q);
               
            } catch (exception $e) {
                error_log($e->getMessage() . " al modificar feriado.");
                $error = "Error al modificar feriado ";
                new Exception($error);
            }
    
        }

        function borrar_feriado($id){
            // Fecha: 30 Jun  2020
            // Autor: Victoria Ganuza	
        
            $q = "UPDATE feriados SET baja=1 WHERE id='$id'";
    
            try {
                $r = $this->bd->excecuteQuery($q);
            } catch (exception $e) {
                error_log($e->getMessage() . " al borrar feriado.");
                $error = "Error al borrar feriado ";
                new Exception($error);
            }
            
        }

        function check_fecha_feriado($fecha, $id){
            // Fecha: 30 Jun 2020
            // Autor: Victoria
            // Devuelve true si no existe un Feriado con fecha igual al parametro 1. 
    
            if (trim($fecha) != "") {
                
                $q = 'SELECT
                        count(*) as cant 
                    FROM 
                        feriados
                    WHERE 
                        fecha ="' . $fecha . '" and 
                        baja = 0 and
                        id != "'. $id . '"';

                try {
                    $r = $this->bd->excecuteQuery($q);
                   
                } catch (exception $e) {
                    error_log($e->getMessage() . " al consultar feriado.");
                    $error = "Error al consultar feriado ";
                    new Exception($error);
                }

                $row = mysqli_fetch_array($r);
                $cant = $row['cant'];
                mysqli_free_result($r);
                return $cant==0;
            } else
                return true;
        }
	}
?>