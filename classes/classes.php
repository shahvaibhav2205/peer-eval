<?php

class Classes{

	private $_db;

    function __construct($db){
    	//parent::__construct();
    	$this->_db = $db;
    }

    public function get_user_classes($fid){
    	try {
			$stmt = $this->_db->prepare('SELECT * FROM class WHERE fid = :fid order by year DESC, cid DESC');
			$stmt->execute(array('fid' => $fid));

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}

    }

    public function get_class_details($cid, $fid){
    	try {
			$stmt = $this->_db->prepare('SELECT * FROM class WHERE cid=:cid and fid = :fid');
			$stmt->execute(array('cid' => $cid, 'fid' => $fid));

			return $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
    }

 

 

}