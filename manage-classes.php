<?php require('includes/config.php');

//if logged in redirect to members page
if(!$user->is_logged_in() ){ header('Location: login.php'); exit(); }

//if form has been submitted process it

$fid = $_SESSION['faculty'];

if(isset($_GET['class']))
{
	$cid = trim($_GET['class']);
	$cdetails = $class->get_class_details($cid, $fid);
}

if(isset($_POST['submit'])){
	
    if (trim($_POST['csemester'])=="") $errors[] = "Please enter semester";
    if (trim($_POST['cyear'])=="") $errors[] = "Please enter year";
    if (trim($_POST['cprefix'])=="") $errors[] = "Please enter class prefix";
    if (trim($_POST['cnumber'])=="") $errors[] = "Please enter class number";
    if (trim($_POST['csection'])=="") $errors[] = "Please enter class section";
    if (trim($_POST['cname'])=="") $errors[] = "Please enter class name";

    
		$stmt = $db->prepare('SELECT * FROM class WHERE cprefix = :cprefix and cnumber= :cnumber and semester=:semester and year=:year');
		$stmt->execute(array(':cprefix' => $_POST['cprefix'], ':cnumber' => $_POST['cnumber'], ':semester' => $_POST['csemester'], ':year' => $_POST['cyear'] ));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(empty($cid)){
			if(!empty($row['cid'])){
				$errors[] = 'You have already added the same class for the same semester.';
			}
		}
		else{
			if(!empty($row['cid']) && $row['cid']!=$cid){
				$errors[] = 'You have already added the same class for the same semester.';
			}
		}

	//if no errors have been created carry on
	if(!isset($errors)){

		try {
			//insert into database with a prepared statement
			if(isset($_GET['class']))
			{
			
				//die();
				$stmtUpdate = $db->prepare('update class set cprefix=:cprefix, cnumber=:cnumber, csection=:csection, cname=:cname, semester=:semester, year=:year where cid=:cid and fid=:fid');
				$stmtUpdate->execute(array(
				':cprefix' => $_POST['cprefix'],
				':cnumber' => $_POST['cnumber'],
				':csection' => $_POST['csection'],
				':cname' => $_POST['cname'],
				':semester' => $_POST['csemester'],
				':year' => $_POST['cyear'],
				':cid' => $cid,
				':fid' => $fid,
				));

				print_r($stmtUpdate->errorInfo());
				echo "updated";
				//echo $stmt->debugDumpParams();

				header('Location: manage-classes.php?class='.$cid.'&action=updated');
				exit;
			}
			else{
			$stmt = $db->prepare('INSERT INTO class (cprefix, cnumber, csection,cname, fid, semester, year) VALUES (:cprefix, :cnumber, :csection, :cname, :fid, :semester, :year)');
			$stmt->execute(array(
				':cprefix' => $_POST['cprefix'],
				':cnumber' => $_POST['cnumber'],
				':csection' => $_POST['csection'],
				':cname' => $_POST['cname'],
				':fid' => $fid,
				':semester' => $_POST['csemester'],
				':year' => $_POST['cyear']
			));	
				header('Location: manage-classes.php?action=added');
				exit;
			}
			
		
		//else catch the exception and show the error.
		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}

	}

}

//define page title
$title = 'Add Class';

//include header template
require('layout/header.php'); 
?>
<div class="wrapper">
  <!-- Sidebar Holder -->
  <?php
  require('layout/sidebar.php'); 
  ?>

  <!-- Page Content Holder -->
  <div id="content">

    <?php
    require('layout/member-header.php'); 
    ?>

    <div class="member-content">

	<div class="row">

	    <div class="col-sm-12">
			<form role="form" method="post" action="" autocomplete="off">
				<p class="lead"> Enter the following details to enter the class</p>
				<hr>

				<?php
				//check for any errors
				//print_r($error);
				if(isset($errors)){
					foreach($errors as $error){
						echo '<p class="bg-danger error">'.$error.'</p>';
					}
				}

				//if action is joined show sucess
				if(isset($_GET['action']) && $_GET['action'] == 'added'){
					echo "<p class='bg-success'> The class has been added.</p>";
				}
				elseif(isset($_GET['action']) && $_GET['action'] == 'updated'){
					echo "<p class='bg-success'> The class has been updated.</p>";
				}
				?>

				<?php
				$month = date('m');
				$year = date('Y');

				$semester = "";
				if(isset($_POST['csemester']))
				{
					$semester = $_POST['csemester'];
				}
				elseif(isset($_GET['class'])) { $semester = $cdetails[0]['semester']; }
				else {
					if($month>=1 && $month<=5){
					$semester = "Spring";
					}
					elseif($month>5 && $month<=8){
					$semester = "Summer";
					}
					elseif($month>8 && $month<=12){
					$semester = "Fall";
					}
				}
				?>
				<small> Based on the current month, we have auto populated the semester and year, you can change if you like.</small><br><br>
				<div class="form-group">
					<select class="form-control" id="csemester" name="csemester">
						<option value="Spring" <?php if($semester=='Spring') { ?> selected <?php } ?>>Spring</option>
						<option value="Summer" <?php if($semester=='Summer') { ?> selected <?php } ?>>Summer</option>
						<option value="Fall" <?php if($semester=='Fall') { ?> selected <?php } ?>>Fall</option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" name="cyear" id="cyear" class="form-control input-lg" placeholder="Class Year" value="<?php if(isset($_POST['cyear'])) { if(isset($error)){  echo htmlspecialchars($_POST['cyear'], ENT_QUOTES); } } elseif(isset($_GET['class'])) { echo $cdetails[0]['year']; } else { echo $year; } ?>" tabindex="1">
				</div>
				<div class="form-group">
					<input type="text" name="cprefix" id="cprefix" class="form-control input-lg" placeholder="Class Prefix" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['cprefix'], ENT_QUOTES); } elseif(isset($_GET['class'])) { echo $cdetails[0]['cprefix']; } ?>" tabindex="1">
				</div>
				<div class="form-group">
					<input type="text" name="cnumber" id="cnumber" class="form-control input-lg" placeholder="Class Number" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['cnumber'], ENT_QUOTES); } elseif(isset($_GET['class'])) { echo $cdetails[0]['cnumber']; } ?>" tabindex="1">
				</div>
				<div class="form-group">
					<input type="text" name="csection" id="csection" class="form-control input-lg" placeholder="Class Section" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['csection'], ENT_QUOTES); } elseif(isset($_GET['class'])) { echo $cdetails[0]['csection']; } ?>" tabindex="2">
				</div>

				<div class="form-group">
					<input type="text" name="cname" id="cname" class="form-control input-lg" placeholder="Class Name" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['cname'], ENT_QUOTES); } elseif(isset($_GET['class'])) { echo $cdetails[0]['cname']; } ?>" tabindex="2">
				</div>
			
				<div class="row">
					<div class="col-xs-12 col-md-12"><input type="submit" name="submit" value="<?php if(isset($_GET['class'])) { ?> Update Class <?php } else { ?> Add Class <?php } ?>" class="btn btn-primary btn-block" tabindex="5"></div>
				</div>
			</form>
		</div>
	</div>
	</div>
</div>

<?php
//include header template
require('layout/footer.php');
?>
