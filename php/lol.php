<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and the project_id is set
if (!isset($_SESSION['userid']) || !isset($_SESSION['project_id'])) {
    die("Unauthorized access");
}

// Connect to the database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "management";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$project_id = $_SESSION['project_id']; // Get the project_id from the session
$manager_id = $_SESSION['manager_id']; // Assuming manager_id is stored in session

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_names = $_POST['task_name'];
    $descriptions = $_POST['description'];
    $assign_tos = $_POST['assign_to'];
    $statuses = $_POST['status'];
     


    for ($i = 0; $i < count($task_names); $i++) {
        // Insert into Task table
        $stmt = $conn->prepare("INSERT INTO Task (project_id, task_details, status, task_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $project_id, $descriptions[$i], $statuses[$i], $task_names[$i]);
        $stmt->execute();
        $task_id = $stmt->insert_id; // Get the ID of the task just inserted
        
        // Insert into Relation table
        $comment = "";
        $stmt = $conn->prepare("INSERT INTO Relation (manager_id, project_id, task_id, member_id, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiii", $manager_id, $project_id, $task_id, $assign_tos[$i], $comment); // Assuming comment is handled elsewhere or you can set a default value
        $stmt->execute();
        $stmt->close();
    }

    header("Location: profile.php");
    exit;
} 
$conn->close();
