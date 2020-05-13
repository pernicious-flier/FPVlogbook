<?php

if (!file_exists('files')) {
	mkdir('files', 0777);
}
if (!file_exists('files/txt')) {
	mkdir('files/txt', 0777);
}
if (!file_exists('files/gpx')) {
	mkdir('files/gpx', 0777);
}
if (!file_exists('files/csv')) {
	mkdir('files/csv', 0777);
}
if (!file_exists('blackbox-tools/logs')) {
	mkdir('blackbox-tools/logs', 0777);
}
	
set_time_limit(180);	//set php timeout at 3min
$filename = $_FILES['file']['name'];
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
if ($ext == 'gpx') 
{
	if(move_uploaded_file($_FILES['file']['tmp_name'], 'files/gpx/'.$_FILES['file']['name']))
	{
		echo "1";
	}
	else
	{
		echo "0";
	}
	/*echo "File uploaded successfully.";*/
}
elseif($ext == 'txt')
{
	if(move_uploaded_file($_FILES['file']['tmp_name'], 'blackbox-tools/logs/'.$_FILES['file']['name']))
	{
		//shell_exec("blackbox-tools/./blackbox_decode blackbox-tools/logs/".$_FILES['file']['name']." 2>&1");	//LINUX
		shell_exec("blackbox-tools\blackbox_decode.exe blackbox-tools\logs\\".$_FILES['file']['name']." 2>&1");	//WINDOWS
		rename("blackbox-tools/logs/".pathinfo($filename, PATHINFO_FILENAME).".01.gps.csv","files/csv/".pathinfo($filename, PATHINFO_FILENAME).".01.gps.csv");
		rename("blackbox-tools/logs/".pathinfo($filename, PATHINFO_FILENAME).".01.csv","files/csv/".pathinfo($filename, PATHINFO_FILENAME).".01.csv");
		rename("blackbox-tools/logs/".pathinfo($filename, PATHINFO_FILENAME).".01.gps.gpx","files/gpx/".pathinfo($filename, PATHINFO_FILENAME).".01.gps.gpx");
		unlink("blackbox-tools/logs/".pathinfo($filename, PATHINFO_FILENAME).".01.event");
	
		//trim the .txt file
		$handle = fopen('blackbox-tools/logs/' . $_FILES['file']['name'], "r");
		$linenum = 0;
		$out = '';
		if ($handle) {
			while ((($line = fgets($handle)) !== false)&&($linenum<81)) {
				$out = $out.$line;
				$linenum=$linenum+1;
			}
			fclose($handle);
		}
		$myfile = fopen('files/txt/' . pathinfo($filename, PATHINFO_FILENAME) . '.01.txt', "w") or die("Unable to open file!");
		fwrite($myfile, $out);
		fclose($myfile);
				
		//decimate the .gps.csv file
		$handle = fopen('files/csv/' . pathinfo($filename, PATHINFO_FILENAME) . ".01.gps.csv", "r")or die("Unable to open file!");
		$linenum = 0;
		$out = '';
		$linecount = 0;
		while(!feof($handle)){
		  $line = fgets($handle);
		  $linecount++;
		}
		fclose($handle);
		
		$handle = fopen('files/csv/' . pathinfo($filename, PATHINFO_FILENAME) . ".01.gps.csv", "r")or die("Unable to open file!");
		if ($handle) {
			$out = fgets($handle); 	//skip title bar
			if (($line = fgets($handle)) !== false)	//get the first line
			{				
				$out = $line;
				$temp = explode(",", $line);
				$time0 = intval(substr($temp[0], 0, -6));//downsize to 1Hz
				while (($line = fgets($handle)) !== false) 
				{
					$temp = explode(",", $line);
					$time1 = intval(substr($temp[0], 0, -6));//downsize to 1Hz
					if($time1 > $time0)
					{
						$time0 = $time1;
						$out = $out.$line;
					}
				}
			} 
		}
		fclose($handle);		
		$myfile = fopen('files/csv/' . pathinfo($filename, PATHINFO_FILENAME) . ".01.gps.csv", "w") or die("Unable to open file!");
		fwrite($myfile, $out);
		fclose($myfile);
				
		//decimate the .csv file
		$handle = fopen('files/csv/' . pathinfo($filename, PATHINFO_FILENAME) . ".01.csv", "r")or die("Unable to open file!");
		$linenum = 0;
		$out = '';
		$linecount = 0;
		while(!feof($handle)){
		  $line = fgets($handle);
		  $linecount++;
		}
		fclose($handle);

		$handle = fopen('files/csv/' . pathinfo($filename, PATHINFO_FILENAME) . ".01.csv", "r")or die("Unable to open file!");
		if ($handle) {
			//$out = fgets($handle); 	//skip title bar
			if (($line = fgets($handle)) !== false)	//get the first line
			{				
				$out = $line;
				$temp = explode(",", $line);
				$time0 = intval(substr($temp[1], 0, -6));//downsize to 1Hz
				while (($line = fgets($handle)) !== false) 
				{
					$temp = explode(",", $line);
					$time1 = intval(substr($temp[1], 0, -6));//downsize to 1Hz
					if($time1 > $time0)
					{
						$time0 = $time1;
						$out = $out.$line;
					}
				}
			} 
		}
		fclose($handle);		
		$myfile = fopen('files/csv/' . pathinfo($filename, PATHINFO_FILENAME) . ".01.csv", "w") or die("Unable to open file!");
		fwrite($myfile, $out);
		fclose($myfile);
		
	
		echo "1";
	}
	else
	{
		echo "0";
	}
}
?>
