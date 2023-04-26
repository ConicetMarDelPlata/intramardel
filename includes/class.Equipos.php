<?php

	// CLASS EQUIPOS
	// Autor: Victoria Ganuza

	Class Equipos {
        public $bd;
			
		function __construct($bd_aux){
			$this->bd=$bd_aux;
        }

        function lista_equipo_salas($vData){
            // Fecha: 01 Jul 2020
            // Autor: Victoria Ganuza
    
            // busqueda
            $sHtml = '
            <form name="frmBus" action="lista_equipo_salas.php" method="POST">
            <table class="tabla">
                <tr><th colspan="6">B&uacute;squeda</th></tr>
                <tr class="modo1">
                    <td>
                        Nombre
                    </td>
                    <td>
                        <input type="text" name="nombreBus" value="{nombreBus}" style="width:100%"/>
                    </td>
                    <td>
                        <input type="submit" name="btnBus" value="Buscar"/>
                    </td>
                </tr>
            </table>
            </form>';
            
            if (array_key_exists('nombreBus', $vData))
                $nombreBus = $vData['nombreBus'];
            else
                $nombreBus = "";
            
            $sHtml = str_replace("{nombreBus}",$nombreBus,$sHtml);
    
            echo $sHtml;	
    
            $sWhere = '';
    
            if ($nombreBus != "") {				
                $sWhere .= ' AND nombre LIKE "'.$nombreBus.'%" ';
            }
    
            echo '<table width="709" border="0" cellpadding="1" cellspacing="1" align="center" class="tabla table-autosort table-autofilter">';
            echo '<thead><tr>';
            echo '<th class="table-sortable:default">Nombre</th>'; 
                echo '<th class="table-sortable:default" colspan="3">Acciones</th>';			
            echo '</tr></thead>';
    
            $q = "SELECT * FROM equipos WHERE baja=0 $sWhere Order By nombre";
    
            $r = $this->bd->excecuteQuery($q);
            $fila = 1;
    
            while ( $row = mysqli_fetch_array($r) ){
                $lnkmodificar = 'form_equipo_salas.php?id=' . $row['id'] . '&opcion=3';
                $lnkborrar = 'form_equipo_salas.php?id=' . $row['id'] . '&opcion=2';				
                $lnkvisualizar = 'form_equipo_salas.php?id=' . $row['id'] . '&opcion=4';
    
                echo '<tr class="modo1">';
                    
                echo '<td>' . $row['nombre'] . '</td>';
    
                if($this->bd->checkPerm($_SESSION["id_usuario"],35,'baja')){				
                    echo '<td align="center"><font color="#333333"><a href=' . $lnkborrar .  '><img src="eliminar.png" width="30" height="30" border="0" alt="Borrar Registro" title="Borrar Registro"></a></td>';					
                }else
                    echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/eliminarg.png" width="30" height="30" border="0" alt="Borrar Registro" title="Borrar Registro"></a></td>';
                
                if($this->bd->checkPerm($_SESSION["id_usuario"],35,'modificacion')){	
                    echo '<td align="center"><font color="#333333"><a href=' . $lnkmodificar.  '><img src="actualizar_datos.png" width="30" height="30" border="0" title="Modificar Registro" alt="Modificar Registro"></a></td>';
                }else
                    echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/actualizar_datosg.png" width="30" height="30" border="0" title="Modificar Registro" alt="Modificar Registro"></a></td>';
    
                if($this->bd->checkPerm($_SESSION["id_usuario"],35,'consulta')){	
                    echo '<td align="center"><font color="#333333"><a href=' . $lnkvisualizar.  '><img src="previsualizar.png" width="30" height="30" border="0" alt="Ver Registro" title="Ver Registro"></a></td>';
                }else
                    echo '<td align="center"><font color="#333333"><a href="#"><img src="iconos_grises/previsualizarg.png" width="30" height="30" border="0" alt="Ver Registro" title="Ver Registro"></a></td>';
                
                $fila++;
            }
            echo '</table>';
        }

        
        function consultar_equipo($id){
        // Fecha: 01 Jul 2020
        // Autor: Victoria Ganuza		
            $q = "SELECT * FROM equipos WHERE id = $id";
            $r = $this->bd->excecuteQuery($q);
            $row = mysqli_fetch_array($r);
            return $row;		
        }

        function check_nombre_equipo($nombre, $id){
            // Fecha: 01 Jul 2020
            // Autor: Victoria
            // Devuelve true si no existe un equipo igual al parametro 1. 
    
            if (trim($nombre) != "") {
                
                $q = "SELECT
                        count(*) as cant 
                    FROM 
                        equipos
                    WHERE 
                        nombre ='" . $nombre . "' and 
                        baja = 0 and
                        id != '". $id . "'";
    
                $r = $this->bd->excecuteQuery($q);			
                $row = mysqli_fetch_array($r);
                $cant = $row['cant'];
                mysqli_free_result($r);
                return $cant==0;
            } else
                return true;
        }

        function agregar_equipo($nombre) {
            // Fecha: 01 jul 2020
            // Autor: Victoria Ganuza
    
            $q = "INSERT INTO equipos (nombre, baja) VALUES ('$nombre',0)";
            
            $this->bd->excecuteQuery($q);
    
            $rs = $this->bd->excecuteQuery("SELECT @@identity AS id");
            if ($row = mysqli_fetch_row($rs)) {
                $id = trim($row[0]);
                $eq = "SELECT id FROM salas_conferencia WHERE baja = 0";
                $r = $this->bd->excecuteQuery($eq);
    
                while ($row = mysqli_fetch_array($r)){
                    $id_s = $row['id'];
                    $eqE = "INSERT INTO equipos_x_salas (key_salas_id, key_equipos_id, asignada, baja) VALUES ('$id_s','$id',0,0)";
                    $this->bd->excecuteQuery($eqE);
                }
            } 
        }
    
        
        function modificar_equipo($id_equipo, $nombre){
            // Fecha: 01 jul 2020
            // Autor: Victoria Ganuza
            
            $q = "UPDATE equipos SET nombre='$nombre' WHERE id='$id_equipo'";
            $this->bd->excecuteQuery($q);

        }

        function borrar_equipo($id){
            // Fecha: 01 Jul  2020
            // Autor: Victoria Ganuza	
        
            $q = "UPDATE equipos SET baja=1 WHERE id='$id'";
    
            $this->bd->excecuteQuery($q);
    
            $q = "SELECT equipos_x_salas.id as id,  equipos.nombre as nombre, equipos_x_salas.asignada as checked 
                    FROM equipos JOIN equipos_x_salas 
                    WHERE equipos_x_salas.baja = 0 AND equipos_x_salas.key_equipos_id = equipos.id AND equipos_x_salas.key_equipos_id =". $id;
    
            $r = $this->bd->excecuteQuery($q);
    
            while ($row=mysqli_fetch_array($r)) {
                $id = $row['id'];
                $ed = "UPDATE equipos_x_salas SET baja=1 WHERE id='$id'";
                $this->bd->excecuteQuery($ed);
            }
            
        }
        
        function consultar_equipos_x_sala($id_sala){
            // Fecha: 18 Sep 2020
            // Autor: Victoria
            // Devuelve la lista de equipos por sala.
        
            $eq = array();
            $q = "SELECT equipos_x_salas.id, equipos_x_salas.asignada AS checked, equipos.nombre";
            $q.= " FROM equipos_x_salas LEFT JOIN equipos ON equipos.id = equipos_x_salas.key_equipos_id WHERE equipos_x_salas.baja = 0 AND key_salas_id = $id_sala";
            $r = $this->bd->excecuteQuery($q);
            while ($row = mysqli_fetch_array($r)){
                array_push($eq,$row);
            }
            return $eq;
        }
            
    }
?>