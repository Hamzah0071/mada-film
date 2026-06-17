Thanks for downloading this template!

Template Name: ZenBlog
Template URL: https://bootstrapmade.com/zenblog-bootstrap-blog-template/
Author: BootstrapMade.com
License: https://bootstrapmade.com/license/

<?php
// app/auth/Sign.php
require_once  './config/db.php';
session_start();

// Détection intelligente du chemin de base
function getBasePath() {
    $currentPath = $_SERVER['SCRIPT_NAME'];
    $pathParts = explode('/', trim($currentPath, '/'));
    if (($pathParts[count($pathParts) - 2] ?? '') === 'auth') {
        return '../../public/';
    }
    return '../public/';
}
$basePath = getBasePath();

$message = '';
$errors = [];
$messageType = ''; // 'signin' ou 'signup'
$showSignUp = false; // Afficher formulaire inscription par défaut

// Initialisation des champs
$email = $nom = $prenom = $date_naissance = $sexe = $telephone = $adresse = $role = '';

// ============ TRAITEMENT INSCRIPTION ============
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'signup') {
   
    $email         = trim($_POST['email'] ?? '');
    $password      = $_POST['password'] ?? '';
    $confirm_pass  = $_POST['confirm_password'] ?? '';
    $nom           = trim($_POST['nom'] ?? '');
    $prenom        = trim($_POST['prenom'] ?? '');
    $date_naissance= trim($_POST['date_naissance'] ?? '');
    $sexe          = $_POST['sexe'] ?? '';
    $telephone     = trim($_POST['telephone'] ?? '');
    $adresse       = trim($_POST['adresse'] ?? '');
    $role          = $_POST['role'] ?? '';
    $terms         = isset($_POST['terms']);
    $showSignUp    = true;

    // Validations
    if (empty($email))          $errors[] = "L'adresse email est obligatoire.";
    if (empty($password))       $errors[] = "Le mot de passe est obligatoire.";
    if (empty($nom))            $errors[] = "Le nom est obligatoire.";
    if (empty($prenom))         $errors[] = "Le prénom est obligatoire.";
    if (empty($date_naissance)) $errors[] = "La date de naissance est obligatoire.";
    if (empty($sexe))           $errors[] = "Le sexe est obligatoire.";
    if (empty($role))           $errors[] = "Le rôle est obligatoire.";
    if (!$terms)                $errors[] = "Vous devez accepter les conditions d'utilisation.";

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }
    if ($role && !in_array($role, ['eleve', 'prof'])) {
        $errors[] = "Rôle invalide.";
    }
    if ($password && strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if ($password !== $confirm_pass) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    if ($sexe && !in_array($sexe, ['M', 'F'])) {
        $errors[] = "Sexe invalide.";
    }
    if ($telephone && !preg_match('/^[\d\s\+\-\(\)]{9,18}$/', $telephone)) {
        $errors[] = "Format de téléphone invalide.";
    }
    if ($date_naissance) {
        $date = DateTime::createFromFormat('Y-m-d', $date_naissance);
        if (!$date || $date->format('Y-m-d') !== $date_naissance) {
            $errors[] = "Format de date invalide.";
        } else {
            $age = (new DateTime())->diff($date)->y;
            if ($age < 5 || $age > 100) {
                $errors[] = "L'âge doit être compris entre 5 et 100 ans.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND deleted_at IS NULL");
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = "Cette adresse email est déjà utilisée.";
            } else {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("
                    INSERT INTO personnes (nom, prenom, date_naissance, sexe, telephone, adresse)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$nom, $prenom, $date_naissance, $sexe, $telephone ?: null, $adresse ?: null]);
                $personne_id = $pdo->lastInsertId();

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO utilisateurs (personne_id, email, password_hash, role, statut)
                    VALUES (?, ?, ?, ?, 'en_attente')
                ");
                $stmt->execute([$personne_id, $email, $hash, $role]);
                $utilisateur_id = $pdo->lastInsertId();

                if ($role === 'eleve') {
                    $stmt = $pdo->prepare("
                        INSERT INTO eleves (utilisateur_id, personne_id, date_inscription)
                        VALUES (?, ?, CURDATE())
                    ");
                    $stmt->execute([$utilisateur_id, $personne_id]);
                } elseif ($role === 'prof') {
                    $stmt = $pdo->prepare("
                        INSERT INTO professeurs (utilisateur_id, personne_id)
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$utilisateur_id, $personne_id]);
                }

                $pdo->commit();

                $message = "<div class='alert alert-success'>
                    <i class='fa-solid fa-circle-check'></i>
                    <strong>Inscription réussie !</strong>
                    <p>Votre compte a été créé et est en attente de validation.</p>
                </div>";
                $messageType = 'signup';

                $email = $nom = $prenom = $date_naissance = $sexe = $telephone = $adresse = $role = '';
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = "Une erreur technique est survenue.";
            error_log("Erreur inscription: " . $e->getMessage());
        }
    }

    if (!empty($errors)) {
        $message = "<div class='alert alert-error'>
            <i class='fa-solid fa-circle-exclamation'></i>
            <strong>Erreurs :</strong>
            <ul>";
        foreach ($errors as $err) {
            $message .= "<li>" . htmlspecialchars($err) . "</li>";
        }
        $message .= "</ul></div>";
        $messageType = 'signup';
    }
}

// ============ TRAITEMENT CONNEXION ============
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'signin') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $showSignUp = false;

    if (empty($email))    $errors[] = "L'adresse email est obligatoire.";
    if (empty($password)) $errors[] = "Le mot de passe est obligatoire.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                SELECT u.*, p.nom, p.prenom
                FROM utilisateurs u
                JOIN personnes p ON u.personne_id = p.id
                WHERE u.email = ? AND u.deleted_at IS NULL
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {

                if ($user['statut'] === 'en_attente') {
                    $errors[] = "Votre compte est en attente de validation.";
                } elseif ($user['statut'] === 'inactif') {
                    $errors[] = "Votre compte a été désactivé.";
                } else {
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_nom']  = $user['nom'] . ' ' . $user['prenom'];

                    switch ($user['role']) {
                        case 'admin':
                            header('Location: ../admin/eleves/liste-eleve.php');
                            break;
                        case 'prof':
                            header('Location: ../prof/mes-classes.php');
                            break;
                        case 'eleve':
                            header('Location: ../eleve/eleve_accueil.php');
                            break;
                        case 'parent':
                            header('Location: ../eleve/eleve_accueil.php');
                            break;
                        default:
                            header('Location: ../auth/Sign.php');
                            break;
                    }
                    exit;
                }
            } else {
                $errors[] = "Email ou mot de passe incorrect.";
            }
        } catch (Exception $e) {
            $errors[] = "Une erreur est survenue.";
            error_log("Erreur connexion: " . $e->getMessage());
        }
    }

    if (!empty($errors)) {
        $message = "<div class='alert alert-error'>
            <i class='fa-solid fa-circle-exclamation'></i>
            <strong>Erreur :</strong>
            <ul>";
        foreach ($errors as $err) {
            $message .= "<li>" . htmlspecialchars($err) . "</li>";
        }
        $message .= "</ul></div>";
        $messageType = 'signin';
    }
}
?>