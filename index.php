<?php
// Point d'entrée principal de l'application
session_start();

// Si l'utilisateur est connecté, rediriger vers la liste des films
if (isset($_SESSION['id_user'])) {
    header("Location: client/films.php");
    exit;
}

// Sinon, afficher la page d'accueil publique
require_once __DIR__ . '/public/home.php';
?>
