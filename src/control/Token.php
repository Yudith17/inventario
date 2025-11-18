<?php
session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-tokenModel.php');

$tipo = $_GET['tipo'] ?? '';

// Instanciar las clases
$objSesion = new SessionModel();
$objToken = new TokenModel();

// Variables de sesión
$id_sesion = $_POST['sesion'] ?? '';
$token = $_POST['token'] ?? '';

// Headers para JSON
header('Content-Type: application/json');

if ($tipo == "listar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        $pagina = $_POST['pagina'] ?? 1;
        $cantidad_mostrar = $_POST['cantidad_mostrar'] ?? 10;
        $id_cliente_api = $_POST['id_cliente_api'] ?? '';
        $estado = $_POST['estado'] ?? '';
        
        $arr_Tokens = $objToken->buscarTokens_tabla($pagina, $cantidad_mostrar, $id_cliente_api, $estado);
        $total_tokens = $objToken->contarTokens($id_cliente_api, $estado);
        
        $arr_contenido = [];
        if (!empty($arr_Tokens)) {
            foreach ($arr_Tokens as $token) {
                $arr_contenido[] = [
                    'id' => $token->id,
                    'cliente_api_nombre' => $token->cliente_nombre,
                    'token_acceso' => substr($token->token_acceso, 0, 50) . '...',
                    'fecha_expiracion' => $token->fecha_expiracion,
                    'estado' => $token->estado,
                    'ip_origen' => $token->ip_origen,
                    'fecha_creacion' => $token->fecha_creacion
                ];
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
            $arr_Respuesta['total'] = $total_tokens;
        } else {
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = [];
            $arr_Respuesta['total'] = 0;
        }
    }
    echo json_encode($arr_Respuesta);
    exit;
}

if ($tipo == "revocar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        $id_token = $_POST['id'] ?? '';
        
        if (empty($id_token)) {
            $arr_Respuesta = array('status' => false, 'mensaje' => 'ID de token no válido');
        } else {
            $resultado = $objToken->revocarToken($id_token);
            if ($resultado) {
                $arr_Respuesta = array('status' => true, 'mensaje' => 'Token revocado exitosamente');
            } else {
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al revocar token');
            }
        }
    }
    echo json_encode($arr_Respuesta);
    exit;
}

echo json_encode(array('status' => false, 'mensaje' => 'Tipo de acción no reconocido'));
?>