<?php 
require('dbinit.php');
	
function OsmAddress($Latitude,$Longitude) {
    //vars
    $USERAGENT = $_SERVER['HTTP_USER_AGENT'];

    //main
    $opts = array('http'=>array('header'=>"User-Agent: $USERAGENT\r\n"));
    $context = stream_context_create($opts);
    $url4 = file_get_contents("https://nominatim.openstreetmap.org/reverse?format=json&lat=$Latitude&lon=$Longitude&zoom=18&addressdetails=1", false, $context);
    $osmaddress = json_decode($url4);  
    $osmaddress1 = $osmaddress->display_name;
    return $osmaddress1;
}	
	
date_default_timezone_set('Europe/Rome');
header('Content-Type: application/json');

$var = $_POST['arguments'];
//extract the flight location
$file = "files/gpx/".$var[0][1].".gps.gpx";
$contents = file_get_contents($file);
$pattern = "/(<trkpt lat=)\"(.*)(\" )/";	
if(!preg_match($pattern,$contents,$lat)){$lat = "NULL";}
$pattern = "/(lon=)\"(.*)(\">)/";	
if(!preg_match($pattern,$contents,$lon)){$lon = "NULL";}

// Create a stream
$opts = array('http'=>array('header'=>"User-Agent: StevesCleverAddressScript 3.7.6\r\n"));
$context = stream_context_create($opts);
$location = OsmAddress($lat[2],$lon[2]);

//extract the technical info of the FC
$file = "files/txt/".$var[0][1].".txt";
$contents = file_get_contents($file);
$searchfor = "Craft name";
$pattern = "/(".$searchfor."):(.*)/";	
if(!preg_match($pattern,$contents,$Craft_name)){$Craft_name = "NULL";}
$searchfor = "Firmware revision";
if(!preg_match($pattern,$contents,$Firmware_revision)){$Firmware_revision = "NULL";}
$searchfor = "datetime";
$pattern = "/(".$searchfor."):(.*)T(.*)\.(.*)/";
if(!preg_match($pattern,$contents,$datetime)){$datetime = "NULL";}

$altMax = 0;
$alt = 0;		
$spdMax = 0; 
$minRSSI = 0;
//get absolute gps altitude and max speed
$file = "files/csv/".$var[0][1].".gps.csv";
$csvData = file_get_contents($file);
$data = str_getcsv($csvData, "\n"); //parse the rows
$alt=0;
foreach ($data as &$row) {
	$row = str_getcsv($row, ","); //parse the items in rows 
	if($alt == 0) $alt = (float)$row[5];		
	if((float)$row[6]>$spdMax) $spdMax = (float)$row[6];	
}

//get max barometer relative altitude and min RSSI
$file = "files/csv/".$var[0][1].".csv";
$csvData = file_get_contents($file);
$data = str_getcsv($csvData, "\n"); //parse the rows
$minRSSI = 1024;
$j=0;
$flightTimeT0 = 0;
$flightTime = 0;
foreach ($data as &$row) {	
	$row = str_getcsv($row, ","); //parse the items in rows 
	if($j==0) {$j++;}
	else if ($j==1)	{ $flightTimeT0=$row[1]; $j++; }
	else { $flightTime=$row[1]; }
	if(((float)$row[32]/100)>$altMax) $altMax = (float)$row[32]/100;	
	if((float)$row[33]<$minRSSI && (float)$row[33]>0) $minRSSI = (float)$row[33];	
}
$input = ($flightTime - $flightTimeT0)/1000000;
$hours = floor($input/3600);
$minutes = floor($input/60) - ($hours*60);
$seconds = floor($input) - ($minutes*60) - ($hours*3600);
$flightTimeStr = $hours.":".$minutes.":".$seconds;

$minRSSI = $minRSSI*100/1024;

$stmt = $db->prepare("SELECT * FROM members WHERE username=:username");
$stmt->execute(['username' => $_SESSION['username']]); 
$userdata = $stmt->fetch();
$stmt = $db->prepare('INSERT INTO activities (memberID,title_act,date_act,time_act,flightTime,model_name,location,altitude,max_altitude,max_speed,min_rssi,gps_fname) 
									VALUES (:memberID, :title_act, :date_act, :time_act, :flightTime, :model_name, :location, :altitude, :max_altitude, :max_speed, :min_rssi, :gps_fname)');
$stmt->execute(array(
			':memberID' => $userdata['memberID'],
			':title_act' => "FPV log",
			//':date_act' => date("Y-m-d"),
			':date_act' => $datetime[2],
			//':time_act' => date("H:i:s"),
			':time_act' => $datetime[3],
			':flightTime' => $flightTimeStr,
			':model_name' => $Craft_name[2],
			':location' => $location,//$lat[2].", ".$lon[2],
			':altitude' => $alt,
			':max_altitude' => $altMax,
			':max_speed' => $spdMax,
			':min_rssi' => $minRSSI,
			':gps_fname' => $var[0][1]
		));
		
$LastActivityID = $db->lastInsertId();

$stmt = $db->prepare("INSERT INTO weather (activityID, weatherCondition, windSpeed, windDirection)
									VALUES (:activityID, :weatherCondition, :windSpeed, :windDirection)");
$stmt->execute(array(
			':activityID' => $LastActivityID,
			':weatherCondition' => 1,
			':windSpeed' => 0,
			':windDirection' => 1,
		));
		
$searchfor = "P interval";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$P_interval)){$P_interval = "NULL";}
$searchfor = "minthrottle";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$minthrottle)){$minthrottle = "NULL";}
$searchfor = "maxthrottle";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$maxthrottle)){$maxthrottle = "NULL";}
$searchfor = "looptime";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$looptime)){$looptime = "NULL";}
$searchfor = "rc_rate";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$rc_rate)){$rc_rate = "NULL";}
$searchfor = "rc_expo";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$rc_expo)){$rc_expo = "NULL";}
$searchfor = "rc_yaw_expo";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$rc_yaw_expo)){$rc_yaw_expo = "NULL";}
$searchfor = "thr_mid";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$thr_mid)){$thr_mid = "NULL";}
$searchfor = "thr_expo";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$thr_expo)){$thr_expo = "NULL";}
$searchfor = "tpa_rate";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$tpa_rate)){$tpa_rate = "NULL";}
$searchfor = "tpa_breakpoint";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$tpa_breakpoint)){$tpa_breakpoint = "NULL";}
$searchfor = "rates";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$rates)){$rates = "NULL";}
$searchfor = "rollPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$rollPID)){$rollPID = "NULL";}
$searchfor = "pitchPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$pitchPID)){$pitchPID = "NULL";}
$searchfor = "yawPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$yawPID)){$yawPID = "NULL";}
$searchfor = "altPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$altPID)){$altPID = "NULL";}
$searchfor = "posPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$posPID)){$posPID = "NULL";}
$searchfor = "posrPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$posrPID)){$posrPID = "NULL";}
$searchfor = "levelPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$levelPID)){$levelPID = "NULL";}
$searchfor = "magPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$magPID)){$magPID = "NULL";}
$searchfor = "velPID";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$velPID)){$velPID = "NULL";}
$searchfor = "yaw_p_limit";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$yaw_p_limit)){$yaw_p_limit = "NULL";}
$searchfor = "yaw_lpf_hz";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$yaw_lpf_hz)){$yaw_lpf_hz = "NULL";}
$searchfor = "dterm_lpf_hz";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$dterm_lpf_hz)){$dterm_lpf_hz = "NULL";}
$searchfor = "dterm_notch_hz";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$dterm_notch_hz)){$dterm_notch_hz = "NULL";}
$searchfor = "dterm_notch_cutoff";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$dterm_notch_cutoff)){$dterm_notch_cutoff = "NULL";}
$searchfor = "deadband";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$deadband)){$deadband = "NULL";}
$searchfor = "yaw_deadband";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$yaw_deadband)){$yaw_deadband = "NULL";}
$searchfor = "gyro_lpf";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$gyro_lpf)){$gyro_lpf = "NULL";}
$searchfor = "gyro_lpf_hz";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$gyro_lpf_hz)){$gyro_lpf_hz = "NULL";}
$searchfor = "gyro_notch_hz";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$gyro_notch_hz)){$gyro_notch_hz = "NULL";}
$searchfor = "gyro_notch_cutoff";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$gyro_notch_cutoff)){$gyro_notch_cutoff = "NULL";}
$searchfor = "acc_lpf_hz";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$acc_lpf_hz)){$acc_lpf_hz = "NULL";}
$searchfor = "acc_notch_hz";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$acc_notch_hz)){$acc_notch_hz = "NULL";}
$searchfor = "acc_notch_cutoff";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$acc_notch_cutoff)){$acc_notch_cutoff = "NULL";}
$searchfor = "gyro_stage2_lowpass_hz";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$gyro_stage2_lowpass_hz)){$gyro_stage2_lowpass_hz = "NULL";}
$searchfor = "pidSumLimit";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$pidSumLimit)){$pidSumLimit = "NULL";}
$searchfor = "acc_hardware";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$acc_hardware)){$acc_hardware = "NULL";}
$searchfor = "baro_hardware";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$baro_hardware)){$baro_hardware = "NULL";}
$searchfor = "mag_hardware";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$mag_hardware)){$mag_hardware = "NULL";}
$searchfor = "motor_pwm_rate";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$motor_pwm_rate)){$motor_pwm_rate = "NULL";}
$searchfor = "waypoints";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$waypoints)){$waypoints = "NULL";}
$searchfor = "axisAccelerationLimitYaw";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$axisAccelerationLimitYaw)){$axisAccelerationLimitYaw = "NULL";}
$searchfor = "axisAccelerationLimitRollPitch";
if(!preg_match("/(".$searchfor."):(.*)/",$contents,$axisAccelerationLimitRollPitch)){$axisAccelerationLimitRollPitch = "NULL";}

$stmt = $db->prepare('INSERT INTO FCparams (activityID, P_interval, minthrottle, maxthrottle, looptime, rc_rate,
									rc_expo, rc_yaw_expo, thr_mid, thr_expo ,tpa_rate, tpa_breakpoint, rates, rollPID,
									pitchPID, yawPID, altPID, posPID, posrPID, levelPID, magPID, velPID, yaw_p_limit,
									yaw_lpf_hz, dterm_lpf_hz, dterm_notch_hz, dterm_notch_cutoff, deadband, 
									yaw_deadband, gyro_lpf, gyro_lpf_hz, gyro_notch_hz, gyro_notch_cutoff, acc_lpf_hz,
									acc_notch_hz, acc_notch_cutoff, gyro_stage2_lowpass_hz, pidSumLimit, acc_hardware, 
									baro_hardware, mag_hardware, motor_pwm_rate, waypoints, axisAccelerationLimitYaw, 
									axisAccelerationLimitRollPitch, csv_fname) 
									VALUES (:activityID, :P_interval, :minthrottle, :maxthrottle, :looptime, :rc_rate, 
									:rc_expo, :rc_yaw_expo, :thr_mid, :thr_expo, :tpa_rate, :tpa_breakpoint,
									:rates, :rollPID, :pitchPID, :yawPID, :altPID, :posPID, :posrPID, 
									:levelPID, :magPID, :velPID, :yaw_p_limit, :yaw_lpf_hz, :dterm_lpf_hz, :dterm_notch_hz,
									:dterm_notch_cutoff, :deadband, :yaw_deadband, :gyro_lpf, :gyro_lpf_hz, 
									:gyro_notch_hz, :gyro_notch_cutoff, :acc_lpf_hz, :acc_notch_hz, :acc_notch_cutoff, 
									:gyro_stage2_lowpass_hz, :pidSumLimit, :acc_hardware, :baro_hardware, :mag_hardware,
									:motor_pwm_rate, :waypoints, :axisAccelerationLimitYaw, :axisAccelerationLimitRollPitch, :csv_fname)');
$stmt->execute(array(
			':activityID' => $LastActivityID,
			':P_interval' => $P_interval[2],
			':minthrottle' => $minthrottle[2],
			':maxthrottle' => $maxthrottle[2],
			':looptime' => $looptime[2],
			':rc_rate' => $rc_rate[2],
			':rc_expo' => $rc_expo[2],
			':rc_yaw_expo' => $rc_yaw_expo[2],
			':thr_mid' => $thr_mid[2],
			':thr_expo' => $thr_expo[2],	
			':tpa_rate' => $tpa_rate[2],	
			':tpa_breakpoint' => $tpa_breakpoint[2],	
			':rates' => $rates[2],	
			':rollPID' => $rollPID[2],	
			':pitchPID' => $pitchPID[2],	
			':yawPID' => $yawPID[2],	
			':altPID' => $altPID[2],	
			':posPID' => $posPID[2],	
			':posrPID' => $posrPID[2],	
			':levelPID' => $levelPID[2],	
			':magPID' => $magPID[2],	
			':velPID' => $velPID[2],	
			':yaw_p_limit' => $yaw_p_limit[2],	
			':yaw_lpf_hz' => $yaw_lpf_hz[2],	
			':dterm_lpf_hz' => $dterm_lpf_hz[2],	
			':dterm_notch_hz' => $dterm_notch_hz[2],	
			':dterm_notch_cutoff' => $dterm_notch_cutoff[2],	
			':deadband' => $deadband[2],	
			':yaw_deadband' => $yaw_deadband[2],	
			':gyro_lpf' => $gyro_lpf[2],			
			':gyro_lpf_hz' => $gyro_lpf_hz[2],			
			':gyro_notch_hz' => $gyro_notch_hz[2],			
			':gyro_notch_cutoff' => $gyro_notch_cutoff[2],			
			':acc_lpf_hz' => $acc_lpf_hz[2],			
			':acc_notch_hz' => $acc_notch_hz[2],		
			':acc_notch_cutoff' => $acc_notch_cutoff[2],	
			':gyro_stage2_lowpass_hz' => $gyro_stage2_lowpass_hz[2],			
			':pidSumLimit' => $pidSumLimit[2],			
			':acc_hardware' => $acc_hardware[2],			
			':baro_hardware' => $baro_hardware[2],			
			':mag_hardware' => $mag_hardware[2],			
			':motor_pwm_rate' => $motor_pwm_rate[2],			
			':waypoints' => $waypoints[2],			
			':axisAccelerationLimitYaw' => $axisAccelerationLimitYaw[2],			
			':axisAccelerationLimitRollPitch' => $axisAccelerationLimitRollPitch[2],		
			':csv_fname' => $var[0][1].".gps.csv"
		));
		
$aResult = array();
$aResult['result'] = "1";
echo json_encode($aResult);
?>
