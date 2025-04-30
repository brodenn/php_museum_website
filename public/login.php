<?php

/**
 * Login Page
 *
 * This page provides a login form for admin users. Upon submitting their credentials,
 * the form data is sent to 'post-redirect.php' for authentication. The page displays
 * error or notice messages stored in the session, if any exist.
 */

include("../config/config.php");
$pageTitle = "Login-sida";
include("../view/header.php");

?>
<div class="wrap-main">
    <main>
        <article class="all-browsers">
            <header>
                <h1>Inloggningssida för admin</h1> <!-- Page Heading -->
            </header>
            <section class="kmom">
                <?php
                // Display error messages from the session, if any
                if ($_SESSION['error_message'] ?? false) {
                    echo '<p class="error">Fel: ' . $_SESSION['error_message'] . "</p>";
                    unset($_SESSION['error_message']); // Clear the error message after displaying
                }
                // Display notice messages from the session, if any
                if ($_SESSION['notice'] ?? false) {
                    echo '<p class="info">Notis: ' . $_SESSION['notice'] . '</p>';
                    unset($_SESSION['notice']); // Clear the notice message after displaying
                }
                ?>
                <form class="contact" method="post" action="../public/post-redirect.php"> <!-- Updated form action path -->
                    <fieldset>
                        <legend>Ange dina inloggningsuppgifter</legend>
                        <label for="user">Användarnamn: </label>
                        <br>
                        <input type="text" id="user" name="user" required> <!-- Username field -->
                        <br>
                        <label for="pass">Lösenord: </label>
                        <br>
                        <input type="password" id="pass" name="pass" required> <!-- Password field -->
                        <br>
                        <input type="hidden" name="sendingpage" value="login">
                        <input type="submit" name="send" value="Logga in"> <!-- Submit button -->
                    </fieldset>
                </form>
            </section>
        </article>
    </main>
</div>

<?php
include("../view/footer.php");
?>
