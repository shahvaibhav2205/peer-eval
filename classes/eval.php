<?php

Class Evaluations{

    private $_db;

    function __construct($db){
        $this->_db = $db;
    }

    function getClass($evalId)
    {
        $query =<<<SQL
            SELECT eval.cid
            FROM eval
            WHERE eval.eid = :eid;
SQL;
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(":eid", $evalId, PDO::PARAM_INT);
        $output = $stmt->execute();

        if (!empty($output)) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['cid'];
        } else {
            return false;
        }

    }

    function getEvalRow($evalId)
    {
        $query =<<<SQL
            SELECT eval.*
            FROM eval
            WHERE eval.eid = :eid;
SQL;
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(":eid", $evalId, PDO::PARAM_INT);
        $output = $stmt->execute();

        if (!empty($output)) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        } else {
            return false;
        }

    }
}