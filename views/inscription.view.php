<?php
// app/auth/Sign.php
require_once __DIR__ . '/../config/db.php';
session_start();

// Initialisation des variables basées sur ta table SQL "Users"
$email = $name_user = '';
$errors = [];
$message = '';
$messageType = '';

// ============ TRAITEMENT INSCRIPTION ============
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'signup') {
   
    $name_user    = trim($_POST['name_user'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';
    $terms        = isset($_POST['terms']);

    // Validations obligatoires
    if (empty($name_user)) $errors[] = "Le nom d'utilisateur est obligatoire.";
    if (empty($email))     $errors[] = "L'adresse email est obligatoire.";
    if (empty($password))  $errors[] = "Le mot de passe est obligatoire.";
    if (!$terms)           $errors[] = "Vous devez accepter les conditions d'utilisation.";

    // Validation du format Email
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    // Validation du mot de passe
    if ($password && strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if ($password !== $confirm_pass) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        try {
            // Vérification de l'unicité de l'email (Table: Users / Colonne: email)
            $check = $pdo->prepare("SELECT id_user FROM Users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = "Cette adresse email est déjà utilisée.";
            } else {
                // Hachage sécurisé du mot de passe
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Insertion directe dans la table Users (Pas besoin de transaction car une seule table)
                $stmt = $pdo->prepare("
                    INSERT INTO Users (name_user, email, password, role)
                    VALUES (?, ?, ?, 'client')
                ");
                $stmt->execute([$name_user, $email, $hash]);

                $message = "<div class='alert alert-success' style='color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 6px; margin-bottom: 20px;'>
                    <strong>Inscription réussie !</strong>
                    <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.</p>
                </div>";
                $messageType = 'signup';

                // Vider les champs après le succès
                $email = $name_user = '';
            }
        } catch (Exception $e) {
            $errors[] = "Une erreur technique est survenue.";
            error_log("Erreur inscription: " . $e->getMessage());
        }
    }

    if (!empty($errors)) {
        $message = "<div class='alert alert-error' style='color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 6px; margin-bottom: 20px;'>
            <strong>Erreurs :</strong>
            <ul style='margin-left: 20px; margin-top: 5px;'>";
        foreach ($errors as $err) {
            $message .= "<li>" . htmlspecialchars($err) . "</li>";
        }
        $message .= "</ul></div>";
        $messageType = 'signup';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | Mada film</title>
    
    <style>
        :root {
            --default-font: "EB Garamond", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            --heading-font: "EB Garamond", sans-serif;
            --nav-font: "Inter", sans-serif;
            --background-color: #ffffff; 
            --default-color: #212529; 
            --heading-color: #535353; 
            --accent-color: #465367; 
            --surface-color: #ffffff; 
            --contrast-color: #ffffff;
            --border-color: #e2e8f0;
            --input-bg: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--default-font);
            background-color: var(--background-color);
            color: var(--default-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-wrapper { width: 100%; max-width: 450px; padding: 40px 20px; }
        .brand-header { text-align: center; margin-bottom: 40px; }
        .brand-header h1 { font-family: var(--heading-font); font-size: 2.5rem; color: var(--heading-color); font-weight: 500; margin-bottom: 8px; }
        .brand-header p { font-size: 1rem; color: #64748b; font-weight: 300; }
        .form-content { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .form-group { margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        label {
            display: block; font-family: var(--nav-font); font-size: 0.85rem; text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 6px; color: var(--heading-color); font-weight: 500;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 12px 14px; border: 1px solid var(--border-color); border-radius: 6px;
            font-family: inherit; font-size: 1rem; background-color: var(--input-bg); color: var(--default-color);
            transition: all 0.2s ease;
        }

        input:focus { outline: none; border-color: var(--accent-color); background-color: #ffffff; box-shadow: 0 0 0 3px rgba(70, 83, 103, 0.08); }
        .checkbox-group { display: flex; align-items: flex-start; gap: 10px; margin: 20px 0; }
        .checkbox-group input[type="checkbox"] { margin-top: 3px; accent-color: var(--accent-color); }
        .checkbox-group label { font-family: var(--default-font); font-size: 0.95rem; color: #64748b; cursor: pointer; }
        .checkbox-group a { color: var(--accent-color); text-decoration: none; }
        .checkbox-group a:hover { text-decoration: underline; }

        .btn-submit {
            width: 100%; padding: 14px; background-color: var(--accent-color); color: var(--contrast-color);
            border: none; border-radius: 6px; font-family: var(--nav-font); font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: background-color 0.2s ease; margin-top: 10px;
        }
        .btn-submit:hover { background-color: #343e4f; }
        .form-footer { text-align: center; margin-top: 25px; font-size: 0.95rem; color: #64748b; }
        .form-footer a { color: var(--accent-color); text-decoration: none; font-weight: 600; }
        .form-footer a:hover { text-decoration: underline; }

        @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; gap: 0; } }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <div class="brand-header">
            <h1>Mada film</h1>
            <p>Votre espace cinéma personnel</p>
        </div>

        <?php if ($messageType === 'signup' && $message): ?>
            <?= $message ?>
        <?php endif; ?>
        
        <div id="signup" class="form-content">
            <form method="POST">
                <input type="hidden" name="action" value="signup">

                <div class="form-group">
                    <label for="name_user">Nom d'utilisateur</label>
                    <input type="text" id="name_user" name="name_user" value="<?= htmlspecialchars($name_user) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="exemple@mail.com" value="<?= htmlspecialchars($email) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" minlength="8" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmation</label>
                        <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">J'accepte les <a href="#">conditions d'utilisation</a></label>
                </div>

                <button type="submit" class="btn-submit">Créer mon compte</button>
            </form>

            <div class="form-footer">
                Déjà inscrit ? <a href="./connexion.view.php">Se connecter</a>
            </div>
        </div>
    </div>

</body>
</html>