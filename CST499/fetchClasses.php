<?php
    // Include necessary files (e.g., database connection)
    include 'ocesDatabase.php';

    // Initialize database connection
    $db = new ocesDatabase();

    // Retrieve available classes based on selected filters
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $availableClasses = $db->getFilteredClasses($semester, $year);

    // Output the list of available classes
    foreach ($availableClasses as $class) {
        echo "<li><a href='enrollmentFinalize.php?ssnID=" . $class['sessionID'] . "'>" . $class['courseName'] . " " . $class['semester'] . " " . $class['year'] . " with " . $class['vacancies'] . " spaces available</a></li>";
    }
?>

