<?php
require_once('includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

if(isset($_SESSION['faculty']))
  $fid = trim($_SESSION['faculty']);

$peerevalid = trim($_GET['peereval']);

$peerdetails = $peereval->get_peereval_details($peerevalid, $fid);

$classid = $peerdetails[0]['cid'];
$students = $peereval->get_students_class_average_score($classid);


$title = "Peer Evaluation Report";
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
        <h5><?php echo $peerdetails[0]['semester']." ".$peerdetails[0]['year'] ?></h5>
        <h5> <?php echo $peerdetails[0]['cprefix']." ".$peerdetails[0]['cnumber']."-".$peerdetails[0]['csection'].": ".$peerdetails[0]['cname'] ?></h5>
        <br><br>
        <h6><?php echo $peerdetails[0]['title'] ?></h6>
        <p> The students in red have not finalized their evaluation yet. The deadline for this evaluation is <?php echo date("m/d/Y", strtotime($peerdetails[0]['deadline'])); ?>.</p>
        <table class="table report table-responsive">
          <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Group</th>
            <th>No Evals</th>
            <th>Score</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach($students as $student){
          
            $status = $student['status'];
          
            $studentid = $student['sid'];

            $studentfilled = $peereval->no_student_filled($peerevalid, $studentid);
            $avgPoints = $peereval->get_students_avg_points($peerevalid, $studentid);
            if(empty($avgPoints))
              $avgPoints=0;
            
            $color = "";
            if($studentfilled==0)
              $color = 'red';

            ?>
            <tr style="color: <?php echo $color ?>">
              <td><?php echo $student['firstname']." ".$student['lastname']; ?></td>
              <td><?php echo $student['email']; ?></td>
              <td><?php echo $student['groupid'] ?></td>
              <td><?php echo $student['noStudents'] ?></td>
              <td><?php echo $avgPoints ?></td>
              <td><a href="report-details.php?peereval=<?php echo $peerevalid ?>&student=<?php echo $studentid ?>">Details</a></td>
            </tr>
            <?php
          }
          ?>
        </tbody>
      </table>
      </div>
    </div>
</body>

<?php
//include footer template
require('layout/footer.php');
?>
</html>