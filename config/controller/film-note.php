<?php
// config/controller/film-note.php
session_start();
require_once __DIR__ . '/../db.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: ../../public/auth/login.php");
    exit;
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../public/client/films.php");
    exit;
}

$id_film = (int) ($_POST['id_film'] ?? 0);
$note_value = (int) ($_POST['note_value'] ?? 0);
$id_user = $_SESSION['id_user'];

// Valider les données
if ($id_film <= 0 || $note_value < 1 || $note_value > 10) {
    header("Location: ../../public/client/film-detail.php?id=$id_film&error=invalid");
    exit;
}

try {
    // Vérifier si le film existe
    $stmt = $pdo->prepare("SELECT id_film FROM film WHERE id_film = ?");
    $stmt->execute([$id_film]);
    if (!$stmt->fetch()) {
        header("Location: ../../public/client/films.php");
        exit;
    }

    // Vérifier si l'utilisateur a déjà noté ce film
    $stmt = $pdo->prepare("SELECT note_value FROM film_note WHERE id_film = ? AND id_user = ?");
    $stmt->execute([$id_film, $id_user]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Mettre à jour la note existante
        $stmt = $pdo->prepare("UPDATE film_note SET note_value = ? WHERE id_film = ? AND id_user = ?");
        $stmt->execute([$note_value, $id_film, $id_user]);
    } else {
        // Insérer une nouvelle note
        $stmt = $pdo->prepare("INSERT INTO film_note (id_film, id_user, note_value) VALUES (?, ?, ?)");
        $stmt->execute([$id_film, $id_user, $note_value]);
    }

    header("Location: ../../public/client/film-detail.php?id=$id_film&success=1");
    exit;

} catch (PDOException $e) {
    error_log("[film-note.php] Erreur PDO: " . $e->getMessage());
    header("Location: ../../public/client/film-detail.php?id=$id_film&error=db");
    exit;
}
?>
