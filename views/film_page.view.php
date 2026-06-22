<?php
// views/film_page.view.php
require_once __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../inscription.php");
    exit();
}

$id_user   = $_SESSION['id_user'];
$name_user = $_SESSION['name_user'];
$films     = [];

try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT f.* FROM Film f
        INNER JOIN Film_Genre fg ON f.id_film = fg.id_film
        INNER JOIN User_Preference up ON fg.id_genre = up.id_genre
        WHERE up.id_user = ?
        ORDER BY f.id_film DESC
    ");
    $stmt->execute([$id_user]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($films)) {
        $films = $pdo->query("SELECT * FROM Film ORDER BY id_film DESC")->fetchAll(PDO::FETCH_ASSOC);
        $is_personalized = false;
    } else {
        $is_personalized = true;
    }
} catch (Exception $e) {
    error_log("Erreur films : " . $e->getMessage());
}

$page_title = 'Films';
require_once __DIR__ . '/header.php';
?>

<main class="container">
    <section class="hero-section" style="padding: var(--spacing-xl) 0; text-align: center; background: var(--secondary-color); color: var(--text-light); border-radius: var(--border-radius); margin-top: var(--spacing-md);">
        
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
                        <h3 class="film-title"><?= htmlspecialchars($film['title_film']) ?></h3>
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
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
