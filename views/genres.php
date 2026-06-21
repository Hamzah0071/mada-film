<?php
// views/genres.php
require_once __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.view.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_genres'])) {
    $selected_genres = $_POST['genres'] ?? [];

    if (empty($selected_genres)) {
        $errors[] = "Veuillez sélectionner au moins un genre.";
    } else {
        try {
            $delete = $pdo->prepare("DELETE FROM User_Preference WHERE id_user = ?");
            $delete->execute([$id_user]);

            $insert = $pdo->prepare("INSERT INTO User_Preference (id_user, id_genre) VALUES (?, ?)");
            foreach ($selected_genres as $id_genre) {
                $insert->execute([$id_user, (int)$id_genre]);
            }

            header("Location: film_page.view.php");
            exit();
        } catch (Exception $e) {
            $errors[] = "Erreur lors de l'enregistrement.";
        }
    }
}

try {
    $genres = $pdo->query("SELECT id_genre, name_genre FROM Genre ORDER BY name_genre ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $genres = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos Préférences — MG Film</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="../assets/css/genre.css">
    
</head>
<body>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 600px;">
            <div class="auth-header">
                <h2>Bonjour <?= htmlspecialchars($_SESSION['name_user']) ?> !</h2>
                <p>Quels genres de films préférez-vous ?</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                    <?php foreach ($errors as $err): ?>
                        <p><?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="genres-grid">
                    <?php foreach ($genres as $genre): ?>
                        <div class="genre-item">
                            <input type="checkbox" name="genres[]" value="<?= $genre['id_genre'] ?>" id="genre_<?= $genre['id_genre'] ?>">
                            <label for="genre_<?= $genre['id_genre'] ?>"><?= htmlspecialchars($genre['name_genre']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="submit_genres" class="btn btn-primary" style="width: 100%;">Découvrir mes films</button>
            </form>
        </div>
    </div>
</body>
</html>
