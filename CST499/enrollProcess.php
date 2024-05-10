<?php
    // Include necessary files
    include 'basic.php';
    include 'ocesDatabase.php';

    // Initialize database object
    $db = new ocesDatabase();

    // Retrieve user name from session
    $userName = $_SESSION['user']['usrUserName'];
    $firstName = $_SESSION['user']['usrFirstName'];

    // Retrieve session ID from form data
    $sessionID = $_POST['ssnID'];

    // Call enrollWithUserNameAndSessionID function
    $enrollResult = $db->enrollWithUserNameAndSessionID($userName, $sessionID);
    
    //Fetch the class information
        // Get class information based on the session ID
        $classInfo = $db->getClassInfoBySessionID($sessionID);
        // Access class information safely
        $courseName = isset($classInfo[0]['courseName']) ? $classInfo[0]['courseName'] : 'Unknown Course';
        $semester = isset($classInfo[0]['semester']) ? $classInfo[0]['semester'] : 'Unknown Semester';
        $year = isset($classInfo[0]['year']) ? $classInfo[0]['year'] : 'Unknown Year';
        $vacancies = isset($classInfo[0]['vacancies']) ? $classInfo[0]['vacancies'] : 1;

    // Handle the result
    if ($enrollResult === -1) {
        // Redirect to underConstruction.php if enrollment failed
        header('Location: underConstruction.php');
        exit;
    } else {
        $echoTitle = ($enrollResult == 0) ? "Enrollment: Success" : "Joined Waitlist";
        $echoHeader = ($enrollResult == 0) ? "Congratulation $firstName" : "Successfully Joined";
        $echoText = ($enrollResult == 0) ? "You are now enrolled in $courseName." : "$firstName, you are number $enrollResult on the waitlist.";
    }

?>


<!DOCTYPE html>
<html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title><?php echo $echoTitle; ?></title>

    <style>
        /* Add some basic table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>

    </head>
    <body>
        <h2><?php echo $echoHeader; ?></h2>
        <p><?php echo $echoText; ?></p>
        <table>
            <tr><th><h3>Class Info</h3></th></tr>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Semester</th>
                <th>year</th>
            </tr>
            <tr>
                <td><?php echo $sessionID; ?></td>
                <td><?php echo $courseName; ?></td>
                <td><?php echo $semester; ?></td>
                <td><?php echo $year; ?></td>
            </tr>
        </table>
        

    </body>
</html>

    


