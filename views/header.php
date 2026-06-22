<?php
// views/header.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — MG Film' : 'MG Film — Cinéma & Critiques' ?></title>
    
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <!-- CSS Pur Organisé -->
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    
    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>

<body>
    <header class="main-header">
        <div class="container d-flex justify-between align-center">
            <a href="../views/film_page.view.php" class="logo">
                <h1>MG<span>Film</span></h1>
            </a>

            <nav class="nav-links">
                <a href="../views/film_page.view.php">Accueil</a>
                <a href="#">Genres</a>
                <a href="#">Contact</a>
                
            </nav>

            <div class="user-profile">
                <?php if (isset($_SESSION['name_user'])): ?>
                    <div class="avatar">
                        <?= strtoupper(substr($_SESSION['name_user'], 0, 1)) ?>
                    </div>
                    <span class="user-name"><?= htmlspecialchars($_SESSION['name_user']) ?></span>
                <?php endif; ?>
                <a href="../deconnexion.php" class="btn btn-outline">Déconnexion</a>
            </div>
        </div>
    </header>
