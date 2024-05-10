<?php 
    error_reporting(E_ALL ^ E_NOTICE);
    require_once 'ocesDatabase.php';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $userName = $_POST['userName'];
        $password = $_POST['password'];

        // Attempt login (details in next step)
        $db = new ocesDatabase();
        $user = $db->getUserByUserName($userName);
        
    if ($user && password_verify($password, $user['usrPassword'])) {
        // Login successful
        // Establish session for logged-in user
        session_start();
        $_SESSION['user'] = $user;
        // Redirect to appropriate page (e.g., home.php)
        
        
        if($_SESSION['user']['utpUserType'] == 'student') {
            $_SESSION['user']['role_id'] = 1;
            header('Location: homeStudent.php');     
        } elseif ($_SESSION['user']['utpUserType'] == 'instructor') {
            $_SESSION['user']['role_id'] = 2;
            header('Location: underConstruction.php');                 
        } elseif ($_SESSION['user']['utpUserType'] == 'admin') {
            $_SESSION['user']['role_id'] = 3;
            header('Location: underConstruction.php');                 
        } else {
            header('Location: index.php?Logout=1');
        }
        
        
        
        exit;
    } else {
        // Login failed
        // Display error message
        echo '<div class="alert alert-danger">Invalid User Name or password.</div>';
    }

    }

?>

<html lang="en">
    <head>
        <title> Login Page </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="http://maxcnd.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <?php require 'basic.php';?>
        
        <div class="container text-center">
            <h1> Welcome to the Login page </h1>
        </div>
        
        <form method="post" action="login.php">
            <div class="form-group">
              <label for="userName">User Name:</label>
              <input type="userName" class="form-control" id="userName" name="userName">
            </div>
            
            <div class="form-group">
              <label for="password">Password:</label>
              <input type="password" class="form-control" id="password" name="password">
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        
        <?php include_once 'footer.php';?>
    </body>
</html>   

