<?php
require_once('../model/admin-clienteApiModel.php');
require_once('../model/admin-tokenModel.php');

class ApiAuthController
{
    private $clienteApiModel;
    private $tokenModel;
    
    function __construct()
    {
        $this->clienteApiModel = new ClienteApiModel();
        $this->tokenModel = new TokenModel();
    }
    
    public function autenticarCliente($api_key, $api_secret)
    {
        $cliente = $this->clienteApiModel->buscarClienteApiByKey($api_key);
        
        if (!$cliente) {
            return array('status' => false, 'mensaje' => 'Cliente API no encontrado');
        }
        
        if (!password_verify($api_secret, $cliente->api_secret)) {
            return array('status' => false, 'mensaje' => 'Credenciales inválidas');
        }
        
        // Verificar límite de requests
        $requests_hoy = $this->clienteApiModel->contarRequestsHoy($cliente->id);
        if ($requests_hoy >= $cliente->limite_requests) {
            return array('status' => false, 'mensaje' => 'Límite de requests diarios excedido');
        }
        
        return array('status' => true, 'cliente' => $cliente);
    }
    
    public function generarTokens($id_cliente_api, $ip_origen, $user_agent)
    {
        // Generar tokens JWT
        $token_acceso = $this->generarJWT($id_cliente_api, 3600); // 1 hora
        $token_renovacion = $this->generarJWT($id_cliente_api, 86400); // 24 horas
        
        $fecha_expiracion = date('Y-m-d H:i:s', time() + 3600);
        $fecha_renovacion = date('Y-m-d H:i:s', time() + 86400);
        
        $id_token = $this->tokenModel->generarToken(
            $id_cliente_api, 
            $token_acceso, 
            $token_renovacion, 
            $fecha_expiracion, 
            $fecha_renovacion, 
            $ip_origen, 
            $user_agent
        );
        
        if ($id_token > 0) {
            return array(
                'status' => true,
                'token_acceso' => $token_acceso,
                'token_renovacion' => $token_renovacion,
                'expiracion' => $fecha_expiracion
            );
        }
        
        return array('status' => false, 'mensaje' => 'Error al generar tokens');
    }
    
    public function validarToken($token_acceso)
    {
        $token = $this->tokenModel->buscarTokenByAccessToken($token_acceso);
        
        if (!$token) {
            return array('status' => false, 'mensaje' => 'Token inválido o expirado');
        }
        
        // Verificar límite de requests
        $requests_hoy = $this->clienteApiModel->contarRequestsHoy($token->id_cliente_api);
        $cliente = $this->clienteApiModel->buscarClienteApiById($token->id_cliente_api);
        
        if ($requests_hoy >= $cliente->limite_requests) {
            return array('status' => false, 'mensaje' => 'Límite de requests diarios excedido');
        }
        
        return array('status' => true, 'token' => $token);
    }
    
    public function renovarToken($token_renovacion, $ip_origen, $user_agent)
    {
        $token = $this->tokenModel->buscarTokenByRefreshToken($token_renovacion);
        
        if (!$token) {
            return array('status' => false, 'mensaje' => 'Token de renovación inválido');
        }
        
        // Invalidar token actual
        $this->tokenModel->invalidarToken($token->id);
        
        // Generar nuevos tokens
        return $this->generarTokens($token->id_cliente_api, $ip_origen, $user_agent);
    }
    
    private function generarJWT($id_cliente_api, $expiracion_segundos)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'iss' => 'hotel_api',
            'iat' => time(),
            'exp' => time() + $expiracion_segundos,
            'client_id' => $id_cliente_api
        ]);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'clave_secreta_hotel', true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
?>