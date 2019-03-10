<?php
require('dbinit.php');
$aResult = array();

$var = $_POST['arguments'];
$stmt = $db->prepare("SELECT gps_fname FROM activities WHERE activities.ID=$var ");
$stmt->execute(); 
$selectOut = $stmt->fetchAll();

$filename = explode('.', $selectOut[0][0]);
unlink("blackbox-tools/logs/".$filename[0].".txt");
//unlink("blackbox-tools/logs/".$selectOut[0][0].".event");
unlink("files/txt/".$selectOut[0][0].".txt");
unlink("files/gpx/".$selectOut[0][0].".gps.gpx");
unlink("files/csv/".$selectOut[0][0].".csv");
unlink("files/csv/".$selectOut[0][0].".gps.csv");

$stmt = $db->prepare("DELETE FROM activities WHERE activities.ID=$var");
$stmt->execute(); 
$stmt = $db->prepare("DELETE FROM FCparams WHERE FCparams.activityID=$var");
$stmt->execute(); 
$stmt = $db->prepare("DELETE FROM weather WHERE weather.activityID=$var");
$stmt->execute();

$aResult['result'] = $filename;
echo json_encode($aResult);

?>