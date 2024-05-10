<?php
    error_reporting(E_ALL ^ E_NOTICE);

    session_start();
    if( isset($_SESSION['user']))
                {echo "Welcome: " . $_SESSION['user']['usrFirstName'];}
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <div class="jumbotron">
            <div class="container text-center">
                <h1>CST499 OCES Assignment Website</h1>
                <?php
                    if( isset($_SESSION['user'])) {
//                            echo "<h1>Welcome, You are signed in!</h1>";
                            echo "<h1>Welcome, " . $_SESSION['user']['usrFirstName'] . "!</h1>";
                    } else {
                            echo "<h1>Welcome to the Home Page</h1>";
                    }
                ?>
            </div>
        </div>
        
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                        <li><a href="underConstruction.php"><span class="glyphicon glyphicon-exclamation-sign"></span> About Us</a></li>
                        <li><a href="underConstruction.php"><span class="glyphicon glyphicon-earphone"></span> Contact Us</a></li>
                    </ul>
                    <ul class="navbar navbar-nav navbar-right">
                        <?php
                            
                            if( isset($_SESSION['user']))
                            {
                                echo '<li style="margin-right: 25px;"><a href="underConstruction.php"><span class="glyphicon glyphicon-briefcase"></span> Profile</a></li>';
                                echo '<li style="margin-right: 25px;"><a href="index.php?Logout=1"><span class="glyphicon glyphicon-off"></span> Logout</a></li>';
                                // Add the button within the navbar-right list, conditionally for employees with role_id == 2
                                if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2) {
                                    echo '<li style="margin-right: 25px;"><a href="underConstruction.php"><span class="glyphicon glyphicon-lock"></span> Employees Only</a></li>';
                                }
                                // Add the button within the navbar-right list, conditionally for students with role_id == 1
                                if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1) {
                                    echo '<li style="margin-right: 25px;"><a href="enrollQuery.php"><span class="glyphicon glyphicon-book"></span> Enroll</a></li>';
                                }
                            }
                            else
                            {
                                echo '<li><a href="login.php"><span class="glyphicon glyphicon-user"></span> Login</a></li>';
                                echo '<li><a href="registration.php"><span class="glyphicon glyphicon-pencil"></span> Registration</a></li>';
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
    </body>
</html>