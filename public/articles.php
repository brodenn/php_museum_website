<?php

/**
 * Dynamic Article Page
 *
 * This script dynamically generates an article page based on the 'page' parameter
 * in the URL. It fetches article content from a database and displays it alongside
 * sidebar links to other articles. Pagination is handled through URL parameters,
 * allowing users to navigate between articles.
 */

require __DIR__ . "/../config/config.php";
$pageTitle = "Artiklar";
include("../view/header.php");

// Establish a connection to the database
$dsn = $databases[$_SESSION['database']] ?? '';
$db = connectToDatabase($dsn);

// Fetch sidebar links for articles
$asideLinks = getPagesFromTable($db, 'Articles', 'ArticleId');

// Determine the current page based on the 'page' URL parameter
$pageId = $_GET['page'] ?? 1;
$pageId = filter_var($pageId, FILTER_VALIDATE_INT) ? $pageId : 1;

// Determine pagination details
$totalArticles = getNumberOfRowsInTable($db, 'Articles');
$prevPageId = $pageId > 1 ? $pageId - 1 : null;
$nextPageId = $pageId < $totalArticles ? $pageId + 1 : null;

// Fetch the content for the current article
$content = fetchPageContent($db, 'Articles', $pageId, true);
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
                            <a href="?page=<?= htmlspecialchars($link['ArticleId']) ?>" class="<?= ($pageId == $link['ArticleId']) ? 'selected' : '' ?>">
                                <?= htmlspecialchars($link['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </aside>
    </div>

    <?php include("../view/footer.php"); ?>
