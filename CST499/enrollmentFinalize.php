<?php
error_reporting(E_ALL ^ E_NOTICE);

session_start();

// Check for valid session and employee role
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) { // Assuming 'employee' role has ID 1
    header('Location: login.php'); // Redirect to login if not authorized
    exit;
}

// Check if the ssnID is provided in the query string
if (!isset($_GET['ssnID'])) {
    header('Location: underConstruction.php'); // Redirect to under construction page if no ssnID is provided
    exit;
}

// Include necessary files
include 'basic.php';
include 'ocesDatabase.php';

$db = new ocesDatabase();

// Retrieve user data
$user = $db->getUserByUserName($_SESSION['user']['usrUserName']);

if (!$user) {
    die("Error: User data not found.");
}

// Retrieve session ID from the query string
$ssnID = $_GET['ssnID'];

// Get class information based on the session ID
$classInfo = $db->getClassInfoBySessionID($ssnID);

// Check if class information is retrieved
if (!$classInfo || empty($classInfo)) {
    echo "No Class Found";
    die("Error: Class information not found.");
}


// Access class information safely
$courseName = isset($classInfo[0]['courseName']) ? $classInfo[0]['courseName'] : 'Unknown Course';
$semester = isset($classInfo[0]['semester']) ? $classInfo[0]['semester'] : 'Unknown Semester';
$year = isset($classInfo[0]['year']) ? $classInfo[0]['year'] : 'Unknown Year';
$vacancies = isset($classInfo[0]['vacancies']) ? $classInfo[0]['vacancies'] : 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Enrollment: Finalize Selection Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcnd.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container text-center">
        <h1>Welcome, <strong><?php echo $user['usrFirstName'] ?></strong>, to the <strong>Finalize Selection Page</strong> of the <strong>Enrollment Process</strong>!</h1>

        <div>
            <?php echo "<strong>Congratulations</strong> on selecting the<br><strong>{$courseName}</strong> class #" . $ssnID; ?>
        </div>

        <div>
            <?php echo "for the <strong>{$semester}</strong> semester of <strong>{$year}</strong>"; ?>
        </div>
        
        <div>
            <?php 
                if ($vacancies > 1){
                    echo "There are {$vacancies} spaces remaining in the class";
                    $join = "Enrol in ";
                } else if ($vacancies == 1) {
                    echo "<strong>Hurry</strong>, there is just 1 space remaining";
                    $join = "Enrol in ";
                } else {
                    echo "Sorry, there are no spaces remaining.  You may join the waitlist.";
                    $join = "Join waitlist for ";
                }
            ?>

            <!-- Add a button to enroll in the class -->
            <form method="post" action="enrollProcess.php">
                <input type="hidden" name="ssnID" value="<?php echo $ssnID; ?>">
                <button type="submit" class="btn btn-primary" name="enroll"> <?php echo $join . $courseName; ?></button>
            </form>

        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>


