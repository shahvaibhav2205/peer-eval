<?php require('includes/config.php');

//if logged in redirect to members page
if(!$user->is_logged_in() ){ header('Location: login.php'); exit(); }

//if form has been submitted process it
if(isset($_SESSION['faculty']))
$fid = trim($_SESSION['faculty']);

if(isset($_GET['peereval']))
{
	$eid = trim($_GET['peereval']);
	$peerdetails = $peereval->get_peereval_details($eid, $fid);
	$checkpeereval = $peereval->check_faculty_peereval($eid, $fid);
}



if(isset($_POST['submit'])){
	
    if (trim($_POST['pclass'])=="0") $errors[] = "Please select class for peer evaluation";
    else $classid = $_POST['pclass'];
    if (trim($_POST['ptemplate'])=="0") $errors[] = "Please select a template for the peer evalutation";
    else $templateid = $_POST['ptemplate'];
    if (trim($_POST['ptitle'])=="") $errors[] = "Please enter peer evaluation title";
	else $title = $_POST['ptitle'];
    if (trim($_POST['pinstructions'])=="") $errors[] = "Please enter instructions for students";
	else $instructions = $_POST['pinstructions'];
    if (trim($_POST['pdeadline'])=="") $errors[] = "Please enter the peer evaluation deadline";
    else $deadline = $_POST['pdeadline'];
    if (trim($_POST['pweight'])=="") $errors[] = "Please enter the weight this peer evaluation has for your class";
    else $weight = $_POST['pweight'];


    $noStudents = $class->get_class_number_students($classid);
    if($noStudents==0)
    	$errors[] = "There are no students in the class you selected, please add the students first <a href='students.php?class=".$classid."' style='color:white'>here</a>";

	
	//if no errors have been created carry on
	if(!isset($errors)){

		try {
			//insert into database with a prepared statement
			if(isset($_GET['peereval']) && $checkpeereval>0)
			{
			
				$stmtUpdate = $db->prepare('update eval set title=:title, deadline=:deadline, weight=:weight, tid=:templateid, cid=:classid, instructions=:instructions where eid=:eid');
				$stmtUpdate->execute(array(
				':title' => $title,
				':deadline' => $deadline,
				':weight' => $weight,
				':templateid' => $templateid,
				':classid' => $classid,
				':instructions' => $instructions,
				':eid' => $eid
				));

				header('Location: manage-peerevals.php?peereval='.$eid.'&action=updated');
				exit;
			}
			else{
			
			$stmt = $db->prepare('INSERT INTO eval (title, deadline, weight,tid, cid, instructions) VALUES (:title, :deadline, :weight, :templateid, :classid, :instructions)');
			$stmt->execute(array(
				':title' => $title,
				':deadline' => $deadline,
				':weight' => $weight,
				':classid' => $classid,
				':templateid' => $templateid,
				':instructions' => $instructions
			));	
				header('Location: manage-peerevals.php?action=added');
				exit;
			}
			
		
		//else catch the exception and show the error.
		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}

	}

}

//define page title
$title = 'Add Peer Evaluations';

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
				<p class="lead"> Enter the following details to start the peer evaluation.</p>
				<?php
				if(isset($_GET['peereval']) && $checkpeereval>0){
					if(time() < strtotime($peerdetails[0]['deadline'])){
					if($peerdetails[0]['startdate']==NULL)
						{
							echo "<p class='text-danger'>This peer evaluation has not been started yet. You can edit or delele it at this time.</p>";
						}
						else{
							echo "<p class='text-danger'>This peer evaluation has been started. You cannot make any changes at this time</p>";
						}
					}
					else
						echo "<p class='text-danger'>This peer evaluation has ended and the deadline has passed. You cannot make any changes at this time</p>";

				}
				?>
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
					echo "<p class='bg-success'> The peer evaluations has been added. Please go to the <a href='peerevals.php' style='color:white'>Peer Evals</a> page to start the evaluation</p>";
				}
				elseif(isset($_GET['action']) && $_GET['action'] == 'updated'){
					echo "<p class='bg-success'> The peer evaluation been updated.</p>";
				}
				?>

				<div class="form-group">
					<?php
					$userclasses = $class->get_user_classes($fid);
					if(count($userclasses>0)){
					?>
					<label class="small">Select a class. Don't see your class, <a href="manage-classes.php">Add New Class</a></label>
					<select class="form-control" id="pclass" name="pclass">
						<option value="0"> Select a class</option>
						<?php
							
							foreach($userclasses as $userclass){
								$classval = 0;
								if(isset($_GET['peereval']))
									$classval = $peerdetails[0]['cid'];

								if(isset($error)){  $classval =  htmlspecialchars($_POST['pclass'], ENT_QUOTES); }
								?>
								<option value="<?php echo $userclass['cid'] ?>" <?php if($classval==$userclass['cid']) { ?> selected <?php } ?>><?php echo $userclass['semester']." ".$userclass['year'].": ".$userclass['cprefix']." ".$userclass['cnumber']." - ".$userclass['cname']; ?></option>
								<?php
							}
						?>
					</select>
					<?php
					}
					else{
						?>
						<p> To start a peer evaluation, you need to have a class with students added to it. You don't have any classes on file. <a href="manage-classes.php"> Add New Class</a>
						<?php
					}
					?>
					<p class="message" id="cmessage"></p>
				</div>

				<div class="form-group">
					<?php
					$usertemplates = $template->get_user_templates($fid);
					if(count($usertemplates)>0) {
					?>
					<label class="small">Select a template for the peer evalutation. Dont't want to use any of these templates?, <a href="manage-templates.php">Add New Template</a></label>
					<select class="form-control" id="ptemplate" name="ptemplate">
						<option value="0"> Select a template</option>
						<?php
							
							foreach($usertemplates as $usertemplate){
								$templateval=0;
								
								if(isset($_GET['peereval']))
									$templateval = $peerdetails[0]['tid'];

								if(isset($error)){  $templateval =  htmlspecialchars($_POST['ptemplate'], ENT_QUOTES); }
								?>
								<option value="<?php echo $usertemplate['tid'] ?>" <?php if($templateval==$usertemplate['tid']) { ?> selected <?php } ?>><?php echo $usertemplate['tname']; ?></option>
								<?php
							}
						?>
					</select>
					<?php
					}
					else{
						?>
						<p> To start a peer evaluation, you need to have the templates ready for use. You don't have any templates on file. <a href="manage-templates.php"> Add New Template</a>
						<?php
					}
					?>
					<p class="message" id="tmessage"></p>
				</div>
				
				<div class="form-group">
					<label class="small">Enter Peer Evaluation Title</label>
					<input type="text" name="ptitle" id="ptitle" class="form-control input-lg" placeholder="Title" value="<?php if(isset($error)){  echo htmlspecialchars($_POST['ptitle'], ENT_QUOTES); } elseif(isset($_GET['peereval'])) { echo $peerdetails[0]['title']; } ?>" tabindex="1">
				</div>
				<div class="form-group">
					<label class="small">Enter Instructions for Students</label>
					<textarea name="pinstructions" id="pinstructions" class="form-control" placeholder="Enter instructions for students"><?php $pinstructions =""; if(isset($error)){  $pinstructions =  htmlspecialchars($_POST['pinstructions'], ENT_QUOTES); } elseif(isset($_GET['peereval'])) { $pinstructions =  $peerdetails[0]['instructions']; } echo $pinstructions;?></textarea>
				</div>
				<div class="form-group">
					<label class="small">Enter Peer Evaluation Deadline</label>
					<input type="date" name="pdeadline" id="pdeadline" class="form-control input-lg" placeholder="Peer Eval Deadline" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['pdeadline'], ENT_QUOTES); } elseif(isset($_GET['peereval'])) { echo htmlspecialchars(date('Y-m-d', strtotime($peerdetails[0]['deadline'])), ENT_QUOTES); } ?>" />
				</div>
				<div class="form-group">
					<label class="small">Enter the overall weight in percentage(only number) this Peer Evaluation holds in your class</label>
					<input type="number" name="pweight" id="pweight" class="form-control input-lg" placeholder="Peer Eval Weight for the class" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['pweight'], ENT_QUOTES); } elseif(isset($_GET['peereval'])) { echo $peerdetails[0]['weight']; } ?>" tabindex="1">
				</div>
				<?php
				if(isset($_GET['peereval']) && $checkpeereval>0){
					if(time() < strtotime($peerdetails[0]['deadline'])){
					if($peerdetails[0]['startdate']==NULL)
						{
							?>
							<div class="row">
								<div class="col-xs-12 col-md-12"><input type="submit" name="submit" value="<?php if(isset($_GET['peereval'])) { ?> Update Peer Eval <?php } else { ?> Add Peer Eval <?php } ?>" class="btn btn-primary btn-block" tabindex="5"></div>
								<div class="col-xs-12 col-md-12"><a href="#" class="btn btn-danger btn-block delete-peereval" data-attr="<?php echo $eid ?>"><?php if(isset($_GET['peereval'])) { ?> Delete Peer Eval <?php } ?></a></div>
							</div>
							<?php
						}
						else{
							echo "<p class='text-danger'>This peer evaluation has been started. You cannot make any changes at this time</p>";
						}
					}
					else
						echo "<p class='text-danger'>This peer evaluation has ended and the deadline has passed. You cannot make any changes at this time</p>";

				}
				else{
					?>
					<div class="row">
							<div class="col-xs-12 col-md-12"><input type="submit" name="submit" value="<?php if(isset($_GET['peereval'])) { ?> Update Peer Eval <?php } else { ?> Add Peer Eval <?php } ?>" class="btn btn-primary btn-block" tabindex="5"></div>
					</div>
					<?php
				}
				?>
				
			</form>
		</div>
	</div>
	</div>
</div>

<?php
//include header template
require('layout/footer.php');
?>
<script>
$('#pclass').change(function(){
var pclass = $(this).val();
if(pclass>0){
$.ajax({
  	url: "class-controller.php",
  	type: "POST",
  	data:'option=getClassNumberStudents&class='+pclass,
  	success: function(response) {
  		//alert(response);	
  		if($.trim(response)>0)
			$('#cmessage').html('There are '+response+' students added in the class. <a target="_blank" href="students.php?class='+pclass+'"> List of Students </a>');
		else
			$('#cmessage').html('There are '+response+' students added in the class. <a target="_blank" href="students.php?class='+pclass+'"> Add Students</a>');
	},
	error: function () {
  		console.log("errr");
	}
});
}
else{
	$('#cmessage').html('');
}
});

$('#ptemplate').change(function(){
var ptemplate = $(this).val();
if(ptemplate>0){
$.ajax({
  	url: "template-controller.php",
  	type: "POST",
  	data:'option=getTemplatFieldNumbers&template='+ptemplate,
  	success: function(response) {
  		$('#tmessage').html(response);
	},
	error: function () {
  		console.log("errr");
	}
});
}
else{
	$('#tmessage').html('');
}
});

</script>
<script type="text/javascript">
             $(document).ready(function () {
                 

                 $('.delete-peereval').on('click', function (e) {
                    var eid = $(this).attr('data-attr');
                    //alert(eid);
                    //console.log(tid);
                    e.preventDefault();
                    bootbox.confirm({
                    message: "Are you sure you want to delete the peer evaluation?",
                        buttons: {
                            confirm: {
                            label: 'Yes',
                            className: 'btn-success btn-sm'
                            },
                        cancel: {
                            label: 'No',
                            className: 'btn-danger btn-sm'
                            }
                        },
                        callback: function (result) {
                            if(result){  
                                 $.ajax({
                                    url: "peereval-controller.php",
                                    type: "POST",
                                    data:'option=deletePeereval&peereval='+eid,
                                    success: function(response) {
                                    	//alert(response);
                                    	if(response=='success')
                                    	bootbox.alert("The peer eval has been deleted", function(){  window.location.href='peerevals.php'; });
                           				else
                                    	bootbox.alert("There was an error while deleting the peer eval. Please try again later", function(){  window.location.href='peerevals.php'; });

                                    },
                                    error: function () {
                                    console.log("errr");
                                    }
                                });
                            }
                        }
                    });
                 });
             });


</script>