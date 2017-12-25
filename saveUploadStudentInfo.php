<?php
require('includes/config.php');

$stmt = $db->prepare("UPDATE student, student_class set " . trim($_POST["column"]) . " = '".trim($_POST["value"])."' WHERE student.sid=student_class.sid and student.sid=".trim($_POST["id"]));
$stmt->execute()

?>