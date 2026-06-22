<?php
// public/client/add-film.php
require_once __DIR__ . '/../../config/db.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$name_user = $_SESSION['name_user'];
$errors = [];
$success = false;

// Récupérer tous les genres
$genres = [];
try {
    $stmt = $pdo->query("SELECT id_genre, name_genre FROM genre ORDER BY name_genre");
    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Erreur genres: " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title_film = trim($_POST['title_film'] ?? '');
    $name_realisateur = trim($_POST['name_realisateur'] ?? '');
    $film_year = (int) ($_POST['film_year'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $img = trim($_POST['img'] ?? '');
    $selected_genres = $_POST['genres'] ?? [];

    // Validations
    if (empty($title_film)) $errors[] = "Le titre du film est obligatoire.";
    if (empty($name_realisateur)) $errors[] = "Le nom du réalisateur est obligatoire.";
    if ($film_year < 1888 || $film_year > 2100) $errors[] = "L'année doit être entre 1888 et 2100.";
    if (empty($selected_genres)) $errors[] = "Sélectionnez au moins un genre.";

    if (empty($errors)) {
        try {
            // Insérer le film
            $stmt = $pdo->prepare("
                INSERT INTO film (title_film, name_realisateur, film_year, description, img)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title_film, $name_realisateur, $film_year, $description, $img]);
            $id_film = $pdo->lastInsertId();

            // Ajouter les genres
            $stmt = $pdo->prepare("INSERT INTO film_genre (id_film, id_genre) VALUES (?, ?)");
            foreach ($selected_genres as $id_genre) {
                $stmt->execute([$id_film, (int) $id_genre]);
            }

            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Une erreur technique est survenue.";
            error_log("[add-film.php] Erreur PDO: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Film — MG Film</title>
    <link rel="stylesheet" href="../../assets/css/variables.css">
    <link rel="stylesheet" href="../../assets/css/base.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
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

    <main class="container" style="padding-top: 40px; max-width: 600px;">
        <a href="films.php" style="margin-bottom: 20px; display: inline-block; color: var(--primary-color); text-decoration: none;">← Retour aux films</a>

        <div class="auth-card" style="margin-top: 20px;">
            <div class="auth-header">
                <h2>Ajouter un Film</h2>
                <p>Partagez vos films préférés</p>
            </div>

            <?php if ($success): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    Film ajouté avec succès ! <a href="films.php">Voir la liste</a>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                    <?php foreach ($errors as $err): ?>
                        <p><?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="title_film">Titre du Film *</label>
                    <input type="text" id="title_film" name="title_film" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="name_realisateur">Réalisateur *</label>
                    <input type="text" id="name_realisateur" name="name_realisateur" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="film_year">Année *</label>
                    <input type="number" id="film_year" name="film_year" class="form-control" min="1888" max="2100" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="img">URL de l'Image</label>
                    <input type="url" id="img" name="img" class="form-control" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label>Genres *</label>
                    <div style="border: 1px solid #ddd; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto;">
                        <?php foreach ($genres as $genre): ?>
                            <label style="display: block; margin-bottom: 8px;">
                                <input type="checkbox" name="genres[]" value="<?= $genre['id_genre'] ?>">
                                <?= htmlspecialchars($genre['name_genre']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Ajouter le Film</button>
            </form>
        </div>
    </main>

    <footer style="margin-top: 60px; padding: 40px 0; background: var(--secondary-color); color: var(--text-light); text-align: center;">
        <div class="container">
            <p>&copy; 2026 MG Film. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
