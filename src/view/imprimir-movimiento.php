<?php
$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1])|| $ruta[1]=="") {
    header("Location: " . BASE_URL . "movimientos");
}
 $curl = curl_init(); //inicia la sesión cURL
 curl_setopt_array($curl, array(
     CURLOPT_URL => BASE_URL_SERVER."src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token']."&data=". $ruta[1], //url a la que se conecta
     CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
     CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
     CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
     CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
     CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
     CURLOPT_CUSTOMREQUEST => "GET", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
     CURLOPT_HTTPHEADER => array(
         "x-rapidapi-host: ".BASE_URL_SERVER,
         "x-rapidapi-key: XXXX"
     ), //configura las cabeceras enviadas al servicio
 )); //curl_setopt_array configura las opciones para una transferencia cURL

 $response = curl_exec($curl); // respuesta generada
 $err = curl_error($curl); // muestra errores en caso de existir

 curl_close($curl); // termina la sesión 
 
 if ($err) {
     echo "cURL Error #:" . $err; // mostramos el error
 } else {
     $respuesta =json_decode($response);
     //print_r($respuesta);
     $contenido_pdf = '';
     $contenido_pdf .='
     <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Papeleta de Rotación de Bienes</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
    }
    h2 {
      text-align: center;
      text-transform: uppercase;
    }
    .info {
      margin-bottom: 20px;
    }
    .info p {
      margin: 5px 0;
    }yt
    .info b {
      display: inline-block;
      width: 100px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid black;
    }
    th, td {
      text-align: center;
      padding: 8px;
    }
    .firmas {
      margin-top: 80px;
      display: flex;
      justify-content: space-between;
      padding: 0 60px;
    }
    .firmas div {
      text-align: center;
    }
    .lugar-fecha {
      text-align: right;
      margin-top: 20px;
    }
    .underline {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <h2>PAPELETA DE ROTACION DE BIENES</h2>

  <div class="info">
    <p><b>ENTIDAD</b>: <span class="underline">DIRECCION REGIONAL DE EDUCACION - AYACUCHO</span></p>
    <p><b>AREA</b>: <span class="underline">OFICINA DE ADMINISTRACIÓN</span></p>
    <p><b>ORIGEN</b>: <span class="underline">'. $respuesta->amb_origen->codigo.' - '. $respuesta->amb_origen->detalle.'</span></p>
    <p><b>DESTINO</b>: <span class="underline">'.$respuesta->amb_destino->codigo.' - '.$respuesta->amb_destino->detalle.'</span></p>
    <p><b>MOTIVO (*)</b>: <span class="underline">'.$respuesta->movimiento->descripcion.'</span></p>
  </div>

  <table>
    <thead>
      <tr>
        <th>ITEM</th>
        <th>CODIGO PATRIMONIAL</th>
        <th>NOMBRE DEL BIEN</th>
        <th>MARCA</th>
        <th>COLOR</th>
        <th>MODELO</th>
        <th>ESTADO</th>
      </tr>
    </thead>
  <tbody>

     ';
     ?>
     
  <?php
  $contador = 1;
  foreach ($respuesta->bienes as $bien) {
     $contenido_pdf.= '<tr>';
      $contenido_pdf.= '<td>' . $contador . '</td>';
      $contenido_pdf.= '<td>' . $bien->cod_patrimonial . '</td>';
      $contenido_pdf.= '<td>' . $bien->denominacion . '</td>';
      $contenido_pdf.= '<td>' . $bien->marca . '</td>';
      $contenido_pdf.= '<td>' . $bien->color . '</td>';
      $contenido_pdf.= '<td>' . $bien->modelo . '</td>';
      $contenido_pdf.= '<td>' . $bien->estado_conservacion . '</td>';
      $contenido_pdf.= '</tr>';
      $contador++;
  }
 
$fecha = new DateTime(); // new DateTime('2025-07-08') si deseas una fija

$dia = $fecha->format('j'); // día sin cero a la izquierda
$mesNumero = $fecha->format('m'); // número del mes

// Meses en español
$meses = [
    '01' => 'enero', '02' => 'febrero', '03' => 'marzo',     '04' => 'abril',
    '05' => 'mayo',  '06' => 'junio',   '07' => 'julio',     '08' => 'agosto',
    '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'
];

// Año fijo: 2025
$año = '2025';

  $contenido_pdf .='
  </tbody>
  </table>
  <div class="lugar-fecha">
  <p><span class="underline">Ayacucho</span>, '. $dia . ' de ' . $meses[$mesNumero] . ' del ' . $año.'</p>
</div>
  <div class="firmas">
    <div>
      <p>-----------------------------</p>
      <p>ENTREGUE CONFORME</p>
    </div>
    <div>
      <p>-----------------------------</p>
      <p>RECIBÍ CONFORME</p>
    </div>
  </div>

</body>
</html>
  ';
    ?>
  

     <?php
     require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

     $pdf = new TCPDF();
     // establecer información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Yudith Rimachi');
$pdf->SetTitle('Reporte de Movimientos');
// establecer márgenes
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// salto de pagina automatico
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// establecer fuente
$pdf->SetFont('helvetica', 'B', 12);

// agregar una pagina
$pdf->AddPage();

// generar el contenido HTML
$pdf->writeHTML($contenido_pdf);
ob_clean();
// Cerrar y generar documento PDF
$pdf->Output('example_006.pdf', 'I');

 }

