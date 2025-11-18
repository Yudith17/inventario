<?php
require_once "../library/conexion.php";

class UsuarioModel
{
    private $conexion;
    
    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }
    
    public function registrarUsuario($dni, $apellidos_nombres, $correo, $telefono, $password)
    {
        $password_secure = password_hash($password, PASSWORD_DEFAULT);
        $sql = $this->conexion->query("INSERT INTO usuarios (dni, nombres_apellidos, correo, telefono, password) VALUES ('$dni','$apellidos_nombres','$correo','$telefono', '$password_secure')");
        if ($sql) {
            $sql = $this->conexion->insert_id;
        } else {
            $sql = 0;
        }
        return $sql;
    }
    
    public function actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado)
    {
        $sql = $this->conexion->query("UPDATE usuarios SET dni='$dni',nombres_apellidos='$nombres_apellidos',correo='$correo',telefono='$telefono',estado ='$estado' WHERE id='$id'");
        return $sql;
    }
    
    // Método mejorado para actualizar contraseña con encriptación
    public function actualizarPassword($id, $password)
    {
        $password_secure = password_hash($password, PASSWORD_DEFAULT);
        $sql = $this->conexion->query("UPDATE usuarios SET password ='$password_secure' WHERE id='$id'");
        return $sql;
    }
    
    // Nuevo método para actualizar contraseña y limpiar datos de reset
    public function actualizarPasswordYLimpiarReset($id, $password)
    {
        $password_secure = password_hash($password, PASSWORD_DEFAULT);
        $sql = $this->conexion->query("UPDATE usuarios SET password ='$password_secure', reset_password='0', token_password='' WHERE id='$id'");
        return $sql;
    }
    
    public function updateResetPassword($id, $token, $estado)
    {
        $sql = $this->conexion->query("UPDATE usuarios SET token_password ='$token', reset_password='$estado' WHERE id='$id'");
        return $sql;
    }

    public function buscarUsuarioById($id)
    {
        $sql = $this->conexion->query("SELECT * FROM usuarios WHERE id='$id'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarUsuarioByDni($dni)
    {
        $sql = $this->conexion->query("SELECT * FROM usuarios WHERE dni='$dni'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarUsuarioByNomAp($nomap)
    {
        $sql = $this->conexion->query("SELECT * FROM usuarios WHERE nombres_apellidos='$nomap'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarUsuarioByApellidosNombres_like($dato)
    {
        $sql = $this->conexion->query("SELECT * FROM usuarios WHERE nombres_apellidos LIKE '%$dato%'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarUsuarioByDniCorreo($dni, $correo)
    {
        $sql = $this->conexion->query("SELECT * FROM usuarios WHERE dni='$dni' AND correo='$correo'");
        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_object();
        }
        return null;
    }
    
    public function buscarUsuariosOrdenados()
    {
        $arrRespuesta = array();
        $sql = $this->conexion->query("SELECT * FROM usuarios WHERE estado='1' ORDER BY nombres_apellidos ASC");
        if ($sql) {
            while ($objeto = $sql->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        return $arrRespuesta;
    }
   
    public function buscarUsuariosOrderByApellidosNombres_tabla_filtro($busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado)
    {
        $condicion = "dni LIKE '$busqueda_tabla_dni%' AND nombres_apellidos LIKE '$busqueda_tabla_nomap%'";
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND estado = '$busqueda_tabla_estado'";
        }
        
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT * FROM usuarios WHERE $condicion ORDER BY nombres_apellidos");
        if ($respuesta) {
            while ($objeto = $respuesta->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        return $arrRespuesta;
    }
    
    public function buscarUsuariosOrderByApellidosNombres_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado)
    {
        $condicion = "dni LIKE '$busqueda_tabla_dni%' AND nombres_apellidos LIKE '$busqueda_tabla_nomap%'";
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND estado = '$busqueda_tabla_estado'";
        }
        
        $iniciar = ($pagina - 1) * $cantidad_mostrar;
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT * FROM usuarios WHERE $condicion ORDER BY nombres_apellidos LIMIT $iniciar, $cantidad_mostrar");
        if ($respuesta) {
            while ($objeto = $respuesta->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        return $arrRespuesta;
    }

<<<<<<< HEAD
    // Método para depuración - obtener el último error
    public function getLastError()
    {
        return $this->conexion->error;
    }
}
=======
    public function listarUsuarios(){
        $arrRespuesta = array();
      $sql = $this->conexion->query("SELECT * FROM usuarios");
      while ($objeto = $sql->fetch_object()) {
          array_push($arrRespuesta, $objeto);
      }
      return $arrRespuesta;
  }
}
>>>>>>> 20cf708c6cb11a97058400688450a9d2e0ce9ba8
