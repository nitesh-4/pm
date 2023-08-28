<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task and Project Management</title>
    <link rel="stylesheet" href="./style/profile.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">Motion</div>
        <ul class="nav-links">
            <li><a href="php/profile.php">Profile</a></li>
            <li><a href="project.html">Create Projects</a></li>
            <li><a href="test.php">Task Details</a></li>
            <li><a href="php/logout.php">Log Out</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php
        session_start();

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

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

        // Query to fetch user details
        $sql_user_details = "SELECT * FROM Users WHERE UserID = ?";
        $stmt_user_details = $conn->prepare($sql_user_details);
        $stmt_user_details->bind_param("i", $user_id);
        $stmt_user_details->execute();
        $user_details = $stmt_user_details->get_result()->fetch_assoc();
        $role = $user_details['Role'];

        $retrieved_id = null;

        if ($role === 'manager') {
            $sql_manager_id = "SELECT manager_id FROM Manager WHERE user_id = ?";
            $stmt_manager_id = $conn->prepare($sql_manager_id);
            $stmt_manager_id->bind_param("i", $user_id);
            $stmt_manager_id->execute();
            $manager_id_details = $stmt_manager_id->get_result()->fetch_assoc();
            $retrieved_id = $manager_id_details['manager_id'];
        } elseif ($role === 'member') {
            $sql_member_id = "SELECT member_id FROM Member WHERE user_id = ?";
            $stmt_member_id = $conn->prepare($sql_member_id);
            $stmt_member_id->bind_param("i", $user_id);
            $stmt_member_id->execute();
            $member_id_details = $stmt_member_id->get_result()->fetch_assoc();
            $retrieved_id = $member_id_details['member_id'];
        }

        if ($role === 'member') {
            $sql = "SELECT Task.*, Projects.Title FROM Relation
            JOIN Task ON Relation.task_id = Task.task_id
            JOIN Projects ON Relation.project_id = Projects.ProjectID
            WHERE Relation.member_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $retrieved_id);
            $stmt->execute();
            $tasks = $stmt->get_result();

            echo "<h2>Your Tasks</h2>";
            echo '<table border="1">';
            echo '<tr><th>Project</th><th>Task</th><th>Status</th><th>Actions</th></tr>';

            while ($task = $tasks->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $task['Title'] . '</td>';
                echo '<td>' . $task['task_name'] . '</td>';
                echo '<td>' . $task['status'] . '</td>';
                echo '<td>';
                echo '<form action="" method="POST">';
                echo '<input type="hidden" name="task_id" value="' . $task['task_id'] . '">';
                echo '<input type="text" name="new_status" placeholder="' . $task['status'] . '">';
                echo '<input type="submit" name="update" value="Update">';
                echo '</form>';
                echo '</td>';
                echo '</tr>';

                if (isset($_POST['update'])) {
                    $new_status = $_POST['new_status'];
                    $task_id = $_POST['task_id'];

                    $sql_update = "UPDATE Task SET status = ? WHERE task_id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param('si', $new_status, $task_id);

                    if (!$stmt_update->execute()) {
                        echo "Error updating status: " . $stmt_update->error;
                    }
                }
            }

            echo '</table>';

        } elseif ($role === 'manager') {
            $sql = "SELECT Projects.Title, Projects.Description, Projects.Status, Task.task_name, Member.name FROM Relation
                        JOIN Projects ON Relation.project_id = Projects.ProjectID
                        JOIN Task ON Relation.task_id = Task.task_id
                        JOIN Member ON Relation.member_id = Member.member_id
                        WHERE Relation.manager_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $retrieved_id);
            $stmt->execute();
            $projects = $stmt->get_result();

            echo "<h2>Your Projects</h2>";
            echo '<table border="1">';
            echo '<tr><th>Title</th><th>Description</th><th>Status</th><th>Task</th><th>Assigned to</th></tr>';

            while ($project = $projects->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $project['Title'] . '</td>';
                echo '<td>' . $project['Description'] . '</td>';
                echo '<td>' . $project['Status'] . '</td>';
                echo '<td>' . $project['task_name'] . '</td>';
                echo '<td>' . $project['name'] . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        }

        ?>
    </div>

    <footer class="footer">
        <p>&copy; 2023 Project Tracker. All rights reserved.</p>
    </footer>

</body>

</html>
