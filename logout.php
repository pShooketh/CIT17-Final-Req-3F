<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page with a logout message
header("Location: index.php?status=logged_out");
exit();
?> 