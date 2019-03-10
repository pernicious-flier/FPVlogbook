<?php 
require('dbinit.php');

$aResult = array();
$var = $_POST['arguments'];
$stmt = $db->prepare("SELECT activities.gps_fname FROM activities WHERE activities.id=$var ");
$stmt->execute(); 
$filename = $stmt->fetchAll();
$file = "files/csv/".$filename[0][0].".csv";

$handle = fopen($file, "r");

$i = 1;
$handle = fopen($file, "r");
if ($handle) {
	while (($line = fgets($handle)) !== false) {
		$line = str_getcsv($line, ","); //parse the items in rows 
		$result[] = 
				 array(
				 'alt' => (float)$line[34],
				 'rssi' => (float)$line[35],
				 'vbat' =>  (float)$line[30],
				 'pitch' =>  (float)$line[42],
				 'roll' =>  (float)$line[43],
				 'yaw' =>  (float)$line[44],
				 'thr' =>  (float)$line[45],
				 'mode' =>  $line[67]
		);	
	}
	fclose($handle);
} else {
	// error opening the file.
} 
$aResult['result'] =  $result;
echo json_encode($aResult);
?>