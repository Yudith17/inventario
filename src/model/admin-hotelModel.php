<?php
require_once "../library/conexion.php";

class HotelModel
{
    private $conexion;
    
    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }
    
    public function registrarHotel($nombre, $direccion, $telefono, $email, $ruc, $estrellas, $estado)
{
    $sql = $this->conexion->query("INSERT INTO hoteles (nombre, direccion, telefono, email, ruc, estrellas, estado) 
                                 VALUES ('$nombre','$direccion','$telefono','$email','$ruc','$estrellas','$estado')");
    if ($sql) {
        $sql = $this->conexion->insert_id;
    } else {
        $sql = 0;
    }
    return $sql;
}

public function actualizarHotel($id, $nombre, $direccion, $telefono, $email, $ruc, $estrellas, $estado)
{
    $sql = $this->conexion->query("UPDATE hoteles SET nombre='$nombre', direccion='$direccion', telefono='$telefono', 
                                 email='$email', ruc='$ruc', estrellas='$estrellas', estado='$estado' 
                                 WHERE id='$id'");
    return $sql;
}
    
    public function buscarHotelById($id)
    {
        $sql = $this->conexion->query("SELECT * FROM hoteles WHERE id='$id'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarHotelByRuc($ruc)
    {
        // El campo RUC no existe en la tabla hoteles, retornamos null
        return null;
    }
    
    public function buscarHotelByNombre($nombre)
    {
        $sql = $this->conexion->query("SELECT * FROM hoteles WHERE nombre='$nombre'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarHotelByEmail($email)
    {
        $sql = $this->conexion->query("SELECT * FROM hoteles WHERE email='$email'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarHoteles_tabla_filtro($busqueda_tabla_nombre, $busqueda_tabla_ciudad, $busqueda_tabla_estado)
    {
        $condicion = " 1=1 ";
        if ($busqueda_tabla_nombre != '') {
            $condicion .= " AND nombre LIKE '%$busqueda_tabla_nombre%'";
        }
        if ($busqueda_tabla_ciudad != '') {
            $condicion .= " AND ciudad LIKE '%$busqueda_tabla_ciudad%'";
        }
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND estado = '$busqueda_tabla_estado'";
        }
        
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT * FROM hoteles WHERE $condicion ORDER BY nombre");
        if ($respuesta) {
            while ($objeto = $respuesta->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        return $arrRespuesta;
    }
    
    public function buscarHoteles_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_nombre, $busqueda_tabla_ciudad, $busqueda_tabla_estado)
    {
        $condicion = " 1=1 ";
        if ($busqueda_tabla_nombre != '') {
            $condicion .= " AND nombre LIKE '%$busqueda_tabla_nombre%'";
        }
        if ($busqueda_tabla_ciudad != '') {
            $condicion .= " AND ciudad LIKE '%$busqueda_tabla_ciudad%'";
        }
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND estado = '$busqueda_tabla_estado'";
        }
        
        $iniciar = ($pagina - 1) * $cantidad_mostrar;
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT * FROM hoteles WHERE $condicion ORDER BY nombre LIMIT $iniciar, $cantidad_mostrar");
        if ($respuesta) {
            while ($objeto = $respuesta->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        return $arrRespuesta;
    }
    
    public function buscarHotelesActivos()
    {
        $arrRespuesta = array();
        $sql = $this->conexion->query("SELECT * FROM hoteles WHERE estado='1' ORDER BY nombre ASC");
        if ($sql) {
            while ($objeto = $sql->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        return $arrRespuesta;
    }

    // Método para depuración - obtener el último error
    public function getLastError()
    {
        return $this->conexion->error;
    }
}
?>