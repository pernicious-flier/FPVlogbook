<?php

$aResult = array();
$var = $_POST['arguments'];

$file = "files/csv/".$var.".gps.csv";
$csvData = file_get_contents($file);
$data = str_getcsv($csvData, "\n"); //parse the rows
foreach ($data as &$row) {
	$row = str_getcsv($row, ","); //parse the items in rows
	$result[] = 
				 array( 
				 'lat' => (float)$row[3],
				 'lon' => (float)$row[4]
				);
}
$aResult['result'] =  $result;
echo json_encode($aResult);

?>