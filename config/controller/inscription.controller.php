<?php
// public/auth/signup.php
session_start();
// Recharge la base de donnée via db.php localisé dans le dossier config
require_once("config/db.php");

// Initialisation des variables
$email       = '';
$name_user   = '';
$date_user   = '';
$age_minimum = 10;
$errors      = [];

// ============ TRAITEMENT INSCRIPTION ============
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'signup') {

    $name_user    = trim($_POST['name_user'] ?? '');
    $date_user    = trim($_POST['date_user'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    // Validations des champs obligatoires et mots de passe
    if (empty($name_user)) $errors[] = "Le nom d'utilisateur est obligatoire.";
    if (empty($email)) $errors[] = "L'adresse email est obligatoire.";
    if (empty($password)) $errors[] = "Le mot de passe est obligatoire.";
    if ($password !== $confirm_pass) $errors[] = "Les mots de passe ne correspondent pas.";
    
    // VERIFICATION DE L'AGE
    if (empty($date_user)) {
        $errors[] = "La date de naissance est obligatoire.";
    } else {
        $date_userObj = DateTime::createFromFormat('Y-m-d', $date_user);
        $dateErrors = DateTime::getLastErrors();

        if (!$date_userObj || $dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0) {
            $errors[] = "Le format de la date de naissance est invalide.";
        } else {
            $today = new DateTime('today');

            if ($date_userObj > $today) {
                $errors[] = "La date de naissance doit être dans le passé.";
            } else {
                $age = $today->diff($date_userObj)->y;

                if ($age < $age_minimum) {
                    $errors[] = "Vous n'avez pas l'âge requis pour pouvoir accéder aux films (minimum $age_minimum ans).";
                }
            }
        }
    }

    // Si aucune erreur (l'âge et les champs sont OK), on traite la base de données
    if (empty($errors)) {
        try {
            // 1. On vérifie si l'email existe déjà
            $check = $pdo->prepare("SELECT id_user FROM users WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                $errors[] = "Cette adresse email est déjà utilisée.";
            } else {
                // 2. Si l'email est libre, on insère le nouvel utilisateur
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name_user, date_user, email, password, role) VALUES (?, ?, ?, ?, 'client')");
                $stmt->execute([$name_user, $date_user, $email, $hash]);

                $_SESSION['id_user']   = (int) $pdo->lastInsertId();
                $_SESSION['name_user'] = htmlspecialchars($name_user, ENT_QUOTES, 'UTF-8');
                $_SESSION['role']      = 'client';

                header("Location: genres.php");
                exit();
            }       
        } catch (PDOException $e) {
            $errors[] = "Une erreur technique est survenue.";
            error_log("[signup.php] Erreur PDO: " . $e->getMessage());
        }
    }
}
?>
