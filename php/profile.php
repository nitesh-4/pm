<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../style/profile.css" />
    <title>User Profile</title>
</head>

<body>
    <nav class="navbar">
        <div class="logo">Motion</div>
        <ul class="nav-links">
            <li><a href="profile.php">Profile</a></li>
            <li><a href="../project.html">Create Projects</a></li>
            <li><a href="../test.php">Task Details</a></li>
            <li><a href="logout.php">Log Out</a></li>
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

        // Prepare and execute a query based on the user's role
        if ($role === 'manager') {
            $sql_manager_id = "SELECT manager_id FROM Manager WHERE user_id = ?";
            $stmt_manager_id = $conn->prepare($sql_manager_id);
            $stmt_manager_id->bind_param("i", $user_id);
            $stmt_manager_id->execute();
            $manager_id_details = $stmt_manager_id->get_result()->fetch_assoc();
            $retrieved_id = $manager_id_details['manager_id'];
            $stmt_manager_id->close();
        
            // Fetch all projects created by this manager
            $sql_projects = "SELECT * FROM Projects WHERE manager_id = ?";
            $stmt_projects = $conn->prepare($sql_projects);
            $stmt_projects->bind_param("i", $retrieved_id);
            $stmt_projects->execute();
            $projects = $stmt_projects->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_projects->close();
        } elseif ($role === 'member') {
            $sql_member_id = "SELECT member_id FROM Member WHERE user_id = ?";
            $stmt_member_id = $conn->prepare($sql_member_id);
            $stmt_member_id->bind_param("i", $user_id);
            $stmt_member_id->execute();
            $member_id_details = $stmt_member_id->get_result()->fetch_assoc();
            $retrieved_id = $member_id_details['member_id'];
            $stmt_member_id->close();
        
            // Fetch all projects that this member is a part of
            $sql_projects = "SELECT Projects.Title, Projects.Description FROM Relation JOIN Projects ON Relation.project_id = Projects.ProjectID WHERE Relation.member_id = ?";
            $stmt_projects = $conn->prepare($sql_projects);
            $stmt_projects->bind_param("i", $retrieved_id);
            $stmt_projects->execute();
            $projects = $stmt_projects->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_projects->close();
        }
        // echo ($retrieved_id);


        // Query to fetch projects for the user
        // $projects_sql = $user_details['Role'] === 'manager'
        //     ? "SELECT * FROM Projects WHERE Manager_id = ?"
        //     : "SELECT * FROM Relation JOIN Projects ON Relation.project_id = Projects.ProjectID WHERE member_id = ?";

        // $stmt_projects = $conn->prepare($projects_sql);
        // $stmt_projects->bind_param("i", $manager_id);
        // $stmt_projects->execute();
        // $projects = $stmt_projects->get_result();

        echo '<div class="profile-section">';
        echo '<div class="default-profile-picture"> </div>';
        echo '<h2>' . $user_details['Name'] . '</h2>';
        echo '<p>Email: ' . $user_details['Email'] . '</p>';
        echo '<p>Password: ' . $user_details['Password'] . '</p>'; // Be cautious about displaying password
        echo '<p>Role: ' . $user_details['Role'] . '</p>';
        echo '</div>';




        echo '<div class="projects-overview">';
        echo"Projects";
        foreach ($projects as $project){
            echo '<div class="project">';
            echo '<h3>name -' . $project['Title'] . '</h3>';
            echo '<p>Description -' . $project['Description'] . '</p>';
            echo '</div>';
        }
        echo '</div>';

        // // Comment section for members
        // if ($user_details['Role'] === 'member') {
        //     echo '<form action="add_comment.php" method="POST">';
        //     echo '<textarea name="comment" placeholder="Enter comment"></textarea>';
        //     echo '<button type="submit">Submit Comment</button>';
        //     echo '</form>';

        //     $sql_insert_comment = "INSERT INTO Relation (project_id, member_id, comment) VALUES (?, ?, ?)";
        //     $stmt_insert_comment = $conn->prepare($sql_insert_comment);
        //     $stmt_insert_comment->bind_param("iis",  $project_id, $member_id, $comment);
        //     $stmt_insert_comment->execute();
        //     $stmt_insert_comment->close();

        //     // Redirect back to the current page after submitting comment
        //     // header("Location: " . $_SERVER['PHP_SELF']);
        // }

        $stmt_user_details->close();
       
        $conn->close();
        ?>
        <footer class="footer">
            <p>&copy; 2023 Project Tracker. All rights reserved.</p>
        </footer>
        <!-- <script src="script/profile_script.js"></script> -->
    </div>
</body>

</html>