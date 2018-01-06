<?php

class PeerEvals{

	private $_db;

    function __construct($db){
    	//parent::__construct();
    	$this->_db = $db;
    }

    public function get_user_evals($fid){
    	try {
			$stmt = $this->_db->prepare('SELECT class.cid, class.cprefix, class.cnumber, class.csection, class.cname, class.fid, class.semester, class.year, eval.eid, eval.title, eval.deadline, eval.weight, eval.startdate FROM class, eval WHERE class.cid = eval.cid and class.fid = :fid order by year DESC, cid DESC');
			$stmt->execute(array('fid' => $fid));

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}

    }

     public function get_peereval_details($eid, $fid){
    	try {
			$stmt = $this->_db->prepare('SELECT * FROM eval, class WHERE eval.cid=class.cid and class.fid=:fid and eval.eid=:eid');
			$stmt->execute(array('eid' => $eid, 'fid' => $fid));

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
    }

    public function check_faculty_peereval($eid, $fid){
    	try {
			$stmt = $this->_db->prepare('SELECT count(*) as isEval FROM eval, class WHERE eval.cid=class.cid and class.fid=:fid and eval.eid=:eid');
			$stmt->execute(array('eid' => $eid, 'fid' => $fid));

			return $stmt->fetchColumn();

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
    }

   public function delete_peer_eval($eid, $fid){
   	try {
			$stmt = $this->_db->prepare('delete eval from eval, class where eval.cid = class.cid and eval.eid=:eid and class.fid=:fid');
			
			if($stmt->execute(array('eid' => $eid, 'fid' => $fid)))
				return "success";
			else
				return "error";

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
   }

 	public function get_students_class_average_score($cid){
    	try {
			$stmt = $this->_db->prepare('SELECT student.sid, firstname, lastname, email, groupid, status, count(DISTINCT filler) as noStudents, AVG(value) as avgScore FROM student LEFT JOIN student_class on student.sid = student_class.sid LEFT JOIN student_eval ON student.sid = student_eval.filledfor where student_class.cid=:cid group by student.sid');
			$stmt->execute(array('cid' => $cid));

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
    }

    public function no_student_filled($eid, $studentid){
    	try {
			$stmt = $this->_db->prepare('SELECT count(*) as nofilled from student_eval, student_class where student_eval.filler = student_class.sid and student_class.status=1 and eid=:eid and filler=:studentid');
			$stmt->execute(array('eid' => $eid, 'studentid' => $studentid));
			//print_r($stmt->fetchColumn());
			//die();
			return $stmt->fetchColumn();

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
    }

    public function get_students_avg_points($eid, $studentid){

			$stmt = $this->_db->prepare('SELECT fields.weight as fweight, fields.field_id, fields.max_score, eval.weight as evalweight from fields INNER JOIN templates ON fields.tid =templates.tid INNER JOIN eval on eval.tid= templates.tid and fields.active=1 and eval.eid=:eid');
			$stmt->execute(array('eid' => $eid));
			
			$allfields = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$evalweight = $allfields[0]['evalweight'];
			$maxpoints = $allfields[0]['max_score'];

			
			$avgPoints = 0;
			foreach($allfields as $fields){

				$fweight = $fields['fweight'];
				$fieldid = $fields['field_id'];

				

				$stmt = $this->_db->prepare('SELECT value from student_eval where student_eval.eid=:eid and fid=:fid and filledfor=:studentid');
				$stmt->execute(array('eid' => $eid, 'studentid' => $studentid, 'fid'=>$fieldid));

				$fieldvalue = $stmt->fetchColumn();
				

				$avgPoints+= ($fieldvalue*$fweight)/100;

		
			}
			$avgPoints = ($avgPoints*$evalweight)/$maxpoints;

			return $avgPoints;

    }

}