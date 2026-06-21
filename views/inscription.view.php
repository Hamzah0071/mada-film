<?php
// views/inscription.view.php
session_start();
require_once __DIR__ . '/../config/db.php';

// Initialisation des variables
$email     = '';
$name_user = '';
$errors    = [];

// ============ TRAITEMENT INSCRIPTION ============
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'signup') {

    $name_user    = trim($_POST['name_user'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (empty($name_user)) $errors[] = "Le nom d'utilisateur est obligatoire.";
    if (empty($email)) $errors[] = "L'adresse email est obligatoire.";
    if (empty($password)) $errors[] = "Le mot de passe est obligatoire.";
    if ($password !== $confirm_pass) $errors[] = "Les mots de passe ne correspondent pas.";

    if (empty($errors)) {
        try {
            $check = $pdo->prepare("SELECT id_user FROM Users WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                $errors[] = "Cette adresse email est déjà utilisée.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO Users (name_user, email, password, role) VALUES (?, ?, ?, 'client')");
                $stmt->execute([$name_user, $email, $hash]);

                $_SESSION['id_user']   = (int) $pdo->lastInsertId();
                $_SESSION['name_user'] = htmlspecialchars($name_user, ENT_QUOTES, 'UTF-8');
                $_SESSION['role']      = 'client';

                header("Location: film_page.view.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Une erreur technique est survenue.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — MG Film</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2 style="color: var(--secondary-color);">MG<span style="color: var(--primary-color);">Film</span></h2>
            <p>Créez votre compte cinéphile</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                <?php foreach ($errors as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="signup">

            <div class="form-group">
                <label for="name_user">Nom d'utilisateur</label>
                <input type="text" id="name_user" name="name_user" class="form-control" value="<?= htmlspecialchars($name_user) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="exemple@mail.com" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmation</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">S'inscrire</button>
        </form>

        <div class="auth-footer">
            Déjà inscrit ? <a href="connexion.view.php">Se connecter</a>
        </div>
    </div>
</div>

</body>
</html>
