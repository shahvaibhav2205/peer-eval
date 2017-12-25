<?php require('includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

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
                	<table class="table table-responsive">
                		<tr>
                			<th> Template Name</th>
                			<th> Action </th>
                		</tr>
                	<?php
                		$ftemplates = $template->get_user_templates($fid);
                		foreach($ftemplates as $ftemplate)
                		{
                			
                			?>
                			<tr>
                				<td><?php echo $ftemplate['tname']; ?></td>
                				<td><a href="manage-templates.php?tid=<?php echo $ftemplate['tid'] ?>" class="btn btn-success btn-sm">Edit</a></td>
                			</tr>
                			<?php
                		}
                	?>
					</table>
                </div>
            </div>
        </div>

<?php 
//include header template
require('layout/footer.php'); 
?>
 <script type="text/javascript">
             $(document).ready(function () {
                 $('#sidebarCollapse').on('click', function () {
                     $('#sidebar').toggleClass('active');
                     $(this).toggleClass('active');
                 });
             });
</script>
