<?php

include("../config/config.php");

if (isset($_SESSION['logedin'])) {
    unset($_SESSION['logedin']); // Log out the user by unsetting the logged-in session
}

$_SESSION['notice'] = 'Du är nu utloggad!'; // Set a notice message for the user

// Redirect to the login page, adjusting the path as necessary for the new structure
header("Location: login.php");
exit(); // Ensure no further code is executed after the redirect
