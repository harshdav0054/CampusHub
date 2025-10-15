<?php
session_start();
$_SESSION = [];
session_destroy();

// Redirect to homepage
header("Location: homepage.php");
exit;
?>
