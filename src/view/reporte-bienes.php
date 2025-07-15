<?php

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setCreator("yp")->setLastModifiedBy("yo")->setTitle("yo")->setDescription("yo");
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle("hoja 1");

for ($i = 1; $i <= 10; $i++) {
    $activeWorksheet->setCellValue('A' . $i, $i);
}

for ($i = 1; $i <= 30; $i++) {
    // Convertir índice de columna numérico a letra (1 → A, 2 → B, ...)
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    $activeWorksheet->setCellValue($colLetter . '1', $i);
}

/*$n1 = 1;
for ($n2=1; $n2 < 12; $n2++) {
    $activeWorksheet->setCellValue('A'.$n2, $n1);
    $activeWorksheet->setCellValue('B'.$n2, 'x');
    $activeWorksheet->setCellValue('C'.$n2, $n1);
    $activeWorksheet->setCellValue('D'.$n2, '=');
    $activeWorksheet->setCellValue('E'.$n2, $n1*$n2);
}*/

// Guardar archivo
$writer = new Xlsx($spreadsheet);
$writer->save('horizontal.xlsx');