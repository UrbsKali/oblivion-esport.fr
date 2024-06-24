<?php

// convert the db query to a csv file
require_once ('../db.php');

$query = $db->query('SELECT * FROM TWC_INSCRIPTION');
$data = [];
while ($row = $query->fetch_assoc()) {
    $data[] = $row;
}

$csv = fopen('php://output', 'w');
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="twc_inscriptions.csv"');
header('Pragma: no-cache');
header('Expires: 0');

fputcsv($csv, array_keys($data[0]));
foreach ($data as $row) {
    fputcsv($csv, $row);
}
fclose($csv);
