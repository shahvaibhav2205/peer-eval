<?php

require('includes/config.php');

if (!empty($_GET['random'])) { //check for required params
    $randomKey = $_GET['random'];
    $currStudentDetails = $student->getStudentClass($randomKey);

    if (empty($currStudentDetails['cid'])) { //no row for student with that class.
        header('Location: login.php?error-code='.base64_encode(102));
        exit();
    }

    if (!empty($currStudentDetails['sid'])) { //found student record with that class
        $isStudentActive = $currStudentDetails['isactive'];
        $_SESSION['userType'] = "student";
        if ($isStudentActive) {
            header('Location: memberpage.php');
            exit();
        } else {
            $messages = [];
            $_SESSION['messageType'] = "notice";
            $messages[] = "Complete your registration to view Evaluations.";
            $userDetails['firstname'] = $currStudentDetails['firstname'];
            $userDetails['lastname'] = $currStudentDetails['lastname'];
            $userDetails['email'] = $currStudentDetails['email'];
            $_SESSION["messages"] = $messages;
            $_SESSION["userDetails"] = $userDetails;

            header('Location: register.php');
            exit();
        }
    }
} else { //redirect to login

    header('Location: login.php?error-code='.base64_encode(100));
    exit();
}




