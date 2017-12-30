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
 

}