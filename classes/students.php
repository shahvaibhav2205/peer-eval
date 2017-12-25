<?php

class Students
{

	private $_db;

    function __construct(PDO $db){
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

    public function getStudent($sId)
    {
        $query =<<<SQL
        SELECT * 
        FROM student
        WHERE sid = :sId
SQL;
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(":sId", $sId, PDO::PARAM_STR);
        $output = $stmt->execute();

        if (!empty($output)) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function getStudentClass($randomKey)
    {
        $query =<<<SQL
        SELECT s.sid, sc.cid, sc.status as evalstatus, s.firstname, s.lastname, s.email, s.isactive
        FROM student_class as sc
        LEFT JOIN student as s ON sc.sid = s.sid
        WHERE sc.randomkey = :randomKey;
SQL;
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(":randomKey", $randomKey, PDO::PARAM_STR);
        $output = $stmt->execute();

        if (!empty($output)) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

}
?>