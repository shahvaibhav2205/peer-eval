<?php

class Fields
{
    private $_db;

    function __construct($db){
        $this->_db = $db;
    }

    function getQuestions($templateId)
    {
        $query =<<<SQL
            SELECT *
            FROM fields
            WHERE tid = :templateid;
SQL;
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(":templateid", $templateId, PDO::PARAM_INT);
        $output = $stmt->execute();

        if (!empty($output)) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        } else {
            return false;
        }
    }
	
}