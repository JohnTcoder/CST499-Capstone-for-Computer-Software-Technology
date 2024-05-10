<?php
    error_reporting(E_ALL ^ E_NOTICE);

    session_start();
    
    // Check for valid session and employee role
    if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {  // Assuming 'employee' role has ID 1
        header('Location: login.php');  // Redirect to login if not authorized
        exit;
    }

?>
<!DOCTYPE html>

<html lang="en">
    
    <head>
        <title> Student Profile Page </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="http://maxcnd.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <?php include 'basic.php';?>
        <?php include 'ocesDatabase.php';?>

        <?php
            $db = new ocesDatabase();
            $user = $db->getUserByUserName($_SESSION['user']['usrUserName']);
        

            if (!$user) {
                die("Error: User data not found.");
            }
        ?>
        
        <div class="container text-center">
            <h1>Welcome, <?php echo $user['usrFirstName'] . ' ' . $user['usrLastname']; ?>!</h1>

            <h2>Student Page</h2>
            <a href="enrollQuery.php" class="btn btn-primary">Enroll in Classes</a>
        </div>
        
        <div>
            <?php
                if (isset($_GET['status'])) {
                    switch ($_GET['status']) {
                        case 'dropSuccess':
                            echo "<div class='alert alert-success'>Class dropped successfully!</div>";
                            break;
                        case 'dropFail':
                            echo "<div class='alert alert-danger'>Failed to drop the class.</div>";
                            break;
                        case 'invalid':
                            echo "<div class='alert alert-warning'>Invalid request.</div>";
                            break;
                    }
                }
            ?>
        </div>
        
        <div>
          <?php
            $userEnrollments = $db->getEnrollmentsByUserId($user['usrID']);
            $userWaitlist = $db->getWaitlistByUserId($user['usrID']);

            if (count($userEnrollments) > 0 || count($userWaitlist) > 0) {
                if (count($userEnrollments) > 0) {
                    echo "<h3>You have successfully enrolled in these classes</h3>";
                    echo '<table class="table table-bordered">';
                    echo '<thead><tr><th>Session ID</th><th>Semester</th><th>Year</th><th>Class Name</th><th>Drop?</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($userEnrollments as $enrollment) {
                      echo "<tr>
                              <td>{$enrollment['ssnID']}</td>
                              <td>{$enrollment['ssnSemester']}</td>
                              <td>{$enrollment['ssnYear']}</td>
                              <td>{$enrollment['crsName']}</td>
                              <td>Click <a href='enrollDrop.php?enrollmentLine={$enrollment['enrID']}'>here</a> to drop this class</td>
                            </tr>";
                    }
                    echo '</tbody></table>';
                }
                if (count($userWaitlist) > 0) {
                    echo "<h3>You are on the waitlist for these classes</h3>";
                    echo '<table class="table table-bordered">';
                    echo '<thead><tr><th>Session ID</th><th>Semester</th><th>Year</th><th>Class Name</th><th>Waitlist Position</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($userWaitlist as $enrollment) {
                      echo "<tr>
                              <td>{$enrollment['ssnID']}</td>
                              <td>{$enrollment['ssnSemester']}</td>
                              <td>{$enrollment['ssnYear']}</td>
                              <td>{$enrollment['crsName']}</td>
                              <td>{$enrollment['wtlPosition']}</td>
                            </tr>";
                    }
                    echo '</tbody></table>';
                }
            } else {
              echo "<div><p>Click <a href='enrollQuery.php'>here</a> to get started!</p></div>";
            }
          ?>
        </div>
            
    
            
        <?php include 'footer.php';?>
        
    </body>
</html>
