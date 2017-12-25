<?php
require_once('includes/config.php');


if(isset($_POST["Import"])){
        
        $filename=$_FILES["file"]["tmp_name"];      


         if($_FILES["file"]["size"] > 0)
         {
            $file = fopen($filename, "r");
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
             {


               $stmt = $db->prepare("INSERT into student (   firstname,lastname,email) 
                   values ('".$getData[0]."','".$getData[1]."','".$getData[2]."')");
                    
                    if($stmt->execute()){
                    
                    $sid = $db->lastInsertId();
                    $stmt2 = $db->prepare("INSERT into student_class (   sid,cid,groupid) 
                   values ('".$sid."','1','".$getData[3]."')");
                
                    if(!$stmt2->execute())
                    {
                    echo "<script type=\"text/javascript\">
                            alert(\"Invalid File:Please Upload CSV File.\");
                            window.location = \"importStudents.php\"
                          </script>";       
                    }
                    else {
                      echo "<script type=\"text/javascript\">
                        alert(\"CSV File has been successfully Imported.\");
                        window.location = \"importStudents.php\"
                    </script>";
                }
             }
         }
            
             fclose($file); 
         }
    }    
?>
<?php
require('layout/header.php');
?>
   
        <div class="container">
            <div class="row">

                <form class="form-horizontal" action="importStudents.php" method="post" name="upload_excel" enctype="multipart/form-data">
                    <fieldset>

                        
                        <div class="form-group">
                           
                            <div class="col-md-12">
                                <input type="file" name="file" id="file" class="input-large">
                            
                                <button type="submit" id="submit" name="Import" class="btn btn-primary button-loading" data-loading-text="Loading...">Import</button>
                            </div>
                        </div>

                    </fieldset>
                </form>

            
            <?php
               $student->get_all_records();
            ?>

            <a class="btn btn-success align-right" href="finalizePeerEvals.php"> Save & Send Emails</a>
        </div>
  </div>
</body>

  <?php
//include header template
require('layout/footer.php');
?>

<script type="text/javascript">
    function highlightEdit(editableObj) {
            $(editableObj).css("background","#f2f2f2");
        } 
function saveInlineEdit(editableObj,column,id) {
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
$(editableObj).css("background","#FDFDFD");
},
error: function () {
console.log("errr");
}
});
}

</script>

</html>