[5:50 p.m., 27/7/2025] Marycielo: <?php
$ruta = explode("/", $_GET['views']);
//if (!isset($ruta[1]) || $ruta[1]=="") { //si no existe la informacion
    //header ("location: " .BASE_URL. "bienes");
//}

    $curl = curl_init(); 
    curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER."src/control/Ambiente.php?tipo=listarTodosAmbientes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'],
    CURLOPT_RETURNTRANSFER => true, 
    CURLOPT_FOLLOWLOCATION => true, 
    CURLOPT_ENCODING => "", 
    CURLOPT_MAXREDIRS => 10, 
    CURLOPT_TIMEOUT => 30, 
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
    CURLOPT_CUSTOMREQUEST => "GET", 
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: ".BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ), 
)); 
$response = curl_exec($curl); 
$err = curl_error($curl); 
curl_close($curl); 
if ($err) {
    echo "cURL Error #:" . $err; 
} else {

   $respuesta = json_decode($response);

   $ambientes = $respuesta->contenido;

    
   $contenido_pdf = '';

   $contenido_pdf .= '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Papeleta de Rotación de ambientes</title>
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
  line-height: 1.8;
}
.info b {
  display: inline-block;
  width: 80px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
  font-size:9px;
}
th, td {
  border: 1px solid black;
  text-align: center;
  padding: 6px;
}
.fecha {
  margin-top: 30px;
  text-align: right;
}

.firma-section tr td{
   border: none;
  }

</style>
</head>
<body>

<h2>REPORTE DE AMBIENTES</h2>

<table>
<thead>
  <tr>
    <th>ITEM</th>
    <th>INSTITUCION</th>
    <th>ENCARGADO</th>
    <th>CODIGO</th>
    <th>DETALLE</th>
    <th>OTROS DETALLES</th>
  </tr>
</thead>
<tbody>';    
     $contador = 1;
    foreach ($ambientes as $ambiente) {
         $contenido_pdf .= '<tr>';
         $contenido_pdf .=  "<td>".  $contador . "</td>";
         $contenido_pdf .=  "<td>".  $ambiente->institucion . "</td>";
         $contenido_pdf .= "<td>" .  $ambiente->encargado . "</td>";
         $contenido_pdf .=  "<td>".  $ambiente->codigo . "</td>";
         $contenido_pdf .=  "<td>".  $ambiente->detalle. "</td>";
         $contenido_pdf .=  "<td>".  $ambiente->otros_detalle. "</td>";
         $contenido_pdf .=  '</tr>';
         $contador ++;
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

$contenido_pdf .='  </tbody>
</table> 

<div class="fecha">
Ayacucho, '. $dia . " de " . $meses[$mesNumero] . " del " . $año.'
</div>
<table  class="firma-section">
<tr>
<td>
<div>
  ------------------------------<br>
  ENTREGUÉ CONFORME
</div>
</td>
<td>
<div>
  ------------------------------<br>
  RECIBÍ CONFORME
</div>
</td>
</tr>
</table>

</body>
</html>';


   
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

// 8. CREAR CLASE PERSONALIZADA PARA ENCABEZADO Y PIE DE PÁGINA
class MYPDF extends TCPDF {
    public function Header() {
        // URL de las imágenes
        $logo_left  = 'https://iestphuanta.edu.pe/wp-content/uploads/2021/12/logo_tecno-1-2.png';
        $logo_right = 'https://dreayacucho.gob.pe/storage/directory/lCcjIpyYl7E5tQjWegZVLZvp1ZIMbY-metaWk9PRUEybXNRUGlYWWtKRng0SkxqcG9SRW5jTEZuLW1ldGFiRzluYnk1d2JtYz0tLndlYnA=-.webp';

        // Logo izquierdo
        $this->Image($logo_left, 15, 10, 25);  
        // Logo derecho
        $this->Image($logo_right, 170, 10, 25); 

        // Título principal 
        $this->SetXY(55, 12); // desplazado al centro 
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(0, 70, 140); // Azul fuerte
        $this->Cell(100, 6, 'INSTITUTO DE EDUCACIÓN SUPERIOR TECNOLÓGICO PÚBLICO', 0, 1, 'C');

        // Subtítulo
        $this->SetX(55);
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(0, 70, 140); // Gris oscuro
        $this->Cell(100, 6, '"HUANTA"', 0, 1, 'C');

         // contenido
        $this->SetX(55, 12);
        $this->SetFont('helvetica', 'I', 10);
        $this->SetTextColor(85, 85, 85); // Gris oscuro
        $this->Cell(100, 6, 'Sistema de Control Patrimonial', 0, 1, 'C');

        // Línea decorativa azul
        $this->SetDrawColor(52, 152, 219);
        $this->SetLineWidth(0.8);
        $this->Line(15, 44, 195, 44);

        $this->Ln(5); // Espacio adicional
    }

  public function Footer() {
    // Posicionar a 15 mm del final de la página
    $this->SetY(-15);

    // Línea superior del footer
    $this->SetDrawColor(189, 195, 199);
    $this->SetLineWidth(0.5);
    $this->Line(15, $this->GetY() - 5, 195, $this->GetY() - 5);

    // Estilo del texto
    $this->SetFont('helvetica', 'I', 8);
    $this->SetTextColor(100, 100, 100);

    // Texto de número de página centrado
    $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');

    // Reset de estilo
    $this->SetTextColor(0, 0, 0);
    $this->SetLineWidth(0.2);

    }
}


$pdf = new MYPDF();
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Marycielo');
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
[5:50 p.m., 27/7/2025] Marycielo: if($tipo == "listarTodosAmbientes"){
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
  if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {  
      $arr_Respuesta = array('status' => false, 'contenido' => '');
      $resAmbiente = $objAmbiente->listarAmbientes();
      $arr_contenido = [];
      if (!empty($resAmbiente)) {
          
          for ($i = 0; $i < count($resAmbiente); $i++) {
              $institucion = $objInstitucion->buscarInstitucionById($resAmbiente[$i]->id_ies);
              $arr_contenido[$i] = (object) [];
              $arr_contenido[$i]->institucion = $institucion->nombre;
              $arr_contenido[$i]->id = $resAmbiente[$i]->id;
              $arr_contenido[$i]->encargado = $resAmbiente[$i]->encargado;
              $arr_contenido[$i]->codigo = $resAmbiente[$i]->codigo;
              $arr_contenido[$i]->detalle = $resAmbiente[$i]->detalle;
              $arr_contenido[$i]->otros_detalle = $resAmbiente[$i]->otros_detalle;

          }
          $arr_Respuesta['status'] = true;
          $arr_Respuesta['contenido'] = $arr_contenido;
      }
  }
  echo json_encode($arr_Respuesta);
}