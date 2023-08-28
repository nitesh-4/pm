<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    die("Please login first");
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

// Get user ID from the session
$user_id = $_SESSION['userid'];

// Get manager ID
$stmt = $conn->prepare("SELECT manager_id FROM manager WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$manager_id = $row['manager_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = []; // Create an empty array to hold the response

    if (isset($_POST["project_name"])) {
        // Handle Project Submission
        $title = $_POST["project_name"];
        $description = $_POST["description"];
        $status = $_POST["status"];

        // Insert into Projects table
        $sql = "INSERT INTO Projects (Title, Description, manager_id, Status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $title, $description, $manager_id, $status);
        echo "helllo";

        if ($stmt->execute()) {
            $response['message'] = 'Project created successfully!';
            $response['success'] = true;
        } else {
            $response['message'] = 'Error: ' . $stmt->error;
            $response['success'] = false;
        }
        $stmt->close();
    } elseif (isset($_POST["task_name"])) {
        // Handle Task Submission
        $task_name = $_POST["task_name"];
        $task_description = $_POST["task_description"];
        $assigned_member = $_POST["assigned_member"];
        $task_status = $_POST["task_status"];
        $project_id = $conn->insert_id;

        // Insert into Task table
        $sql_task = "INSERT INTO Task (project_id, task_details, status, task_name) VALUES (?, ?, ?, ?)";
        $stmt_task = $conn->prepare($sql_task);
        $stmt_task->bind_param("issi", $project_id, $task_description, $task_status, $task_name);
        $stmt_task->execute();
        $task_id = $conn->insert_id;

        // Insert into Relation table
        $sql_relation = "INSERT INTO Relation (manager_id, project_id, task_id, member_id) VALUES (?, ?, ?, ?)";
        $stmt_relation = $conn->prepare($sql_relation);
        $stmt_relation->bind_param("iiii", $manager_id, $project_id, $task_id, $assigned_member);
        $stmt_relation->execute();

        $response['message'] = 'Task added successfully!';
        $response['success'] = true;

        // Close statements
        $stmt_task->close();
        $stmt_relation->close();
    }

    
    header('Content-Type: application/json'); // Set the Content-Type header
    echo json_encode($response); // Encode the array as JSON and send it
}
$conn->close();
?>