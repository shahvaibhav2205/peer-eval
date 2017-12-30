<?php

require('includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

//define page title
$title = 'Your Pending Evaluations';

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
<!--            <div class="row">-->
<!--                <div class="col-sm-12">-->
<!---->
<!---->
<!--                    <a href="manage-classes.php" class="btn btn-primary btn-sm float-right">Add Class</a>-->
<!--                </div>-->
<!--            </div>-->
<!--            <br>-->
            <table class="table table-responsive">
                <tr>
                    <th> Semester </th>
                    <th> Class </th>
                    <th> Evaluation </th>
                    <th> Deadline </th>
                    <th> Status </th>
                </tr>
                <?php
                $evals = $student->getEvaluations();
                foreach($evals as $eval)
                {

                    ?>
                    <tr>
                        <td><?php echo $eval['semester']." ".$eval['year']; ?></td>
                        <td><?php echo $eval['cprefix']." ".$eval['cnumber']."-".$eval['csection']." ".$eval['cname']; ?></td>
                        <td><?php echo $eval['title']; ?></td>
                        <td><?php echo $globalFunctions->getFormatedDateTime($eval['deadline'], 'm/d/Y g:i A'); ?></td>
                        <td><a href="evaluate.php?eval=<?= base64_encode($eval['eid']) ?>" class="btn btn-link">Evaluate</a></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>

<?php
//include header template
require('layout/footer.php');
?>
