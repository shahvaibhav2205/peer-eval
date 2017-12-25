<?php

class Students
{

	private $_db;

    function __construct(PDO $db){
    	//parent::__construct();
    	$this->_db = $db;
    }

    function get_all_records()
    {
        $stmt = $this->_db->prepare('SELECT * FROM student, student_class where student.sid = student_class.sid and student_class.cid=1');
        $stmt->execute();

                //return $stmt->fetch();

        if ($stmt->fetchColumn() > 0) {
            echo "<div class='table-responsive'><table id='myTable' class='table'>
                  <thead><tr>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Email</th>
                      <th>Group</th>
                  </tr></thead><tbody>";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $studentid = $row["sid"];
                ?><tr>
                    <td contenteditable="true" data-old_value="<?php echo $row["firstname"]; ?>" onBlur="saveInlineEdit(this,'firstname','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['firstname'] ?></td>
                    <td contenteditable="true" data-old_value="<?php echo $row["lastname"]; ?>" onBlur="saveInlineEdit(this,'lastname','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['lastname'] ?></td>
                    <td contenteditable="true" data-old_value="<?php echo $row["email"]; ?>" onBlur="saveInlineEdit(this,'email','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['email'] ?></td>
                    <td contenteditable="true" data-old_value="<?php echo $row["groupid"]; ?>" onBlur="saveInlineEdit(this,'groupid','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['groupid'] ?></td>
                </tr>
                <?php
            }

            echo "</tbody></table></div>";

        } else {
            echo "you have no records";
        }
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