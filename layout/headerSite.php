<?php require('dbinit.php');
if($_SESSION['loggedin'] == FALSE) 
{ 
		header("location: index.php");
}
?>

<html ondragover="return false">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style/style.css">
		<link rel="stylesheet" href="style/table.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
		  integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
		  crossorigin=""/>
		<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
		  integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
		  crossorigin=""></script>
		<script src="https://cdn.plot.ly/plotly-latest.min.js"></script> 
	</head>
	<body onload="onload()" ondrop="upload_file(event)">		
	<div class="topnav">
		<a id="fpvlog" onclick="selectFPVlog()">history</a>
		<a id="activity" onclick="selectActivity()">activity</a>
		<a href='logout.php'>logout</a>
	</div>
	<div>
		<div>
			<img src="images/header.png" class="banner">
			<div id="toptitle"></div>
		</div>
	</div>