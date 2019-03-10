<?php
require_once('layout/login_header.php');
$_SESSION['loggedin'] = FALSE;
?>
<div class="container">
	<div class="row">
	    <div>
			<form role="form" method="post" action="" autocomplete="off">
				<h1 style="color: #f2f2f2;">Please Login</h1>
				<p  style="color: #f2f2f2;">Not a member? <a href='register.php'>Sign Up</a></p>
				<hr>

				<?php
				//check for any errors
				if(isset($error)){
					foreach($error as $error){
						echo '<p>'.$error.'</p>';
					}
				}

				if(isset($_GET['action'])){

					//check the action
					switch ($_GET['action']) {
						case 'active':
							echo "<h2>Your account is now active you may now log in.</h2>";
							break;
						case 'reset':
							echo "<h2>Please check your inbox for a reset link.</h2>";
							break;
						case 'resetAccount':
							echo "<h2>Password changed, you may now login.</h2>";
							break;
					}

				}

				
				?>

				<div class="form-group">
					<input type="text" name="username" id="username" class="form-control" placeholder="User Name" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['username'], ENT_QUOTES); } ?>" tabindex="1">
				</div>

				<div class="form-group">
					<input type="password" name="password" id="password" class="form-control" placeholder="Password" tabindex="3">
				</div>
				
				<hr>
				<div class="row">
					<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Login" class="btn" style="background-color:#3c753c; border:0px;" tabindex="5"></div>
				</div>
			</form>
		</div>
	</div>
</div>

<?php

if($_POST) {
	effettua_login();
} else {
	mostra_form();
}

function mostra_form()
{
	// mostro un eventuale messaggio
	if(isset($_GET['msg'])) {
		echo '<b>'.htmlentities($_GET['msg']).'</b><br /><br />';
	}	
}

function effettua_login()
{
	require('dbinit.php');
	// recupero il username e la password inseriti dall'utente
	$username      = trim($_POST['username']);
	$password  = trim($_POST['password']);
	// verifico se devo eliminare gli slash inseriti automaticamente da PHP
	if(get_magic_quotes_gpc()) {
		$username      = stripslashes($username);
		$password  = stripslashes($password);
	}

	// verifico la presenza dei campi obbligatori
	if(!$username || !$password) {
		$messaggio = urlencode("Non hai inserito il username o la password");
		header("location: $_SERVER[PHP_SELF]?msg=$messaggio");
		exit;
	}
	// effettuo l'escape dei caratteri speciali per inserirli all'interno della query
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string($password);	
	
	$hashedPsw = MD5($password);
	$stmt = $db->prepare('SELECT memberID,username,password FROM members WHERE username = :username AND password = :password AND active="Yes" ');
	$stmt->execute(array('username' => $username , 'password' => $hashedPsw));
	// controllo l'esito
	if (!$stmt) {
		die("Errore nella query : '$hashedPsw'" . mysql_error());
	}

	$result = $stmt->fetch();

	if(!$result) {
		//$messaggio = urlencode('wrong username or password!!');
		//header("location: $_SERVER[PHP_SELF]?msg=$messaggio");
	} else {
		session_start();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['username'] = $result['username'];
		$_SESSION['memberID'] = $result['memberID'];
		//$messaggio = urlencode('Login avvenuto con successo');
		header("location: fpvlog.php");
		exit();
	}
}
?>