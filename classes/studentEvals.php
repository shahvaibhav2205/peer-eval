<?php

class StudentEvals
{
	private $_db;
	
	function __construct($db){
		$this->_db = $db;
	}
	
	function getFieldResponses($evalId, $filler)
	{
		$query =<<<SQL
            SELECT *
            FROM student_eval
            WHERE eid = :evalId and filler = :filler;
SQL;

		$stmt = $this->_db->prepare($query);
		$stmt->bindParam(":evalId", $evalId, PDO::PARAM_INT);
		$stmt->bindParam(":filler", $filler, PDO::PARAM_INT);
		$output = $stmt->execute();
		
		if (!empty($output)) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		} else {
			return false;
		}
	}
}