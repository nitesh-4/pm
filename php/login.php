<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "management";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST["username"];
    $passwrd = $_POST["password"];

    // Select role along with other details
    $stmt = $conn->prepare("SELECT userid, email, password, role FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param('ss', $email, $passwrd);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Store user's ID in session
        $_SESSION['userid'] = $row['userid'];
        // Check if role is 'Manager'
        if ($row['role'] === 'manager') {
            header("location: ../project.html");
        } else if ($row['role'] === 'member') {
            header("location: profile.php");
        }
    } else {
        // Login failed
        echo "Login failed.";
    }
}
