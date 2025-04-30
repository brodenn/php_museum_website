<?php

/**
 * Home Page Script
 *
 * This script generates the home page for the website. It features a dynamically selected "Road of the Day"
 * from the 'Roads' table in the database. The selection is based on the day of the year to ensure a different
 * road is featured each day.
 */

include("../config/config.php");
$pageTitle = "Hemsida";
include("../view/header.php");

// Establish a connection to the database
$dsn = $databases[$_SESSION['database']] ?? '';
$db = connectToDatabase($dsn);

// Determine the day of the year to use as a basis for selecting the featured road
$dayOfYear = date('z');

// Fetch the total number of roads from the database to ensure the selection wraps around correctly
$totalRoadss = getNumberOfRowsInTable($db, 'Roads');

// Calculate the index of the road to feature based on the day of the year
$RoadsIndex = ($dayOfYear % $totalRoadss) + 1;

// Fetch the content for the selected road to be featured
$todayRoadsContent = fetchPageContent($db, 'Roads', $RoadsIndex, false);
// Determine the current hour
$currentHour = date('G');

// Determine the greeting based on the current time
if ($currentHour < 12) {
    $greeting = "God morgon och välkommen!";
} elseif ($currentHour < 18) {
    $greeting = "God eftermiddag och välkommen!";
} else {
    $greeting = "God kväll och välkommen!";
}

?>
<main>
    <section>
        <h2 style="text-align: center;"><?= $greeting ?></h2>
        <p style="text-align: center;">Utforska dagens utvalda väg nedan.</p>
    </section>

    <section>
        <?= $todayRoadsContent ?>
    </section>
</main>

<?php include("../view/footer.php");  ?>
