<?php
require 'includes/config.php';

$option=trim($_POST['option']);

if($option=='getClassNumberStudents')
{
	$classid= trim($_POST['class']);
	$nostudents = $class->get_class_number_students($classid);
	echo $nostudents;
}