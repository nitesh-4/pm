<?php
// Database connection setup
$host = "your_host";
$username = "your_username";
$password = "your_password";
$database = "your_database";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Task Submission
$response = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $taskData = json_decode($_POST["taskData"], true);

    foreach ($taskData as $task) {
        $task_name = $task["task_name"];
        $task_description = $task["description"];
        $assigned_member = $task["assigned_members"];
        $task_status = $task["status"];

        // Insert into Task table
        $sql_task = "INSERT INTO Task (task_details, status, task_name) VALUES (?, ?, ?)";
        $stmt_task = $conn->prepare($sql_task);
        $stmt_task->bind_param("sss", $task_description, $task_status, $task_name);
        $stmt_task->execute();
        $task_id = $conn->insert_id;

        // Insert into Relation table
        $sql_relation = "INSERT INTO Relation (member_id, task_id) VALUES (?, ?)";
        $stmt_relation = $conn->prepare($sql_relation);
        $stmt_relation->bind_param("ii", $assigned_member, $task_id);
        $stmt_relation->execute();

        $stmt_task->close();
        $stmt_relation->close();
    }

    $response['message'] = 'Tasks added successfully!';
    $response['success'] = true;
} else {
    $response['message'] = 'Invalid request.';
    $response['success'] = false;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
