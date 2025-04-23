<?php
// api/auth/logout.php

include_once '../../php/config/constants.php';
include_once '../../php/core/Session.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: ../../public/login.php');
exit;
?>