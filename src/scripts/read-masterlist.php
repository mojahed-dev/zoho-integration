<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = __DIR__ . '/../masterlist/edited_data.xlsx';

// Load Excel file
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);

// Loop through rows
foreach ($rows as $index => $row) {
    if ($index === 1) continue; // skip header

    $id     = $row['A']; // ID
    $name   = $row['B']; // Name
    $email  = $row['C']; // Email
    $status = strtolower(trim($row['D'])); // Status (Update/Delete)

    echo "[$status] ID: $id, Name: $name, Email: $email\n";

    // Later you can route this to:
    // if ($status === 'update') call update API
    // if ($status === 'delete') call delete API
}
