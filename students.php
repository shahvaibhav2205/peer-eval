<?php
require_once('includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

$cid=0;
if(isset($_GET['class']))
  $cid = trim($_GET['class']);
else
header('Location: classes.php');

if(isset($_POST['option']) && $_POST['option']=='deleteStudent'){
  $studentId = trim($_POST['student']);
  $cid = trim($_GET['class']);
  $student->delete_student($studentId, $cid);
}

$checkStudents = $student->get_all_students();
$emails = [];
foreach($checkStudents as $checkStudent)
{
  $emails[$checkStudent['sid']] = $checkStudent['email'];
}

$classStudents = $student->get_all_class_students($cid);
//print_r($classStudents);

$sids = [];
foreach($classStudents as $classStudent)
{
  $sids[] = $classStudent['sid'];
}

//print_r($sids);
$title = 'Class Students';

//include header template

if(isset($_POST["Import"])){

  $filename=$_FILES["file"]["tmp_name"];      

  if($_FILES["file"]["size"] > 0)
  {
    $file = fopen($filename, "r");
    $noedu = 0;
    $noadded = 0;

    while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
    {
      $cid = trim($_POST["cid"]);

      $sid = 0;
      $lastThree=substr($getData[2], -3);

      if($lastThree!='edu')
        $noedu++;

      if(!in_array($getData[2], $emails)){

        $stmt = $db->prepare("INSERT into student(first_name, last_name, email) values ('".$getData[0]."','".$getData[1]."','".$getData[2]."')");
        if($stmt->execute()){
          $sid = $db->lastInsertId();
        }

      }
      else {
        $sid = array_search($getData[2], $emails);
      }


      if(!in_array($sid, $sids)) {
        $noadded++;
        $stmt2 = $db->prepare("INSERT into student_class(sid,cid,groupid) values ('".$sid."',$cid,'".$getData[3]."')");
        if(!$stmt2->execute())
        {

          $message = 'ivalid';
        }
        else {

          $message = 'upload';
        }
      }

    }

    if($noadded==0)
      $message = "noadded";


    if($message=='upload')
      header("Location: students.php?class=".$cid."&message=".$message."&noedu=".$noedu."&noadded=".$noadded); 
    else
      header("Location: students.php?class=".$cid."&message=".$message);   

    fclose($file); 
  }
}    
?>
<?php
require('layout/header.php'); 
?>
<div class="wrapper">
  <!-- Sidebar Holder -->
  <?php
  require('layout/sidebar.php'); 
  ?>

  <!-- Page Content Holder -->
  <div id="content">

    <?php
    require('layout/member-header.php'); 
    ?>

    <div class="member-content">
      <div class="row">
        <p> To add students to the class, download the csv example, add values and upload the same file. The header of the csv is important and no other file is allowed.</p>
        <p class="text-danger">All students with only .edu addresses are allowed and will be sent an email invitations. All other email addresses are ignored. </p>

        <form class="form-horizontal" action="students.php?class=<?php echo $cid ?>" method="post" name="upload_excel" enctype="multipart/form-data">
          <fieldset>


            <div class="form-group">

              <div class="col-md-12">
                <input type="file" name="file" id="file" class="input-large">
                <input type="hidden" name="cid" id="cid" value="<?php echo $cid ?>" />

                <button type="submit" id="submit" name="Import" class="btn btn-primary button-loading btn-sm" data-loading-text="Loading...">Import</button>
              </div>
            </div>

          </fieldset>
        </form>


        <?php
        if(isset($_GET['message'])){
         if($_GET['message']=='invalid')
          $error = "There was an error uploading the file. Please try again with the right template";
        elseif($_GET['message']=='upload')
        {
          $noedu = $_GET['noedu']; 
          $noadded = $_GET['noadded']; 
          $error = "The file has been uploaded and the students are added to the class. ".$noadded." students were added to the database. There were ".$noedu." students' with email address that is not an edu address.";
        } 
        elseif($_GET['message']=='noadded'){
          $error = "The file has been uploaded. No new students have been added.";
        }
        elseif($_GET['message']=='deleted'){
         $error = "The student has been deleted.";
       }
       echo '<p class="bg-danger error">'.$error.'</p>';
     }
           //$all_students = $student->get_all_class_students($cid);
     if(count($classStudents)>0) {
      echo "<div class='table-responsive'><table id='myTable' class='table'>
      <thead><tr>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Email</th>
      <th>Group</th>
      <th>Action</th>
      </tr></thead><tbody>";
      foreach ($classStudents as $row) {
        $studentid = $row["sid"];
        ?><tr><td contenteditable="true" data-old_value="<?php echo $row["first_name"]; ?>" onBlur="saveInlineEdit(this,'first_name','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['first_name'] ?></td>
        <td contenteditable="true" data-old_value="<?php echo $row["last_name"]; ?>" onBlur="saveInlineEdit(this,'last_name','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['last_name'] ?></td>
        <td contenteditable="true" data-old_value="<?php echo $row["email"]; ?>" onBlur="saveInlineEdit(this,'email','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['email'] ?></td>
        <td contenteditable="true" data-old_value="<?php echo $row["groupid"]; ?>" onBlur="saveInlineEdit(this,'groupid','<?php echo $studentid ?>')" onClick="highlightEdit(this);"> <?php echo $row['groupid'] ?></td>
        <td><a href="#" class="btn btn-link delete-student" data-attr="<?php echo $row['sid'] ?>">Delete</a>
        </tr>
        <?php       
      }

      echo "</tbody></table></div>";
    }
    else{
      echo "<p class='bg-primary text-white'> No students in this class. Download the csv template, fill data and upload to add students.</p>";
    }
    ?>

  </div>
</div>
</div>
</body>

<?php
//include header template
require('layout/footer.php');
?>

<script type="text/javascript">
$('.delete-student').on('click', function (e) {
  var sid = $(this).attr('data-attr');
                //console.log(tid);
                e.preventDefault();
                bootbox.confirm({
                  message: "Are you sure you want to delete the student?",
                  buttons: {
                    confirm: {
                      label: 'Yes',
                      className: 'btn-success btn-sm'
                    },
                    cancel: {
                      label: 'No',
                      className: 'btn-danger btn-sm'
                    }
                  },
                  callback: function (result) {
                    var cid = '<?php echo $_GET["class"] ?>';
                    if(result){
                           // alert(sid);
                           $.ajax({
                            url: "students.php?class="+cid,
                            type: "POST",
                            data:'option=deleteStudent&student='+sid,
                            success: function(response) {
 // alert(response);
 window.location.href = 'students.php?class='+cid+'&message=deleted';
},
error: function () {
  console.log("errr");
}
});
                         }
                       }
                     });
});
function highlightEdit(editableObj) {
  $(editableObj).css("background","#f2f2f2");
} 
function saveInlineEdit(editableObj,column,id) {

  if(column=='email')
  {
    var emailval = editableObj.innerHTML;
    var lastThree = emailval.substr(emailval.length - 3);
    if(lastThree!='edu'){
      bootbox.alert("Only edu addresses are allowed");
      return false;
    }
  }
// no change change made then return false
if($(editableObj).attr('data-old_value') === editableObj.innerHTML)
  return false;
// send ajax to update value
$(editableObj).css("background","#FFF url(loader.gif) no-repeat right");
$.ajax({
  url: "saveUploadStudentInfo.php",
  type: "POST",
  dataType: "json",
  data:'column='+column+'&value='+editableObj.innerHTML+'&id='+id,
  success: function(response) {
// set updated value as old value
$(editableObj).attr('data-old_value',editableObj.innerHTML);
$(editableObj).css("background","#222");
},
error: function () {
  console.log("errr");
}
});
}

</script>

</html>