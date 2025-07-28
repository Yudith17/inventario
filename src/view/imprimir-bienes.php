<?php
$ruta = explode("/", $_GET['views']);
//if (!isset($ruta[1]) || $ruta[1]=="") { //si no existe la informacion
    //header ("location: " .BASE_URL. "bienes");
//}

$curl = curl_init(); //inicia la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Bien.php?tipo=listarBienes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'], //url a la que se conecta
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
        $respuesta = json_decode($response);

        $bienes = $respuesta->bienes;
      //print_r($respuesta);

      $contenido_pdf = ' ';
      $contenido_pdf = '
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
    }

    .info span.label {
      font-weight: bold;
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
      padding: 5px;
    }

    .firma-container {
      margin-top: 80px;
      display: flex;
      justify-content: space-between;
      padding: 0 40px;
    }

    .firma {
      text-align: center;
    }

    .fecha {
      text-align: right;
      margin-top: 30px;
    }
.firma-container {
    display: flex;
    justify-content: space-around; /* Espacio entre las firmas */
    margin-top: 30px; /* Espaciado opcional arriba */
  }

  .firma {
    text-align: center;
    width: 45%; /* Ajusta según tu necesidad */
  }

  table {
    border-collapse: collapse;
    width: 100%;
    font-size: 9pt;
  }
  th, td {
    border: 1px solid #000;
    padding: 4px;
    text-align: center;
  }
  thead th {
    background-color: #f2f2f2;
    font-weight: bold;
  }

  </style>
</head>
<body>

  <h2>LISTA DE BIENES</h2>


  <table>
    <thead>
      <tr>
        <th>ITEM</th>
        <th>CÓDIGO PATRIMONIAL</th>
        <th>NOMBRE DEL BIEN</th>
        <th>MARCA</th>
        <th>COLOR</th>
        <th>MODELO</th>
        <th>ESTADO</th>
      </tr>
    </thead>
      <tbody>
      ';
 
    $contador = 1;
    foreach ($bienes as $bien) {
       $contenido_pdf .= "<tr>";
        $contenido_pdf .=  "<td>" . $contador . "</td>";
        $contenido_pdf .=  "<td>" . $bien->cod_patrimonial . "</td>";
        $contenido_pdf .=  "<td>" . $bien->denominacion . "</td>";
        $contenido_pdf .=  "<td>" . $bien->marca . "</td>";
        $contenido_pdf .=  "<td>" . $bien->color . "</td>";
        $contenido_pdf .=  "<td>" . $bien->modelo . "</td>";
        $contenido_pdf .=  "<td>" . $bien->estado_conservacion . "</td>";
        $contenido_pdf .=  "</tr>";
        $contador +=1;
    }
if (isset($respuesta->movimiento->fecha_registro) && $respuesta->movimiento->fecha_registro != '') {
                setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
                $fecha = strtotime($respuesta->movimiento->fecha_registro);
                // Si no funciona setlocale en el servidor, usar un array de meses en español
                $meses = [
                    1 => 'enero',
                    2 => 'febrero',
                    3 => 'marzo',
                    4 => 'abril',
                    5 => 'mayo',
                    6 => 'junio',
                    7 => 'julio',
                    8 => 'agosto',
                    9 => 'septiembre',
                    10 => 'octubre',
                    11 => 'noviembre',
                    12 => 'diciembre'
                ];
                $dia = date('d', $fecha);
                $mes = $meses[(int)date('m', $fecha)];
                $anio = date('Y', $fecha);
                $contenido_pdf.= "Ayacucho, $dia de $mes del $anio";
            }

$contenido_pdf .= '
    </tbody>
  </table>

  <div class="firma-container">
  <div class="firma">
    <p>------------------------------</p> 
    <p>ENTREGUÉ CONFORME</p>
  </div>
  <div class="firma">
    <p>------------------------------</p>
    <p>RECIBÍ CONFORME</p>
  </div>
</div>

</body>
</html>
';


   
  require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

// Clase personalizada para agregar encabezado y pie de página
class MYPDF extends TCPDF {
    
  private $logo_left_url = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQnRyplBMCnfQAKteOxoWIf4nQsLmsdxvts2Q&s'; 
  private $logo_right_url = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRVdxkNoyHgePcrwP7lKmpMspDuWsHoF0D9Ww&s';

  public function setLogoUrls($left_url, $right_url = null) {
      $this->logo_left_url = $left_url;
      if ($right_url !== null) {
          $this->logo_right_url = $right_url;
      }
  }

  public function setLeftLogoUrl($url) {
      $this->logo_left_url = $url;
  }

  public function setRightLogoUrl($url) {
      $this->logo_right_url = $url;
  }

  public function Header() {
      $logo_left_url = $this->logo_left_url;
      $logo_right_url = $this->logo_right_url;

      function checkUrlExists($url) {
          if (empty($url)) return false;
          if (!filter_var($url, FILTER_VALIDATE_URL)) return false;
          $headers = @get_headers($url);
          return $headers && strpos($headers[0], '200') !== false;
      }

      $logo_left = checkUrlExists($logo_left_url) ? $logo_left_url : null;
      $logo_right = checkUrlExists($logo_right_url) ? $logo_right_url : null;

      $this->SetY(6); // Altura inicial de los logos

      // Logo izquierdo (más grande)
      if ($logo_left) {
          $this->Image($logo_left, 15, 6, 25); // width = 25
      } else {
          $this->SetFillColor(52, 152, 219);
          $this->Circle(26.5, 16, 12, 0, 360, 'F'); // centro ajustado
          $this->SetFont('helvetica', 'B', 16);
          $this->SetTextColor(255, 255, 255);
          $this->SetXY(21.5, 12);
          $this->Cell(10, 10, 'I', 0, false, 'C', 0, '', 0, false, 'M', 'M');
      }

      // Logo derecho (más grande)
      if ($logo_right) {
          $this->Image($logo_right, 170, 6, 25); // width = 25
      } else {
          $this->SetFillColor(39, 174, 96);
          $this->Circle(183.5, 16, 12, 0, 360, 'F');
          $this->SetFont('helvetica', 'B', 16);
          $this->SetTextColor(255, 255, 255);
          $this->SetXY(178.5, 12);
          $this->Cell(10, 10, 'M', 0, false, 'C', 0, '', 0, false, 'M', 'M');
      }

      // Títulos más arriba
      $this->SetTextColor(0, 0, 0);
      $this->SetFont('helvetica', 'B', 13);
      $this->SetY(22); // antes era 28
      $this->Cell(0, 6, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');

      $this->SetFont('helvetica', 'B', 12);
      $this->Cell(0, 6, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, 1, 'C');

      $this->SetFont('helvetica', '', 10);
      $this->Cell(0, 5, 'DIRECCION DE ADMINISTRACION', 0, 1, 'C');

      $this->Ln(2); // más pequeño el espacio

      // Líneas decorativas
      $this->SetLineWidth(0.8);
      $this->SetDrawColor(52, 152, 219);
      $this->Line(15, 45, 70, 45); // subido de 50 a 45

      $this->SetDrawColor(231, 76, 60);
      $this->Line(70, 45, 125, 45);

      $this->SetDrawColor(46, 204, 113);
      $this->Line(125, 45, 180, 45);

      $this->SetDrawColor(241, 196, 15);
      $this->Line(180, 45, 195, 45);

      $this->SetLineWidth(0.2);
      $this->SetDrawColor(0, 0, 0);
      $this->Ln(5);
  }

  public function Footer() {
      $this->SetY(-20);
      $this->SetLineWidth(0.5);
      $this->SetDrawColor(52, 73, 94);
      $this->Line(15, $this->GetY(), 195, $this->GetY());
      $this->Ln(3);

      $this->SetFont('helvetica', '', 8);
      $this->SetTextColor(70, 70, 70);
      $this->Cell(0, 4, 'Jr. 28 de Julio Nº 383 – Huamanga', 0, 1, 'C');
      $this->Cell(0, 4, 'Teléfono: (066) 31-2364 | www.dreaya.gob.pe', 0, 1, 'C');
  }

  public function AddSectionLabel($text) {
      $this->Ln(5);
      $this->SetFillColor(52, 73, 94);
      $this->SetTextColor(255, 255, 255);
      $this->SetFont('helvetica', 'B', 12);
      $text_width = $this->GetStringWidth($text) + 10;
      $x = (210 - $text_width) / 2;
      $this->RoundedRect($x, $this->GetY(), $text_width, 8, 2, '1111', 'F');
      $this->SetXY($x, $this->GetY() + 1);
      $this->Cell($text_width, 6, $text, 0, false, 'C', 0, '', 0, false, 'M', 'M');
      $this->SetTextColor(0, 0, 0);
      $this->SetFillColor(255, 255, 255);
      $this->Ln(12);
  }
}



$pdf = new MYPDF();
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Yudith');
$pdf->SetTitle('Reporte de bienes');

// 10. CONFIGURAR MÁRGENES Y PÁGINA
$pdf->SetMargins(PDF_MARGIN_LEFT, 45, PDF_MARGIN_RIGHT); 
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);                
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  

// Configurar salto de página automático
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Configurar fuente por defecto
$pdf->SetFont('helvetica', '', 8);

// Agregar nueva página
$pdf->AddPage();

// 11. INSERTAR CONTENIDO HTML EN EL PDF
// Convertir HTML a PDF y renderizarlo
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

// 12. GENERAR Y MOSTRAR EL PDF
// Generar archivo PDF con nombre único (incluye fecha y hora)
$pdf->Output('reporte_bienes_' . date('Ymd_His') . '.pdf', 'I');
    }