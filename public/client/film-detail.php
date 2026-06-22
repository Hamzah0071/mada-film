<?php
// public/client/film-detail.php
require_once __DIR__ . '/../../config/db.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_film  = (int) $_GET['id'] ?? 0;
$id_user  = $_SESSION['id_user'];
$name_user = $_SESSION['name_user'];

if ($id_film <= 0) {
    header('Location: films.php');
    exit;
}

// ── 1. Récupérer le film ────────────────────────────────────────────────────
$stmtFilm = $pdo->prepare("
    SELECT id_film, title_film, name_realisateur, film_year, description, img
    FROM film
    WHERE id_film = :id
");
$stmtFilm->execute([':id' => $id_film]);
$film = $stmtFilm->fetch(PDO::FETCH_ASSOC);

if (!$film) {
    header('Location: films.php');
    exit;
}

// ── 2. Récupérer les genres du film ────────────────────────────────────────
$stmtGenres = $pdo->prepare("
    SELECT g.id_genre, g.name_genre
    FROM genre g
    INNER JOIN film_genre fg ON fg.id_genre = g.id_genre
    WHERE fg.id_film = :id
    ORDER BY g.name_genre
");
$stmtGenres->execute([':id' => $id_film]);
$genres = $stmtGenres->fetchAll(PDO::FETCH_ASSOC);

// ── 3. Note moyenne + nombre de votes ──────────────────────────────────────
$stmtAvg = $pdo->prepare("
    SELECT AVG(note_value) AS avg_note, COUNT(*) AS nb_votes
    FROM film_note
    WHERE id_film = :id
");
$stmtAvg->execute([':id' => $id_film]);
$row     = $stmtAvg->fetch(PDO::FETCH_ASSOC);
$avgNote = $row['avg_note'] ? round($row['avg_note'], 1) : null;
$nbVotes = (int) $row['nb_votes'];

// ── 4. Note de l'utilisateur connecté ──────────────────────────────────────
$stmtUser = $pdo->prepare("
    SELECT note_value
    FROM film_note
    WHERE id_film = :id_film AND id_user = :id_user
");
$stmtUser->execute([':id_film' => $id_film, ':id_user' => $id_user]);
$userNoteRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
$userNote    = $userNoteRow ? (int) $userNoteRow['note_value'] : null;

// ── 5. Traitement du message flash (après soumission de note) ──────────────
$successMsg = '';
$errorMsg = '';
if (isset($_GET['success'])) {
    $successMsg = 'Votre note a bien été enregistrée !';
}
if (isset($_GET['error'])) {
    $msgs = [
        'invalid' => 'Note invalide (doit être entre 1 et 10).',
        'db'      => 'Une erreur est survenue, veuillez réessayer.',
    ];
    $errorMsg = $msgs[$_GET['error']] ?? 'Une erreur est survenue.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($film['title_film']) ?> — MG Film</title>
    <link rel="stylesheet" href="../../assets/css/variables.css">
    <link rel="stylesheet" href="../../assets/css/base.css">
    <link rel="stylesheet" href="../../assets/css/film-detail.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <h1><a href="../../index.php" style="text-decoration: none; color: inherit;">MG<span>Film</span></a></h1>
            </div>
            <nav class="navbar-nav">
                <a href="films.php">Films</a>
                <a href="dashboard.php">Mon Tableau de Bord</a>
                <div class="user-menu">
                    <span><?= htmlspecialchars($name_user) ?></span>
                    <a href="../auth/logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px;">
        <a href="films.php" style="margin-bottom: 20px; display: inline-block; color: var(--primary-color); text-decoration: none;">← Retour aux films</a>

        <?php if ($successMsg): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMsg): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php endif; ?>

        <div class="film-detail-container">
            <div class="film-detail-image">
                <?php if (!empty($film['img'])): ?>
                    <img src="<?= htmlspecialchars($film['img']) ?>" alt="<?= htmlspecialchars($film['title_film']) ?>">
                <?php else: ?>
                    <div style="height: 400px; display: flex; align-items: center; justify-content: center; background: #1a1a1a; color: #666; font-size: 80px;">🎬</div>
                <?php endif; ?>
            </div>

            <div class="film-detail-content">
                <h1><?= htmlspecialchars($film['title_film']) ?></h1>

                <div class="film-meta-info">
                    <p><strong>Réalisateur:</strong> <?= htmlspecialchars($film['name_realisateur'] ?? 'Inconnu') ?></p>
                    <?php if (!empty($film['film_year'])): ?>
                        <p><strong>Année:</strong> <?= htmlspecialchars($film['film_year']) ?></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($genres)): ?>
                    <div class="film-genres">
                        <strong>Genres:</strong>
                        <div class="genre-list">
                            <?php foreach ($genres as $genre): ?>
                                <span class="genre-badge"><?= htmlspecialchars($genre['name_genre']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="film-rating">
                    <strong>Note Moyenne:</strong>
                    <div class="rating-display">
                        <?php if ($avgNote !== null): ?>
                            <span class="rating-value"><?= number_format($avgNote, 1) ?>/10</span>
                            <span class="rating-votes">(<?= $nbVotes ?> avis)</span>
                        <?php else: ?>
                            <span class="rating-value">Pas encore noté</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($film['description'])): ?>
                    <div class="film-description">
                        <strong>Description:</strong>
                        <p><?= nl2br(htmlspecialchars($film['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de notation -->
                <div class="film-rating-form">
                    <strong>Votre Note:</strong>
                    <form method="POST" action="../../config/controller/film-note.php" style="display: flex; gap: 10px; align-items: center;">
                        <input type="hidden" name="id_film" value="<?= $id_film ?>">
                        
                        <div class="rating-input">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <label class="star-label">
                                    <input type="radio" name="note_value" value="<?= $i ?>" <?= ($userNote === $i ? 'checked' : '') ?> required>
                                    <span class="star">★</span>
                                </label>
                            <?php endfor; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Valider</button>
                    </form>
                    <?php if ($userNote !== null): ?>
                        <p style="margin-top: 10px; font-size: 0.9rem; color: var(--text-muted);">Votre note actuelle: <strong><?= $userNote ?>/10</strong></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer style="margin-top: 60px; padding: 40px 0; background: var(--secondary-color); color: var(--text-light); text-align: center;">
        <div class="container">
            <p>&copy; 2026 MG Film. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
