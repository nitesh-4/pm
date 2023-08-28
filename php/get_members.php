<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "management";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT m.member_id as UserId, u.name FROM Users u JOIN Member m ON u.UserId = m.user_id WHERE u.role = 'member'";
$result = $conn->query($sql);

$members = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
} else {
    // Optionally handle the error here
    echo "Error: " . $conn->error;
}

echo json_encode($members);

$conn->close();
?>
