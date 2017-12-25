<?php require('includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

$fid = trim($_SESSION['faculty']);

//define page title
$title = 'Your Classes';

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
                			
                		
                			<a href="manage-classes.php" class="btn btn-primary btn-sm float-right">Add Class</a>
                		</div>
                	</div>
                	<br>
                	<table class="table table-responsive">
                		<tr>
                            <th> Semester</th>
                			<th> Class Number</th>
                            <th> Class Name</th>
                			<th> Edit </th>
                            <th> Students </th>
                		</tr>
                	<?php
                		$fclasses = $class->get_user_classes($fid);
                		//print_r($ftemplates);
                		foreach($fclasses as $fclass)
                		{
                			
                			?>
                			<tr>
                                <td><?php echo $fclass['semester']." ".$fclass['year']; ?></td>
                				<td><?php echo $fclass['cprefix']." ".$fclass['cnumber']; ?></td>
                                <td><?php echo $fclass['cname']; ?></td>
                				<td><a href="manage-classes.php?class=<?php echo $fclass['cid'] ?>" class="btn btn-link">Edit</a></td>
                                <td><a href="students.php?class=<?php echo $fclass['cid'] ?>" class="btn btn-link">Students</a></td>
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

