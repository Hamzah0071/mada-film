<?php
// controller/film.detail.controller.php

require_once 'config/db.php'; // ton fichier de connexion PDO

$id_film  = (int) $_GET['id'];
$id_user  = (int) $_SESSION['id_user'];

// ── 1. Récupérer le film ────────────────────────────────────────────────────
$stmtFilm = $pdo->prepare("
    SELECT id_film, title_film, name_realisateur, film_year, description, img
    FROM film
    WHERE id_film = :id
");
$stmtFilm->execute([':id' => $id_film]);
$film = $stmtFilm->fetch(PDO::FETCH_ASSOC);

if (!$film) {
    header('Location: index.php');
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
