<?php
require_once "../library/conexion.php";

class TokenModel
{
    private $conexion;
    
    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }
    
    public function buscarTokens_tabla($pagina = 1, $cantidad_mostrar = 10, $id_cliente_api = '', $estado = '')
    {
        $condicion = "1=1";
        if (!empty($id_cliente_api)) {
            $condicion .= " AND t.id_cliente_api = '$id_cliente_api'";
        }
        if (!empty($estado)) {
            $condicion .= " AND t.estado = '$estado'";
        }
        
        $iniciar = ($pagina - 1) * $cantidad_mostrar;
        $arrRespuesta = array();
        
        $sql = $this->conexion->query("
            SELECT t.*, c.nombre as cliente_nombre 
            FROM tokens_acceso t 
            LEFT JOIN cliente_api c ON t.id_cliente_api = c.id 
            WHERE $condicion 
            ORDER BY t.fecha_creacion DESC 
            LIMIT $iniciar, $cantidad_mostrar
        ");
        
        if ($sql) {
            while ($objeto = $sql->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        return $arrRespuesta;
    }
    
    public function contarTokens($id_cliente_api = '', $estado = '')
    {
        $condicion = "1=1";
        if (!empty($id_cliente_api)) {
            $condicion .= " AND id_cliente_api = '$id_cliente_api'";
        }
        if (!empty($estado)) {
            $condicion .= " AND estado = '$estado'";
        }
        
        $sql = $this->conexion->query("SELECT COUNT(*) as total FROM tokens_acceso WHERE $condicion");
        if ($sql && $sql->num_rows > 0) {
            $resultado = $sql->fetch_object();
            return $resultado->total;
        }
        return 0;
    }
    
    public function revocarToken($id)
    {
        $sql = $this->conexion->query("UPDATE tokens_acceso SET estado = 'revocado' WHERE id = '$id'");
        return $sql;
    }
    
    public function getLastError()
    {
        return $this->conexion->error;
    }
}
?>