<?php
// controller/film.note.controller.php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';

$id_user    = (int) $_SESSION['id_user'];
$id_film    = isset($_POST['id_film'])    ? (int) $_POST['id_film']    : 0;
$note_value = isset($_POST['note_value']) ? (int) $_POST['note_value'] : 0;

// Validation
if ($id_film <= 0 || $note_value < 1 || $note_value > 10) {
    header("Location: ../film.detail.php?id=$id_film&error=invalid");
    exit;
}

try {
    // INSERT ou UPDATE (UPSERT) : si la note existe déjà, on la met à jour
    $stmt = $pdo->prepare("
        INSERT INTO film_note (id_user, id_film, note_value)
        VALUES (:id_user, :id_film, :note)
        ON DUPLICATE KEY UPDATE note_value = :note
    ");
    $stmt->execute([
        ':id_user' => $id_user,
        ':id_film' => $id_film,
        ':note'    => $note_value,
    ]);

    header("Location: ../film.detail.php?id=$id_film&success=1");
    exit;

} catch (PDOException $e) {
    header("Location: ../film.detail.php?id=$id_film&error=db");
    exit;
}
