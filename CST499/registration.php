<?php
//CST499
// registration.php

// Include necessary files (e.g., database connection)
include 'basic.php';
include 'ocesDatabase.php';

 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Page</title>



  </head>
    <body>
        <div class="container text-center">
            <h1> Welcome to the Registration page</h1>
        </div>
        
        <!--Warning messages will appear entries invalid-->
        <?php if (isset($_GET['error']) && $_GET['error'] == 'email_required') : ?>
            <div class="alert alert-danger">Please enter your email address.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_email') : ?>
            <div class="alert alert-danger">Please enter a valid email address.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'username_exists') : ?>
            <div class="alert alert-danger">The entered User Name already exist.  UserName must be unique.</div>
        <?php endif; ?>
        
        <div class="container">

            <form method="POST" action="processRegistration.php">
                <div class="form-group">
                    <label for="email">User Name:</label>
                    <input type="text" id="userName" name="userName" class="form-control">
                </div>

                <div class="form-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" class="form-control">
                </div>
                            
                <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" class="form-control">
                </div>
                            
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>

    </body>
</html>
