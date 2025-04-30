<?php

/**
 * Admin Panel Script
 *
 * Provides administrative functionalities for managing the application's database.
 * It includes security checks to ensure only logged-in users can access the panel.
 * Key features include the ability to restore the database from a backup file.
 *
 * Usage:
 * - Verifies if a user is logged in; redirects to the login page if not.
 * - Checks for the selected database and redirects if none is selected.
 * - Handles database connection errors gracefully.
 * - Offers a database restoration feature, which is triggered by a POST request.
 * - Utilizes JavaScript for user confirmation before database restoration.
 */

require_once("../config/config.php");
$pageTitle = "Adminpanel";
require_once("../view/header.php");

// Check if the user is logged in
if (!isset($_SESSION['logedin'])) {
    $_SESSION['error_message'] = 'Var god logga in först!';
    header("Location: login.php");
    exit();
}

// Ensure a database is selected
if (empty($_SESSION['database'])) {
    $_SESSION['error_message'] = "Ingen databas är vald.";
    header("Location: select-database.php");
    exit();
}

// Attempt to connect to the selected database
$dsn = $databases[$_SESSION['database']] ?? '';

try {
    $db = connectToDatabase($dsn);
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Databasanslutningsfel: " . $e->getMessage();
    header("Location: admin.php");
    exit();
}

// Handle database restoration if requested
if (isset($_POST['restore']) && $_POST['restore'] === 'true') {
    $sqlFilePath = __DIR__ . "/../db/nvm.sqllite";

    try {
        $sql = file_get_contents($sqlFilePath);
        $db->exec($sql);
        $_SESSION['success_message'] = "Databasen återställdes framgångsrikt!";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Återställning av databasen misslyckades: " . $e->getMessage();
    }

    header("Location: admin.php");
    exit();
}

displayAndClearSessionMessage();
?>

<script>
function confirmRestore() {
    return confirm("Är du säker på att du vill återställa databasen? Detta kan inte ångras.");
}
</script>

<main>
    <article>
        <header>
            <h2>Adminpanel</h2>
        </header>
        <section class="manage-database" style="text-align: center; margin-top: 20px;">
            <a href="manage-database.php" class="btn" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 20px;">Hantera Databas</a>
            <form action="admin.php" method="POST" style="display: inline;" onsubmit="return confirmRestore();">
                <input type="hidden" name="restore" value="true">
                <button type="submit" class="btn" style="background-color: #ffc107; color: black; padding: 10px 20px; border: none; border-radius: 5px;">Återställ Databas</button>
            </form>
        </section>
    </article>
</main>

<?php require_once("../view/footer.php"); ?>
