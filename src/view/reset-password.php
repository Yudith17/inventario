<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Actualizar Contraseña</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(-45deg, #ffd6e8, #ffe0f0, #fbe8ff, #e1d5f9);
      background-size: 400% 400%;
      animation: animateBackground 15s ease infinite;
    }

    @keyframes animateBackground {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .login-container {
      width: 360px;
      padding: 40px 30px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      border-radius: 25px;
      box-shadow: 0 8px 25px rgba(255, 182, 193, 0.3);
      text-align: center;
      animation: fadeIn 0.8s ease-in-out;
    }

    .login-container h1 {
      font-size: 2rem;
      color: #d16ba5;
      margin-bottom: 10px;
    }

    .login-container h4 {
      font-size: 1rem;
      color: #555;
      font-weight: 400;
      margin-bottom: 25px;
    }

    .login-container img {
      width: 220px;
      margin-bottom: 20px;
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #d9b2dd;
      border-radius: 10px;
      font-size: 1rem;
      background-color: #fff5fa;
      transition: all 0.3s ease-in-out;
    }

    .login-container input:focus {
      border-color: #da88d1;
      outline: none;
      box-shadow: 0 0 8px rgba(218, 136, 209, 0.4);
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      background: linear-gradient(to right, #ec77ab, #7873f5);
      border: none;
      border-radius: 10px;
      color: white;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
    }

    .login-container button:hover {
      transform: scale(1.05);
      background: linear-gradient(to right, #f694c1, #9c89f6);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 400px) {
      .login-container {
        width: 90%;
        padding: 30px 20px;
      }
    }
  </style>
  <!-- Sweet Alerts CSS -->
  <link href="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
  <script>
    const base_url = '<?php echo BASE_URL; ?>';
    const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
  </script>
</head>

<body>
  <input type="hidden" id="data" value='<?php echo $_GET['data'];?>'>
  <input type="hidden" id="data2" value='<?php echo urldecode($_GET['data2']);?>'>
  <div class="login-container" id="logincontainer">
    <h1>recuperar contraseña</h1>
    <img src="https://img.freepik.com/vector-premium/plantilla-diseno-logotipo-moda-ninos_754499-254.jpg?semt=ais_items_boosted&w=740" alt="Logo" style="width: 300px; max-height: 300px; height: auto; display: block; margin: 0 auto 15px auto;">
    <h4>Sistema de Control de Ropa</h4>
    <form id="frm_reset_password">
      <input type="password" name="password" id="password" placeholder=" nueva Contraseña" required>
      <input type="password" name="password" id="password1" placeholder=" confirmar Contraseña" required>
      <button type="button" onclick="validar_imputs_password();">actualizar contraseña</button>
    </form>
  </div>
</body>
<script src="<?php echo BASE_URL; ?>src/view/js/principal.js"></script>
<script>
  validar_datos_reset_password();
</script>

<!-- Sweet Alerts Js-->
<script src="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.js"></script>
</html>