<?php

/**
 * Database Management Page
 *
 * This script provides an interface for managing database records. It displays
 * a list of tables from the database and allows the user to add new records or edit
 * existing ones. It ensures that only logged-in users can access the database management
 * functionalities and redirects unauthenticated users to the login page.
 */

include("../config/config.php");
$pageTitle = "Databashantering";
include("../view/header.php");

// Display and clear session messages
displayAndClearSessionMessage();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['logedin'])) {
    header("Location: login.php");
    exit();
}

// Establish database connection
$dsn = $databases[$_SESSION['database']] ?? '';
if (!$dsn) {
    // Handle missing database connection string
    include("../view/footer.php");
    exit();
}

$db = connectToDatabase($dsn);

echo "<h2>Databashantering</h2>";

// Fetch table and column names from the database
$tables = fetchTableAndColumnNames($dsn);
if (!$tables) {
    echo "Failed to fetch table information from the database.";
    include("../view/footer.php");
    exit();
}

// Iterate through each table and display its records
foreach ($tables as $tableName => $columns) {
    echo "<div class='table-management'>";
    echo "<h3>Table: $tableName</h3>";
    echo "<a href='record-action.php?action=add&table=$tableName' class='btn btn-primary'>Add New Record</a>";

    // Fetch all records from the current table
    $records = fetchAllRecords($db, $tableName);
    if (empty($records)) {
        echo "<p>No records found in $tableName.</p>";
    } else {
        // Render a table for the fetched records
        echo renderTable($records, "record-action.php?action=edit&table=$tableName&key=", 0);
    }
    echo "</div>";
}

include("../view/footer.php");
