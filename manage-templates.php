<?php require('includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

$fid = trim($_SESSION['faculty']);

$template_details = array();
$tid=0;
if(isset($_GET['tid'])){
    $tid = trim($_GET['tid']);
}

if(isset($_GET['tid']) && $tid!=0){
    $template_details = $template->get_template_details($tid, $fid);
}
//print_r($template_details);

$template_added=0;

if (isset($_POST['btnSubmit'])) {

   $tname = trim($_POST['tname']);

   if(isset($_GET['tid']) && $tid!=0){
        $stmt = $db->prepare('update templates set tname = :tname where tid = :tid');
        $stmt->execute(array(':tname' => $tname, ':tid' => $tid));
        $inserted_template_id = $tid;
   }
   else{
     $stmt = $db->prepare('INSERT INTO templates (tname, fid) values (:tname, :fid)');
     $stmt->execute(array('tname' => $tname, 'fid' => $fid));
     $inserted_template_id = $db->lastInsertId('tid');
   }

   if(isset($_GET['tid']) && $tid!=0){
    $stmt = $db->prepare('delete from fields where tid=:tid');
    $stmt->execute(array('tid' => $tid));
   }

    //Check if user has actually added additional fields to prevent a php error
    if ($_POST['captions']) {
        
      
        $captions = $_POST['captions'];
        $maxpoints = $_POST['maxpoints'];
        $weights = $_POST['weights'];

        for($i=0; $i<count($_POST['captions']); $i++) {
                                
            $caption = mysql_real_escape_string($captions[$i]);
            $maxpoint = mysql_real_escape_string($maxpoints[$i]);
            $weight = mysql_real_escape_string($weights[$i]);

            $stmt = $db->prepare('INSERT INTO fields (caption, weight, max_score, tid) values (:caption, :weight, :maxpoint, :inserted_template_id)');
            $stmt->execute(array('caption' => $caption, 'weight' => $weight, 'maxpoint' => $maxpoint, 'inserted_template_id' => $inserted_template_id));
          
            //$sql_fields= "INSERT INTO fields (caption, weight, max_score, tid) VALUES ('$caption', $weight, $maxpoint, $inserted_template_id)";  

           // $result_fields = $db->query($sql_fields);
            $template_added=1;
            
        }
        
    } else {
    
        //No additional fields added by user
        
    }
   
}
//define page title
$title = 'Manage Templates';

//include header template
require('layout/header.php'); 
?>
    <link rel="stylesheet" href="style/sidebar.css">
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

                    <?php if (!isset($_POST['btnSubmit'])) { ?>
   
                    <form name="test" method="post" action="">
                        <?php
                        $template_name= "";
                        if(isset($_GET['tid']) && $tid!=0){
                            $template_name=$template_details[0]['tname'];
                        }
                        ?>
                        <input type="text" name="tname" id="tname" class="form-control" placeholder="Enter Template Name" value="<?php echo $template_name ?>" />
                        <br><br>
                        <div id="allfields">
                        <a href="#" class="btn btn-success btn-sm" id="add_field"><span>&raquo; Add Template Fields</span></a><br><br>
                        <?php
                        if(isset($template_details)){
                            //echo count($template_details);
                            $i=0;
                            foreach($template_details as $template_det) {
                            $i++;
                            ?>
                            <p class="lead">Field # <?php echo $i ?></p>
                            <div class="row fields">
                                <div class="col-sm-12">
                                    <input id="caption_<?php echo $i ?>" name="captions[]" type="text" class="form-control" placeholder="Enter caption" value="<?php echo $template_det['caption'] ?>" />
                                </div>
                                <div class="col-sm-6">
                                    <input id="maxpoint_<?php echo $i ?>" name="maxpoints[]" type="number" class="form-control col-sm-12" placeholder="Enter max points allowed" value="<?php echo $template_det['max_score'] ?>" />
                                </div>
                                <div class="col-sm-6">
                                    <input id="weight_<?php echo $i ?>" name="weights[]" type="number"  class="form-control col-sm-12" placeholder="Enter weight of this field" value="<?php echo $template_det['weight'] ?>" />
                                </div>
                            </div>
                        <?php
                        }
                        }   
                        ?>
                        </div>
        
                        <hr>
                        <input id="go" name="btnSubmit" type="submit" value="Submit" class="btn btn-primary" />
                    <br><br>
                    </form>
                    <?php } else {
                         if( $template_added == 1) {
                         echo "<p class='lead'>Template Added, <strong>" . count($_POST['captions']) . "</strong> fields(s) added for this user!</p>";
                         echo "<a href='templates.php'>Back to Templates</a>";
                      }
                      else{

                      }
                    }
                    ?>
          
                    </div>
            </div>
        </div>

<?php 
//include header template
require('layout/footer.php'); 
?>
 <script type="text/javascript">
        
    <?php 
    if(isset($_GET['tid'])) { ?>
    var count = <?php echo count($template_details) ?>;
    <?php } else { ?> var count = 0 <?php } ?>
    //console.log(count);
    $(function(){
    $('a#add_field').click(function(){
        count += 1;
        $('#allfields').append(
                '<p class="lead">Field #' + count + '</p><div class="row fields">' 
                + '<div class="col-sm-12"><input id="caption_' + count + '" name="captions[]' + '" type="text" class="form-control" placeholder="Enter caption" /></div>'
                + '<div class="col-sm-6"><input id="maxpoint_' + count + '" name="maxpoints[]' + '" type="number" class="form-control col-sm-12" placeholder="Enter max points allowed" /></div>'
                + '<div class="col-sm-6"><input id="weight_' + count + '" name="weights[]' + '" type="number"  class="form-control col-sm-12" placeholder="Enter weight of this field" /></div></div>'
        );
    
    });
});
</script>
