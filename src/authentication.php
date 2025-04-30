<?php

/**
 * Attempts to verify a user's credentials against the database.
 *
 * @param string $dsn The Data Source Name for the PDO connection.
 * @param string $username The username provided by the user.
 * @param string $password The plaintext password provided by the user.
 * @return bool Returns true if credentials are correct, otherwise false.
 */
function checkUserCredentials(string $dsn, string $username, string $password): bool
{
    // Create database connection
    $db = connectToDatabase($dsn);
    if (!$db) {
        $_SESSION['error_message'] = 'Database connection error.';
        return false;
    }

    // Prepare the SQL statement to select the user
    $sql = "SELECT Username, Password FROM User WHERE Username = :username";
    $stmt = $db->prepare($sql);

    // Execute the query with the provided username
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and verify the password
    if ($user && password_verify($password, $user['Password'])) {
        // Set session variable to indicate user is logged in
        $_SESSION['logedin'] = true;
        return true;
    } else {
        // Set error message and indicate failure
        $_SESSION['error_message'] = 'Invalid username or password.';
        return false;
    }
}
