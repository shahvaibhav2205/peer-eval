<?php require('includes/config.php');

//if logged in redirect to members page
if( $user->is_logged_in() ){ header('Location: memberpage.php'); exit(); }


//if form has been submitted process it
if ($_SESSION['userType'] === "student") {
	$userType = $_SESSION['userType'];
} else {
	$userType = "faculty";
}
$messages = [];
$errors = [];
if(isset($_POST['submit'])){
	
	if (trim($_POST['firstname'])=="") $errors[] = "Please enter firstname";
	if (trim($_POST['lastname'])=="") $errors[] = "Please enter lastname";
	if (trim($_POST['email'])=="") $errors[] = "Please enter email";
	if (trim($_POST['password'])=="") $errors[] = "Please enter password";

	
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = htmlspecialchars_decode($_POST['email'], ENT_QUOTES);

	//very basic validation
	/*if(!$user->isValidUsername($username)){
	 $errors[] = 'Usernames must be at least 3 Alphanumeric characters';
	} else {
		$stmt = $db->prepare('SELECT email FROM faculty WHERE email = :email');
		$stmt->execute(array(':username' => $username));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row['username'])){
			$errors[] = 'Username provided is already in use.';
		}

	}*/
	if(strlen($_POST['password']) < 3){
		$errors[] = 'Password is too short.';
	}

	if(strlen($_POST['passwordConfirm']) < 3){
		$errors[] = 'Confirm password is too short.';
	}

	if($_POST['password'] != $_POST['passwordConfirm']){
		$errors[] = 'Passwords do not match.';
	}

	//email validation for student with same email as found with randomKey

	if(!empty($_SESSION['userDetails']['email']) && $email != $_SESSION['userDetails']['email']){
		$errors[] = 'You can only register with the email you are invited with!';
	}

	//email validation
	
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$errors[] = 'Please enter a valid email address';
	} else {
		$query = "SELECT email FROM $userType WHERE email = :email";

		if ($userType === "student") {
			$query .= " and isactive = 1";
		}
		$stmt = $db->prepare($query);

		$stmt->execute(array(':email' => $email));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row['email'])){
			$errors[] = 'Email provided is already in use.';
		}

	}

	if (!empty($errors)) {
		$messages = $errors;
		$messagesType = "error";
	}

	$userDetails = $_POST;

	//if no errors have been created carry on
	if(empty($errors)){
		//hash the password
		$hashedpassword = $user->password_hash($_POST['password'], PASSWORD_BCRYPT);

		//create the activasion code
		$activasion = md5(uniqid(rand(),true));

		try {

			if ($userType === "student") {
				//insert into database with a prepared statement for student

				$query = "UPDATE $userType SET firstname = :firstname, lastname = :lastname, password = :password, isactive = 1 WHERE email = :email";

				$stmt = $db->prepare($query);
				$output = $stmt->execute(array(
					':firstname' => $firstname,
					':lastname' => $lastname,
					':password' => $hashedpassword,
					':email' => $email
				));

				if ($output) {
					$action = "studentJoined";
				}

			} else {
				//insert into database with a prepared statement for faculty
				$stmt = $db->prepare("INSERT INTO $userType (firstname, lastname, password, email, active) VALUES (:firstname, :lastname, :password, :email, :active)");
				$stmt->execute(array(
					':firstname' => $firstname,
					':lastname' => $lastname,
					':password' => $hashedpassword,
					':email' => $email,
					':active' => $activasion
				));
				$id = $db->lastInsertId('fid');

				//send email
				$to = $_POST['email'];
				$subject = "Registration Confirmation";
				$body = "<p>Thank you for registering at demo site.</p>
			<p>To activate your account, please click on this link: <a href='" . DIR . "activate.php?x=$id&y=$activasion'>" . DIR . "activate.php?x=$id&y=$activasion</a></p>
			<p>Regards Site Admin</p>";

				$mail = new Mail();
				$mail->setFrom(SITEEMAIL);
				$mail->addAddress($to);
				$mail->subject($subject);
				$mail->body($body);
				//$mail->send();

				//redirect to index page
				$action = "facultyJoined";
			}

		//else catch the exception and show the error.
		} catch(PDOException $e) {
			$errors[] = $e->getMessage();
		}

	}

} else if (!empty($_SESSION['messages'])) { //get notice and student user details
	$messages = $_SESSION['messages'];
	$messagesType = $_SESSION['messageType'];
	$userDetails = $_SESSION['userDetails'];
}

//define page title
$title = 'Register your account';

//include header template
require('layout/header.php');
?>


<div class="container">

	<div class="row">

		<div class="col-xs-12 col-sm-8 col-md-6 mx-auto">
			<form role="form" method="post" action="" autocomplete="off">
				<h2> Sign Up</h2>
				<p class="lead"> Enter the following details to sign up</p>
				<hr>

				<?php
				//check for any errors

				if (isset($messagesType)){
					$messageClass = ($messagesType === "error") ? "bg-danger" : "bg-success";
					foreach($messages as $message){
						echo '<p class="'.$messageClass.' info">'.$message.'</p>';
					}
				}

				//if action is joined show sucess
				if (isset($action) && $action == 'facultyJoined') {
					echo "<p class='info bg-success'>Registration successful, please check your email to activate your account.</p>";
				} else if (isset($action) && $action == 'studentJoined') {
					echo "<p class='info bg-success'>Registration successful, please Login to continue.</p>";
				}
				?>
				<div class="form-group">
					<input type="text" name="firstname" id="firstname" class="form-control input-lg" placeholder="First Name" value="<?php if(isset($messagesType)){ echo htmlspecialchars($userDetails['firstname'], ENT_QUOTES); } ?>" tabindex="1">
				</div>
				<div class="form-group">
					<input type="text" name="lastname" id="lastname" class="form-control input-lg" placeholder="Last Name" value="<?php if(isset($messagesType)){ echo htmlspecialchars($userDetails['lastname'], ENT_QUOTES); } ?>" tabindex="1">
				</div>
				<div class="form-group">
					<input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email Address" value="<?php if(isset($messagesType)){ echo htmlspecialchars($userDetails['email'], ENT_QUOTES); } ?>" tabindex="2">
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="3">
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control input-lg" placeholder="Confirm Password" tabindex="4">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-md-12"><input type="submit" name="submit" value="Register" class="btn btn-primary btn-block btn-lg" tabindex="5">
					<br>
					<p>Already a member? <a href='login.php'>Login</a></p>
					</div>

				</div>
			</form>
		</div>
	</div>

</div>

<?php
//include header template
require('layout/footer.php');

// unset student session
if (!empty($action) && $action === "studentJoined") {
    unset($_SESSION['userType']);
    unset($_SESSION['messages']);
    unset($_SESSION['userDetails']);
    unset($_SESSION['messageType']);
}
?>
