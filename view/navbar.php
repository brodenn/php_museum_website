<nav class="navbar">
    <a href="home.php" class='<?= $uriFile == "home.php" ? "selected" : null ?>'>Hem</a>
    <a href="roads.php?page=-1" class='<?= $uriFile == "roads.php" && isset($_GET['page']) && $_GET['page'] == '-1' ? "selected" : null ?>'>Vägar</a>
    <a href="articles.php" class='<?= $uriFile == "articles.php" ? "selected" : null ?>'>Artiklar</a>
    <a href="gallery.php" class='<?= $uriFile == "gallery.php" ? "selected" : null ?>'>Galleri</a>
    <a href="about.php" class='<?= $uriFile == "about.php" ? "selected" : null ?>'>Om</a>
    <a href="search.php" class='<?= $uriFile == "search.php" ? "selected" : null ?>'>Sök</a>
    <?php if (isset($_SESSION['logedin']) && $_SESSION['logedin']) : ?>
        <a href="admin.php" class='<?= $uriFile == "admin.php" ? "selected" : null ?>'>Admin</a>
        <a href="logout.php" class='<?= preg_match("/^logout.php/", $uriFile) ? "selected" : null ?>'>Logga ut</a>
    <?php else : ?>
        <a href="login.php" class='<?= preg_match("/^login.php/", $uriFile) ? "selected" : null ?>'>Logga in</a>
    <?php endif; ?>
</nav>
