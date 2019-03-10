<?php 
require('dbinit.php');

$aResult = array();
$var = $_POST['arguments'];
$data = [
    'activityID' => $var[0][0],
    'weatherCondition' => $var[0][1],
    'windSpeed' => $var[0][2],
    'windDirection' => $var[0][3],
];

$sql = "UPDATE weather SET weatherCondition=:weatherCondition, windSpeed=:windSpeed, windDirection=:windDirection WHERE activityID=:activityID";
$stmt= $db->prepare($sql);
$stmt->execute($data);		
		
$activityID = $var[0][0];
$stmt = $db->prepare("SELECT weather.weatherCondition, weather.windSpeed, weather.windDirection , windIco.filename, weatherIco.filename
								FROM weather
								INNER JOIN weatherIco ON weather.weatherCondition=weatherIco.ID  
								INNER JOIN windIco ON weather.windDirection=windIco.ID 
								WHERE weather.activityID=$activityID");
$stmt->execute(); 

$aResult['result'] = $stmt->fetchAll();
echo json_encode($aResult);
?>