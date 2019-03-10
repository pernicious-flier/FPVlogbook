<?php 
function select($var){
	require('dbinit.php');
	switch($var[0])
	{
		case "tabledata":			
			$stmt = $db->prepare("SELECT activities.id, activities.title_act, members.username, activities.date_act, activities.flightTime, activities.location, activities.model_name
													FROM activities 
													INNER JOIN members ON activities.memberID = members.memberID 
													WHERE members.memberID=:memberID ORDER BY $var[1] $var[2]");
			$stmt->execute(['memberID' => $_SESSION['memberID']]); 
			$data = $stmt->fetchAll();
			return $data;
            break;
		case "column":
            break;
		default:
			break;
	}
}

function loadActivity($var){
	require('dbinit.php');
	$stmt = $db->prepare("SELECT activities.id, activities.title_act, members.username, activities.date_act, activities.time_act, activities.location, activities.model_name,
												activities.altitude, activities.max_altitude, activities.max_speed, activities.min_rssi, activities.gps_fname, activities.notes, activities.flightTime
											FROM activities 
											INNER JOIN members ON activities.memberID = members.memberID 
											WHERE activities.id=$var");
	$stmt->execute(); 
	$data = $stmt->fetchAll();
	return $data;
}


function loadFCparam($var){
	require('dbinit.php');
	$stmt = $db->prepare("SELECT P_interval, minthrottle, maxthrottle, looptime, rc_rate,
											rc_expo, rc_yaw_expo, thr_mid, thr_expo ,tpa_rate, tpa_breakpoint, rates, rollPID,
											pitchPID, yawPID, altPID, posPID, posrPID, levelPID, magPID, velPID, yaw_p_limit,
											yaw_lpf_hz, dterm_lpf_hz, dterm_notch_hz, dterm_notch_cutoff, deadband, 
											yaw_deadband, gyro_lpf, gyro_lpf_hz, gyro_notch_hz, gyro_notch_cutoff, acc_lpf_hz,
											acc_notch_hz, acc_notch_cutoff, gyro_stage2_lowpass_hz, pidSumLimit, acc_hardware, 
											baro_hardware, mag_hardware, motor_pwm_rate, waypoints, axisAccelerationLimitYaw, 
											axisAccelerationLimitRollPitch 
											FROM FCparams 
											WHERE FCparams.activityID=$var");
	$stmt->execute(); 
	$data = $stmt->fetchAll();
	return $data;
}

header('Content-Type: application/json');
$aResult = array();
if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }
if( !isset($aResult['error']) ) {
	switch($_POST['functionname']) {
		case 'insert':
			$aResult['result'] = insert($_POST['arguments']);
		   break;
		case 'select':
			$aResult['result'] = select($_POST['arguments'][0]);
			break;
		case 'loadActivity':
			$aResult['result'] = loadActivity($_POST['arguments']);
			break;
		case 'loadFCparam':
			$aResult['result'] = loadFCparam($_POST['arguments']);
			break;
		case 'getRuntimeDataCSV':
			$aResult['result'] = getRuntimeDataCSV($_POST['arguments']);
			break;
		default:
		   $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
		   break;
	}
}
echo json_encode($aResult);
?>