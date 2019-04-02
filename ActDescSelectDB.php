<?php 
require('dbinit.php');
$aResult = array();
$var = $_POST['arguments'];

$activityID = $var[0][0];
$stmt = $db->prepare("SELECT weather.weatherCondition, weather.windSpeed, weather.windDirection , windIco.filename, weatherIco.filename
						FROM weather
						INNER JOIN weatherIco ON weather.weatherCondition=weatherIco.ID INNER JOIN windIco ON weather.windDirection=windIco.ID 
						WHERE weather.activityID=$activityID");

$stmt->execute(); 

$aResult['result'] = $stmt->fetchAll();
echo json_encode($aResult);
?>