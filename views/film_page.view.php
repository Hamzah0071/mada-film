<?php
// views/film_page.view.php
require_once __DIR__ . '/../config/db.php';
session_start();

// Sécurité : redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: inscription.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$name_user = $_SESSION['name_user'];

$films = [];

try {
    // 1. On cherche d'abord les films qui correspondent aux genres préférés de l'utilisateur
    $query_suggestions = "
        SELECT DISTINCT f.* FROM Film f
        INNER JOIN Film_Genre fg ON f.id_film = fg.id_film
        INNER JOIN User_Preference up ON fg.id_genre = up.id_genre
        WHERE up.id_user = ?
        ORDER BY f.id_film DESC
    ";
    
    $stmt = $pdo->prepare($query_suggestions);
    $stmt->execute([$id_user]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mode de secours : Si aucune suggestion n'est trouvée (pas de film dans ses genres), 
    // on affiche tous les films récents de la plateforme
    if (empty($films)) {
        $query_all = "SELECT * FROM Film ORDER BY id_film DESC";
        $films = $pdo->query($query_all)->fetchAll(PDO::FETCH_ASSOC);
        $is_personalized = false;
    } else {
        $is_personalized = true;
    }

} catch (Exception $e) {
    error_log("Erreur lors de la récupération des films : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Mada Film - Vos Films</title>
    <link rel='stylesheet' href='../assets/css/main.css'> 
    <style>
        :root {
            --font-serif: "EB Garamond", serif;
            --font-sans: "Inter", sans-serif;
            --bg-color: #ffffff;
            --text-color: #212529;
            --accent-color: #465367;
            --muted-color: #64748b;
            --card-bg: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            font-family: var(--font-serif);
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }

        /* Header Style */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            border-bottom: 1px solid var(--border-color);
            background-color: #fff;
        }

        header h1 {
            font-size: 1.8rem;
            color: var(--accent-color);
            margin: 0;
        }

        .user-nav {
            font-family: var(--font-sans);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-nav span {
            font-weight: 500;
        }

        .btn-logout {
            color: #ef4444;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn-logout:hover { opacity: 0.8; }

        /* Main Content */
        main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .section-title {
            font-size: 2.2rem;
            margin-bottom: 8px;
            color: #334155;
        }

        .section-subtitle {
            font-family: var(--font-sans);
            font-size: 1rem;
            color: var(--muted-color);
            margin-bottom: 30px;
        }

        /* Grille des Films */
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }

        .movie-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .movie-img-wrapper {
            width: 100%;
            height: 350px;
            background-color: #cbd5e1;
            overflow: hidden;
        }

        .movie-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .movie-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .movie-title {
            font-size: 1.4rem;
            margin-bottom: 5px;
            color: #1e293b;
            line-height: 1.2;
        }

        .movie-meta {
            font-family: var(--font-sans);
            font-size: 0.85rem;
            color: var(--muted-color);
            margin-bottom: 12px;
        }

        .movie-desc {
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 15px;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 40px 20px;
            margin-top: 60px;
            border-top: 1px solid var(--border-color);
            font-family: var(--font-sans);
            font-size: 0.85rem;
            color: var(--muted-color);
        }
    </style>
</head>
<body>

    <header>
        <h1>Mada film</h1>
        <div class='user-nav'>
            <span>Bonjour, <strong><?= htmlspecialchars($name_user) ?></strong></span>
            <a href="genres.php" style="color: var(--accent-color); text-decoration: none;">Ajuster mes goûts</a>
            <a href="deconnexion.php" class="btn-logout">Se déconnecter</a>
        </div>
    </header>

    <main>
        <?php if ($is_personalized): ?>
            <h2 class='section-title'>Inspiré par vos goûts</h2>
            <p class='section-subtitle'>Voici les films correspondant aux genres que vous avez sélectionnés.</p>
        <?php else: ?>
            <h2 class='section-title'>Découvrir des films</h2>
            <p class='section-subtitle'>Voici les dernières nouveautés disponibles sur Mada film.</p>
        <?php endif; ?>

        <div class='movies-grid'>
            <?php if (!empty($films)): ?>
                <?php foreach ($films as $film): ?>
                    <div class='movie-card'>
                        <div class='movie-img-wrapper'>
                            <?php if (!empty($film['img'])): ?>
                                <img src='../assets/img/<?= htmlspecialchars($film['img']) ?>' alt='Affiche de <?= htmlspecialchars($film['title_film']) ?>'>
                            <?php else: ?>
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #94a3b8; font-family: var(--font-sans);">Pas d'affiche</div>
                            <?php endif; ?>
                        </div>
                        <div class='movie-info'>
                            <h3 class='movie-title'><?= htmlspecialchars($film['title_film']) ?></h3>
                            <div class='movie-meta'>
                                <?= htmlspecialchars($film['name_realisateur']) ?> &bull; <?= htmlspecialchars($film['film_year']) ?>
                            </div>
                            <p class='movie-desc'><?= htmlspecialchars($film['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; color: var(--muted-color); font-family: var(--font-sans); padding: 40px 0;">
                    Aucun film n'est disponible pour le moment.
                </p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        &copy; <?= date('Y') ?> Mada film - Tous droits réservés.
    </footer>

</body>
</html>