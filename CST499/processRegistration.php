<?php
    require_once 'ocesDatabase.php';

    //Validate email
    if (empty($_POST['email'])) {
        // Display an error message
        header('Location: registration.php?error=email_required');
        exit();
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        // Display an error message
        header('Location: registration.php?error=invalid_email');
        exit();
    }

    // Access form data
    $userName = $_POST['userName'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    // Connect to database
    $db = new ocesDatabase();

    //Make sure username
    $sql = "SELECT COUNT(*) FROM tbluser WHERE usrUserName = '$userName'";
    $result = $db->executeSelectQuery($sql);
    $count = $result->fetch_row()[0];
    if ($count > 0) {
        // Display an error message
        header('Location: registration.php?error=username_exists');
        exit();
    }

    // Insert data into database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $sql = "INSERT INTO tbluser (usrUserName, usrFirstName, usrLastname, usrEmail, usrPassword, utpUserType) VALUES ('$userName', '$firstName', '$lastName', '$email', '$hashedPassword', 'student')";
    $db->executeQuery($sql);


    // Redirect to confirmation page
    header('Location: index.php');
?>
