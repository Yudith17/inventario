<?php
require_once "../library/conexion.php";

class ClienteApiModel
{
    private $conexion;
    
    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }
    
    public function registrarClienteApi($nombre, $descripcion, $ip_permitidas, $limite_requests, $estado)
    {
        // Generar API Key y Secret automáticamente
        $api_key = 'key_' . bin2hex(random_bytes(16));
        $api_secret = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
        
        $sql = $this->conexion->query("INSERT INTO cliente_api (nombre, descripcion, api_key, api_secret, ip_permitidas, limite_requests, estado) 
                                     VALUES ('$nombre','$descripcion','$api_key','$api_secret','$ip_permitidas','$limite_requests','$estado')");
        if ($sql) {
            return $this->conexion->insert_id;
        } else {
            return 0;
        }
    }
    
    public function buscarClientesApiOrdenados()
    {
        $arrRespuesta = array();
        $sql = $this->conexion->query("SELECT * FROM cliente_api ORDER BY nombre ASC");
        if ($sql) {
            while ($objeto = $sql->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        return $arrRespuesta;
    }
    
    public function buscarClienteApiById($id)
    {
        $sql = $this->conexion->query("SELECT * FROM cliente_api WHERE id='$id'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarClienteApiByKey($api_key)
    {
        $sql = $this->conexion->query("SELECT * FROM cliente_api WHERE api_key='$api_key' AND estado='1'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function getLastError()
    {
        return $this->conexion->error;
    }
}
?>