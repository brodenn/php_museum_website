<?php

/**
 * Handles form submissions and redirects accordingly.
 *
 * This script processes form submissions, particularly focusing on login attempts.
 * It validates user credentials and redirects the user to the appropriate page based on the outcome.
 * For successful logins, users are redirected to the admin panel. Failed attempts lead back to the login page.
 */

include("../config/config.php");
include("../src/functions/includes.php");

// Check if the form was submitted
if ($_POST["send"] ?? false) {
    // Specifically handle login attempts
    if ($_POST["sendingpage"] == "login") {
        // Sanitize user input to prevent XSS attacks
        $userForm = filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING);
        $passForm = filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING);

        // Verify credentials and determine the redirect URL based on the outcome
        if (!checkUserCredentials($databases['credentials'], $userForm, $passForm)) {
            // Set an error message for failed login attempts
            $_SESSION['error_message'] = 'Incorrect username or password.';
            $url = "login.php"; // Redirect back to the login page on failure
        } else {
            $url = "admin.php"; // Redirect to the admin panel on success
        }
    }
} else if ($_GET["send"] ?? false) {
    // Handle GET requests here, if necessary
}

// Redirect to the determined URL, defaulting to a generic page for unknown senders
$url = $url ?? 'unknown-sender.php';
header("Location: $url");
exit();
