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
  <title>Enrollment: Select Classes Page</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
      // Attach event listeners to the dropdowns
      $(document).ready(function() {
        $('#yearSelect, #semesterSelect').change(function() {
          const selectedYear = $('#yearSelect').val();
          const selectedSemester = $('#semesterSelect').val();
          fetchClasses(selectedYear, selectedSemester);
        });
      });

      // Function to fetch and display classes based on selected filters
      function fetchClasses(year, semester) {
        $.ajax({
          type: 'POST',
          url: 'fetchClasses.php', // The PHP file to handle the AJAX request
          data: { year: year, semester: semester },
          success: function(response) {
            // Update the list of available classes based on the response
            $('#classList').html(response);
          }
        });
      }
    </script>

  </head>
<body>
    <div class="container text-center">
        <h1>Welcome, <strong><?php echo $user['usrFirstName']; ?></strong>, to the <strong>Select Classes Page</strong> of the <strong>Enrollment Process</strong>!</h1>

        <label for="yearSelect">Select Year:</label>
        <select id="yearSelect" name="filterYear">
          <option value="all">All Years</option>
          <?php foreach ($years as $year) : ?>
            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
          <?php endforeach; ?>
        </select>

        <label for="semesterSelect">Select Semester:</label>
        <select id="semesterSelect" name="filterSemester">
          <option value="all">All Semesters</option>
            <?php foreach ($semesters as $semester) : ?>
                <option value="<?php echo $semester; ?>"><?php echo $semester; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div id="classList">

    </div>

  </body>
</html>


