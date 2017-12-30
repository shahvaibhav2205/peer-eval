<?php require('includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

$fid = trim($_SESSION['faculty']);

//define page title
$title = 'Your Evals';

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
                			<a href="manage-peerevals.php" class="btn btn-primary btn-sm float-right">Add Peer Eval</a>
                		</div>
                	</div>
                	<br>
                	<table class="table table-responsive">
                		<tr>
                            <th> Semester</th>
                			<th> Class </th>
                            <th> Title</th>
                			<th> Deadline </th>
                            <th> Weight </th>
                            <th> Edit </th>
                            <th> Report </th>
                            <th> Status </th>
                		</tr>
                	<?php
                		$fpeerevals = $peereval->get_user_evals($fid);
                		//print_r($ftemplates);
                		foreach($fpeerevals as $fpeereval)
                		{
                			
                			?>
                			<tr>
                                <td><?php echo $fpeereval['semester']." ".$fpeereval['year']; ?></td>
                				<td><?php echo $fpeereval['cprefix']." ".$fpeereval['cnumber']."<br>".$fpeereval['cname']; ?></td>
                                <td><?php echo $fpeereval['title']; ?></td>
                                <td><?php echo date('m/d/Y', strtotime($fpeereval['deadline'])); ?></td>
                                <td><?php echo $fpeereval['weight']."%"; ?></td>
                				<td>
                                    <?php  if(time() < strtotime($fpeereval['deadline'])) { 
                                        if($fpeereval['startdate']==NULL) { ?>
                                            <a href="manage-peerevals.php?peereval=<?php echo $fpeereval['eid'] ?>" class="btn btn-link">Edit</a>
                                        <?php } else { echo "Started and not editable"; }
                                    } else {
                                        echo "Completed and not editable";
                                    } ?>

                                </td>
                                <td><a href="peereval-report.php?peereval=<?php echo $fpeereval['eid'] ?>" class="btn btn-link">Report</a></td>
                                <td>
                                    <?php  if(time() < strtotime($fpeereval['deadline'])) { 
                                        if($fpeereval['startdate']==NULL) { ?>
                                            <a href="#" data-class="<?php echo $fpeereval['cid'] ?>" data-peer="<?php echo $fpeereval['eid'] ?>" class="btn btn-success btn-sm start-eval">Start</a>
                                    <?php } else { echo "Started"; }
                                    } else {
                                        echo "Completed";
                                    } ?>
                                </td>
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
                 

                 $('.start-eval').on('click', function (e) {
                    var cid = $(this).attr('data-class');
                    var eid = $(this).attr('data-peer');

                    e.preventDefault();
                    bootbox.confirm({
                    message: "Are you sure you want to start the peer evalutaion? You will not be able to make any changes after it has been started",
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
                                var dialog = bootbox.dialog({
                                    message: '<p>Starting the evaluations and sending emails to student. Please wait...</p>',
                                });
                                 $.ajax({
                                    url: "peereval-controller.php",
                                    type: "POST",
                                    data:'option=startPeerEval&class='+cid+'&peereval='+eid,
                                    success: function(response) {
                                       // alert(response);
                                        dialog.modal('hide');

                                        if(response=='success')
                                            bootbox.alert("The peer eval has been started", function(){  window.location.reload(); });
                                        else
                                            bootbox.alert("There was an error while starting the peer eval. Please try again later", function(){  window.location.reload(); });

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

