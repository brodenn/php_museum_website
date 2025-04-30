<?php

/**
 * Roads Page Script.
 *
 * This script dynamically displays content for roads or special articles based on
 * the 'page' parameter in the query string. If 'page' equals -1, it shows a special
 * article; otherwise, it displays information about a specific road or all roads.
 * It utilizes utility functions to fetch and display the required content from the database.
 *
 * @package ContentDisplay
 */

require __DIR__ . "/../config/config.php";
$pageTitle = "Vägar";
include("../view/header.php");

// Retrieve the Data Source Name from the session and establish a database connection
$dsn = $databases[$_SESSION['database']] ?? '';
$db = connectToDatabase($dsn);

// Retrieve the page ID from the query string, defaulting to 1 if not set
$pageId = $_GET['page'] ?? 1;
$pageId = filter_var($pageId, FILTER_VALIDATE_INT) ? $pageId : 1;

// Decide the content to display based on the page ID
if ($pageId == -1) {
    // Fetch sidebar links and display a special article for 'page=-1'
    $asideLinks = getPagesFromTable($db, 'Roads', 'RoadsId');
    $content = fetchPageContent($db, 'Articles', 2, false);
    $pageTitle = "Specialartikel";
} else {
    // For other page IDs, fetch relevant content and display it
    $asideLinks = getPagesFromTable($db, 'Roads', 'RoadsId');
    $totalRoadss = getNumberOfRowsInTable($db, 'Roads');
    $prevPageId = $pageId > 1 ? $pageId - 1 : null;
    $nextPageId = $pageId < $totalRoadss ? $pageId + 1 : null;
    $content = fetchPageContent($db, 'Roads', $pageId, true);
    $pageTitle = "Dynamiska Vägsidan";
}

?>
    <div class="wrap-main">
        <main class="main-content">
            <?= $content ?>
        </main>
        <aside class="aside-class">
            <nav>
                <ul>
                    <?php foreach ($asideLinks as $link) : ?>
                        <li>
                            <a href="?page=<?= htmlspecialchars($link['RoadsId']) ?>" class="<?= ($pageId == $link['RoadsId']) ? 'selected' : '' ?>">
                                <?= htmlspecialchars($link['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="?page=-1">:: Om museet ::</a></li>
                </ul>
            </nav>
        </aside>
    </div>

    <?php include("../view/footer.php"); ?>
