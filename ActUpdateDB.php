<?php 
require('dbinit.php');

$aResult = array();
$var = $_POST['arguments'];
$data = [
    'activityID' => $var[0][0],
    'notes' => $var[0][1],
];

$sql = "UPDATE activities SET notes=:notes WHERE activities.ID=:activityID";
$stmt= $db->prepare($sql);
$stmt->execute($data);		
		
$activityID = $var[0][0];
$stmt = $db->prepare("SELECT notes
								FROM activities
								WHERE weather.activityID=$activityID");
$stmt->execute(); 

$aResult['result'] = $stmt->fetchAll();
echo json_encode($aResult);
?>