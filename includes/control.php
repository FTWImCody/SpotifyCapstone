<?php //switch statement that will open each webpage from the Ajax function in Index.php
    $page = $_REQUEST['page'] ?? "null";
	switch($page){
		case 'Home':
			include "Home.php";
			break;
		case 'myAccount':
			include 'myAccount.php';
			break;
		case 'signUp':
			include 'signUp.php';
			break;
		case 'signIn':
			include 'signIn.php';
			break;
		case 'ChangePass':
			include 'changePass.php';
			break;
		case 'PasswordReset':
			include 'Forgot.php';
			break;
		case 'SecurityQ':
			include 'SecurityQ.php';
			break;
		case 'Logout':
			@session_start();
			@session_destroy();
			echo '<script>swal({
                title: "Success",
                text: "Successfully logged out!",
                icon: "error"
                }).then(function() {
                window.location.replace("index.php")});</script>';
			break;
		case 'RandomGen':
			include 'randomizer.php';
			break;
		break;
		default:
			echo "Webpage doesn't exist!";
	};
	?>