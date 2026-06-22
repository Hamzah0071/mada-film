<?php
// public/home.php - Page d'accueil publique
session_start();
require_once __DIR__ . '/../config/db.php';

// Récupérer quelques films pour l'affichage
$films = [];
try {
    $stmt = $pdo->query("SELECT * FROM film LIMIT 6");
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Erreur films: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MG Film — Découvrez les meilleurs films</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/landing.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <h1>MG<span>Film</span></h1>
            </div>
            <nav class="navbar-nav">
                <a href="#films">Découvrir</a>
                <a href="auth/login.php" class="btn btn-primary">Se connecter</a>
                <a href="auth/signup.php" class="btn btn-secondary">S'inscrire</a>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <h2>Bienvenue sur MG Film</h2>
                <p>Découvrez, notez et partagez vos films préférés</p>
                <div class="hero-buttons">
                    <a href="auth/signup.php" class="btn btn-primary btn-lg">Commencer</a>
                    <a href="auth/login.php" class="btn btn-secondary btn-lg">Se connecter</a>
                </div>
            </div>
        </section>

        <!-- Films Section -->
        <section id="films" class="films-section">
            <div class="container">
                <h2>Films Populaires</h2>
                <div class="film-grid">
                    <?php if (empty($films)): ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                            <p>Aucun film disponible pour le moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($films as $film): ?>
                            <div class="film-card">
                                <div class="film-img-container">
                                    <?php if (!empty($film['img'])): ?>
                                        <img src="<?= htmlspecialchars($film['img']) ?>" alt="<?= htmlspecialchars($film['title_film']) ?>">
                                    <?php else: ?>
                                        <div class="film-placeholder">🎬</div>
                                    <?php endif; ?>
                                </div>
                                <div class="film-content">
                                    <h3><?= htmlspecialchars($film['title_film']) ?></h3>
                                    <p class="film-director"><?= htmlspecialchars($film['name_realisateur'] ?? 'Inconnu') ?></p>
                                    <?php if (!empty($film['film_year'])): ?>
                                        <p class="film-year"><?= htmlspecialchars($film['film_year']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Prêt à explorer ?</h2>
                <p>Rejoignez notre communauté de cinéphiles</p>
                <a href="auth/signup.php" class="btn btn-primary btn-lg">Créer un compte</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 MG Film. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
