<?php

    // Check for Logout parameter and handle logout if present
    if (isset($_GET['Logout'])) {
      // Logout procedure
      session_start(); // Start session if not already started
      session_destroy();
      header('Location: index.php');
      exit;
    }

?>

<!DOCTYPE html>

<!DOCTYPE html>
<html>
<head>
  <title>CST 499 OCES Homepage</title>
  <style>
    /* Center the button */
    form {
      text-align: center;
    }

    /* Style for the warning and success message box */
    .message-box {
      background-color: #f0f0f0;
      border: 2px solid #ccc;
      border-radius: 5px;
      padding: 10px;
      margin: 20px auto; /* Center the box */
      width: fit-content;
    }

    /* Style for the success message */
    .success-message {
      color: green;
      font-weight: bold;
    }
  </style>
</head>
<body>

<?php
// Include the ocesDatabase class and dbm class
require_once 'ocesDatabase.php';
require_once 'dbm.php';
require_once 'basic.php';
?>

<div class="message-box">
  <p style="text-align: center; font-weight: bold; color: red;">
    This button is for example purposes only.<br>
    It will clear all data out of the databases, then put in default example data</p>
  <h2>There are no additional warnings!</h2> 
  <p style="text-align: center; font-weight: bold; color: red;">This button is not a part of the normal program, but exists only for example purposes.
  </p>
  <form method="post">
    <button type="submit" name="clear_and_populate">Clear and Populate Database</button>
  </form>
  <?php
  // Check if the button to clear and populate the database is clicked
  if (isset($_POST['clear_and_populate'])) {
    // Create an instance of the dbm class
    $dbManager = new dbm();

    // Clear existing entries from the database
    $dbManager->clearDatabase();

    // Populate users, courses, sessions, and waitlist
    $dbManager->populateUsers();
    $dbManager->populateCourses();
    $dbManager->populateSessions();
    $dbManager->populateWaitlist();

    echo "<p class='success-message'>Database cleared and populated successfully!</p>";
  }
  ?>
</div>

</body>
</html>

