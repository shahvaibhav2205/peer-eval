<?php

require('includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

//define page title
$title = 'Complete Evaluation';

if (!empty($_GET['eval'])) {
    $evalId = base64_decode($_GET['eval']);
} else {
    header('Location: pending-evals.php'); exit();
}

$currEval = $eval->getEvalRow($evalId);

if (empty($currEval)) {
    $errStr = "Evaluation not found!";
} else {
    $classId = $currEval['cid'];
    $groupId = $student->getGroup($classId);
    $excludeSelf = true;
    $groupMembers = $student->getGroupMembers($groupId, $classId, $excludeSelf);
    $gMCount = count($groupMembers);
    $questions = $fields->getQuestions($currEval['tid']);
    $questionCount = count($questions);
    $studentSelfId = $_SESSION['sid'];
}

//This array effectively represents rows of student_eval table with columns -> [vid, fid, value, filler, comment] for eid and filledfor is set.
$evalFieldResponses = [];

if (empty($_POST)) { // create the array while loading the page
	$savedEvalResponses = $studentEvals->getFieldResponses($evalId, $studentSelfId);
	
	foreach ($groupMembers as $k=>$groupMember) {
		foreach ($questions as $l=>$question) {
		    if ($savedEvalResponses[($questionCount*$k)+$l]['fid'] == $question['field_id'] && $savedEvalResponses[($questionCount*$k)+$l]['filler'] == $groupMember['sid']) { // since responses will be saved in order, this is formality check
				$evalFieldResponses[] = [$savedEvalResponses[($questionCount*$k)+$l]['vid'], $question['field_id'], $savedEvalResponses[($questionCount*$k)+$l]['value'], $groupMember['sid'], $savedEvalResponses[($questionCount*$k)+$l]['comment']];
            }
		}
	}

} else if (!empty($_POST['saveSubmit']) || !empty($_POST['finalSubmit'])) {
    $fieldValues = $_POST['peerValue'];
    $responseIds = $_POST['vid'];
    
    foreach ($groupMembers as $k=>$groupMember) {
		foreach ($questions as $l=>$question) {
			$evalFieldResponses[] = [intval($responseIds[($questionCount*$k)+$l]), $question['field_id'], intval($fieldValues[($questionCount*$k)+$l]), $groupMember['sid'], ""];
        }
    }
//      var_dump($evalFieldResponses);
//	    exit();
    
    foreach ($evalFieldResponses as $k=>$evalFieldResponse) { // time to insert or update all responses
        if (empty($evalFieldResponse[0]) || $evalFieldResponse[0] == -1 ) { //means it has no student_eval row id, time to insert.
			$stmt = $db->prepare('INSERT INTO student_eval(eid, fid, value, filler, filledfor, comment) VALUES (:eid, :fid, :value, :filler, :filledfor, :comment)');
			$stmt->execute(array('eid' => $evalId, 'fid' => $evalFieldResponse[1], 'value' => $evalFieldResponse[2], 'filler' => $evalFieldResponse[3], 'filledfor' => $studentSelfId, 'comment' => ""));
			$evalFieldResponses[$k][0] = $db->lastInsertId();
        } else {
			$stmt = $db->prepare('UPDATE student_eval SET value = :value,  comment = :comment WHERE vid = :vid');
			$stmt->execute(array('value' => $evalFieldResponse[2], 'comment' => "", 'vid' => $evalFieldResponse[0]));
        }
    }
}

echo "<br>";

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

            <?php

            if (!empty($errStr)) {
                echo "<p class=\"bg-danger\">$errStr</p>";
            }
            ?>

            <form name="evaluations" method="post" action="">
                <div id="allfields">
<!--                    <a href="#" class="btn btn-success btn-sm" id="add_field"><span>&raquo; Add Template Fields</span></a>-->
                    <?php foreach ($groupMembers as $k=>$groupMember) { ?>
                    <div id="peer_evaluations">
                        <p class="lead">Submit your responses for <text id="peer-name"><?=$groupMember['firstname']." ".$groupMember['lastname'] ?></text></p>
                        <?php foreach ($questions as $l=>$question) { ?>
                        <p class="lead question"
                        ><?=($l+1).") ".$question['caption']." ? ".$question['weight']."%" ?></p>
<!--                        <input type="hidden" name="fieldId[]" value="--><?//=$question['field_id'] ?><!--" >-->
                        <input type="hidden" name="vid[]" value="<?=(!empty($evalFieldResponses[($questionCount*$k)+$l][0])) ? $evalFieldResponses[($questionCount*$k)+$l][0] : -1; ?>" >  <!--saving to -1 since empty string is int typecasted to 0-->
                        <input type="number" name="peerValue[]" class="form-control col-sm-12" placeholder="Rate.." value="<?=(!empty($evalFieldResponses[($questionCount*$k)+$l][2])) ? $evalFieldResponses[($questionCount*$k)+$l][2] : ""; ?>">
                        
<!--                        <div class="btn-group mr-2" role="group" aria-label="First group">-->
<!--                            <button type="button" class="btn btn-secondary">1</button>-->
<!--                            <button type="button" class="btn btn-secondary">2</button>-->
<!--                            <button type="button" class="btn btn-secondary">3</button>-->
<!--                            <button type="button" class="btn btn-secondary">4</button>-->
<!--                        </div>-->
                        
					    <?php } ?>
                        <br><br>
                    </div>
                    <?php } ?>
                </div>
                <hr>
                <input id="save" name="saveSubmit" type="submit" value="Save" class="btn btn-primary" />
                <input id="finalize" name="finalSubmit" type="submit" value="Finalize" class="btn btn-primary" />

                <br><br>
            </form>

        </div>
    </div>

<?php
//include header template
require('layout/footer.php');
?>
