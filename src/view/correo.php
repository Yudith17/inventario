<!DOCTYPE html>
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
      font-family: 'Segoe UI', sans-serif;
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
        Si no solicitaste este cambio, por favor ignora este correo. Tu contraseña actual seguirá siendo válida.
      </p>
      <p>Gracias por confiar en nosotros.</p>
    </div>
    <a href="'.BASE_URL.'reset-password/'.$datos_usuario->id.'/'.$token.'" class="button">Cambiar Contraseña</a>
      <p>Gracias por tu preferencia y confianza en nosotros.</p>
    </div>
    <div class="footer">
      © 2025 Peques con Estilo. Todos los derechos reservados.<br>
      <a href="'.BASE_URL.'">Cancelar suscripción</a>
    </div>
  </div>
</body>
</html>
