<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-usuarioModel.php');
require_once('../model/adminModel.php');

require '../../vendor/autoload.php';

require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';
$tipo = $_GET['tipo'];

//instanciar la clase categoria model
$objSesion = new SessionModel();
$objUsuario = new UsuarioModel();
$objAdmin = new AdminModel();

//variables de sesion
$id_sesion = $_POST['sesion'];
$token = $_POST['token'];

if ($tipo == "validar_datos_reset_password"){
  $id_email = $_POST['id'];
  $token_email = $_POST['token'];
  $arr_Respuesta = array('status'=> false, 'msg' => 'Link Caducado');
  $datos_usuario = $objUsuario->buscarSesionLoginById($id_email);
  if ($datos_usuario->reset_password==1 && password_verify($datos_usuario->token_password,$token_email)) {
    $arr_Respuesta = array('status'=> true, 'msg' => 'ok');
  }
  echo json_encode($arr_Respuesta);
}

if ($tipo == "listar_usuarios_ordenados_tabla") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $pagina = $_POST['pagina'];
        $cantidad_mostrar = $_POST['cantidad_mostrar'];
        $busqueda_tabla_dni = $_POST['busqueda_tabla_dni'];
        $busqueda_tabla_nomap = $_POST['busqueda_tabla_nomap'];
        $busqueda_tabla_estado = $_POST['busqueda_tabla_estado'];
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $busqueda_filtro = $objUsuario->buscarUsuariosOrderByApellidosNombres_tabla_filtro($busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado);
        $arr_Usuario = $objUsuario->buscarUsuariosOrderByApellidosNombres_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado);
        $arr_contenido = [];
        if (!empty($arr_Usuario)) {
            // recorremos el array para agregar las opciones de las categorias
            for ($i = 0; $i < count($arr_Usuario); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_Usuario[$i]->id;
                $arr_contenido[$i]->dni = $arr_Usuario[$i]->dni;
                $arr_contenido[$i]->nombres_apellidos = $arr_Usuario[$i]->nombres_apellidos;
                $arr_contenido[$i]->correo = $arr_Usuario[$i]->correo;
                $arr_contenido[$i]->telefono = $arr_Usuario[$i]->telefono;
                $arr_contenido[$i]->estado = $arr_Usuario[$i]->estado;
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar' . $arr_Usuario[$i]->id . '"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-info" title="Resetear Contraseña" onclick="reset_password(' . $arr_Usuario[$i]->id . ')"><i class="fa fa-key"></i></button>';
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
            $dni = $_POST['dni'];
            $apellidos_nombres = $_POST['apellidos_nombres']; 
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $password = $_POST['password']; // Nuevo campo

            // Validar campos
            if ($dni == "" || $apellidos_nombres == "" || $correo == "" || $telefono == "" || $password == "") {
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos vacíos');
            } else {
                $arr_Usuario = $objUsuario->buscarUsuarioByDni($dni);
                if ($arr_Usuario) {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Registro Fallido, Usuario ya se encuentra registrado');
                } else {
                    // Encriptar la contraseña
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    // Registrar usuario con contraseña encriptada
                    $id_usuario = $objUsuario->registrarUsuario($dni, $apellidos_nombres, $correo, $telefono, $passwordHash);
                    
                    if ($id_usuario > 0) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Registro Exitoso');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar usuario');
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
        //print_r($_POST);
        //repuesta
        if ($_POST) {
            $id = $_POST['data'];
            $dni = $_POST['dni'];
            $nombres_apellidos = $_POST['nombres_apellidos'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $estado = $_POST['estado'];

            if ($id == "" || $dni == "" || $nombres_apellidos == "" || $correo == "" || $telefono == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos vacíos');
            } else {
                $arr_Usuario = $objUsuario->buscarUsuarioByDni($dni);
                if ($arr_Usuario) {
                    if ($arr_Usuario->id == $id) {
                        $consulta = $objUsuario->actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado);
                        if ($consulta) {
                            $arr_Respuesta = array('status' => true, 'mensaje' => 'Actualizado Correctamente');
                        } else {
                            $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar registro');
                        }
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'dni ya esta registrado');
                    }
                } else {
                    $consulta = $objUsuario->actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado);
                    if ($consulta) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Actualizado Correctamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar registro');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}
if ($tipo == "reiniciar_password") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $id_usuario = $_POST['id'];
        $password = $objAdmin->generar_llave(10);
        $pass_secure = password_hash($password, PASSWORD_DEFAULT);
        $actualizar = $objUsuario->actualizarPassword($id_usuario, $pass_secure);
        if ($actualizar) {
            $arr_Respuesta = array('status' => true, 'mensaje' => 'Contraseña actualizado correctamente a: ' . $password);
        } else {
            $arr_Respuesta = array('status' => false, 'mensaje' => 'Hubo un problema al actualizar la contraseña, intente nuevamente');
        }
    }
    echo json_encode($arr_Respuesta);
}
if ($tipo == "sent_email_password") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        $datos_sesion = $objSesion->buscarSesionLoginById($id_sesion);
        $datos_usuario = $objUsuario->buscarUsuarioById($datos_sesion->id_usuario);
        $llave = $objAdmin->generar_llave(30);
        $token = password_hash($llave, PASSWORD_DEFAULT);
        $update = $objUsuario->updateResetPassword($datos_sesion->id_usuario,$llave,1);
        if ($update) {

           //Load Composer's autoloader (created by composer, not included with PHPMailer)
          

          //Create an instance; passing `true` enables exceptions
           $mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.dpweb2024.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'inventario_yudith@dpweb2024.com';                     //SMTP username
    $mail->Password   = '{{!1Cx3H^}0s';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('inventario_yudith@dpweb2024.com', 'Cambio de contraseña');
    $mail->addAddress($datos_usuario->correo, $datos_usuario->nombres_apellidos );     //Add a recipient
    /*$mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');

    //Attachments
    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    */
    //Content
    $mail->isHTML(true); 
    $mail->CharSet = 'UTF-8';                                 //Set email format to HTML
    $mail->Subject = 'Cambio de Contraseña - Sistema de Inventario';
    $mail->Body    = '<!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Correo Empresarial</title>
      <style>
        body {
          margin: 0;
          padding: 0;
          background-color: #FFF0F5; /* Fondo bonito */
          font-family:sans-serif;
        }
        .container {
          max-width: 600px;
          margin: 40px auto;
          background-color: #ffffff;
          border-radius: 12px;
          overflow: hidden;
          box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }
        .header {
          background-color: #F7C6D9; /* Rosa pastel */
          color: #4A1C40;
          text-align: center;
          padding: 30px 20px;
        }
        .header img {
          max-height: 60px;
          margin-bottom: 10px;
        }
        .header h2 {
          margin: 0;
          font-size: 26px;
        }
        .content {
          padding: 30px;
          color: #444;
        }
        .content h1 {
          font-size: 22px;
          margin-bottom: 20px;
          color: #D04C87;
        }
        .content p {
          font-size: 16px;
          line-height: 1.6;
          margin-bottom: 15px;
        }
        .button {
          display: inline-block;
          background-color: #F7C6D9;
          color: #4A1C40 !important;
          padding: 12px 25px;
          margin: 20px 0;
          text-decoration: none;
          border-radius: 8px;
          font-weight: bold;
          border: 2px solid #F7C6D9;
          transition: background-color 0.3s ease;
        }
        .button:hover {
          background-color: #f0a7c0;
          color: white !important;
        }
        .footer {
          background-color: #fce4ec;
          text-align: center;
          padding: 15px;
          font-size: 12px;
          color: #6d4c5b;
        }
        .footer a {
          color: #D04C87;
          text-decoration: none;
        }
        @media screen and (max-width: 600px) {
          .content, .header, .footer {
            padding: 20px !important;
          }
          .button {
            padding: 10px 20px !important;
          }
        }
      </style>
    </head>
    <body>
      <div class="container">
        <div class="header">
        <img src="src/view/img/imagen.png" alt="Logo de la empresa" style="width: 120px; max-height: 60px; height: auto; display: block; margin: 0 auto 15px auto;">
          <h2>Peques con Estilo</h2>
        </div>
        <div class="content">
          <h1>Hola '.$datos_usuario->nombres_apellidos.',</h1>
          <p>
            Hemos recibido una solicitud para cambiar tu contraseña en <strong>Peques con Estilo</strong>.
          </p>
          <p>
            Si fuiste tú quien solicitó este cambio, haz clic en el siguiente botón para restablecer tu contraseña:
          </p>
          <p>
          Si no solicitaste este cambio, por favor ignora este correo. Tu contraseña actual seguirá siendo válida.
        </p>
        <p>Gracias por confiar en nosotros.</p>
      </div>
          <a href="'.BASE_URL.'reset-password?data='.$datos_usuario->id.'&data2='.$token.'" class="button">Cambiar Contraseña</a>
          <p>Gracias por tu preferencia y confianza en nosotros.</p>
        </div>
        <div class="footer">
          © 2025 Peques con Estilo. Todos los derechos reservados.<br>
          <a href="'.BASE_URL.'">Cancelar suscripción</a>
        </div>
      </div>
    </body>
    </html>';
    
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
        } else{
            echo "fallo al actualizar";
        }
        //print_r($token);
    }
}