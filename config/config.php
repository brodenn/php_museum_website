<?php

/**
 * Configuration file for the project, including error reporting,
 * session management, and database connection configurations.
 */

error_reporting(-1); // Report all PHP errors
ini_set('display_errors', 1); // Display errors to the browser - should be turned off in production

// Generate a session name based on the directory name to avoid conflicts
$name = preg_replace('/[^a-z\d]/i', '', __DIR__);
session_name($name);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Adjust base directory to point to the correct location of the SQLite database files
$baseDir = __DIR__ . "/../db/"; // Going up one directory to access db
$overrideDir = "C:/db/nvm/"; // Custom override directory remains the same

// Determine the correct base path depending on the environment
$basePath = ($_SERVER["SERVER_NAME"] === 'www.student.bth.se') ? $baseDir : $overrideDir;

// Update the DSNs to reflect the new paths
$databases = [
    'nvm' => "sqlite:" . $basePath . "nvm.sqlite",
    'credentials' => "sqlite:" . $basePath . "credentials.sqlite"
];

// No changes needed for database selection
$_SESSION['database'] = $_SESSION['database'] ?? 'nvm';

// Update the error log path to point to the logs directory
ini_set('error_log', __DIR__ . '/../logs/php_error.log'); // Adjusted to new 'logs' directory

// Update the include path to reflect the new structure for includes.php
require_once __DIR__ . "/../src/includes.php"; // Adjust path to access src from config

// URI handling remains the same, no adjustments needed
$uriFile = basename($_SERVER['REQUEST_URI']);
$uriFileWithoutQuery = parse_url($uriFile, PHP_URL_PATH);
$uriBasename = basename($uriFileWithoutQuery, '.php');
