<?php
// admin/films.php
session_start();
require_once __DIR__ . '/../config/db.php';

// Sécurité : Seul un admin peut accéder
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/auth/login.php");
    exit();
}

$name_user = $_SESSION['name_user'];

// Récupérer tous les films
$films = [];
try {
    $stmt = $pdo->query("
        SELECT f.*, AVG(fn.note_value) as avg_note, COUNT(fn.note_value) as nb_notes 
        FROM film f 
        LEFT JOIN film_note fn ON f.id_film = fn.id_film 
        GROUP BY f.id_film
        ORDER BY f.id_film DESC
    ");
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
    <title>Gestion Films — MG Film Admin</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>MG<span>Film</span></h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php">Tableau de bord</a></li>
                    <li><a href="films.php" class="active">Gestion Films</a></li>
                    <li><a href="users.php">Utilisateurs</a></li>
                    <li><a href="../index.php">Voir le site</a></li>
                    <li><a href="../public/auth/logout.php" style="color: var(--danger);">Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <header style="margin-bottom: var(--spacing-xl); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>Gestion des Films</h1>
                    <p>Gérez tous les films de la plateforme</p>
                </div>
                <a href="film-add.php" class="btn btn-primary">+ Ajouter un film</a>
            </header>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Réalisateur</th>
                            <th>Année</th>
                            <th>Note Moy.</th>
                            <th>Avis</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($films)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">Aucun film trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($films as $film): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($film['title_film']) ?></strong></td>
                                    <td><?= htmlspecialchars($film['name_realisateur']) ?></td>
                                    <td><?= $film['film_year'] ?></td>
                                    <td><span style="color: var(--primary-color); font-weight: bold;"><?= $film['avg_note'] ? number_format($film['avg_note'], 1) : '-' ?></span></td>
                                    <td><?= $film['nb_notes'] ?></td>
                                    <td>
                                        <a href="film-edit.php?id=<?= $film['id_film'] ?>" class="btn btn-primary btn-sm">Éditer</a>
                                        <a href="film-delete.php?id=<?= $film['id_film'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film ?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
