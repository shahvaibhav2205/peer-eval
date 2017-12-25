<?php
//include config
require_once('includes/config.php');

//check if already logged in move to home page
if( $user->is_logged_in() ){ header('Location: memberpage.php'); exit(); }


//process login form if submitted
if(isset($_POST['submit'])){

	if (!isset($_POST['email'])) $errors[] = "Please fill out all fields";
	if (!isset($_POST['password'])) $errors[] = "Please fill out all fields";

	$email = trim($_POST['email']);
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
	    $errors[] = 'Please enter a valid email address';
	} else {
	//if ( $user->isValidUsername($username)){
		if (!isset($_POST['password'])){
			$errors[] = 'A password must be entered';
		}
		$password = $_POST['password'];

		if($user->login($email,$password)){
			$_SESSION['email'] = $email;
			$fid = $user->get_user_id($email);
			$faculty_id = $fid['fid'];
			$_SESSION['faculty'] = $faculty_id;
			header('Location: memberpage.php');
			exit;

		} else {
			$errors[] = 'Wrong username or password or your account has not been activated.';
		}
	} /*else{
		$errors[] = 'Usernames are required to be Alphanumeric, and between 3-16 characters long';
	}*/


//end if submit
} else if (!empty($_GET['error-code'])) { // redirected to login page with error
    $errorCode = base64_decode($_GET['error-code']);
    switch ($errorCode) {
        case 100 : { // case when randomKey is invalid
            $errors[] = "Invalid URL.";
            break;
        }
        case 102 : { // case when class doesn't have that student
            $errors[] = "Sorry, you are not assigned to class.";
            break;
        }
        case 103 : { // ...
            $errors[] = "...";
            break;
        }
        default : {
            $errors[] = "There was a problem, please contact support.";
        }
    }
}

//define page title
$title = 'Login to your account';

//include header template
require('layout/header.php'); 
?>

	
<div class="container">

	<div class="row">

	    <div class="col-xs-12 col-sm-8 col-md-6 mx-auto">
			<form role="form" method="post" action="" autocomplete="off">
				<h2>Please Login</h2>
				<p class="lead"> Enter your email address and password to login</p>
				
				<hr>

				<?php
				//check for any errors
				if(isset($errors)){
					foreach($errors as $error){
						echo '<p class="bg-danger error">'.$error.'</p>';
					}
				}

				if(isset($_GET['action'])){

					//check the action
					switch ($_GET['action']) {
						case 'active':
							echo "<p class='lead bg-success'>Your account is now active you may now log in.</p>";
							break;
						case 'reset':
							echo "<p class='lead bg-success'>Please check your inbox for a reset link.</p>";
							break;
						case 'resetAccount':
							echo "<p class='lead bg-success'>Password changed, you may now login.</p>";
							break;
					}

				}

				
				?>

				<div class="form-group">
					<input type="text" name="email" id="email" class="form-control input-lg" placeholder="Email Address" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['email'], ENT_QUOTES); } ?>" tabindex="1">
				</div>

				<div class="form-group">
					<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="3">
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						 <a href='reset.php'>Forgot your Password?</a>
					</div>
				</div>
				
				<hr>
				<div class="row">
					<div class="col-xs-12 col-md-12"><input type="submit" name="submit" value="Login" class="btn btn-primary btn-block btn-lg form-control" tabindex="5"></div>
				</div>
			</form>

			<p class="text-left"><a href='./'><< Back to home page</a></p>
		</div>
	</div>



</div>


<?php 
//include header template
require('layout/footer.php'); 
?>
