<?php
// logout.php
session_start(); // If you're using PHP sessions
session_destroy(); // Destroy all session data
header('Location: ../home.html'); // Redirect to login page or wherever you want
exit;
?>