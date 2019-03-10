<?php 
require('layout/headerSite.php');
?>
		<div id="fpvlogDiv">
			<div class="tableContainer" >
				<div id="table">
				</div>
			</div>
			<div  class="box" id="dataBox">
				<div  class="left" id="mapBox"></div>
				<div  class="right" id="activityDesc">
					<div class="title">Activity log Details</div>
					<div style='margin-top: 10px;'>
						<div class="column" style="margin-left:2.5%; margin-right:1.25%;">
							<div id="title_act" class="content"></div>
							<div id="model_name" class="content"></div>
							<div id="location" class="content"></div>
							<div id="date_act" class="content"></div>
							<div id="time_act" class="content"></div>
						</div>
						<div class="column" style="margin-left:1.25%; margin-right:2.5%;">
							<div id="alt" class="content"></div>
							<div id="max_alt" class="content"></div>
							<div id="max_spd" class="content"></div>
							<div id="min_rssi" class="content"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="activityDiv" style="margin-top: 35px;"></div>
	</div>
<?php 
//include header template
require('layout/footer.php'); 
?>

<script type="text/javascript">

var activityID;
var editable = 0;

var mapBoxHTML1 = '<div  class="left" id="mapBox"></div> \
				<div id="loader"></div></div> \
				<div class="right" id="activityDesc"> \
					<div class="title">Activity log Details</div> \
					<div style="margin-top: 10px;"> \
						<div class="column" style="margin-left:2.5%; margin-right:1.25%;"> \
							<div id="title_act" class="content"></div> \
							<div id="model_name" class="content"></div> \
							<div id="location" class="content"></div> \
							<div id="date_act" class="content"></div> \
							<div id="time_act" class="content"></div> \
						</div> \
						<div class="column" style="margin-left:1.25%; margin-right:2.5%;"> \
							<div id="alt" class="content"></div> \
							<div id="max_alt" class="content"></div> \
							<div id="max_spd" class="content"></div> \
							<div id="min_rssi" class="content"></div> \
						</div> \
					</div> \
				</div> ';
					
var mapBoxHTML2 = '<div class="box" id="dataBox2" style="height:510px;"> \
				<div style="width:20%;"></div> \
					<div class="left" id="mapBox" style="width:calc(98% - 220px);"></div> \
					<div class="right2" id="instruments"> \
						<td><div class="img-container"  style="margin: 20px;"> \
							<img class="top" id="compass" src="images/instruments/compass_needle.png" class="banner" style="width:100px; height:100px; margin:15px"> \
							<img class="bottom" src="images/instruments/compass_bk2.png" class="banner" style="width:130px; height:130px; "> \
						</div></td> \
						<td><div class="img-container"  style="margin: 20px;"> \
							<img class="top" id="roll" src="images/instruments/roll_needle.png" class="banner" style="width:100px; height:100px; margin:15px"> \
							<img class="bottom" src="images/instruments/IMU_bk.png" class="banner" style="width:130px; height:130px; "> \
						</div></td> \
						<td><div class="img-container"  style="margin: 20px;"> \
							<img class="top" id="pitch" src="images/instruments/pitch_needle.png" class="banner" style="width:100px; height:100px; margin:15px"> \
							<img class="bottom" src="images/instruments/IMU_bk.png" class="banner" style="width:130px; height:130px; "> \
						</div></td> \
					</div> \
				</div> \
				<div class="box" style="height:1200px;"> \
					<div style="width:97%; height:1140px; background:grey; margin:1%; margin-top:5px;"> \
						<div ><div id="graphDiv" ></div></div> \
						<div class="activityPanels" style="width:98%; height:400px; overflow:auto;"> \
							<img id="editActivity" src="images/icons/gear.png" style="float:right; margin-right:30px; margin-top:20px; width:50px;" onclick="editActivity()"><\img> \
							<div id="saveBut" style="float:right; margin-top:300px; margin-right:80;" ></div> \
							<div> \
								<div class="title">Activity log Details</div> \
								<ul id="first-col"> \
									<ul class="leftcolActLogDetail"> \
										<div style="position:relative; margin-top: 10px;"> \
											<div id="title_act" class="content2"></div> \
											<div id="model_name" class="content2"></div> \
											<div id="location" class="content2"></div> \
											<div id="date_act" class="content2"></div> \
											<div id="time_act" class="content2"></div> \
											<div id="alt" class="content2"></div> \
											<div id="max_alt" class="content2"></div> \
											<div id="max_spd" class="content2"></div> \
											<div id="min_rssi" class="content2"></div> \
										</div> \
									</ul> \
							   </ul> \
							   <ul id="second-col"> \
									<ul id="editableAct" class="leftcolParam"> \
									</ul> \
							   </ul> \
							   <ul id="third-col"> \
									<ul id="notesCol" class="leftcolParam"> \
										<div class="content3"> \
											<div style="color: white;">Notes: </div> \
											<textarea readonly rows="10" cols="80" id="notes" style="color: black;"></textarea> \
										</div> \
									</ul> \
							   </ul> \
							</div> \
						</div> \
						<div class="activityPanels" style="width:98%; height:400px; overflow:auto;"> \
							<div class="title">Flight Controller Parameters</div> \
							<div style="position:relative; margin-top: 10px;"> \
					<ul id="first-col"> \
						<ul class="leftcolParam"> \
									<div id="P_interval" class="content2"></div> \
									<div id="minthrottle" class="content2"></div> \
									<div id="maxthrottle" class="content2"></div> \
									<div id="looptime" class="content2"></div> \
									<div id="rc_rate" class="content2"></div> \
									<div id="rc_expo" class="content2"></div> \
									<div id="rc_yaw_expo" class="content2"></div> \
									<div id="thr_mid" class="content2"></div> \
									<div id="thr_expo" class="content2"></div> \
						</ul> \
				   </ul> \
				   <ul id="second-col"> \
						<ul class="leftcolParam"> \
									<div id="tpa_rate" class="content2"></div> \
									<div id="tpa_breakpoint" class="content2"></div> \
									<div id="rates" class="content2"></div> \
									<div id="rollPID" class="content2"></div> \
									<div id="pitchPID" class="content2"></div> \
									<div id="yawPID" class="content2"></div> \
									<div id="altPID" class="content2"></div> \
									<div id="posPID" class="content2"></div> \
									<div id="posPID" class="content2"></div> \
						</ul> \
				   </ul> \
				   <ul id="third-col"> \
						<ul class="leftcolParam"> \
									<div id="posrPID" class="content2"></div> \
									<div id="levelPID" class="content2"></div> \
									<div id="magPID" class="content2"></div> \
									<div id="velPID" class="content2"></div> \
									<div id="yaw_p_limit" class="content2"></div> \
									<div id="yaw_lpf_hz" class="content2"></div> \
									<div id="dterm_lpf_hz" class="content2"></div> \
									<div id="dterm_notch_hz" class="content2"></div> \
									<div id="dterm_notch_cutoff" class="content2"></div> \
						</ul> \
				   </ul> \
				   <ul id="fourth-col"> \
						<ul class="leftcolParam"> \
									<div id="deadband" class="content2"></div> \
									<div id="yaw_deadband" class="content2"></div> \
									<div id="gyro_lpf" class="content2"></div> \
									<div id="gyro_lpf_hz" class="content2"></div> \
									<div id="gyro_notch_hz" class="content2"></div> \
									<div id="gyro_notch_cutoff" class="content2"></div> \
									<div id="acc_lpf_hz" class="content2"></div> \
									<div id="acc_notch_hz" class="content2"></div> \
									<div id="acc_notch_cutoff" class="content2"></div> \
						</ul> \
				   </ul> \
				   <ul id="fifth-col"> \
						<ul class="leftcolParam"> \
									<div id="gyro_stage2_lowpass_hz" class="content2"></div> \
									<div id="pidSumLimit" class="content2"></div> \
									<div id="acc_hardware" class="content2"></div> \
									<div id="baro_hardware" class="content2"></div> \
									<div id="mag_hardware" class="content2"></div> \
									<div id="motor_pwm_rate" class="content2"></div> \
									<div id="waypoints" class="content2"></div> \
									<div id="axisAccelerationLimitYaw" class="content2"></div> \
									<div id="axisAccelerationLimitRollPitch" class="content2"></div> \
						</ul> \
				   </ul> \
								</div> \
							</div> \
						</div> \
					</div> \
				</div>';
				
function selectFPVlog()
{
	document.getElementById("fpvlog").className = "active";
	document.getElementById("activity").className = "null";
	x = document.getElementById('fpvlogDiv');
	x.style.display = "block";
	x = document.getElementById('activityDiv');
	x.style.display = "none";			
	
	document.getElementById("activityDiv").innerHTML  = ""; 
	document.getElementById("dataBox").innerHTML  = mapBoxHTML1 ; 
	DB_select("date_act","DESC");	
}

function selectActivity()
{
	document.getElementById("activity").className = "active";
	document.getElementById("fpvlog").className = "null";
	x = document.getElementById('fpvlogDiv');
	x.style.display = "none";
	x = document.getElementById('activityDiv');
	x.style.display = "block";
		
	document.getElementById("dataBox").innerHTML  = ""; 
	document.getElementById("activityDiv").innerHTML = mapBoxHTML2 ; 
	getRuntimeData(activityID);
	editable = 0;
	editActivity();
	DB_loadActivity(activityID);
	DB_loadFCparam(activityID);
}

function onload(){
	var text = "<div style='margin-top: 60px;'>" + "<?php echo $_SESSION['username'] ?>" + "'s <span><font color='#cc0000' face='arial''>fpv</font></span> logbook</div>";
	sessionStorage.setItem("username","<?php echo $_SESSION['username'] ?>");
	document.getElementById("toptitle").innerHTML = text;
			
	selectFPVlog();
}

</script>
</script>
<script  type="text/javascript" src="js/js_functions.js"></script>