<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MG-Film — Le cinéma, autrement</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/landing.css">
</head>
<body>

<!-- NAV -->
<nav class="nav">
    <a href="#" class="nav-logo">MG<span>·</span>FILM</a>
    <ul class="nav-links">
        <li><a href="./connexion.view.php">Connexion</a></li>
        <li><a href="./inscription.view.php" class="nav-cta">Commencer</a></li>
    </ul>
</nav>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-film-strip" aria-hidden="true">
        <?= str_repeat('<div class="hero-film-strip-cell"></div>', 140) ?>
    </div>

    <div class="hero-inner">
        <p class="hero-eyebrow">Plateforme cinéma</p>
        <h1 class="hero-title">Le cinéma<br><em>à votre goût</em></h1>
        <p class="hero-sub">Découvrez, notez et explorez des milliers de films. MG-Film adapte chaque sélection à vos préférences.</p>
        <div class="hero-actions">
            <a href="./inscription.view.php" class="btn-primary">▶ Commencer gratuitement</a>
            <a href="./film_page.view.php"  class="btn-ghost">Explorer les films →</a>
        </div>
    </div>

    
</section>


<!-- CTA -->
<!-- <section class="cta-section">
    <h2 class="cta-title">Prêt à explorer<br><em>le cinéma ?</em></h2>
    <p class="cta-sub">Créez votre compte en quelques secondes et commencez dès maintenant.</p>
    <div class="cta-actions">
        <a href="./inscription.view.php" class="btn-primary">Créer un compte gratuit</a>
        <a href="./connexion.view.php"  class="btn-ghost">Se connecter</a>
    </div>
</section> -->

<!-- FOOTER -->
<footer class="footer">
    <span class="footer-logo">MG<span>·</span>FILM</span>
    <span class="footer-copy">© <?= date('Y') ?> MG-Film — Projet scolaire</span>
</footer>

</body>
</html>
