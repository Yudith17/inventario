<?php
session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-clienteApiModel.php');

$tipo = $_GET['tipo'] ?? '';

// Instanciar las clases
$objSesion = new SessionModel();
$objClienteApi = new ClienteApiModel();

// Variables de sesión
$id_sesion = $_POST['sesion'] ?? '';
$token = $_POST['token'] ?? '';

// Headers para JSON
header('Content-Type: application/json');

if ($tipo == "listar" || $tipo == "listar_activos") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        $arr_ClienteApi = $objClienteApi->buscarClientesApiOrdenados();
        $arr_contenido = [];
        
        if (!empty($arr_ClienteApi)) {
            foreach ($arr_ClienteApi as $cliente) {
                $arr_contenido[] = [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'descripcion' => $cliente->descripcion,
                    'api_key' => $cliente->api_key,
                    'api_secret' => $cliente->api_secret,
                    'ip_permitidas' => $cliente->ip_permitidas,
                    'limite_requests' => $cliente->limite_requests,
                    'estado' => $cliente->estado,
                    'fecha_creacion' => $cliente->fecha_creacion,
                    'fecha_actualizacion' => $cliente->fecha_actualizacion
                ];
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        } else {
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = [];
        }
    }
    echo json_encode($arr_Respuesta);
    exit;
}

if ($tipo == "registrar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        if ($_POST) {
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $ip_permitidas = $_POST['ip_permitidas'] ?? '';
            $limite_requests = $_POST['limite_requests'] ?? '1000';
            $estado = $_POST['estado'] ?? '1';

            if (empty($nombre)) {
                $arr_Respuesta = array('status' => false, 'mensaje' => 'El nombre es obligatorio');
            } else {
                $id_cliente = $objClienteApi->registrarClienteApi($nombre, $descripcion, $ip_permitidas, $limite_requests, $estado);
                if ($id_cliente > 0) {
                    $arr_Respuesta = array('status' => true, 'mensaje' => 'Cliente API registrado exitosamente');
                } else {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar cliente API');
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
    exit;
}

// Si no se reconoce el tipo, devolver error
echo json_encode(array('status' => false, 'mensaje' => 'Tipo de acción no reconocido'));
?>