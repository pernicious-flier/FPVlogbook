<?php 
//logout
session_start();
$_SESSION['loggedin'] = FALSE;

//logged in return to index page
header('Location: index.php');
exit();
?>