<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../css/<?= $_SESSION['custom_style'] ?? 'style.css' ?>">
    <link rel="icon" href="../img/logo.jpg" type="image/jpeg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0">
</head>

<body>
    <header class="site-header">
        <div class="header-content">
            <a href="../img/logo.jpg" target="_blank" class="site-logo-link">
                <img src="../img/logo.jpg" alt="Nättraby Vägmuseum Logo" class="site-logo">
            </a>
            <h1 class="site-title">Nättraby Vägmuseum</h1>
        </div>
        <?php include("../view/navbar.php"); ?>
    </header>
