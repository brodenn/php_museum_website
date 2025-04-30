<?php

/**
 * Script for handling record actions (add, edit, delete) in the admin panel.
 *
 * This script provides functionality for adding, editing, or deleting records
 * in specified database tables. It supports operations on tables with dynamically
 * determined primary keys and required fields. After performing an action, the user
 * is redirected back to the manage-database page with a success or error message.
 *
 */

ob_start(); // Start output buffering
include("../config/config.php"); // Database configuration and connection settings
$pageTitle = "Skriv till databasen"; // Page title
include("../view/header.php"); // Header HTML

displayAndClearSessionMessage(); // Display any session messages and clear them

if (!isset($_SESSION['logedin'])) {
    header("Location: login.php");
    exit();
}

$dsn = $databases[$_SESSION['database']] ?? '';
$db = connectToDatabase($dsn);

$action = $_REQUEST['action'] ?? '';
$table = $_REQUEST['table'] ?? '';
$key = $_REQUEST['key'] ?? '';

if (!isValidTableName($db, $table)) {
    redirectWithMessage("manage-database.php", "Invalid table name: " . htmlspecialchars($table, ENT_QUOTES, 'UTF-8'), true);
}

$primaryKeyColumn = getPrimaryKeyColumn($table);
$requiredFields = getRequiredFields($table);

try {
    switch ($action) {
        case 'add':
            handleAddAction($db, $table, $requiredFields);
            break;
        case 'edit':
            handleEditAction($db, $table, $key, $primaryKeyColumn, $requiredFields);
            break;
        case 'delete':
            handleDeleteAction($db, $table, $primaryKeyColumn, $key);
            break;
        default:
            echo "<p>Invalid action specified.</p>";
    }
} catch (PDOException $e) {
    redirectWithMessage("manage-database.php", "Database error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'), true);
}

if (in_array($action, ['add', 'edit']) && !isset($_POST['submit'])) {
    $allColumns = fetchColumnNames($db, $table);
    $excludeColumns = $action === 'add' ? [] : [$primaryKeyColumn]; // Include primary key for 'edit' but exclude for 'add'
    $record = ($action === 'edit' && !empty($key)) ? fetchRowFromDBasArray($db, $table, (int)$key, $primaryKeyColumn) : array_fill_keys($allColumns, '');
    echo renderDynamicForm($action, $table, $record, $excludeColumns, $primaryKeyColumn, $requiredFields);
}

include("../view/footer.php");
ob_end_flush();
