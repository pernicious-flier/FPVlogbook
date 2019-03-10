create database fpvlog;
use fpvlog;

CREATE TABLE `members` (
  `memberID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active` varchar(255) NOT NULL,
  `resetToken` varchar(255) DEFAULT NULL,
  `resetComplete` varchar(3) DEFAULT 'No',
  PRIMARY KEY (`memberID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `activities` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `memberID` int(11) NOT NULL,
  `title_act` varchar(255) NOT NULL,
  `date_act`DATE NOT NULL,
  `time_act`TIME NOT NULL,  
  `flightTime` varchar(255) NOT NULL,
  `model_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `altitude` float(11) NOT NULL,
  `max_altitude` float(11) NOT NULL,
  `max_speed` float(11) NOT NULL,
  `min_rssi` float(11) NOT NULL,
  `gps_fname` varchar(255) NOT NULL,
  `notes` varchar(255),
  PRIMARY KEY (`ID`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
    
CREATE TABLE `weather` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `activityID` varchar(255) NOT NULL,
  `weatherCondition` varchar(255) NOT NULL,
  `windSpeed` int(11) NOT NULL,
  `windDirection` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;    
 
CREATE TABLE `weatherIco` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `weatherIco` 
    (ID, filename) 
VALUES 
    (1,"images/icons/nc.png"),
    (2,"images/icons/sunny.png"),
    (3,"images/icons/clear.png"),
    (4,"images/icons/cloudy.png"),
    (5,"images/icons/rainy.png"),
    (6,"images/icons/snowy.png"),
    (7,"images/icons/thunder.png");
	
CREATE TABLE `windIco` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
 
INSERT INTO `windIco` 
    (ID, filename) 
VALUES 
    (1,"images/icons/nc.png"),
    (2,"images/icons/N.png"),
    (3,"images/icons/NE.png"),
    (4,"images/icons/E.png"),
    (5,"images/icons/SE.png"),
    (6,"images/icons/S.png"),
    (7,"images/icons/SO.png"),
    (8,"images/icons/O.png"),
    (9,"images/icons/NO.png");
	
CREATE TABLE `FCparams` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `activityID` int(11) NOT NULL,
  `P_interval` varchar(255) NOT NULL,
  `minthrottle` varchar(255) NOT NULL,
  `maxthrottle` varchar(255) NOT NULL,
  `looptime` varchar(255) NOT NULL,
  `rc_rate` varchar(255) NOT NULL,
  `rc_expo` varchar(255) NOT NULL,
  `rc_yaw_expo` varchar(255) NOT NULL,
  `thr_mid` varchar(255) NOT NULL,
  `thr_expo` varchar(255) NOT NULL,
  `tpa_rate` varchar(255) NOT NULL,
  `tpa_breakpoint` varchar(255) NOT NULL,
  `rates` varchar(255) NOT NULL,
  `rollPID` varchar(255) NOT NULL,
  `pitchPID` varchar(255) NOT NULL,
  `yawPID` varchar(255) NOT NULL,
  `altPID` varchar(255) NOT NULL,
  `posPID` varchar(255) NOT NULL,
  `posrPID` varchar(255) NOT NULL,
  `levelPID` varchar(255) NOT NULL,
  `magPID` varchar(255) NOT NULL,
  `velPID` varchar(255) NOT NULL,
  `yaw_p_limit` varchar(255) NOT NULL,
  `yaw_lpf_hz` varchar(255) NOT NULL,
  `dterm_lpf_hz` varchar(255) NOT NULL,
  `dterm_notch_hz` varchar(255) NOT NULL,
  `dterm_notch_cutoff` varchar(255) NOT NULL,
  `deadband` varchar(255) NOT NULL,
  `yaw_deadband` varchar(255) NOT NULL,
  `gyro_lpf` varchar(255) NOT NULL,
  `gyro_lpf_hz` varchar(255) NOT NULL,
  `gyro_notch_hz` varchar(255) NOT NULL,
  `gyro_notch_cutoff` varchar(255) NOT NULL,
  `acc_lpf_hz` varchar(255) NOT NULL,
  `acc_notch_hz` varchar(255) NOT NULL,
  `acc_notch_cutoff` varchar(255) NOT NULL,
  `gyro_stage2_lowpass_hz` varchar(255) NOT NULL,
  `pidSumLimit` varchar(255) NOT NULL,
  `acc_hardware` varchar(255) NOT NULL,
  `baro_hardware` varchar(255) NOT NULL,
  `mag_hardware` varchar(255) NOT NULL,
  `motor_pwm_rate` varchar(255) NOT NULL,
  `waypoints` varchar(255) NOT NULL,
  `axisAccelerationLimitYaw` varchar(255) NOT NULL,
  `axisAccelerationLimitRollPitch` varchar(255) NOT NULL,
  `csv_fname` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
  