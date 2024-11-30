<?php
require_once 'includes/session_manager.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

initializeSession();
requireAuth();

if (!isset($_GET['file'])) {
    die('No file specified');
}

$inputFile = 'concour/' . basename($_GET['file']);
if (!file_exists($inputFile)) {
    die('File not found');
}

try {
    // Load the Excel file
    $spreadsheet = IOFactory::load($inputFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $data = $worksheet->toArray();

    // Remove header row
    $headers = array_shift($data);

    // Find column indexes - search for exact column names
    $cneIndex = array_search('CNE', array_map('strtoupper', $headers));
    $cinIndex = array_search('CIN', array_map('strtoupper', $headers));
    $emailIndex = array_search('EMAIL', array_map('strtoupper', $headers));
    $nameIndex = array_search('NOM_FR', array_map('strtoupper', $headers));
    
    // Search for exact grade column names
    $deustIndex = array_search('Note DEUST/DEUG/DUT', $headers);
    $lstIndex = array_search('Note LST', $headers);

    // Process data and remove duplicates
    $processed = [];
    $seen = ['cne' => [], 'cin' => [], 'email' => []];

    // Determine if this is a master program file
    $isMaster = strpos(strtolower(basename($inputFile)), 'master') !== false;

    foreach ($data as $row) {
        if (empty($row[$cneIndex])) continue; // Skip empty rows

        $cne = trim($row[$cneIndex]);
        $cin = trim($row[$cinIndex]);
        $email = trim($row[$emailIndex]);

        // Skip if any identifier is already seen
        if (in_array($cne, $seen['cne']) || 
            in_array($cin, $seen['cin']) || 
            in_array($email, $seen['email'])) {
            continue;
        }

        // Calculate the correct average based on program type
        $note = 0;
        if ($isMaster && isset($deustIndex) && isset($lstIndex)) {
            // For master: 33% DEUST and 67% LST
            $noteDeust = floatval(str_replace(',', '.', $row[$deustIndex]));
            $noteLst = floatval(str_replace(',', '.', $row[$lstIndex]));
            $note = ($noteDeust * 0.33) + ($noteLst * 0.67);
        } else if (!$isMaster && isset($deustIndex)) {
            // For cycle: direct DEUST/DEUG/DUT note
            $note = floatval(str_replace(',', '.', $row[$deustIndex]));
        }

        if ($note > 0) { // Only add if we have a valid note
            $seen['cne'][] = $cne;
            $seen['cin'][] = $cin;
            $seen['email'][] = $email;

            $processed[] = [
                'cne' => $cne,
                'name' => $row[$nameIndex],
                'note' => number_format($note, 2)
            ];
        }
    }

    // Sort by note in descending order
    usort($processed, function($a, $b) {
        return $b['note'] <=> $a['note'];
    });

    // 7ado 300 students
    $processed = array_slice($processed, 0, 300);

    $newSpreadsheet = new Spreadsheet();
    $sheet = $newSpreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'CNE');
    $sheet->setCellValue('B1', 'Nom Complet');
    $sheet->setCellValue('C1', 'Note');

    $row = 2;
    foreach ($processed as $student) {
        $sheet->setCellValue('A' . $row, $student['cne']);
        $sheet->setCellValue('B' . $row, $student['name']);
        $sheet->setCellValue('C' . $row, $student['note']);
        $row++;
    }

    foreach (range('A', 'C') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $programName = substr(basename($inputFile), 0, strpos(basename($inputFile), '_'));
    $outputFilename = $programName . '_list_de_merite.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $outputFilename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($newSpreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = 'Error processing file: ' . $e->getMessage();
    header('Location: excel_files.php');
    exit;
}
