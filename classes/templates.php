<?php

class Templates{

	private $_db;

    function __construct($db){
    	//parent::__construct();
    	$this->_db = $db;
    }

    public function get_user_templates($fid){
    	try {
			$stmt = $this->_db->prepare('SELECT * FROM templates WHERE fid = :fid and active=1');
			$stmt->execute(array('fid' => $fid));

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}

    }

    public function get_template_details($tid, $fid){
    	try {
			$stmt = $this->_db->prepare('SELECT * FROM templates, fields WHERE templates.tid = fields.tid and templates.fid = :fid and templates.tid=:tid');
			$stmt->execute(array('tid' => $tid, 'fid' => $fid));

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}

    }

    public function delete_template($tid){
    	try {
			$stmt = $this->_db->prepare('update templates set active=0 where tid=:tid');
			$stmt->execute(array('tid' => $tid));

			return "success";

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
    }
 

}