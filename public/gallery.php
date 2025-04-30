<?php

/**
 * Gallery Page Script
 *
 * This script generates a gallery page displaying a set of images. It supports pagination by displaying
 * a limited number of images per page and providing navigation to move between pages of images.
 * Images are stored in a specific directory and are displayed with options to view the previous
 * and next set of images.
 */

include("../config/config.php");
$pageTitle = "Galleri";
include("../view/header.php");

// Define the directory containing the images and fetch all JPG images from it
$directory = "../img/150x150"; // Update path to images
$images = glob($directory . "/*.jpg");
$nrOfImages = count($images);

// Determine the starting image number based on the 'image-page-nr' URL parameter
$startNr = $_GET['image-page-nr'] ?? 0;

// Template for the pagination links
$aTag = '<a href="?image-page-nr=$linkId">$Value</a>';

// Generate the link for the previous set of images
if ($startNr > 0) {
    $replacement['$linkId'] = $startNr - 9;
    $replacement['$Value'] = 'Föregående';
    $previousItemLink = strtr($aTag, $replacement);
} else {
    $previousItemLink = '<div></div>'; // No link if on the first set of images
}

// Generate the link for the next set of images
if ($startNr + 9 < $nrOfImages) {
    $replacement['$linkId'] = $startNr + 9;
    $replacement['$Value'] = 'Nästa';
    $nextItemLink = strtr($aTag, $replacement);
} else {
    $nextItemLink = '<div></div>'; // No link if on the last set of images
}
?>
<main>
    <article class="all-browsers">
        <header class="grid">
            <?= $previousItemLink ?>
            <h1>Bildgalleri</h1>
            <?= $nextItemLink ?>
        </header>
        <div class="gallery-grid">
            <?php
            // Display each image in the current set
            foreach (array_slice($images, $startNr, 9) as $image) {
                $largeImage = '../img/orig/' . basename($image);
                echo <<<IMG
                    <div class="div-content">
                        <figure>
                            <a href="$largeImage">
                                <img src="$image" alt="">
                            </a>
                        </figure>
                    </div>
                IMG;
            }
            ?>
        </div>
    </article>
</main>

<?php include("../view/footer.php"); ?>
