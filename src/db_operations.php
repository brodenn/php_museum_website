<?php

/**
 * Establishes a connection to the database using the provided DSN and returns a PDO Roads.
 * Throws an exception if the connection fails.
 *
 * @param string $dsn The Data Source Name specifying the database connection details.
 * @return PDO The PDO Roads for interacting with the database.
 */
function connectToDatabase(string $dsn): PDO
{
    try {
        $database = new PDO($dsn);
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $database;
    } catch (PDOException $exception) {
        echo "Connection error with DSN: $dsn<br>";
        throw $exception;
    }
}

/**
 * Constructs a SQL query string for fetching records from a specified table with optional filtering and pagination.
 *
 * @param string $tableName The name of the database table from which to fetch records.
 * @param string|null $whereClause An optional SQL WHERE clause for filtering records. Must be properly sanitized.
 * @param int $paginationStart The zero-based index indicating the starting point for pagination.
 * @param int $rowsPerPage The maximum number of rows to return for pagination. If set to 0, all rows are returned.
 * @return string The constructed SQL query string.
 */
function buildQuery($tableName, $whereClause, $paginationStart, $rowsPerPage)
{
    $query = "SELECT * FROM `$tableName`";
    if (!empty($whereClause)) {
        $query .= " $whereClause";
    }
    if ($rowsPerPage > 0) {
        $offset = $paginationStart * $rowsPerPage;
        $query .= " LIMIT $offset, $rowsPerPage";
    }
    return $query;
}

/**
 * Executes a given SQL query using the provided database connection and fetches the resulting records.
 *
 * @param PDO $databaseConnection The PDO database connection object.
 * @param string $query The SQL query string to execute. Must be a properly prepared statement to avoid SQL injection.
 * @return array|string An array of fetched records if the query succeeds, or an error message string on failure.
 * @throws PDOException if there is an error executing the query.
 */

function fetchRecords(PDO $databaseConnection, $query)
{
    try {
        $statement = $databaseConnection->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        return "SQL error: " . $exception->getMessage();
    }
}

/**
 * Fetches data from a specified table in the database, optionally applying filtering and pagination, and formats the data as an HTML table.
 * Validates the table name against the actual database tables to ensure it exists before attempting to fetch data.
 *
 * @param PDO $databaseConnection The PDO database connection object.
 * @param string $tableName The name of the database table to fetch data from.
 * @param int $paginationStart The zero-based index of the page to start fetching rows from, for pagination purposes.
 * @param int $rowsPerPage The maximum number of rows to return, for pagination purposes. Set to 0 to return all rows.
 * @param string|null $whereClause An optional SQL WHERE clause for filtering the rows. Must be properly sanitized.
 * @param int $keyIndex The index of the primary key column, used for generating action links if actions are included.
 * @param bool $addActions Whether to include edit/delete actions in the table for each row.
 * @return string HTML representation of the fetched data as a table, or an error message if the table name is invalid or data fetching fails.
 */
function fetchTableFromDB(PDO $databaseConnection, string $tableName, int $paginationStart = 0, int $rowsPerPage = 0, ?string $whereClause = null, int $keyIndex = 0, bool $addActions = false): string
{
    if (!isValidTableName($databaseConnection, $tableName)) {
        return "Invalid or non-existent table name: $tableName";
    }

    $query = buildQuery($tableName, $whereClause, $paginationStart, $rowsPerPage);
    $records = fetchRecords($databaseConnection, $query);
    if (is_string($records)) {
        return $records; // Early return if SQL error occurred
    }

    return generateHtmlTable($records, $tableName, $keyIndex, $addActions);
}




/**
 * Fetch a table, mark it as a html table, put in the string and return it
 *
 * @param PDO $db Database connection Roads.
 * @param string $tableName Name of the table to fetch column names from.
 * @return array List of column names from the specified table.
 * @throws InvalidArgumentException If the table name is invalid.
 */
function fetchColumnNames(PDO $db, string $tableName): array
{
    // Ensure the table name is valid to prevent SQL injection.
    if (empty($tableName) || !preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
        throw new InvalidArgumentException("Invalid table name: " . $tableName);
    }

    // Construct the query to fetch a single row to minimize data fetched.
    $sql = "SELECT * FROM `$tableName` LIMIT 1";

    // Prepare and execute the SQL statement.
    $stmt = $db->prepare($sql);
    $stmt->execute();

    // Fetch a single row from the table.
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug: Uncomment the following line to log the fetched row's structure for debugging.
    // error_log(print_r($row, true));

    // If no rows are returned, the table might be empty or not exist.
    if (!$row) {
        return [];
    }

    // Return the column names of the fetched row.
    return array_keys($row);
}



/**
 * Retrieves a specific row from a table based on a given key and returns it as an associative array.
 *
 * @param PDO $database Database connection Roads.
 * @param string $tableName Name of the table.
 * @param int $key The value of the key to search for.
 * @param string $keyColumnName Name of the column that acts as a key.
 * @return array Associative array representing the fetched row. If no row is found, returns null.
 */
function fetchRowFromDBasArray(PDO $database, string $tableName, int $key, string $keyColumnName): array
{
    // Validate the table and key column names to prevent SQL injection
    if (empty($tableName) || !preg_match('/^[a-zA-Z0-9_]+$/', $tableName) || empty($keyColumnName) || !preg_match('/^[a-zA-Z0-9_]+$/', $keyColumnName)) {
        throw new InvalidArgumentException("Invalid table name or key column name.");
    }

    // Prepare the SQL statement using named placeholders to avoid SQL injection
    $query = "SELECT * FROM `$tableName` WHERE `$keyColumnName` = :key";
    $statement = $database->prepare($query);

    // Debug: Uncomment the next line to log the query for debugging purposes
    // error_log("Executing query: $query with key: $key");

    // Execute the prepared statement with the provided key value
    $statement->execute([':key' => $key]);

    // Fetch the result as an associative array
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    // Debug: Uncomment the next line to log the fetched row
    // error_log("Fetched row: " . print_r($row, true));

    // Return the fetched row, or null if no row was found
    return $row ? $row : null;
}


/**
 * Retrieves names of all tables and their columns from the database.
 *
 * @param string $dsn The DSN (Data Source Name) for connecting to the database.
 * @return array An associative array with table names as keys and arrays of column names as values.
 */
function fetchTableAndColumnNames(string $dsn): array
{
    // Connect to the database using the provided DSN
    $database = connectToDatabase($dsn);

    // Query to select all table names from the SQLite system table
    $query = "SELECT name FROM sqlite_master WHERE type='table'";

    // Prepare and execute the query
    $statement = $database->prepare($query);
    $statement->execute();

    // Fetch all table names as a single-column array
    $tables = $statement->fetchAll(PDO::FETCH_COLUMN);

    // Debug: Uncomment the next line to log the list of table names
    // error_log("Fetched tables: " . implode(', ', $tables));

    // Initialize an associative array to hold the mapping of table names to column names
    $tableColumnMapping = [];

    // Iterate over each table to fetch its column names
    foreach ($tables as $table) {
        // Fetch column names for the current table and add them to the mapping array
        $tableColumnMapping[$table] = fetchColumnNames($database, $table);

        // Debug: Uncomment the next line to log the fetched column names for the current table
        // error_log("Columns in $table: " . implode(', ', $tableColumnMapping[$table]));
    }

    // Return the mapping of table names to their column names
    return $tableColumnMapping;
}



/**
 * Calculates the total number of rows in a given table.
 *
 * Validates the table name to prevent SQL injection before executing a COUNT query to determine the number of rows.
 * Returns the row count as an integer, or -1 if an error occurs.
 *
 * @param PDO $database Database connection Roads.
 * @param string $tableName Name of the table to count rows in.
 * @return int Total number of rows in the table, or -1 on failure.
 */
function getNumberOfRowsInTable(PDO $database, string $tableName): int
{
    // Validate the table name to prevent SQL injection
    if (empty($tableName) || !preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
        echo "Invalid table name: " . $tableName;
        return -1;
    }

    // Prepare the COUNT query
    $query = "SELECT COUNT(*) FROM `$tableName`";
    try {
        $statement = $database->prepare($query);

        // Debug: Uncomment the next line to log the query for debugging purposes
        // error_log("Executing query: $query");

        $statement->execute();

        // Fetch the row count from the query result
        return (int) $statement->fetchColumn();
    } catch (PDOException $e) {
        // Log the error message for debugging purposes
        echo "Error getting row count for $tableName: {$e->getMessage()}";

        // Return -1 to indicate failure
        return -1;
    }
}



/**
 * Retrieves a list of pages and their titles from a specified table.
 *
 * This function assumes that each page can be identified by a unique column (defaulting to 'RoadsId')
 * and that each page has a 'name' and 'title' associated with it. The function queries the database
 * for these details and returns them in an array of associative arrays.
 *
 * Note: It's essential to validate or sanitize inputs like table and column names if they are coming from user input
 * to prevent SQL injection vulnerabilities.
 *
 * @param PDO $database The database connection Roads.
 * @param string $tableName The table containing page information.
 * @param string $idColumnName The name of the column to use as the identifier, defaults to 'RoadsId'.
 * @return array An array of associative arrays containing page identifiers, names, and titles.
 */
function getPagesFromTable(PDO $database, string $tableName, string $idColumnName = 'RoadsId'): array
{
    // Ensure the table name is valid to prevent SQL injection
    if (empty($tableName) || !preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
        throw new InvalidArgumentException("Invalid table name: $tableName");
    }

    // Construct the SQL query to fetch page identifiers, names, and titles
    $query = "SELECT `$idColumnName`, name, title FROM `$tableName`";

    // Prepare and execute the query
    $statement = $database->prepare($query);

    // Debug: Uncomment the next line to log the query for debugging purposes
    // error_log("Executing query: $query");

    $statement->execute();

    // Fetch all matching records as an array of associative arrays
    $pages = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Uncomment the next line to inspect the fetched pages
    // error_log("Fetched pages: " . print_r($pages, true));

    return $pages;
}



/**
 * Fetches and formats page content from a database table based on pagination and entity type.
 *
 * This function retrieves specific content from a database based on the provided entity type (e.g., Articles or Roads)
 * and page number. It supports pagination by dynamically generating links to the previous and next pages if applicable.
 * The function is designed to be versatile for fetching different types of content by adjusting the base URL path
 * according to the entity type. It formats the fetched row into HTML using a separate function assumed to be named
 * `formatContentAsHtml`.
 *
 * @param PDO $databaseConnection The database connection Roads.
 * @param string $entityType The type of entity to fetch content for, typically a table name.
 * @param int $pageNumber The current page number for which content is to be fetched.
 * @param bool $isPaginated Indicates whether the content fetching supports pagination (default true).
 *
 * @return string HTML formatted content for the specified page and entity type. Includes navigation links for
 *                pagination if applicable.
 */
function fetchPageContent(PDO $databaseConnection, string $entityType, int $pageNumber, bool $isPaginated = true): string
{
    // Debug: Enable the following lines to log the start of the function and the parameters received
    // error_log("Debugging fetchPageContent - Start");
    // error_log("Requested Page ID: $pageNumber");
    // error_log("Entity Type: $entityType");

    // Determine the correct ID column based on the entity type
    $columnIdName = $entityType === 'Articles' ? 'ArticleId' : 'RoadsId';

    if ($isPaginated) {
        // Dynamically calculate total rows based on entity type for pagination
        $totalRows = getNumberOfRowsInTable($databaseConnection, $entityType);
        // Debug: Log total rows
        // error_log("Total Rows: $totalRows");

        // Adjust the base link path based on the entity type
        $baseLinkPath = $entityType === 'Articles' ? 'articles.php?page=' : 'roads.php?page=';
        $prevLinkHtml = $pageNumber > 1 ? "<a href='{$baseLinkPath}" . ($pageNumber - 1) . "'>Previous</a>" : '';
        $nextLinkHtml = $pageNumber < $totalRows ? "<a href='{$baseLinkPath}" . ($pageNumber + 1) . "'>Next</a>" : '';
    } else {
        $prevLinkHtml = '';
        $nextLinkHtml = '';
    }

    // Fetch the content for the specified page number or entity ID
    $query = "SELECT title, data, gps, author, image1, image1Alt, image1Text, image2, image2Alt, image2Text FROM `$entityType` WHERE `$columnIdName` = :pageNumber LIMIT 1";
    $statement = $databaseConnection->prepare($query);
    $statement->bindParam(':pageNumber', $pageNumber, PDO::PARAM_INT);
    $statement->execute();

    $rowData = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$rowData) {
        // Debug: Log if no content found
        // error_log("No content found for Page ID: $pageNumber");
        return "<p>No content available for Page ID: $pageNumber.</p>";
    }

    // Assume formatRowAsHtml is a function that formats the fetched data into HTML
    $formattedHtml = formatRowAsHtml($rowData, $prevLinkHtml, $nextLinkHtml);

    // Debug: Enable the following line to mark the end of the debugging process
    // error_log("Debugging fetchPageContent - End");

    return $formattedHtml;
}


/**
 * Fetches all records from a specified table in the database.
 *
 * Utilizes a PDO database connection to retrieve all rows from a given table and returns them
 * as an associative array. It's crucial to ensure the table name is validated or sanitized before
 * calling this function to prevent SQL injection risks.
 *
 * @param PDO $db The PDO database connection Roads.
 * @param string $tableName The name of the table from which to fetch records.
 * @return array An associative array of all records in the specified table.
 */
function fetchAllRecords(PDO $db, string $tableName): array
{
    // Validate the table name to ensure it's a safe string to prevent SQL injection
    if (empty($tableName) || !preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
        // Consider how to handle invalid table names. Throwing an exception is one approach.
        throw new InvalidArgumentException("Invalid table name: $tableName");
    }

    // Construct the SQL query to select all records from the specified table
    $sql = "SELECT * FROM `$tableName`";

    // Prepare and execute the SQL statement
    $stmt = $db->prepare($sql);

    // Debug: Uncomment the next line to log the query for debugging purposes
    // error_log("Executing query: $sql");

    $stmt->execute();

    // Fetch all rows from the table as an associative array
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Uncomment the next line to log the fetched records
    // error_log("Fetched records: " . print_r($records, true));

    return $records;
}
/**
 * Handles the action to add a new record to the database.
 *
 * This function processes the POST request from a form submission to add a new record to a specified table.
 * It validates required fields, constructs an INSERT SQL statement based on the form data, and executes the statement.
 * If the operation is successful, it redirects the user with a success message; otherwise, it shows an error.
 *
 * @param PDO $db The database connection object.
 * @param string $table The name of the table to which the record will be added.
 * @param array $requiredFields An array of required field names that must be present in the form data.
 */

function handleAddAction(PDO $db, $table, $requiredFields)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $formData = $_POST;
        unset($formData['action'], $formData['table'], $formData['submit']);

        // Validate required fields
        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                redirectWithMessage("manage-database.php", "Missing required field: $field", true);
            }
        }

        $columns = implode(", ", array_keys($formData));
        $placeholders = ":" . implode(", :", array_keys($formData));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $stmt = $db->prepare($sql);
        foreach ($formData as $param => $value) {
            $stmt->bindValue(":$param", $value);
        }
        $stmt->execute();

        redirectWithMessage("manage-database.php", "Record successfully added.");
    }
}

/**
 * Handles the action to edit an existing record in the database.
 *
 * This function processes the POST request from a form submission to update an existing record in a specified table.
 * It validates required fields, constructs an UPDATE SQL statement based on the form data and primary key, and executes the statement.
 * If the operation is successful, it redirects the user with a success message; otherwise, it shows an error.
 *
 * @param PDO $db The database connection object.
 * @param string $table The name of the table where the record will be updated.
 * @param mixed $key The primary key value of the record to update.
 * @param string $primaryKeyColumn The name of the primary key column.
 * @param array $requiredFields An array of required field names that must be present in the form data.
 */

function handleEditAction(PDO $db, $table, $key, $primaryKeyColumn, $requiredFields)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $formData = $_POST;
        unset($formData['action'], $formData['table'], $formData['submit']);

        // Validate required fields
        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                redirectWithMessage("manage-database.php", "Missing required field: $field", true);
            }
        }

        $updates = array_map(fn($col) => "$col = :$col", array_keys($formData));
        $sql = "UPDATE $table SET " . implode(", ", $updates) . " WHERE $primaryKeyColumn = :key";

        $stmt = $db->prepare($sql);
        foreach ($formData as $param => $value) {
            $stmt->bindValue(":$param", $value);
        }
        $stmt->bindValue(':key', $key);
        $stmt->execute();

        redirectWithMessage("manage-database.php", "Record successfully updated.");
    } else {
        // Fetch and display the existing record for editing
        // This part is typically handled by the form display logic before form submission
    }
}
/**
 * Handles the action to delete an existing record from the database.
 *
 * This function processes the request to delete a record from a specified table using its primary key.
 * It constructs a DELETE SQL statement using the primary key value and executes the statement.
 * If the operation is successful, it redirects the user with a success message; otherwise, it shows an error.
 *
 * @param PDO $db The database connection object.
 * @param string $table The name of the table from which the record will be deleted.
 * @param string $primaryKeyColumn The name of the primary key column.
 * @param mixed $key The primary key value of the record to delete.
 */

function handleDeleteAction(PDO $db, $table, $primaryKeyColumn, $key)
{
    if (!empty($key)) {
        $sql = "DELETE FROM $table WHERE $primaryKeyColumn = :key";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':key', $key);
        $stmt->execute();

        redirectWithMessage("manage-database.php", "Record successfully deleted.");
    } else {
        redirectWithMessage("manage-database.php", "Invalid request for deletion.", true);
    }
}
