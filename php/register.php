<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "management";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    $sql = "INSERT INTO Users (Name, Email, Password, Role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id; // Get the inserted user ID

        // Depending on the role, insert the user into the corresponding table (manager or member)
        if ($role === 'manager') {
            $insert_manager_sql = "INSERT INTO manager (user_id, name) VALUES (?, ?)";
            $manager_stmt = $conn->prepare($insert_manager_sql);
            $manager_stmt->bind_param("is", $user_id, $name);
            $manager_stmt->execute();
            $manager_stmt->close();
        } elseif ($role === 'member') {
            $insert_member_sql = "INSERT INTO member (name ,user_id) VALUES (?, ?)";
            $member_stmt = $conn->prepare($insert_member_sql);
            $member_stmt->bind_param("si", $name, $user_id);
            $member_stmt->execute();
            $member_stmt->close();
        }

        header("location: ../index.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
