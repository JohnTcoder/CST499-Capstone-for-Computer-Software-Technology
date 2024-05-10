<?php
    error_reporting(E_ALL ^ E_NOTICE)
?>
<!DOCTYPE html>

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
        <div class="row-fluid">
            <div class="navbar-inner">
                <div class="container text-center">
                        <?php
                            $year = date('Y');
                        ?>
                        Copyright John Turner <?php echo $year; ?>
                        <br>CST 499 Computer Software Technology Capstone
                        <br>Final Assignment
                </div>
            </div>
        </div>
    </body>
</html>