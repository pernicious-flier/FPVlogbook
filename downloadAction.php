<?php
require('dbinit.php');
$aResult = array();

$var = $_POST['arguments'][0];
$stmt = $db->prepare("SELECT activities.gps_fname FROM activities WHERE activities.ID=$var ");
$stmt->execute(); 
$selectOut = $stmt->fetchAll();

$filename = explode('.', $selectOut[0][0]);
$filename ='blackbox-tools/logs/'.$filename[0].'.txt';

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
header('Cache-Control: must-revalidate');
header('Content-Length: '.filesize($filename));
readfile($filename);
?>