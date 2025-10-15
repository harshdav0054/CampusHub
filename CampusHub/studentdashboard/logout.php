<?php
session_start(); // Must be at the very top

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to homepage
header("Location: ../homepage/homepage.php");
exit;
