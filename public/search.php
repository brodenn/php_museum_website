<?php

/**
 * Search functionality for the website.
 *
 * This script allows users to search for roads and articles based on a provided search term.
 * It performs a database query to find matching roads and articles, displaying the results
 * to the user. If no search term is provided, it optionally lists all roads and articles.
 *
 * @package SearchFunctionality
 */

require __DIR__ . "/../config/config.php";
$pageTitle = "Sök";
include __DIR__ . "/../view/header.php";

$searchTerm = $_GET['search'] ?? ''; // Retrieve the search term from the query parameters

try {
    $db = connectToDatabase($databases['nvm']); // Establish a connection to the database

    // Prepare the SQL query based on whether a search term has been provided
    if (!empty($searchTerm)) {
        // SQL query to search both Roads and Articles tables for the search term
        $sql = "SELECT 'Roads' AS type, RoadsId AS id, title, author FROM Roads WHERE title LIKE :searchTerm OR author LIKE :searchTerm
                UNION ALL
                SELECT 'Articles' AS type, ArticleId AS id, title, author FROM Articles WHERE title LIKE :searchTerm OR author LIKE :searchTerm";
    } else {
        // SQL to fetch all entries from Roads and Articles when no search term is provided
        $sql = "SELECT 'Roads' AS type, RoadsId AS id, title, author FROM Roads
                UNION ALL
                SELECT 'Articles' AS type, ArticleId AS id, title, author FROM Articles";
    }

    $stmt = $db->prepare($sql);
    if (!empty($searchTerm)) {
        $stmt->bindValue(':searchTerm', "%$searchTerm%");
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage(); // Gracefully handle any database errors
    exit;
}

?>

<main>
    <article class="search-results-container">
        <header>
            <h1>Sökresultat</h1>
        </header>
        <form action="search.php" method="get" class="search-form">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Sök efter vägar och artiklar" class="search-input">
            <input type="submit" value="Sök" class="search-button">
        </form>
        <?php if (!empty($results)) : ?>
            <section class="search-results">
                <h3>Sökträffar</h3>
                <ul class="search-list">
                    <?php foreach ($results as $row) : ?>
                        <li class="search-item">
                            <?php
                            $link = ($row['type'] === 'Roads') ? "roads.php?page=" . htmlspecialchars($row['id']) : "articles.php?page=" . htmlspecialchars($row['id']);
                            ?>
                            <a href="<?= $link ?>" class="search-link">
                                <?= htmlspecialchars($row['title']) ?> - <?= htmlspecialchars($row['author']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php else : ?>
            <p class="no-results">Inga träffar för '<?= htmlspecialchars($searchTerm) ?>'.</p>
        <?php endif; ?>
    </article>
</main>

<?php include __DIR__ . "/../view/footer.php"; ?>
