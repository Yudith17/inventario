<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Actualizar Contraseña</title>
  <style>
     body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #333;
      background: linear-gradient(-45deg, #fbc2eb, #a6c1ee, #fad0c4, #ffdde1);
      background-size: 400% 400%;
      animation: animateBackground 15s ease infinite;
    }

    @keyframes animateBackground {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }

    .login-container {
      width: 340px;
      padding: 40px 30px;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0,0,0,0.15);
      text-align: center;
      animation: fadeIn 0.7s ease-in-out;
    }

    .login-container h1 {
      margin-bottom: 10px;
      font-size: 1.8rem;
      color: #6e45e2;
    }

    .login-container h4 {
      margin-top: 0;
      margin-bottom: 20px;
      font-weight: 400;
      color: #555;
      font-size: 1rem;
    }

    .login-container img {
      width: 300px;
      max-height: 300px;
      height: auto;
      display: block;
      margin: 0 auto 15px auto;
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      background: #fff;
      transition: all 0.2s ease-in-out;
    }

    .login-container input:focus {
      border-color: #a6c1ee;
      box-shadow: 0 0 5px rgba(166, 193, 238, 0.6);
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      background: linear-gradient(to right, #6e45e2, #88d3ce);
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .login-container button:hover {
      transform: scale(1.03);
      background: linear-gradient(to right, #7a5ad9, #9de1d6);
    }

    .login-container a {
      display: block;
      margin-top: 15px;
      color: #6e45e2;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .login-container a:hover {
      text-decoration: underline;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 400px) {
      .login-container {
        width: 90%;
        padding: 30px 20px;
      }
    }
  </style>

  <!-- Sweet Alerts CSS -->
  <link href="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" />

  <script>
    const base_url = '<?php echo BASE_URL; ?>';
    const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
  </script>
</head>

<body>
  <input type="hidden" id="data" value='<?php echo $_GET['data'] ?>'>
  <input type="hidden" id="data2" value='<?php echo urldecode($_GET['data2']); ?>'>
  <div class="login-container">
    <h1>Recuperar Contraseña</h1>
    <img src="https://img.freepik.com/vector-premium/plantilla-diseno-logotipo-moda-ninos_754499-254.jpg?semt=ais_items_boosted&w=740" alt="Logo">
    <h4>Sistema de Control de Inventario</h4>
    <form id="frm_reset_password">
      <input type="password" name="password" id="password" placeholder="Nueva Contraseña" required />
      <input type="password" name="password1" id="password1" placeholder="Confirmar Contraseña" required />
      <button type="button" onclick="validad_imputs_password();">Actualizar</button>
    </form>
  </div>
</body>
<script src="<?php echo BASE_URL; ?>src/view/js/principal.js"></script>
<script>
  validar_datos_reset_password();
</script>
<script src="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.js"></script>
</html>
