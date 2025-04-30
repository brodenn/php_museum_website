<?php

/**
 * The About page of the website.
 *
 * This script fetches and displays content for a specific article from the database, specifically article ID 7,
 * which contains information about the museum. It also provides additional information about the creator of the site.
 * The goal is to inform visitors about the museum and the website's background.
 */

// Include configuration and header
include("../config/config.php");
$pageTitle = "Om webbsidan"; // Page title
include("../view/header.php");

// Database connection
$dsn = $databases[$_SESSION['database']] ?? '';
$db = connectToDatabase($dsn);

// Fetch content for article id 7 (specific to the museum)
$articleId = 7; // Explicitly set the article ID to fetch
$articleContent = fetchPageContent($db, 'Articles', $articleId, false); // Fetch the article without pagination

?>

<main>
    <article class="all-browsers">
        <?= $articleContent ?>

        <h2>Om skaparen av sidan</h2>
        <p>
            Jag heter Niklas Brodén och är student på programmet för Webbprogrammering vid Blekinge Tekniska Högskola (BTH). Denna hemsida skapades som ett projekt i kursen Webbteknologier, med fokus på HTML, CSS, och PHP.
        </p>
        <p>
            Projektets mål var att bygga en webbplats för Nättraby Vägmuseum som inte bara fungerar som en informationskälla för besökare men också som ett praktiskt exempel på tillämpad webbutveckling. Genom detta arbete har jag haft möjlighet att fördjupa mina tekniska färdigheter samtidigt som jag bidrar till att bevara och framhäva kulturarvet i Nättraby.
        </p>
        <p>
            Arbetet med webbplatsen har gett mig värdefull erfarenhet i att utveckla tillgängliga och användarvänliga webblösningar. Jag ser fram emot att fortsätta utveckla mina kunskaper och bidra till fler projekt som kombinerar teknik med samhällsnytta.
        </p>
        <p>
            För mer detaljerad information om kodstrukturen och utvecklingsbeslut bakom denna webbplats, vänligen besök sidan <a href="doc.php">Dokumentation</a>.
        </p>
    </article>
</main>

<?php
// Include the footer
include("../view/footer.php");
?>
