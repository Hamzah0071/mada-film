<?php
// public/auth/login.php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Si déjà connecté, rediriger directement
if (isset($_SESSION['id_user'])) {
    header('Location: ../client/films.php');
    exit();
}

$email   = '';
$erreurs = [];

// ============ TRAITEMENT CONNEXION ============
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'signin') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validations de base
    if (empty($email)) {
        $erreurs[] = "L'adresse email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Adresse email invalide.";
    }

    if (empty($password)) {
        $erreurs[] = "Le mot de passe est obligatoire.";
    }

    if (empty($erreurs)) {
        try {
            // Recherche de l'utilisateur dans la table "users"
            $stmt = $pdo->prepare("SELECT id_user, name_user, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Vérification du mot de passe
            if ($user && password_verify($password, $user['password'])) {

                // Régénérer l'ID de session par sécurité
                session_regenerate_id(true);

                // Stockage des informations en session
                $_SESSION['id_user']   = $user['id_user'];
                $_SESSION['name_user'] = htmlspecialchars($user['name_user'], ENT_QUOTES, 'UTF-8');
                $_SESSION['role']      = $user['role'];

                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header('Location: ../../admin/dashboard.php');
                } else {
                    header('Location: ../client/films.php');
                }
                exit();

            } else {
                $erreurs[] = "Identifiants incorrects.";
            }

        } catch (PDOException $e) {
            $erreurs[] = "Une erreur technique est survenue.";
            error_log("[login.php] Erreur PDO: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — MG Film</title>
    <link rel="stylesheet" href="../../assets/css/variables.css">
    <link rel="stylesheet" href="../../assets/css/base.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2 style="color: var(--secondary-color);">MG<span style="color: var(--primary-color);">Film</span></h2>
            <p>Connectez-vous à votre espace cinéma</p>
        </div>

        <?php if (!empty($erreurs)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                <?php foreach ($erreurs as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                Déconnexion réussie.
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="signin">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="exemple@mail.com" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Se connecter</button>
        </form>

        <div class="auth-footer">
            Pas encore de compte ? <a href="signup.php">Créer un compte</a>
        </div>
    </div>
</div>

</body>
</html>
