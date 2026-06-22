<?php
// public/client/films.php
require_once __DIR__ . '/../../config/db.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user   = $_SESSION['id_user'];
$name_user = $_SESSION['name_user'];
$films     = [];

try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT f.* FROM film f
        INNER JOIN film_genre fg ON f.id_film = fg.id_film
        INNER JOIN user_preference up ON fg.id_genre = up.id_genre
        WHERE up.id_user = ?
        ORDER BY f.id_film DESC
    ");
    $stmt->execute([$id_user]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($films)) {
        $films = $pdo->query("SELECT * FROM film ORDER BY id_film DESC")->fetchAll(PDO::FETCH_ASSOC);
        $is_personalized = false;
    } else {
        $is_personalized = true;
    }
} catch (Exception $e) {
    error_log("Erreur films : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Films — MG Film</title>
    <link rel="stylesheet" href="../../assets/css/variables.css">
    <link rel="stylesheet" href="../../assets/css/base.css">
    <link rel="stylesheet" href="../../assets/css/landing.css">
    <link rel="stylesheet" href="../../assets/icon/fontAwesome/all.min.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <h1><a href="../../index.php" style="text-decoration: none; color: inherit;">MG<span>Film</span></a></h1>
            </div>
            <nav class="navbar-nav">
                <a href="films.php">Films</a>
                <a href="add-film.php" class="btn btn-secondary btn-sm">+ Ajouter un film</a>
                <a href="dashboard.php">Mon Tableau de Bord</a>
                <div class="user-menu">
                    <span><?= htmlspecialchars($name_user) ?></span>
                    <a href="../auth/logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px;">
        <section class="hero-section" style="padding: var(--spacing-xl) 0; text-align: center; background: var(--secondary-color); color: var(--text-light); border-radius: var(--border-radius); margin-bottom: var(--spacing-xl);">
            <h2>Liste des Films</h2>
            <p>
                <?php if (!empty($is_personalized)): ?>
                    Films sélectionnés selon vos genres préférés
                <?php else: ?>
                    Découvrez tous nos films disponibles
                <?php endif; ?>
            </p>
        </section>

        <div class="film-grid">
            <?php if (empty($films)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: var(--spacing-xl);">
                    <p>Aucun film disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($films as $film): ?>
                    <div class="film-card">
                        <div class="film-img-container" style="height: 250px; background: #1a1a1a;">
                            <?php if (!empty($film['img'])): ?>
                                <img src="<?= htmlspecialchars($film['img']) ?>" alt="<?= htmlspecialchars($film['title_film']) ?>" class="film-img">
                            <?php else: ?>
                                <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #333;">🎬</div>
                            <?php endif; ?>
                        </div>
                        <div class="film-content">
                            <h3 class="film-title"><a href="film-detail.php?id=<?= $film['id_film'] ?>" style="text-decoration: none; color: inherit;"><?= htmlspecialchars($film['title_film']) ?></a></h3>
                            <p class="film-meta">
                                Réalisé par <strong><?= htmlspecialchars($film['name_realisateur'] ?? 'Inconnu') ?></strong>
                                <?php if (!empty($film['film_year'])): ?>
                                    (<?= htmlspecialchars($film['film_year']) ?>)
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($film['description'])): ?>
                                <p class="film-description" style="font-size: 0.85rem; color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($film['description']) ?>
                                </p>
                            <?php endif; ?>
                            <a href="film-detail.php?id=<?= $film['id_film'] ?>" class="btn btn-primary btn-sm" style="margin-top: 10px;">Voir détails</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer style="margin-top: 60px; padding: 40px 0; background: var(--secondary-color); color: var(--text-light); text-align: center;">
        <div class="container">
            <p>&copy; 2026 MG Film. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
