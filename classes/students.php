<?php

class Students{

	private $_db;

    function __construct($db){
    	//parent::__construct();
    	$this->_db = $db;
    }

	function get_all_class_students($cid){
    	$stmt = $this->_db->prepare('SELECT * FROM student, student_class where student.sid = student_class.sid and student_class.cid=:cid');
    	$stmt->execute(array('cid' => $cid));
    	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 	function delete_student($sid, $cid){
 		$stmt = $this->_db->prepare('delete FROM student_class where sid=:sid and cid=:cid');
     	$stmt->execute(array('sid' => $sid, 'cid' => $cid));
     	echo "success";	 
 	}

 	function get_all_students(){
 		 $stmt = $this->_db->prepare('SELECT sid, email FROM student');
    	 $stmt->execute();
     	 return $stmt->fetchAll(PDO::FETCH_ASSOC);
 	}

}
?>