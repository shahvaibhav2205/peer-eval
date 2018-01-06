<?php

class Students
{

    private $_db;

    function __construct(PDO $db){
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

    public function getEvaluations($completed = false)
    {
        $studentId = $_SESSION['sid'];

        if (empty($studentId)) {
            echo "Student is not set, please report or re-login.";
            exit();
        }
        $query =<<<SQL
        SELECT class.semester, class.year, class.cprefix, class.cnumber, class.csection, class.cname
        , eval.title, eval.deadline, student_class.status, eval.eid
        FROM student_class
        JOIN class ON student_class.cid = class.cid
        JOIN eval ON class.cid = eval.cid
        WHERE student_class.sid = :studentId and eval.startdate IS NOT null
SQL;
        if ($completed) {
            $query .= " and student_class.status = 1";
        } else {
            $query .= " and student_class.status = 0";
        }

        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(":studentId", $studentId, PDO::PARAM_INT);
        $output = $stmt->execute();

        if (!empty($output)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function getGroup($classId)
    {
        $studentId = $_SESSION['sid'];

        $query =<<<SQL
        SELECT student_class.groupid
        FROM student_class
        WHERE student_class.sid = :studentId and student_class.cid = :classId;
SQL;

        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(":studentId", $studentId, PDO::PARAM_INT);
        $stmt->bindParam(":classId", $classId, PDO::PARAM_INT);

        $output = $stmt->execute();

        if (!empty($output)) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['groupid'];
        } else {
            return false;
        }
    }

    public function getGroupMembers($groupId, $classId, $excludeSelf)
    {

        $query =<<<SQL
        select student.*
        from student
        join student_class as sc on sc.sid = student.sid
        where sc.groupid = :groupId and sc.cid = :classId
SQL;
        if ($excludeSelf) { //logic to evaluate yourself with peers.
            $query .= " and sc.sid != :currStudent";
        }
        $stmt = $this->_db->prepare($query);
        
        if ($excludeSelf) { //logic to evaluate yourself with peers.
            $stmt->bindParam(":currStudent", $_SESSION['sid'], PDO::PARAM_INT);
        }
  
        $stmt->bindParam(":groupId", $groupId, PDO::PARAM_INT);
        $stmt->bindParam(":classId", $classId, PDO::PARAM_INT);
        

        $output = $stmt->execute();

        if (!empty($output)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

}
?>