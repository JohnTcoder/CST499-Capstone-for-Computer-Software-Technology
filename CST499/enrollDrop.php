
<?php
    error_reporting(E_ALL ^ E_NOTICE);
    session_start();

    // Ensure there is a session and the user is authorized
    if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
        header('Location: login.php');
        exit;
    }

    require 'ocesDatabase.php';
    $db = new ocesDatabase();

    $enrollmentID = isset($_GET['enrollmentLine']) ? intval($_GET['enrollmentLine']) : null;

    if ($enrollmentID) {
        $success = $db->dropClassByEnrollmentId($enrollmentID);
        if ($success) {
            header("Location: homeStudent.php?status=dropSuccess");
            exit;
        } else {
            header("Location: homeStudent.php?status=dropFail");
            exit;
        }
    } else {
        header("Location: homeStudent.php?status=invalid");
        exit;
    }
?>




