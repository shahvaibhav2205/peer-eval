<?php
require 'includes/config.php';

if(isset($_SESSION['faculty'])) { 
		$fid=trim($_SESSION['faculty']);
	}
	else{
		header("Location:login.php");
	}

$option=trim($_POST['option']);

if($option=='getTemplatFieldNumbers')
{
	$templateid= trim($_POST['template']);
	$fields = $template->get_template_details($templateid, $fid);
	$noFields = count($fields);
	if($noFields>0){
	$message="The template has ".$noFields." fields'. ";
	foreach($fields as $field){
		$message.=$field['caption']." (".$field['weight']."%), ";
	}
	$message = substr($message, 0, -2);
	echo $message;
	}
	else{
		echo "There are no fields in this template. <a href='manage-templates.php?tid=".$templateid."''>Add Fields</a>";
	}
}