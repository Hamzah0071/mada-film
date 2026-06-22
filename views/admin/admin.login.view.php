<?php
// views/admin.login.view.php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ? AND role = 'admin'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['name_user'] = $user['name_user'];
                $_SESSION['role'] = 'admin';
                header("Location: ../admin.php");
                exit();
            } else {
                $error = "Accès refusé ou identifiants incorrects.";
            }
        } catch (PDOException $e) {
            $error = "Une erreur technique est survenue.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — MG Film</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body style="background: #111;">

<div class="auth-container">
    <div class="auth-card" style="border-top: 4px solid var(--primary-color);">
        <div class="auth-header">
            <h2 style="color: var(--secondary-color);">MG<span style="color: var(--primary-color);">Film</span></h2>
            <p style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; color: var(--text-muted);">Espace Administration</p>
        </div>

        <?php if (!empty($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email Admin</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="admin@mgfilm.fr" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; background: var(--secondary-color); color: #fff;">Accéder au Panel</button>
        </form>

        <div class="auth-footer">
            <a href="../views/connexion.view.php">Redevenir client</a>
        </div>
    </div>
</div>

</body>
</html>
