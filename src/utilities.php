<?php

/**
 * Validates if the given table name exists in the database.
 *
 * @param PDO $db The database connection Roads.
 * @param string $tableName The table name to validate.
 * @return bool True if valid, false otherwise.
 */
function isValidTableName(PDO $db, string $tableName): bool
{
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return in_array($tableName, $tables);
}

/**
 * Displays success or error messages stored in the session, then clears them.
 *
 * This function should be called at the beginning of a page load to show messages from previous operations.
 * It checks for 'success_message' and 'error_message' in the session, displays them as bootstrap alerts,
 * and then clears the session variables.
 */
function displayAndClearSessionMessage()
{
    if (isset($_SESSION['success_message'])) {
        echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
        unset($_SESSION['error_message']);
    }
}

/**
 * Redirects to a specified URL with a message stored in the session.
 *
 * This function sets a session variable for a success or error message and then redirects the user to a given URL.
 * The message will be available on the next page load and can be displayed to the user.
 *
 * @param string $url The URL to redirect to.
 * @param string $message The message to display on the next page.
 * @param bool $isError Whether the message is an error message. Defaults to false.
 */
function redirectWithMessage($url, $message, $isError = false)
{
    $sessionKey = $isError ? 'error_message' : 'success_message';
    $_SESSION[$sessionKey] = $message;
    header("Location: $url");
    exit();
}

/**
 * Retrieves the name of the primary key column for a given table.
 *
 * This function provides a mapping between table names and their primary key column names.
 * If the table name is not found in the predefined list, it defaults to 'id'.
 *
 * @param string $table The name of the table for which the primary key column name is requested.
 * @return string The name of the primary key column.
 */
function getPrimaryKeyColumn($table)
{
    $primaryKeyMapping = [
        'Roads' => 'RoadsId',
        'Articles' => 'ArticleId',
    ];
    return $primaryKeyMapping[$table] ?? 'id';
}

/**
 * Retrieves an array of required field names for a given table.
 *
 * This function defines required fields for specified tables. If a table has required fields,
 * they are returned as an array. If no specific required fields are defined for the table, an empty array is returned.
 *
 * @param string $table The name of the table.
 * @return array An array of required field names for the table.
 */
function getRequiredFields($table)
{
    $reqFieldsMap = [
        'Roads' => ['name', 'title', 'RoadsId'],
        'Articles' => ['name', 'title', 'ArticleId'],
    ];
    return $reqFieldsMap[$table] ?? [];
}
