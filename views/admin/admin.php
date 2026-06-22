<?php
// admin.php
session_start();
require_once __DIR__ . '/config/db.php';

// Sécurité : Seul un admin peut accéder
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: views/admin.login.view.php");
    exit();
}

// Statistiques
$count_films = $pdo->query("SELECT COUNT(*) FROM Film")->fetchColumn();
$count_users = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'client'")->fetchColumn();
$avg_note = $pdo->query("SELECT AVG(note_value) FROM Film_Note")->fetchColumn();

// Liste des films avec leurs notes moyennes
$films = $pdo->query("
    SELECT f.*, AVG(fn.note_value) as avg_note, COUNT(fn.note_value) as nb_notes 
    FROM Film f 
    LEFT JOIN Film_Note fn ON f.id_film = fn.id_film 
    GROUP BY f.id_film
    ORDER BY f.id_film DESC
")->fetchAll();

// Liste des derniers clients
$clients = $pdo->query("SELECT * FROM Users WHERE role = 'client' ORDER BY id_user DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin — MG Film</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>MG<span>Film</span></h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="#" class="active">Tableau de bord</a></li>
                    <li><a href="#">Gestion Films</a></li>
                    <li><a href="#">Utilisateurs</a></li>
                    <li><a href="../film_page.view.php">Voir le site</a></li>
                    <li><a href="deconnexion.php" style="color: var(--danger);">Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <header style="margin-bottom: var(--spacing-xl);">
                <h1>Tableau de Bord</h1>
                <p>Bienvenue dans votre espace de gestion.</p>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Films</h3>
                    <div class="value"><?= $count_films ?></div>
                </div>
                <div class="stat-card">
                    <h3>Clients</h3>
                    <div class="value"><?= $count_users ?></div>
                </div>
                <div class="stat-card">
                    <h3>Note Moyenne</h3>
                    <div class="value"><?= number_format($avg_note, 1) ?>/10</div>
                </div>
            </div>

            <section>
                <h2 style="margin-bottom: var(--spacing-md);">Liste des Films</h2>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Réalisateur</th>
                                <th>Année</th>
                                <th>Note Moy.</th>
                                <th>Avis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($films as $film): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($film['title_film']) ?></strong></td>
                                    <td><?= htmlspecialchars($film['name_realisateur']) ?></td>
                                    <td><?= $film['film_year'] ?></td>
                                    <td><span style="color: var(--primary-color); font-weight: bold;"><?= $film['avg_note'] ? number_format($film['avg_note'], 1) : '-' ?></span></td>
                                    <td><?= $film['nb_notes'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section style="margin-top: var(--spacing-xl);">
                <h2 style="margin-bottom: var(--spacing-md);">Derniers Clients</h2>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td><?= htmlspecialchars($client['name_user']) ?></td>
                                    <td><?= htmlspecialchars($client['email']) ?></td>
                                    <td><span class="badge badge-client"><?= $client['role'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
