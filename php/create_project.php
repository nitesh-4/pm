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

// Query to fetch user role
$sql_user_role = "SELECT role FROM Users WHERE UserID = ?";
$stmt_user_role = $conn->prepare($sql_user_role);
$stmt_user_role->bind_param("i", $user_id);
$stmt_user_role->execute();
$user_role_result = $stmt_user_role->get_result()->fetch_assoc();
$user_role = $user_role_result['role'];
$stmt_user_role->close();

// Get manager ID if user is a manager
$manager_id = null;
if ($user_role === 'manager') {
    $stmt = $conn->prepare("SELECT manager_id FROM manager WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $manager_id = $row['manager_id'];
}

if ($user_role === 'manager') {
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $response = []; // Create an empty array to hold the response

        // Handle Project Submission
        $title = $_POST["project_name"];
        $description = $_POST["description"];
        $status = $_POST["status"];

        // Insert into Projects table
        $sql = "INSERT INTO Projects (Title, Description, manager_id, Status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $title, $description, $manager_id, $status);

        if ($stmt->execute()) {
            $response['message'] = 'Project created successfully!';
            $response['success'] = true;
        } else {
            $response['message'] = 'Error: ' . $stmt->error;
            $response['success'] = false;
        }

        $_SESSION['project_id'] = $conn->insert_id; // Assuming this is the ID of the project just inserted
        $_SESSION['manager_id'] = $manager_id;
        $stmt->close();

        echo json_encode($response); // Encode the array as JSON and send it
    }
} else {
    echo json_encode(['message' => 'Members cannot create projects', 'success' => false]);
}

$conn->close();
?>
