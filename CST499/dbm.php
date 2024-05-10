
<?php

require_once 'ocesDatabase.php';

class dbm {
    
    private $database;
    
    public function __construct() {
        $this->database = new ocesDatabase();
    }

   
    // Populate tblUser with example students
    public function populateUsers() {
        $sampleNames = array("John Doe", "Jane Smith", "Michael Johnson", "Emily Davis", "Chris Wilson", "Sarah Brown", "David Martinez", "Jessica Anderson", "James Taylor", "Olivia Thomas");

        foreach ($sampleNames as $sampleName) {
            // Split the full name into first name and last name
            list($firstName, $lastName) = explode(" ", $sampleName);

            // Generate the username by concatenating first name and last name without spaces
            $userName = strtolower($firstName) . ucfirst(strtolower($lastName));

            // Generate the email by concatenating first name, dot, last name, and example.com
            $email = strtolower($firstName) . "." . strtolower($lastName) . "@example.com";

            // Hash the default password
            $password = password_hash("password123", PASSWORD_DEFAULT);

            // Create the user record
            $this->database->createUserRecord($userName, $password, $firstName, $lastName, $email, "student");
        }
    }


    // Populate tblCourse with example courses
    public function populateCourses() {
        $courseNames = array("Introduction to Programming", "Database Management", "Web Development", "Software Engineering", "Computer Networks", "Data Structures", "Algorithm Design", "Operating Systems", "Artificial Intelligence", "Cybersecurity");
        foreach ($courseNames as $courseName) {
            $this->database->insertCourse($courseName);
        }
    }

    // Populate tblSession with sessions for the Summer 2024 semester
    public function populateSessions() {
        $courses = $this->database->getAllCourses();
        $semester = "Summer";
        $year = 2024;
        $capacity = 5; // Assuming each session has a capacity of 5

        foreach ($courses as $course) {
            $sessionId = $this->database->insertSession($course['crsID'], $semester, $year, $capacity);

            if ($sessionId !== false) {
                // Insert students into sessions (assuming some sessions have vacancies)
                if ($course['crsName'] == "Introduction to Programming" || $course['crsName'] == "Database Management") {
                    // Get the first student ID from the tblUser table
                    $studentId = $this->database->getFirstStudentId();

                    // Check if a student ID was retrieved
                    if ($studentId !== false) {
                        // Enroll the student in the session
                        $this->database->enrollStudent($sessionId, $studentId);
                    } else {
                        echo "Error: No students found in the database.";
                    }
                }
            }
        }
        
        $courses = $this->database->getAllCourses();
        $semester = "Spring";
        $year = 2024;
        $capacity = 5; // Assuming each session has a capacity of 5

        foreach ($courses as $course) {
            $sessionId = $this->database->insertSession($course['crsID'], $semester, $year, $capacity);

            if ($sessionId !== false) {
                // Insert students into sessions (assuming some sessions have vacancies)
                if ($course['crsName'] == "Introduction to Programming" || $course['crsName'] == "Database Management") {
                    // Get the first student ID from the tblUser table
                    $studentId = $this->database->getFirstStudentId();

                    // Check if a student ID was retrieved
                    if ($studentId !== false) {
                        // Enroll the student in the session
                        $this->database->enrollStudent($sessionId, $studentId);
                    } else {
                        echo "Error: No students found in the database.";
                    }
                }
            }
        }    
    }


    // Populate the waitlist for a session that is full
    public function populateWaitlist() {
        $session = $this->database->getSessionById(1); // Assuming session ID 1 is full
        if ($session && $session['ssnCapacity'] <= count($session['enrolledStudents'])) {
            // Add students to the waitlist
            for ($i = 4; $i <= 10; $i++) {
                $studentId = $i; // Assuming student IDs 4 to 10 exist in tblUser
                $this->database->addToWaitlist($session['ssnID'], $studentId);
            }
        }
    }

    // Clear the database
    public function clearDatabase() {
        $this->database->clearTables();
    }
}

?>

