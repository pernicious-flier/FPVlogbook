<?php
ob_start();
session_start();

//set timezone
date_default_timezone_set('Europe/London');

//database credentials
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','password');
define('DBNAME','fpvlog');

try {

	//create PDO connection
	$db = new PDO("mysql:host=".DBHOST.";port=3307;charset=utf8mb4;dbname=".DBNAME, DBUSER, DBPASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Suggested to comment on production websites
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch(PDOException $e) {
	//show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}
?>