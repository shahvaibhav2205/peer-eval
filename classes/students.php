<?php

class Students{

	private $_db;

    function __construct($db){
    	//parent::__construct();
    	$this->_db = $db;
    }

function get_all_records(){


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
         ?><tr><td contenteditable="true" data-old_value="<?php echo $row["first_name"]; ?>" onBlur="saveInlineEdit(this,'first_name','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['first_name'] ?></td>
         	<td contenteditable="true" data-old_value="<?php echo $row["last_name"]; ?>" onBlur="saveInlineEdit(this,'last_name','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['last_name'] ?></td>
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
}
?>