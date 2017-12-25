<?php require('includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

if(isset($_POST['option']) && $_POST['option']=='deleteTemplate'){
 $templateid = $_POST['template'];
 $result= $template->delete_template($templateid);
 echo $result;
}

$fid = trim($_SESSION['faculty']);
//define page title
$title = 'Your Templates';

//include header template
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
                		<div class="col-sm-12">
                			
                		
                			<a href="manage-templates.php" class="btn btn-primary btn-sm float-right">Add Template</a>
                		</div>
                	</div>
                	<br>
                    <?php
                    $ftemplates = $template->get_user_templates($fid);
                    if(count($ftemplates)>0){
                    ?>
                	<table class="table table-responsive">
                		<tr>
                			<th> Template Name</th>
                			<th> Action </th>
                		</tr>
                	<?php
                		
                		foreach($ftemplates as $ftemplate)
                		{
                			
                			?>
                			<tr>
                				<td><?php echo $ftemplate['tname']; ?></td>
                				<td><a href="manage-templates.php?tid=<?php echo $ftemplate['tid'] ?>" class="btn btn-link">Edit</a> 
                                <a href="#" class="btn btn-link delete-template" data-attr="<?php echo $ftemplate['tid'] ?>">Delete</a></td>
                			</tr>
                			<?php
                		}
                	?>
					</table>
                    <?php
                }
                else{
                    echo "<p class='lead'> You don't have any templates</p>";
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
             $(document).ready(function () {
                 

                 $('.delete-template').on('click', function (e) {
                    var tid = $(this).attr('data-attr');
                    //console.log(tid);
                    e.preventDefault();
                    bootbox.confirm({
                    message: "Are you sure you want to delete the template?",
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
                            if(result){  
                                 $.ajax({
                                    url: "templates.php",
                                    type: "POST",
                                    data:'option=deleteTemplate&template='+tid,
                                    success: function(response) {
                                        window.location.reload();
                                    },
                                    error: function () {
                                    console.log("errr");
                                    }
                                });
                            }
                        }
                    });
                 });
             });


</script>
