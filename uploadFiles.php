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
	
$filename = $_FILES['file']['name'];
$ext = pathinfo($filename, PATHINFO_EXTENSION);
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
				
		$GPSlinesAfterDecimation = 2000;
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
		if($linecount>$GPSlinesAfterDecimation)
		{
			$division= intval($linecount/$GPSlinesAfterDecimation);//divide in order to have GPSlinesAfterDecimation samples
			$CSVlinesAfterDecimation  = $GPSlinesAfterDecimation;
		}
		else
		{
			$division = 1;
			$CSVlinesAfterDecimation = $linecount;//this is in order to have same lines of gps and csv data
		}
		
		$i = 1;
		$handle = fopen('files/csv/' . pathinfo($filename, PATHINFO_FILENAME) . ".01.gps.csv", "r")or die("Unable to open file!");
		if ($handle) {
			$out = fgets($handle); 	//skip title bar
			while (($line = fgets($handle)) !== false) {
				if($i == $division)
				{
					$out = $out.$line;
					$i=1;
				}
				else 
				{ $i++;}
			}
		} else {
			// error opening the file.
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
		if($linecount>$CSVlinesAfterDecimation) $division= intval($linecount/$CSVlinesAfterDecimation);//divide in order to have CSVlinesAfterDecimation samples
		else $division = 1;
		
		$i = 1;
		$handle = fopen('files/csv/' . pathinfo($filename, PATHINFO_FILENAME) . ".01.csv", "r")or die("Unable to open file!");
		if ($handle) {
			$out = fgets($handle); 	//skip title bar
			while (($line = fgets($handle)) !== false) {
				if($i == $division)
				{
					$out = $out.$line;
					$i=1;
				}
				else 
				{ $i++;}
			}
		} else {
			// error opening the file.
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