<?php
session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-hotelModel.php');

$tipo = $_GET['tipo'];

// Instanciar las clases
$objSesion = new SessionModel();
$objHotel = new HotelModel();

// Variables de sesión
$id_sesion = $_POST['sesion'] ?? '';
$token = $_POST['token'] ?? '';

if ($tipo == "listar_hoteles_tabla") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        $pagina = $_POST['pagina'] ?? 1;
        $cantidad_mostrar = $_POST['cantidad_mostrar'] ?? 10;
        $busqueda_tabla_nombre = $_POST['busqueda_tabla_nombre'] ?? '';
        $busqueda_tabla_ruc = $_POST['busqueda_tabla_ruc'] ?? '';
        $busqueda_tabla_estado = $_POST['busqueda_tabla_estado'] ?? '';
        
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $busqueda_filtro = $objHotel->buscarHoteles_tabla_filtro($busqueda_tabla_nombre, $busqueda_tabla_ruc, $busqueda_tabla_estado);
        $arr_Hotel = $objHotel->buscarHoteles_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_nombre, $busqueda_tabla_ruc, $busqueda_tabla_estado);
        
        $arr_contenido = [];
        if (!empty($arr_Hotel)) {
            for ($i = 0; $i < count($arr_Hotel); $i++) {
                $arr_contenido[$i] = (object) [];
                $arr_contenido[$i]->id = $arr_Hotel[$i]->id;
                $arr_contenido[$i]->nombre = $arr_Hotel[$i]->nombre;
                $arr_contenido[$i]->direccion = $arr_Hotel[$i]->direccion;
                $arr_contenido[$i]->telefono = $arr_Hotel[$i]->telefono;
                $arr_contenido[$i]->email = $arr_Hotel[$i]->email;
                $arr_contenido[$i]->ruc = $arr_Hotel[$i]->ruc;
                $arr_contenido[$i]->estrellas = $arr_Hotel[$i]->estrellas;
                $arr_contenido[$i]->estado = $arr_Hotel[$i]->estado;
                
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" 
                            data-toggle="modal" data-target=".modal_editar' . $arr_Hotel[$i]->id . '">
                            <i class="fa fa-edit"></i></button>';
                $arr_contenido[$i]->options = $opciones;
            }
            $arr_Respuesta['total'] = count($busqueda_filtro);
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "registrar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        if ($_POST) {
            $nombre = $_POST['nombre'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $email = $_POST['email'] ?? '';
            $ruc = $_POST['ruc'] ?? '';
            $estrellas = $_POST['estrellas'] ?? '';
            $estado = $_POST['estado'] ?? '';

            if ($nombre == "" || $direccion == "" || $telefono == "" || $ruc == "" || $estrellas == "" || $estado == "") {
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $arr_Hotel = $objHotel->buscarHotelByRuc($ruc);
                if ($arr_Hotel) {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Registro Fallido, Hotel con RUC ya registrado');
                } else {
                    // CORRECCIÓN: Solo pasar 7 parámetros
                    $id_hotel = $objHotel->registrarHotel($nombre, $direccion, $telefono, $email, $ruc, $estrellas, $estado);
                    if ($id_hotel > 0) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Hotel registrado exitosamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar hotel');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "actualizar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        if ($_POST) {
            $id = $_POST['data'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $email = $_POST['email'] ?? '';
            $ruc = $_POST['ruc'] ?? '';
            $estrellas = $_POST['estrellas'] ?? '';
            $estado = $_POST['estado'] ?? '';

            if ($id == "" || $nombre == "" || $direccion == "" || $telefono == "" || $ruc == "" || $estrellas == "" || $estado == "") {
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $arr_Hotel = $objHotel->buscarHotelByRuc($ruc);
                if ($arr_Hotel && $arr_Hotel->id != $id) {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'RUC ya está registrado en otro hotel');
                } else {
                    // CORRECCIÓN: Solo pasar 8 parámetros (incluye $id)
                    $consulta = $objHotel->actualizarHotel($id, $nombre, $direccion, $telefono, $email, $ruc, $estrellas, $estado);
                    if ($consulta) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Hotel actualizado correctamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar hotel');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}

// Agregar este caso para listar hoteles activos
if ($tipo == "listar_activos") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        $arr_Hotel = $objHotel->buscarHotelesActivos();
        $arr_contenido = [];
        if (!empty($arr_Hotel)) {
            foreach ($arr_Hotel as $hotel) {
                $arr_contenido[] = [
                    'id' => $hotel->id,
                    'nombre' => $hotel->nombre,
                    'direccion' => $hotel->direccion,
                    'telefono' => $hotel->telefono,
                    'email' => $hotel->email,
                    'ruc' => $hotel->ruc,
                    'estrellas' => $hotel->estrellas,
                    'estado' => $hotel->estado
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
}
?>