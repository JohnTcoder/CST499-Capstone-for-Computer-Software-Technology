<?php

class ocesDatabase {

    private $host = "localhost";
    private $user = "JTAdmin";
    private $password = "@Jadm";
    private $db = "cst499_OnlineCourseEnrollmentSystem_OCES";
    private $con;

    
    //------------Basic Open and close functions------------
    //establish connection
    public function __construct() {
        $this->con = mysqli_connect($this->host, $this->user, $this->password, $this->db);
        if (!$this->con) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
    
    //test connection
    public function getConnection() {
      return $this->con;
    }
   
    //close connection
    public function __destruct() {
        mysqli_close($this->con);
    }
    
    // Function to clear all tables
    public function clearTables() {
      $tables = ["tblWaitlist", "tblEnrollment", "tblSession", "tblCourse", "tblUser"]; // Adjust table names as needed

      foreach ($tables as $table) {
        $stmt = $this->con->prepare("DELETE FROM $table");
        $stmt->execute();
      }

      echo "Database tables cleared successfully. <br>";
    }
    
    
    
    
    //------------Fetch basic info------------
    // User's role as student=1, instructor=2, admin=3
    public function getUserRoleId($userId) {
      $stmt = $this->con->prepare("SELECT role_id FROM tbluser WHERE id = ?");
      $stmt->bind_param("i", $userId);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      $role_id = 0;

      if ($row['utpUserType'] === 'student') {
        $role_id = 1;
      } elseif ($row['utpUserType'] === 'instructor') {
        $role_id = 2;
      } elseif ($row['utpUserType'] === 'admin') {
        $role_id = 3;
      }

      return $role_id;
    }
    
    //Get an array of all the courses
    public function getAllCourses() {
        $stmt = $this->con->prepare("SELECT * FROM tblCourse");
        $stmt->execute();
        $result = $stmt->get_result();
        $courses = []; // Initialize an empty array to store courses

        while ($row = $result->fetch_assoc()) {
          $courses[] = $row; // Add each course row to the courses array
        }

        return $courses; // Return the array containing all courses
    }
    
    //Select a random student
    public function getRandomStudentIds($count) {
        $stmt = $this->con->prepare("SELECT usrID FROM tblUser ORDER BY RAND() LIMIT ?");
        $stmt->bind_param("i", $count);
        $stmt->execute();
        $result = $stmt->get_result();
        $studentIds = [];
        while ($row = $result->fetch_assoc()) {
          $studentIds[] = $studentId = $row['usrID'];
        }
        return $studentIds;
    }
    
    // Get the first student ID from the tblUser table
    public function getFirstStudentId() {
        $stmt = $this->con->prepare("SELECT usrID FROM tblUser ORDER BY usrID ASC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['usrID'];
        } else {
            return false; // No students found
        }
    }

    // Get session details by session ID
    public function getSessionById($sessionId) {
        $stmt = $this->con->prepare("SELECT * FROM tblSession WHERE ssnID = ?");
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false; // Session not found
        }
    }

    
    
    
    
    //------------Queries based on User Input------------
    //Get user for login
    public function getUserByUserName($userName) {
        $stmt = $this->con->prepare("SELECT * FROM tblUser WHERE usrUserName = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    
    //-----------Programatically Defined Queries-------------
    //Select
    public function executeSelectQuery($sql) {
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            die("Query failed: " . mysqli_error($this->con));
        }
        return $result;
    }
    //Insert
    public function executeQuery($sql) {
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            die("Query failed: " . mysqli_error($this->con));
        }
        return $result; // Return the result set
    }
    
    
    
    
    //-----------Queries for Enrollment Process
    // Get available classes (including vacancies)
    public function getAvailableClasses() {
        $stmt = $this->con->prepare("
            SELECT
                c.crsID AS courseID, c.crsName AS courseName, s.ssnID AS sessionID, s.ssnSemester AS semester, s.ssnYear AS year, s.ssnCapacity - COUNT(e.enrID) AS vacancies
            FROM
                tblCourse c
            INNER JOIN
                tblSession s ON c.crsID = s.crsID
            LEFT JOIN
                tblEnrollment e ON s.ssnID = e.ssnID
            GROUP BY
                s.ssnID
            HAVING
                vacancies >= 0
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $classes = [];

        while ($row = $result->fetch_assoc()) {$classes[] = $row;}
        
        return $classes;
    }
    
    public function getFilteredClasses($filterSemester, $filterYear) {
        // Initialize the WHERE clause and parameter types
        $whereClause = "";
        $types = "";

        // Check if the filterSemester is not 'All Semesters'
        if ($filterSemester !== 'All Semesters') {
            $whereClause .= " AND s.ssnSemester = ?";
            $types .= "s";
        }

        // Check if the filterYear is not 'All Years'
        if ($filterYear !== 'All Years') {
            $whereClause .= ($whereClause ? " AND " : "") . " s.ssnYear = ?";
            $types .= "s";
        }

        // Prepare the SQL query with the dynamic WHERE clause
        $sql = "
            SELECT
                c.crsID AS courseID,
                c.crsName AS courseName,
                s.ssnID AS sessionID,
                s.ssnSemester AS semester,
                s.ssnYear AS year,
                s.ssnCapacity - COUNT(e.enrID) AS vacancies
            FROM
                tblCourse c
            INNER JOIN
                tblSession s ON c.crsID = s.crsID
            LEFT JOIN
                tblEnrollment e ON s.ssnID = e.ssnID
            GROUP BY
                s.ssnID
            HAVING
                vacancies >= 0
            $whereClause
        ";

        // Initialize an empty array to store the results
        $classes = [];

        // Execute the query and fetch the results
        $stmt = $this->con->prepare($sql);

        // Bind parameters if any filters are applied
        if ($whereClause) {
            $stmt->bind_param($types, $filterSemester, $filterYear);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the results and store them in the array
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }

        // Return the array of classes
        return $classes;
    }

     
    // Get class information by session ID
    public function getClassInfoBySessionID($sessionID) {
        $stmt = $this->con->prepare("
            SELECT
                c.crsID AS courseID,
                c.crsName AS courseName,
                s.ssnID AS sessionID,
                s.ssnSemester AS semester,
                s.ssnYear AS year,
                s.ssnCapacity - COUNT(e.enrID) AS vacancies
            FROM
                tblCourse c
            INNER JOIN
                tblSession s ON c.crsID = s.crsID
            LEFT JOIN
                tblEnrollment e ON s.ssnID = e.ssnID
            WHERE
                s.ssnID = ?
            GROUP BY
                s.ssnID

        ");

        // Bind session ID parameter
        $stmt->bind_param("i", $sessionID);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch class information
        $specifiedSession = [];
        while ($row = $result->fetch_assoc()) {
            $specifiedSession[] = $row;
        }

        return $specifiedSession;
    }



    //Get list of years in which classes are offered
    public function getDistinctYears() {
        $stmt = $this->con->prepare("SELECT DISTINCT ssnYear FROM tblSession");
        $stmt->execute();
        $result = $stmt->get_result();
        $years = [];

        while ($row = $result->fetch_assoc()) {
          $years[] = $row['ssnYear'];
        }

        return $years;
    }
    
    //Get list of semesters in which classes are offered
    public function getDistinctSemesters() {
        $stmt = $this->con->prepare("SELECT DISTINCT ssnSemester FROM tblSession");
        $stmt->execute();
        $result = $stmt->get_result();
        $semesters = [];

        while ($row = $result->fetch_assoc()) {
          $semesters[] = $row['ssnSemester'];
        }

        return $semesters;
    }


    
    


    //------------Create records------------
    //Create a new user record
    public function createUserRecord($userName, $password, $firstName, $lastName, $email, $userType) {
        $stmt = $this->con->prepare("INSERT INTO tblUser (usrUserName, usrPassword, usrFirstName, usrLastName, usrEmail, utpUserType) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $userName, $password, $firstName, $lastName, $email, $userType);
      if ($stmt->execute()) {
        return true;
      } else {
        echo "Error creating a user record: " . $stmt->error;
        return false;
      }        
    }
    
    // Add a student to the waitlist for a session
    public function addToWaitlist($ssnID, $usrID) {
      $stmt = $this->con->prepare("INSERT INTO tblWaitlist (ssnID, usrID) VALUES (?, ?)");
      $stmt->bind_param("ii", $ssnID, $usrID);
      if ($stmt->execute()) {
        return true;
      } else {
        echo "Error adding to waitlist: " . $stmt->error;
        return false;
      }
    }

    // Enroll a student in a session
    public function enrollStudent($ssnID, $usrID) {
      $stmt = $this->con->prepare("INSERT INTO tblEnrollment (ssnID, usrID) VALUES (?, ?)");
      $stmt->bind_param("ii", $ssnID, $usrID);


      if ($stmt->execute()) {
        return true;
      } else {
        echo "Error enrolling student: " . $stmt->error;
        return false;
      }
    }

    // Insert a new course into the database
    public function insertCourse($crsName) {
      $stmt = $this->con->prepare("INSERT INTO tblCourse (crsName) VALUES (?)");
      $stmt->bind_param("s", $crsName);
      if ($stmt->execute()) {
        return true;
      } else {
        echo "Error inserting course: " . $stmt->error;
        return false;
      }
    }
    
    //Insert a new session into tblSession
    public function insertSession($courseId, $semester, $year, $capacity) {
        $stmt = $this->con->prepare("INSERT INTO tblSession (crsID, ssnSemester, ssnYear, ssnCapacity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isii", $courseId, $semester, $year, $capacity);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
          $sessionId = $stmt->insert_id; // Get the ID of the inserted session
          return $sessionId; // Return the session ID
        } else {
          return false; // Indicate error or unsuccessful session creation
        }
    }
    
    
    //------------Enroll or Waitlist Student--------------------
    public function enrollWithUserNameAndSessionID($userName, $sessionID) {
        // Get user ID based on username
        $user = $this->getUserByUserName($userName);
        if (!$user) {
            return -1; // User not found
        }
        $userID = $user['usrID'];

        // Check if user is already enrolled in the session
        $isEnrolled = $this->isEnrolled($userID, $sessionID);
        if ($isEnrolled) {
            return 0; // User is already enrolled
        }

        // Get class information based on session ID
        $classInfo = $this->getClassInfoBySessionID($sessionID);
        if (!$classInfo || empty($classInfo)) {
            return -1; // Class information not found
        }

        // Check for vacancies
        $vacancies = $classInfo[0]['vacancies'];

        if ($vacancies > 0) {
            // Enroll user in the class
            $stmt = $this->con->prepare("
                INSERT INTO tblEnrollment (ssnID, usrID)
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii", $sessionID, $userID);
            $stmt->execute();

            if ($stmt->affected_rows === 1) {
                return 0; // Enrolled successfully
            } else {
                return -1; // Enrollment failed (database issue)
            }
        } else {
            // Add user to waitlist
            $waitlistPosition = $this->getWaitlistPosition($sessionID); // Get current waitlist position
            $stmt = $this->con->prepare("
                INSERT INTO tblWaitlist (ssnID, usrID, wtlPosition)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iii", $sessionID, $userID, $waitlistPosition);
            $stmt->execute();

            if ($stmt->affected_rows === 1) {
                return $waitlistPosition; // Added to waitlist with position
            } else {
                return -1; // Waitlist insertion failed (database issue)
            }
        }
    }

    // Function to check if a user is already enrolled in a session
    private function isEnrolled($userID, $sessionID) {
        $stmt = $this->con->prepare("
            SELECT COUNT(*) AS enrolled
            FROM tblEnrollment
            WHERE usrID = ? AND ssnID = ?
        ");
        $stmt->bind_param("ii", $userID, $sessionID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['enrolled'] > 0;
    }

    public function GeminienrollWithUserNameAndSessionID($userName, $sessionID) {
        // Get user ID based on username
        $user = $this->getUserByUserName($userName);
        if (!$user) {
          return -1; // User not found
        }
        $userID = $user['usrID'];

        // Get class information based on session ID
        $classInfo = $this->getClassInfoBySessionID($sessionID);
        if (!$classInfo || empty($classInfo)) {
          return -1; // Class information not found
        }

        // Check for vacancies (optional, can be removed if not relevant)
        $vacancies = $classInfo[0]['vacancies'];

        // Enroll user in the class
        $stmt = $this->con->prepare("
          INSERT INTO tblEnrollment (ssnID, usrID)
          VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $sessionID, $userID);

        try {
          $stmt->execute();
          // Check for successful insert (affected_rows) or duplicate key error
          if ($stmt->affected_rows === 1 || $stmt->errno === 1062) { // 1062 is MySQL code for duplicate key error
            return 0; // Enrolled successfully (or already enrolled)
          } else {
            return -1; // Enrollment failed (database issue)
          }
        } catch (mysqli_sql_exception $e) {
          // Handle other potential database errors (optional)
          return -1;
        } finally {
          $stmt->close(); // Close the statement regardless of success or error
        }
    }

    public function OLDenrollWithUserNameAndSessionID($userName, $sessionID) {
        // Get user ID based on username
        $user = $this->getUserByUserName($userName);
        if (!$user) {
          return -1; // User not found
        }
        $userID = $user['usrID'];

        // Get class information based on session ID
        $classInfo = $this->getClassInfoBySessionID($sessionID);
        if (!$classInfo || empty($classInfo)) {
          return -1; // Class information not found
        }


        // Check for vacancies
        $vacancies = $classInfo[0]['vacancies'];
        
        if ($vacancies > 0) {
          // Enroll user in the class
          $stmt = $this->con->prepare("
            INSERT INTO tblEnrollment (ssnID, usrID)
            VALUES (?, ?)
          ");
          $stmt->bind_param("ii", $sessionID, $userID);
          $stmt->execute();

          if ($stmt->affected_rows === 1) {
            return 0; // Enrolled successfully
          } else {
            return -1; // Enrollment failed (database issue)
          }
        } else {
          // Add user to waitlist
          $waitlistPosition = $this->getWaitlistPosition($sessionID); // Get current waitlist position
          $stmt = $this->con->prepare("
            INSERT INTO tblWaitlist (ssnID, usrID, wtlPosition)
            VALUES (?, ?, ?)
          ");
          $stmt->bind_param("iii", $sessionID, $userID, $waitlistPosition);
          $stmt->execute();

          if ($stmt->affected_rows === 1) {
            return $waitlistPosition; // Added to waitlist with position
          } else {
            return -1; // Waitlist insertion failed (database issue)
          }
        }
    }
    
    
    public function getWaitlistPosition($sessionID) {
        $stmt = $this->con->prepare("
          SELECT MAX(wtlPosition) AS highestPosition
          FROM tblWaitlist
          WHERE ssnID = ?
        ");
        $stmt->bind_param("i", $sessionID);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any rows were returned (no waitlist entries for this session)
        if ($result->num_rows === 0) {
          return 1; // Start waitlist at position 1 if no existing entries
        }

        $row = $result->fetch_assoc();
        return $row['highestPosition'] + 1; // Increment the highest position by 1
    }

    
    public function getEnrollmentsByUserId($userId) {
        $sql = "SELECT e.ssnID, e.enrID, s.ssnSemester, s.ssnYear, c.crsName
                FROM tblEnrollment e
                INNER JOIN tblSession s ON e.ssnID = s.ssnID
                INNER JOIN tblCourse c ON s.crsID = c.crsID
                WHERE e.usrID = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $enrollments = [];
        while ($row = $result->fetch_assoc()) {
            $enrollments[] = $row;
        }
        $stmt->close();
        return $enrollments;
    }

    public function getWaitlistByUserId($userId) {
        $sql = "SELECT e.ssnID, s.ssnSemester, s.ssnYear, c.crsName, e.wtlPosition
                FROM tblWaitlist e
                INNER JOIN tblSession s ON e.ssnID = s.ssnID
                INNER JOIN tblCourse c ON s.crsID = c.crsID
                WHERE e.usrID = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $enrollments = [];
        while ($row = $result->fetch_assoc()) {
            $enrollments[] = $row;
        }
        $stmt->close();
        return $enrollments;
    }

    public function dropClassByEnrollmentId($enrollmentID){
        $sql = "DELETE FROM tblEnrollment WHERE enrID = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $enrollmentID);
        if ($stmt->execute()) {
            return true; // Success
        } else {
            return false; // Failure
        }        
    }

     
}

    







