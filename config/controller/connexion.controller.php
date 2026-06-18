<?php
session_start();
require_once 'config/db.php';

// Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: film_page.php");
    exit;
}

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['passwords'] ?? '';

    // Validations basiques
    if (empty($email)) $erreurs[] = "L'adresse email est obligatoire.";
    if (empty($mdp))   $erreurs[] = "Le mot de passe est obligatoire.";

    // Vérification en base
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT id, nom, passwords FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($mdp, $user['passwords'])) {
            // Message volontairement vague pour ne pas indiquer si c'est l'email ou le mdp qui est faux
            $erreurs[] = "Email ou mot de passe incorrect.";
        }
    }

    // Connexion réussie
    if (empty($erreurs)) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];

        header("Location: film_page.php");
        exit;
    }
}
?>