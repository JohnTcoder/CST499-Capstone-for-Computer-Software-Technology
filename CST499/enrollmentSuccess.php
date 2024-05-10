<?php
    // enrollSelectClasses.php

    // Include necessary files (e.g., database connection)
    include 'basic.php';
    include 'ocesDatabase.php';

    // Initialize database connection
    $db = new ocesDatabase();

    // Get user data from session
    $user = $db->getUserByUserName($_SESSION['user']['usrUserName']);

    // Retrieve available classes
    $availableClasses = $db->getAvailableClasses();

    // Retrieve years and semesters
    $years = $db->getDistinctYears(); 
    $semesters = $db->getDistinctSemesters(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Enrollment: Success</title>


    </head>
    <body>
        That was fun

    </body>
</html>
