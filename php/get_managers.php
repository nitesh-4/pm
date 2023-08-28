<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


$host = "localhost";
$username = "root";
$password = "";
$dbname = "management";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT UserID, Name FROM Users WHERE Role = 'manager'";
$result = $conn->query($sql);

$managers = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $managers[] = $row;
    }
}

header('Conternt-Type: application/json');
echo json_encode($managers);

$conn->close();
