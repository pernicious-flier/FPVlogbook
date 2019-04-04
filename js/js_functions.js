var track;
var posMarker = L.icon({
    iconUrl: 'images/icons/posMarker.png',
    iconSize:     [24, 24], // size of the icon
    iconAnchor:   [12, 12], // point of the icon which will correspond to marker's location
    popupAnchor:  [-3, -3] // point from which the popup should open relative to the iconAnchor
});
var startMarker = L.icon({
    iconUrl: 'images/icons/pin-icon-start.png',
    iconSize:     [24, 35], // size of the icon
    iconAnchor:   [14, 35], // point of the icon which will correspond to marker's location
    popupAnchor:  [-3, -3] // point from which the popup should open relative to the iconAnchor
});
var endMarker = L.icon({
    iconUrl: 'images/icons/pin-icon-end.png',
    iconSize:     [24, 35], // size of the icon
    iconAnchor:   [14, 35], // point of the icon which will correspond to marker's location
    popupAnchor:  [-3, -3] // point from which the popup should open relative to the iconAnchor
});

function sizeObj(obj) {
  return Object.keys(obj).length;
}

function makeTableHTML(myArray) {
   var result = "<div class='title'>Logs history table</div>";
		result += "<div class='tbl-header'>";
		result += "<table  cellpadding='0' cellspacing='0' border='0'>";
		result += "<thead>";
        result += "<tr>";
	//header
	var header = [["Title","Username","Date","Flight Time","Location","Vehicle Name","",""],["title_act","members.username","date_act","time_act","location","model_name","del","download"]];
	for(var j=0; j<6; j++){
            result += "<th  style='width:18%; height:5%;' onclick='DB_select(\""+header[1][j]+"\",\"NULL\")' >"+header[0][j]+"</th>";
        }
	result += "<th  style='width:6%; height:5%;'>"+header[0][j]+"</th>";
	result += "<th  style='width:6%; height:5%;'>"+header[0][j]+"</th>";
	result += "</tr>";
	result += "</thead>";
    result += "</table>";
	result += "</div>";
    result += "<div class='tbl-content'>";
	result += "<table  cellpadding='0' cellspacing='0' border='0'>";
	result += "<tbody>";
	//data
    for(var i=0; i<sizeObj(myArray); i++) {
        result += "<tr class='line' id='"+i+"'>";
        for(var j=1; j<sizeObj(myArray[i])/2; j++){
            result += "<td style='width:18%; height:5%; line-height: 12px;'  class='lineseg' onclick='lineLightup("+i+","+sizeObj(myArray)+"); loadActivity("+myArray[i][0]+");'>"+myArray[i][j]+"</td>";
        }
		result += "<td  style='width:3%; height:5%; line-height: 12px;'  onmouseover='this.bgColor=\"#336699\"'  onmouseout='this.bgColor=\"transparent \"' onclick='deleteAct("+myArray[i][0]+");'><img style='width:20px;height:20px' src='images/icons/bin.png'</td>";
		result += "<td  style='width:3%; height:5%; line-height: 12px;'  onmouseover='this.bgColor=\"#336699\"'  onmouseout='this.bgColor=\"transparent \"'  onclick='downloadAct("+myArray[i][0]+");'><img style='width:20px;height:20px' src='images/icons/download.png'</td>";
        result += "</tr>";
    }
	result += "</tbody>";
    result += "</table>";
	result += "</div>";
	document.getElementById("table").innerHTML =result;
return result;
}

function lineLightup(index,linenum)
{
	for(i=0;i<linenum;i++)
	{
		if(i==index) document.getElementById(i).className="lineActive";
		else  document.getElementById(i).className="line";
	}
}

function deleteAct(id)
{
	if (confirm('Are you sure you want to delete this activity log?\n\nBe careful, all data will be lost!')) 
	{
		jQuery.ajax({
		type: "POST",
		url: 'deleteAction.php',
		dataType: 'json',
		timeout: 60000, // sets timeout to 60 seconds
		data: {arguments: id},
		success: function (obj, textstatus) {
			if( !('error' in obj) ) {
				//alert(obj.result);
				DB_select("date_act","DESC");
				loadGPS(null,1,0);
			} else {
				//console.log(obj.error);
				//alert("error");
			}
		}
	});
	} else {
	}
}

function downloadAct(id)
{
	var res;
	jQuery.ajax({
		type: "POST",
		url: 'functions.php',
		dataType: 'json',
		timeout: 60000, // sets timeout to 60 seconds
		data: {functionname: 'loadActivity', arguments: id},
		success: function (obj, textstatus) {
			if( !('error' in obj) ) {
				res = obj.result;
				filename = (res[0][11].split("."))[0]+".txt";
				window.location.assign('blackbox-tools/logs/'+filename);
				//alert(filename);
			} else {
				//console.log(obj.error);
				//alert("error");
			}
		}
	});
}

function loadActivity(id)
{
	activityID = id;
	DB_loadActivity(id);
}

var track;
var MarkerActPos;
var map = new L.Map("mapBox", {center: new L.LatLng(44.4639937, 9.1019311), zoom: 11, scrollWheelZoom: false});
function loadGPS(filename,start,end){	
	//alert(filename);
	var res;
	var i;
	var GPSdata;
	GPSdata = [];
	map.remove();
	map = new L.Map("mapBox", {center: new L.LatLng(44.4639937, 9.1019311), zoom: 11, scrollWheelZoom: false});
	var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	//var osm = new L.TileLayer('https://tile.thunderforest.com/outdoors/{z}/{x}/{y}.png?apikey=e0defdbbc87744ddb07a61bb84a18b9e', {
		maxNativeZoom:19,
		maxZoom:20
	});		
	map.on('click', function() {
		if (map.scrollWheelZoom.enabled()) 
		{
			map.scrollWheelZoom.disable();
		}
		else 
		{
			map.scrollWheelZoom.enable();
		}
	});
	//map.removeLayer(track);
	map.eachLayer(function (layer) { map.removeLayer(layer); });	//remove ll layer
	map.addLayer(osm);
	jQuery.ajax({
		type: "POST",
		url: 'loadGPS.php',
		dataType: 'json',
		timeout: 60000, // sets timeout to 60 seconds
		data: {arguments: filename},
		success: function (obj, textstatus) {
			if( !('error' in obj) ) {
				res = obj.result;
				//alert(res[1]['lat']+" - " +res[1]['lon']);
				if(end==0) end = res.length;
				var t=0;
				for(i=start;i<end;i++)
				{
					GPSdata[t] = new L.LatLng(res[i]['lat'], res[i]['lon']);
					t++;
				}
				track = new L.Polyline(GPSdata, {
					color: 'red',
					weight: 3,
					opacity: 0.5,
					smoothFactor: 1
				});
				track.addTo(map);
				var MarkerStartPos = new L.marker(GPSdata[0], {icon: startMarker}).addTo(map);
				var MarkerEndPos = new L.marker(GPSdata[t-2], {icon: endMarker}).addTo(map);
				MarkerActPos = new L.marker([track.getLatLngs()[0].lat,track.getLatLngs()[0].lng], {icon: posMarker}).addTo(map);
				var group = new L.featureGroup([MarkerStartPos,track]);
				map.fitBounds(group.getBounds());
			} else {
				//console.log(obj.error);
				//alert("error");
			}
		}
	});
}

function upload_file(e) {
	//alert("dropped file");
	document.getElementById('loader').innerHTML = '<div class="loader"></div>';	
	var fileobj;  
	e.preventDefault();
	fileobj = e.dataTransfer.files[0];
	ajax_file_upload(fileobj);
}

function file_explorer() {
	var fileobj;  
	document.getElementById('selectfile').click();
	document.getElementById('selectfile').onchange = function() {
		fileobj = document.getElementById('selectfile').files[0];
		ajax_file_upload(fileobj);
	};
}

function ajax_file_upload(file_obj) {
	//alert("ajax_file_upload");
	if(file_obj != undefined) {
		var form_data = new FormData();    
		var d = new Date();       
		var filenamesplit =  file_obj.name.split(".");
		var extension = filenamesplit[sizeObj(filenamesplit)-1];
		//var newfilename = "<?php echo $_SESSION['username'] ?>"+"_"+d.valueOf();
		var newfilename = sessionStorage.getItem("username")+"_"+d.valueOf();
		var fileindex = '';
		if(extension=="txt" || extension=="TXT")
		{
			form_data.append('file', file_obj, (newfilename+".txt"));
			fileindex = ".01";
			//alert(newfilename+".txt");
		}
		else if(extension=="gpx")  form_data.append('file', file_obj, newfilename+".gpx");
		$.ajax({
			type: 'POST',
			url: 'uploadFiles.php',
			contentType: false,
			processData: false,
			data: form_data,
			timeout: 180000, // sets timeout to 1 min
			success:function(obj) { 
				//alert(obj);
				if(obj=="1")
				{	
					DB_insert(newfilename+fileindex);
					DB_select("date_act","DESC");
					loadGPS(newfilename+fileindex+".gpx",1,0);
					$('#selectfile').val('');
				}
				document.getElementById('loader').innerHTML = "";	
			}
		});
	}
}

function DB_insert(filename) {
	//alert(filename);
	var args = ["memberid",filename];
	jQuery.ajax({
    type: "POST",
    url: 'ActInsertDB.php',
    dataType: 'json',
	timeout: 180000, // sets timeout to 60 seconds
    data: {arguments: [args]},
    success: function (obj, textstatus) {
			//alert("DB_insert ok");		
			if( !('error' in obj) ) 
			{
				//var res = obj.result;				
				//alert(obj.result[0]);		
				DB_select("date_act","DESC");
			}
			else {
				console.log(obj.error);
				//alert("error");
			}
		}
	});	
}

var dir = "ASC";
function DB_select(order,direction) {	
	if(direction=="NULL")
	{
		if (dir=="DESC") dir = "ASC";
		else dir = "DESC";
	}
	else { dir = direction; }
	
	var args = ["tabledata",order,dir];
	jQuery.ajax({
		type: "POST",
		url: 'functions.php',
		dataType: 'json',
		timeout: 60000, // sets timeout to 60 seconds
		data: {functionname: 'select', arguments: [args]},
		success: function (obj, textstatus) {
			if( !('error' in obj) ) {
					var res = obj.result;
					makeTableHTML(res);
					activityID=res[0][0];
					lineLightup(0,res.length);
					DB_loadActivity(res[0][0]);
					//alert(res[0][0]);
				}
				else {
					console.log(obj.error);
					//alert("error");
				}
			}
		});
}

function DB_loadActivity(id) {		
	var res;
	jQuery.ajax({
		type: "POST",
		url: 'functions.php',
		dataType: 'json',
		timeout: 60000, // sets timeout to 60 seconds
		data: {functionname: 'loadActivity', arguments: id},
		success: function (obj, textstatus) {
			if( !('error' in obj) ) {
				res = obj.result;
				//alert(res[0][7]);
				document.getElementById("title_act").innerHTML = "Activity name: <span><font color=limegreen>" + res[0][1] + "</span>";
				document.getElementById("model_name").innerHTML = "Vehicle name: <span><font color=limegreen>" + res[0][6] + "</span>";
				document.getElementById("date_act").innerHTML = "Date&Time: <span><font color=limegreen>" + res[0][3] + " - " + res[0][4] + "</span>";
				document.getElementById("time_act").innerHTML = "Flight Time: <span><font color=limegreen>" + res[0][13] + "</span>";
				document.getElementById("alt").innerHTML = "Altitude Home: <span><font color=limegreen>" + (res[0][7]).toFixed(2)  + " m</span>";
				document.getElementById("max_alt").innerHTML = "Maximum rel Altitude: <span><font color=limegreen>" + (res[0][8]).toFixed(2)  + " m</span>";
				document.getElementById("max_spd").innerHTML = "Maximum GPS Speed: <span><font color=limegreen>" + (res[0][9]*3.6).toFixed(2)  + " Km/h</span>";
				document.getElementById("min_rssi").innerHTML = "Minimum RSSI: <span><font color=limegreen>" + (res[0][10]).toFixed(2) + " %</span>";
				if(document.getElementById("notes")) document.getElementById("notes").value = res[0][12];
				loadGPS(res[0][11],1,0);
				document.getElementById("location").innerHTML = "Location: <span><font color=limegreen>" + res[0][5] + "</span>"; 
			} else {
				//console.log(obj.error);
				//alert("error");
			}
		}
	});
	return res;
}

function editActivity()
{	
	//alert("editActivity");
	var args = [activityID]
	jQuery.ajax({
	type: "POST",
	url: 'ActDescSelectDB.php',
	dataType: 'json',
	timeout: 60000, // sets timeout to 60 seconds
	data: {arguments: [args]},
	success: function (obj, textstatus) {
			if( !('error' in obj) ) 
			{
				//var res = obj.result;				
				//alert(obj.result[0][4]);	
				if(editable == 1)
				{	
					document.getElementById("editActivity").src = "images/icons/eye.png";
					document.getElementById("editableAct").innerHTML = '<div class="content3"> \
					<div style="color: white;">Weather Condition</div> \
					<div> \
						<select id="weather" style="color:black; margin-bottom:50px;"> \
							<option value=1>Not Reported</option> \
							<option value=2>Sunny</option> \
							<option value=3>Clear</option> \
							<option value=4>Cloudy</option> \
							<option value=5>Rainy</option> \
							<option value=6>Snowy</option> \
							<option value=7>Thundery</option> \
						</select> \
					</div> \
					<div style="color: white;">Wind Direction</div>  \
					<div> \
						<select id="wind" style="color:black; margin-bottom:50px;"> \
							<option value=1>Not Reported</option> \
							<option value=2>N</option> \
							<option value=3>NE</option> \
							<option value=4>E</option> \
							<option value=5>SE</option> \
							<option value=6>S</option> \
							<option value=7>SO</option> \
							<option value=8>O</option> \
							<option value=9>NO</option> \
						</select> \
					</div> \
					<div> \
						<div contenteditable="true" style="color: white; margin-bottom:50px;">Wind Speed: \
							<input  id="wspd" type="number" name="quantity" style="color:black;" name="quantity" min="1" max="50" value="0"> \
						</div> \
					</div> \
					<div>';		
					//alert(obj.result[0][0] + "  -  " + obj.result[0][2]);
					document.getElementById("weather").selectedIndex  = obj.result[0][0]-1;		
					document.getElementById("wind").selectedIndex  = obj.result[0][2]-1;
					document.getElementById("wspd").value  = obj.result[0][1];									
					document.getElementById("notesCol").innerHTML = '<div class="content3"> \
						<div style="color: white;">Notes: </div> \
						<textarea rows="8" cols="55" id="notes" style="color: black; resize: none; font-size:16px;"></textarea> \
					</div> ';	
					document.getElementById("saveBut").innerHTML = "<button type='button' style='position:absolute;' onclick='saveActDesc()'>Save</button>" ;
					editable = 0;
				}
				else
				{
					document.getElementById("editActivity").src = "images/icons/gear.png";
					document.getElementById("editableAct").innerHTML = '<div class="content3"> \
					<div> \
						<div style="color: white; margin-bottom:10px;">Weather Conditions</div>\
						<div style="height:90px; width:90px; margin-bottom:40px;"><img id="weatherIcon" style="max-height: 100%;"></img></div> \
					</div> \
					<div> \
						<div style="color: white; margin-bottom:10px;">Wind Direction</div>\
						<div style="height:60px; width:60px; margin-bottom:40px;"><img id="windIcon" style="max-height: 100%;"></img></div> \
					</div> \
					<div> \
						<div id="wspd" contenteditable="false" style="color: white; margin-bottom:40px;">Wind Speed: <span><font color=limegreen>' + obj.result[0][1] + ' Knots</span></div> \
					</div> ';
					document.getElementById("notesCol").innerHTML = '<div class="content3"> \
						<div style="color: white;">Notes: </div> \
						<textarea readonly rows="12" cols="55" id="notes" style="color: black; resize: none; font-size:16px;"></textarea> \
					</div> ';					
					//alert(obj.result[0][4] + "  -  " + obj.result[0][3]);
					document.getElementById("weatherIcon").src = obj.result[0][4];		
					document.getElementById("windIcon").src = obj.result[0][3];	
					document.getElementById("saveBut").innerHTML = "" ;
					editable = 1;					
				}
	
				var res;	
				jQuery.ajax({
					type: "POST",
					url: 'functions.php',
					dataType: 'json',
					timeout: 60000, // sets timeout to 60 seconds
					data: {functionname: 'loadActivity', arguments: activityID},
					success: function (obj, textstatus) {
						if( !('error' in obj) ) {
							res = obj.result;
							if(document.getElementById("notes")) document.getElementById("notes").value = res[0][12];
						} else {
						}
					}
				});
			}
			else {
				console.log(obj.error);
				//alert("error");
			}	
		}
	});
}

function saveActDesc()
{
	var e = document.getElementById("weather");
	var weatherCondition = e.options[e.selectedIndex].value;	
	var e = document.getElementById("wind");
	var windDirection = e.options[e.selectedIndex].value;	
	var windSpeed = document.getElementById("wspd").value;	
	var args = [activityID,weatherCondition,windSpeed,windDirection];
	
	jQuery.ajax({
    type: "POST",
    url: 'ActDescInsertDB.php',
    dataType: 'json',
	timeout: 60000, // sets timeout to 60 seconds
    data: {arguments: [args]},
    success: function (obj, textstatus) {
			//alert("DB_insert ok");			
			//alert(obj.result[0][4]);	
			if( !('error' in obj) ) 
			{
				//var res = obj.result;				
				//alert(obj.result[0][0]);		
			}
			else {
				console.log(obj.error);
				//alert("error");
			}
		}
	});	
	
	var notes = document.getElementById("notes").value;
	var args = [activityID,notes];
	jQuery.ajax({
    type: "POST",
    url: 'ActUpdateDB.php',
    dataType: 'json',
	timeout: 60000, // sets timeout to 60 seconds
    data: {arguments: [args]},
    success: function (obj, textstatus) {
			//alert("DB_insert ok");			
			//alert(obj.result[0][4]);	
			if( !('error' in obj) ) 
			{
				//var res = obj.result;				
				//alert(obj.result[0][0]);		
			}
			else {
				console.log(obj.error);
				//alert("error");
			}
		}
	});	
	editable = 0;
	editActivity();
}

function DB_loadFCparam(id) {		
	var res;
	jQuery.ajax({
		type: "POST",
		url: 'functions.php',
		dataType: 'json',
		timeout: 120000, // sets timeout to 60 seconds
		data: {functionname: 'loadFCparam', arguments: id},
		success: function (obj, textstatus) {
			if( !('error' in obj) ) {
				res = obj.result;
				//alert(res[0][7]);
				document.getElementById("P_interval").innerHTML = "P_interval: <span><font color=limegreen>" + res[0][1] + "</span>";
				document.getElementById("minthrottle").innerHTML = "minthrottle: <span><font color=limegreen>" + res[0][2] + "</span>";
				document.getElementById("maxthrottle").innerHTML = "maxthrottle: <span><font color=limegreen>" + res[0][3] + "</span>";
				document.getElementById("looptime").innerHTML = "looptime: <span><font color=limegreen>" + res[0][4] + "</span>";
				document.getElementById("rc_rate").innerHTML = "rc_rate: <span><font color=limegreen>" + res[0][5] + "</span>";
				document.getElementById("rc_expo").innerHTML = "rc_expo: <span><font color=limegreen>" + res[0][6] + "</span>";
				document.getElementById("rc_yaw_expo").innerHTML = "rc_yaw_expo: <span><font color=limegreen>" + res[0][7] + "</span>";
				document.getElementById("thr_mid").innerHTML = "thr_mid: <span><font color=limegreen>" + res[0][8] + "</span>";
				document.getElementById("thr_expo").innerHTML = "thr_expo: <span><font color=limegreen>" + res[0][9] + "</span>";
				document.getElementById("tpa_rate").innerHTML = "tpa_rate: <span><font color=limegreen>" + res[0][10] + "</span>";
				document.getElementById("tpa_breakpoint").innerHTML = "tpa_breakpoint: <span><font color=limegreen>" + res[0][11] + "</span>";
				document.getElementById("rates").innerHTML = "rates: <span><font color=limegreen>" + res[0][12] + "</span>";
				document.getElementById("rollPID").innerHTML = "rollPID: <span><font color=limegreen>" + res[0][13] + "</span>";
				document.getElementById("pitchPID").innerHTML = "pitchPID: <span><font color=limegreen>" + res[0][14] + "</span>";
				document.getElementById("yawPID").innerHTML = "yawPID: <span><font color=limegreen>" + res[0][15] + "</span>";
				document.getElementById("altPID").innerHTML = "altPID: <span><font color=limegreen>" + res[0][16] + "</span>";
				document.getElementById("posPID").innerHTML = "posPID: <span><font color=limegreen>" + res[0][17] + "</span>";
				document.getElementById("posrPID").innerHTML = "posrPID: <span><font color=limegreen>" + res[0][18] + "</span>";
				document.getElementById("levelPID").innerHTML = "levelPID: <span><font color=limegreen>" + res[0][19] + "</span>";
				document.getElementById("magPID").innerHTML = "magPID: <span><font color=limegreen>" + res[0][20] + "</span>";
				document.getElementById("velPID").innerHTML = "velPID: <span><font color=limegreen>" + res[0][21] + "</span>";
				document.getElementById("yaw_p_limit").innerHTML = "yaw_p_limit: <span><font color=limegreen>" + res[0][22] + "</span>";
				document.getElementById("yaw_lpf_hz").innerHTML = "yaw_lpf_hz: <span><font color=limegreen>" + res[0][23] + "</span>";
				document.getElementById("dterm_lpf_hz").innerHTML = "dterm_lpf_hz: <span><font color=limegreen>" + res[0][24] + "</span>";
				document.getElementById("dterm_notch_hz").innerHTML = "dterm_notch_hz: <span><font color=limegreen>" + res[0][25] + "</span>";
				document.getElementById("dterm_notch_cutoff").innerHTML = "dterm_notch_cutoff: <span><font color=limegreen>" + res[0][26] + "</span>";
				document.getElementById("deadband").innerHTML = "deadband: <span><font color=limegreen>" + res[0][27] + "</span>";
				document.getElementById("yaw_deadband").innerHTML = "yaw_deadband: <span><font color=limegreen>" + res[0][28] + "</span>";
				document.getElementById("gyro_lpf").innerHTML = "gyro_lpf: <span><font color=limegreen>" + res[0][29] + "</span>";
				document.getElementById("gyro_lpf_hz").innerHTML = "gyro_lpf_hz: <span><font color=limegreen>" + res[0][30] + "</span>";
				document.getElementById("gyro_notch_hz").innerHTML = "gyro_notch_hz: <span><font color=limegreen>" + res[0][31] + "</span>";
				document.getElementById("gyro_notch_cutoff").innerHTML = "gyro_notch_cutoff: <span><font color=limegreen>" + res[0][32] + "</span>";
				document.getElementById("acc_lpf_hz").innerHTML = "acc_lpf_hz: <span><font color=limegreen>" + res[0][33] + "</span>";
				document.getElementById("acc_notch_hz").innerHTML = "acc_notch_hz: <span><font color=limegreen>" + res[0][34] + "</span>";
				document.getElementById("acc_notch_cutoff").innerHTML = "acc_notch_cutoff: <span><font color=limegreen>" + res[0][35] + "</span>";
				document.getElementById("gyro_stage2_lowpass_hz").innerHTML = "gyro_stage2_lowpass_hz: <span><font color=limegreen>" + res[0][36] + "</span>";
				document.getElementById("pidSumLimit").innerHTML = "pidSumLimit: <span><font color=limegreen>" + res[0][37] + "</span>";
				document.getElementById("acc_hardware").innerHTML = "acc_hardware: <span><font color=limegreen>" + res[0][38] + "</span>";
				document.getElementById("baro_hardware").innerHTML = "baro_hardware: <span><font color=limegreen>" + res[0][39] + "</span>";
				document.getElementById("mag_hardware").innerHTML = "mag_hardware: <span><font color=limegreen>" + res[0][40] + "</span>";
				document.getElementById("motor_pwm_rate").innerHTML = "motor_pwm_rate: <span><font color=limegreen>" + res[0][41] + "</span>";
				document.getElementById("waypoints").innerHTML = "waypoints: <span><font color=limegreen>" + res[0][42] + "</span>";
				document.getElementById("axisAccelerationLimitYaw").innerHTML = "axisAccelerationLimitYaw: <span><font color=limegreen>" + res[0][43] + "</span>";
				document.getElementById("axisAccelerationLimitRollPitch").innerHTML = "axisAccelerationLimitRollPitch: <span><font color=limegreen>" + res[0][44] + "</span>";
			} else {
				//console.log(obj.error);
				//alert("error");
			}
		}
	});
	return res;
}

function getRuntimeData(id)
{	
	//alert(id);
	jQuery.ajax({
	type: "POST",
	url: 'getRuntimeData.php',
	dataType: 'json',
	timeout: 60000, // sets timeout to 60 seconds
	data: {arguments: id},
	success: function (obj) {
		if( !('error' in obj) ) {
				//alert("getRuntimeData ok");		
				plotDraw(obj.result);
			}
			else {
				console.log(obj.error);
				//alert("error");
			}
		}
	});
}

function medianFilter(dataIn)
{
	var dataOut = new Array;
	for(var i=0;i<dataIn.length-3;i++)
	{
		if(dataIn[i+1]<dataIn[i] && dataIn[i]<dataIn[i+2]) dataOut[i] = dataIn[i];
		else if(dataIn[i+2]<dataIn[i] && dataIn[i]<dataIn[i+1]) dataOut[i] = dataIn[i];
		else if(dataIn[i]<dataIn[i+1] && dataIn[i+1]<dataIn[i+2]) dataOut[i] = dataIn[i+1];
		else if(dataIn[i+2]<dataIn[i+1] && dataIn[i+1]<dataIn[i]) dataOut[i] = dataIn[i+1];
		else if(dataIn[i+1]<dataIn[i+2] && dataIn[i+2]<dataIn[i]) dataOut[i] = dataIn[i+2];
		else if(dataIn[i]<dataIn[i+2] && dataIn[i+2]<dataIn[i+1]) dataOut[i] = dataIn[i+2];		
	}
	return dataOut;
}

function decimate(dataIn,N)
{
	var dataOut = new Array;
	var j=1, t=0;
	for(var i=1;i<=dataIn.length;i++)
	{
		if(j < N)
		{
			if(!dataOut[t]) dataOut[t] = dataIn[i];
			else dataOut[t] = dataIn[i] + dataOut[t];
			j++;
		}
		else 
		{
			dataOut[t] = dataOut[t] + dataIn[i];
			dataOut[t] = dataOut[t] / N;
			//dataOut[t] = dataIn[t];
			t++;
			j=1;
		}
	}
	if(j>1) dataOut[t] = dataOut[t] / j;
	return dataOut;
}

function decodeModes(dataIn)
{
	var dataOut = new Array;
	var t=1;
	for(var i=2;i<=dataIn.length;i++)
	{		
		dataOut[t] = 0;
		if(dataIn[t].includes('ANGLE_MODE')) dataOut[t] += 1;
		if(dataIn[t].includes('HORIZON_MODE')) dataOut[t] += 2;
		if(dataIn[t].includes('HEADING_MODE')) dataOut[t] += 3;
		if(dataIn[t].includes('NAV_ALTHOLD_MODE')) dataOut[t] += 4;
		if(dataIn[t].includes('NAV_RTH_MODE')) dataOut[t] += 5;
		if(dataIn[t].includes('NAV_POSHOLD_MODE')) dataOut[t] += 6;
		if(dataIn[t].includes('HEADFREE_MODE')) dataOut[t] += 7;
		if(dataIn[t].includes('NAV_LAUNCH_MODE')) dataOut[t] += 8;
		if(dataIn[t].includes("MANUAL_MODE")) dataOut[t] += 9;
		if(dataIn[t].includes("FAILSAFE_MODE")) dataOut[t] += 10;
		if(dataIn[t].includes("AUTO_TUNE")) dataOut[t] += 11;
		if(dataIn[t].includes("NAV_WP_MODE")) dataOut[t] += 12;
		if(dataIn[t].includes("NAV_CRUISE_MODE")) dataOut[t] += 13;
		if(dataIn[t].includes("FLAPERON")) dataOut[t] += 14;
		t++;
	}
	return dataOut;
}

var yaw,pitch,roll;
function plotDraw(dataArray)
{
	yaw_ = dataArray.map(function(value,index) { return value['yaw']; });
	yaw = yaw_.map(function(x) { return x*360/4096; }); //normalize
	pitch_ = dataArray.map(function(value,index) { return value['pitch']; });
	pitch = pitch_.map(function(x) { return x*180/1024; }); //normalize
	roll_ = dataArray.map(function(value,index) { return value['roll']; });
	roll = roll_.map(function(x) { return x*180/1024; }); //normalize
	
	var alt = dataArray.map(function(value,index) { return value['alt']; });
	var initAlt = alt[1];
	var altDec = alt.map(function(x) { return (x-initAlt)/100; }); //normalize (m)
	var altTrace = {
	  y: altDec, 
	  mode: 'lines', 
	  name: 'Altitude', 
	  line: {shape: 'spline'}, 
	  type: 'scatter'
	};
	
	var rssi = dataArray.map(function(value,index) { return value['rssi']; });
	var rssiDec = rssi.map(function(x) { return x*100/1023; }); //normalize
	var rssiTrace = {
	  y: rssiDec, 
	  mode: 'lines', 
	  name: 'RSSI (%)', 
	  line: {shape: 'spline'}, 
	  type: 'scatter'
	};
	
	var vbat = dataArray.map(function(value,index) { return value['vbat']; });
	var vbatDec = vbat.map(function(x) { return x/100; }); //normalize
	var vbatTrace = {
	  y: vbatDec, 
	  mode: 'lines', 
	  name: 'V Bat (V)', 
	  line: {shape: 'spline'}, 
	  type: 'scatter'
	};
	
	var thr = dataArray.map(function(value,index) { return value['thr']; });
	var thrDec = thr.map(function(x) { return (x-1000)/10; }); //normalize
	var thrTrace = {
	  y: thrDec, 
	  mode: 'lines', 
	  name: 'Throttle (%)', 
	  line: {shape: 'spline'}, 
	  type: 'scatter'
	};
	
	var mode = dataArray.map(function(value,index) { return value['mode']; });
	var modeDec = decodeModes(mode);
	var modeTrace = {
	  y: modeDec, 
	  mode: 'lines', 
	  name: 'Flight Mode', 
	  line: {shape: 'spline'}, 
	  type: 'scatter'
	};

var myPlot = document.getElementById('graphDiv'),
    data = [altTrace,rssiTrace,vbatTrace,thrTrace,modeTrace],
    layout = {
		title: 'Flight data plot',
		legend: {
		y: 0.5, 
		traceorder: 'reversed', 
		font: {size: 16}, 
		yref: 'paper'
		}
    };

Plotly.newPlot('graphDiv', data, layout, {showSendToCloud: false, displaylogo: false, responsive: true});

var start=1;
var end=0;
myPlot.on('plotly_relayout', function(eventdata){	
	if(!eventdata['xaxis.range[0]']) { start = 1; }
	else { start = parseInt(eventdata['xaxis.range[0]']); }
	if(!eventdata['xaxis.range[1]']) { end = 0; }
	else { end = parseInt(eventdata['xaxis.range[1]']); }
	//alert( 'ZOOM!' + '\n\n' + 'Event data:' + '\n' + JSON.stringify(eventdata) + '\n\n' + 'x-axis start:' + start + '\n' + 'x-axis end:' + end );

	jQuery.ajax({
		type: "POST",
		url: 'functions.php',
		dataType: 'json',
		timeout: 60000, // sets timeout to 60 seconds
		data: {functionname: 'loadActivity', arguments: activityID},
		success: function (obj, textstatus) {
			if( !('error' in obj) ) {
				var res = obj.result;
				loadGPS(res[0][11],start,end);
			} 
		}
	});	
});

myPlot.on('plotly_doubleclick', function() {
	jQuery.ajax({
		type: "POST",
		url: 'functions.php',
		dataType: 'json',
		timeout: 60000, // sets timeout to 60 seconds
		data: {functionname: 'loadActivity', arguments: activityID},
		success: function (obj, textstatus) {
			if( !('error' in obj) ) {
				var res = obj.result;
				loadGPS(res[0][11],1,0);
			} 
		}
	});	
});

//set the IMU indicators if a touch device is used
myPlot.on('plotly_click', function(data){	
	if("ontouchstart" in document.documentElement)
	{
    	var xpos = data.points[0]['x']-start;
    	rotateImage("compass",yaw[parseInt(xpos)]);
    	rotateImage("pitch",pitch[parseInt(xpos)]);
    	rotateImage("roll",roll[parseInt(xpos)]);
    	placePosMarker(track.getLatLngs()[xpos].lat,track.getLatLngs()[xpos].lng);
	}
});

myPlot.on('plotly_hover', function(data){	
	var xpos = data.points[0]['x']-start;
	rotateImage("compass",yaw[parseInt(xpos)]);
	rotateImage("pitch",pitch[parseInt(xpos)]);
	rotateImage("roll",roll[parseInt(xpos)]);
	placePosMarker(track.getLatLngs()[xpos].lat,track.getLatLngs()[xpos].lng);
});

}

function rotateImage(img,angle) {
	var img = document.getElementById(img);
	img.style.transform = 'rotate('+angle+'deg)';
}

// attach handler to the move event of the mouse
//if (document.attachEvent) document.attachEvent('onmousemove', handler);
//else document.addEventListener('mousemove', handler);
function placePosMarker(Lat,Lon)
{
	MarkerActPos.setLatLng([Lat, Lon]); 
	
}