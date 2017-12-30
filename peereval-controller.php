<?php
require 'includes/config.php';

$option=trim($_POST['option']);

if(isset($_SESSION['faculty']))
	$fid = trim($_SESSION['faculty']);

if($option=='deletePeereval')
{
	$peerevalid= trim($_POST['peereval']);
	$result = $peereval->delete_peer_eval($peerevalid, $fid);
	echo $result;
}
if($option='startPeerEval'){
	$cid= trim($_POST['class']);
	$eid= trim($_POST['peereval']);

	$students = $class->get_students_in_class($cid);

	$peerevals = $peereval->get_peereval_details($eid, $fid);

	$error = "";

	$faculty = $user->get_faculty_details($fid);

	$course = $peerevals[0]['cprefix']." ".$peerevals[0]['cnumber'];
	$semester = $peerevals[0]['semester']." ".$peerevals[0]['year'];

	$title = $peerevals[0]['title'];
	$deadline = $peerevals[0]['deadline'];

	$facultyemail = $faculty['email'];
	$facultyname = $faculty['firstname']." ".$faculty['lastname'];

	foreach($students as $student){
		$name = $student['firstname']." ".$student['lastname'];
		$email = $student['email'];

		$randomkey = $student['randomkey'];

		$subject = "Complete Peer Evaluation for ".$course."-".$semester;


		$message = "<p> The peer evaluations for the course ".$course." has been started. Please visit the link below to complete your evaluation. The deadline is ".date("m/d/Y", strtotime($deadline)).".</p>";
		$message.= "<p> <a href='" . DIR . "eval.php?random='".$randomkey.">" . DIR . "activate.php</a></p>";

		$mail = new Mail();
		$mail->setFrom($facultyemail);
		$mail->addAddress('vivekarora86@gmail.com');
		$mail->subject($subject);
		$mail->body($message);
		$mail->send();
		  //if($mail->send()){
			$startdate = date('Y-m-d');
			$stmt = $db->prepare('Update eval set startdate=:startdate where eid=:eid');
			if(!$stmt->execute(array('eid' => $eid, 'startdate' => $startdate)))
				$error = "error";
	//	}
		//else{
		//	echo "error";
		//}
	}
	if($error == "")
		echo "success";
}