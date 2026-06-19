<?php
// app/views/genres.php (ou à la racine selon l'agencement de tes fichiers)
require_once __DIR__ . '/../config/db.php'; // Ajuste le chemin vers db.php si besoin
session_start();

// Sécurité : Si l'utilisateur n'est pas connecté, retour à la case départ
if (!isset($_SESSION['id_user'])) {
    header("Location: inscription.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$errors = [];
$success = false;

// ============ ENREGISTREMENT DES PRÉFÉRENCES ============
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_genres'])) {
    $selected_genres = $_POST['genres'] ?? [];

    if (empty($selected_genres)) {
        $errors[] = "S'il te plaît, sélectionne au moins un genre pour que l'on puisse te suggérer des films !";
    } else {
        try {
            // Nettoyer les anciennes préférences au cas où l'utilisateur y revient
            $delete = $pdo->prepare("DELETE FROM User_Preference WHERE id_user = ?");
            $delete->execute([$id_user]);

            // Insérer les nouvelles préférences
            $insert = $pdo->prepare("INSERT INTO User_Preference (id_user, id_genre) VALUES (?, ?)");
            foreach ($selected_genres as $id_genre) {
                $insert->execute([$id_user, (int)$id_genre]);
            }

            // Une fois enregistré, on redirige vers l'accueil ou la page des films
            header("Location: film_page.view.php");
            exit();

        } catch (Exception $e) {
            $errors[] = "Impossible d'enregistrer vos préférences.";
            error_log("Erreur préférences genres: " . $e->getMessage());
        }
    }
}

// ============ RÉCUPÉRATION DES GENRES DEPUIS LA BDD ============
try {
    $query = $pdo->query("SELECT id_genre, name_genre FROM Genre ORDER BY name_genre ASC");
    $genres = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $genres = [];
    error_log("Erreur récupération genres : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos Goûts Cinéma | Mada film</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <style>
        :root {
            --default-font: "EB Garamond", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            --heading-font: "EB Garamond", sans-serif;
            --nav-font: "Inter", sans-serif;
            --background-color: #ffffff; 
            --default-color: #212529; 
            --heading-color: #535353; 
            --accent-color: #465367; 
            --border-color: #e2e8f0;
            --input-bg: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--default-font);
            background-color: var(--background-color);
            color: var(--default-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .genres-wrapper {
            width: 100%;
            max-width: 600px;
            padding: 40px 20px;
            text-align: center;
        }

        .header-section { margin-bottom: 40px; }
        .header-section h1 { 
            font-family: var(--heading-font); 
            font-size: 2.5rem; 
            color: var(--heading-color); 
            font-weight: 500; 
            margin-bottom: 12px; 
        }
        .header-section p { font-size: 1.1rem; color: #64748b; font-weight: 300; }
        .welcome-back { font-family: var(--nav-font); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: var(--accent-color); margin-bottom: 10px; font-weight: 600; }

        /* Grille des genres sous forme de "Tags / Chips" */
        .genres-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
            margin-bottom: 35px;
        }

        .genre-item input[type="checkbox"] { display: none; }

        .genre-item label {
            display: block;
            padding: 14px 10px;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: var(--nav-font);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
            user-select: none;
        }

        .genre-item label:hover {
            border-color: var(--accent-color);
            background-color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        /* Style quand la case est cochée */
        .genre-item input[type="checkbox"]:checked + label {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(70, 83, 103, 0.2);
        }

        .btn-submit {
            width: 100%;
            max-width: 300px;
            padding: 14px;
            background-color: var(--accent-color);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-family: var(--nav-font);
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-submit:hover { background-color: #343e4f; }

        .alert-error {
            color: #721c24; 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            padding: 12px; 
            border-radius: 6px; 
            margin-bottom: 25px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>

    <div class="genres-wrapper">
        <div class="header-section">
            <div class="welcome-back">Ravi de vous compter parmi nous, <?= htmlspecialchars($_SESSION['name_user']) ?> !</div>
            <h1>Qu'aimez-vous regarder ?</h1>
            <p>Sélectionnez vos genres favoris afin de personnaliser vos suggestions de films.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <?php foreach ($errors as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="genres-grid">
                <?php if (!empty($genres)): ?>
                    <?php foreach ($genres as $genre): ?>
                        <div class="genre-item">
                            <input type="checkbox" name="genres[]" value="<?= $genre['id_genre'] ?>" id="genre_<?= $genre['id_genre'] ?>">
                            <label for="genre_<?= $genre['id_genre'] ?>"><?= htmlspecialchars($genre['name_genre']) ?></label>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column: 1/-1; color: #64748b;">Aucun genre configuré en base de données pour le moment.</p>
                <?php endif; ?>
            </div>

            <button type="submit" name="submit_genres" class="btn-submit">Découvrir mes suggestions</button>
        </form>
    </div>

</body>
</html>