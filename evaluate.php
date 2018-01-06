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

    $x = 0;
	foreach ($groupMembers as $k=>$groupMember) {
		foreach ($questions as $l=>$question) {
	   		if (!empty($savedEvalResponses)) {
                $evalFieldResponses[] = [$savedEvalResponses[($questionCount*$k)+$l]['vid'], $question['field_id'], $savedEvalResponses[($questionCount*$k)+$l]['value'], $groupMember['sid'], $savedEvalResponses[($questionCount*$k)+$l]['comment']];
            }
		}
	}

} else if (!empty($_POST['saveSubmit']) || !empty($_POST['finalSubmit'])) {
    $fieldValues = $_POST['peerValue'];
    $responseIds = $_POST['vid'];

    foreach ($groupMembers as $k=>$groupMember) {
		foreach ($questions as $l=>$question) {
			$evalFieldResponses[] = [intval($responseIds[($questionCount*$k)+$l]), $question['field_id'], (!empty($fieldValues[$k]) && !empty($fieldValues[$k][$l])) ? intval($fieldValues[$k][$l]) : 0, $groupMember['sid'], ""];
        }
    }
    
    //print_r($evalFieldResponses);
    $stmt = $db->prepare('select count(*) from student_eval where filler=:filler and eid=:eid');
    $stmt->execute(array('eid' => $evalId, 'filler' => $studentSelfId));
    $isAlready = $stmt->fetchColumn();


    foreach ($evalFieldResponses as $k=>$evalFieldResponse) { // time to insert or update all responses    
        
        if ((empty($evalFieldResponse[0]) || $evalFieldResponse[0] == -1) && $isAlready==0) { //means it has no student_eval row id, time to insert.
			$stmt = $db->prepare('INSERT INTO student_eval(eid, fid, value, filler, filledfor, comment) VALUES (:eid, :fid, :value, :filler, :filledfor, :comment)');
			$stmt->execute(array('eid' => $evalId, 'fid' => $evalFieldResponse[1], 'value' => $evalFieldResponse[2], 'filler' => $studentSelfId, 'filledfor' => $evalFieldResponse[3], 'comment' => ""));
			$evalFieldResponses[$k][0] = $db->lastInsertId();
        } else {
           
			$stmt = $db->prepare('UPDATE student_eval SET value = :value,  comment = :comment WHERE vid = :vid');
			$stmt->execute(array('value' => $evalFieldResponse[2], 'comment' => "", 'vid' => $evalFieldResponse[0]));
        }
    }
}

//      var_dump($evalFieldResponses);
//	    exit();
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
                        <div class="btn-group score-rating-div" data-toggle="buttons">
                            <?php
                            for ($i = 1; $i <= intval($question['max_score']); $i++) {
                            $classColor=$isChecked=0;
                            if(!empty($evalFieldResponses)){

                                $classColor = ($evalFieldResponses[($questionCount*$k)+$l][2] == $i) ?  'active': '';
                                $isChecked = ($evalFieldResponses[($questionCount*$k)+$l][2] == $i) ?  "checked" : "";
                            } 
                                ?>
                                <label
                                        class="btn btn-secondary <?php echo $classColor ?>"
                                        style="width:<?=100/intval($question['max_score']);?>%"
                                >
                                    <input type="radio"
                                           name="peerValue[<?=$k;?>][<?=$l;?>]"
                                           id="rating_<?=$k;?>_<?=$l;?>_<?=$i;?>"
                                           autocomplete="off"
                                           value="<?=$i;?>"
                                           <?php echo $isChecked; ?>
                                    > <?=$i;?>
                                </label>
                                <?php } ?>
                            </div>
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
