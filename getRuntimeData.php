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
	$line = fgets($handle);
	$titles = str_getcsv($line, ",");
	
	while (($line = fgets($handle)) !== false) {
		$line = str_getcsv($line, ","); //parse the items in rows 
		$result[] = 
				 array(
				 'alt' => (float)$line[array_search(" BaroAlt (cm)",$titles)],//$line[34],
				 'rssi' => (float)$line[array_search(" rssi",$titles)],
				 'vbat' =>  (float)$line[array_search(" vbat",$titles)],
				 'roll' =>  (float)$line[array_search(" attitude[0]",$titles)],
				 'pitch' =>  (float)$line[array_search(" attitude[1]",$titles)],
				 'yaw' =>  (float)$line[array_search(" attitude[2]",$titles)],
				 'thr' =>  (float)$line[array_search(" motor[0]",$titles)],
				 'mode' =>  $line[array_search(" flightModeFlags (flags)",$titles)]
		);	
	}
	fclose($handle);
} else {
	// error opening the file.
} 
$aResult['result'] =  $result;
echo json_encode($aResult);
?>
