<?php
// public/client/dashboard.php
require_once __DIR__ . '/../../config/db.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$name_user = $_SESSION['name_user'];

// Statistiques
$count_films_notes = 0;
$avg_note = 0;
$films_notes = [];

try {
    // Nombre de films notés
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM film_note WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $count_films_notes = $stmt->fetchColumn();

    // Note moyenne donnée
    $stmt = $pdo->prepare("SELECT AVG(note_value) FROM film_note WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $avg_note = $stmt->fetchColumn();
    $avg_note = $avg_note ? round($avg_note, 1) : 0;

    // Films notés par l'utilisateur
    $stmt = $pdo->prepare("
        SELECT f.id_film, f.title_film, f.name_realisateur, fn.note_value
        FROM film f
        INNER JOIN film_note fn ON f.id_film = fn.id_film
        WHERE fn.id_user = ?
        ORDER BY fn.note_value DESC, f.title_film
        LIMIT 10
    ");
    $stmt->execute([$id_user]);
    $films_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Erreur dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Tableau de Bord — MG Film</title>
    <link rel="stylesheet" href="../../assets/css/variables.css">
    <link rel="stylesheet" href="../../assets/css/base.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
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
        <h1>Mon Tableau de Bord</h1>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Bienvenue, <?= htmlspecialchars($name_user) ?> !</p>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Films Notés</h3>
                <div class="value"><?= $count_films_notes ?></div>
            </div>
            <div class="stat-card">
                <h3>Note Moyenne</h3>
                <div class="value"><?= $avg_note ?>/10</div>
            </div>
        </div>

        <?php if (!empty($films_notes)): ?>
            <section style="margin-top: 40px;">
                <h2 style="margin-bottom: var(--spacing-md);">Mes Films Notés</h2>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Réalisateur</th>
                                <th>Ma Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($films_notes as $film): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($film['title_film']) ?></strong></td>
                                    <td><?= htmlspecialchars($film['name_realisateur']) ?></td>
                                    <td><span style="color: var(--primary-color); font-weight: bold;"><?= $film['note_value'] ?>/10</span></td>
                                    <td><a href="film-detail.php?id=<?= $film['id_film'] ?>" class="btn btn-primary btn-sm">Voir</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php else: ?>
            <section style="margin-top: 40px; text-align: center; padding: 40px; background: #f5f5f5; border-radius: 4px;">
                <p>Vous n'avez pas encore noté de films.</p>
                <a href="films.php" class="btn btn-primary" style="margin-top: 20px;">Découvrir des films</a>
            </section>
        <?php endif; ?>
    </main>

    <footer style="margin-top: 60px; padding: 40px 0; background: var(--secondary-color); color: var(--text-light); text-align: center;">
        <div class="container">
            <p>&copy; 2026 MG Film. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
