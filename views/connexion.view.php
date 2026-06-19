<?php
// app/auth/Sign.php
require_once __DIR__ . '/../config/db.php';
session_start();

$email = '';
$erreurs = [];

// ============ TRAITEMENT CONNEXION ============
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'signin') {
    
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validations de base
    if (empty($email)) {
        $erreurs[] = "L'adresse email est obligatoire.";
    }
    if (empty($password)) {
        $erreurs[] = "Le mot de passe est obligatoire.";
    }

    if (empty($erreurs)) {
        try {
            // Recherche de l'utilisateur dans la table "Users"
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Si l'utilisateur existe et que le mot de passe correspond au hash
            if ($user && password_verify($password, $user['password'])) {
                
                // Régénérer l'ID de session par sécurité
                session_regenerate_id(true);

                // Stockage des informations utiles en session
                $_SESSION['user_id']   = $user['id_user'];
                $_SESSION['user_name'] = $user['name_user'];
                $_SESSION['user_role'] = $user['role'];

                // Redirection vers l'accueil ou le tableau de bord
                header('Location: ../index.php');
                exit();
            } else {
                $erreurs[] = "Identifiants incorrects.";
            }
        } catch (Exception $e) {
            $erreurs[] = "Une erreur technique est survenue.";
            error_log("Erreur connexion: " . $e->getMessage());
        }
    }
    header("Location: film_page.view.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Mada film</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    
    <style>
        /* Variables CSS */
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

        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        /* Layout Épuré */
        .auth-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 40px 20px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-header h1 {
            font-family: var(--heading-font);
            font-size: 2.5rem;
            color: var(--heading-color);
            font-weight: 500;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .brand-header p {
            font-size: 1rem;
            color: #64748b;
            font-weight: 300;
        }

        /* Formulaires */
        .form-content {
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-family: var(--nav-font);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            color: var(--heading-color);
            font-weight: 500;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-family: inherit;
            font-size: 1rem;
            background-color: var(--input-bg);
            color: var(--default-color);
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-color);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(70, 83, 103, 0.08);
        }

        /* Alertes et Erreurs */
        .erreurs {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 22px;
            font-size: 0.95rem;
        }

        .erreurs ul {
            margin-left: 20px;
            margin-top: 5px;
        }

        /* Bouton Minimal */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: var(--accent-color);
            color: var(--contrast-color);
            border: none;
            border-radius: 6px;
            font-family: var(--nav-font);
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #343e4f;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        /* Pied de formulaire */
        .form-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.95rem;
            color: #64748b;
        }

        .form-footer a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .auth-wrapper {
                padding: 20px 10px;
            }
        }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <div class="brand-header">
            <h1>Mada film</h1>
            <p>Votre espace cinéma personnel</p>
        </div>

        <?php if (!empty($erreurs)): ?>
            <div class="erreurs">
                <strong>Connexion échouée :</strong>
                <ul>
                    <?php foreach ($erreurs as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div style="
                background: #F0FFF4;
                border-left: 5px solid #375623;
                border-radius: 6px;
                padding: 14px 16px;
                margin-bottom: 22px;
                color: #375623;
                font-size: 14px;
                font-weight: bold;">
                <p>Vous avez été déconnecté avec succès.</p>
            </div>
        <?php endif; ?>

        <div id="signin" class="form-content">
            <form method="POST">
                <input type="hidden" name="action" value="signin">

                <div class="form-group">
                    <label for="signin-email">Email</label>
                    <input type="email" id="signin-email" name="email" placeholder="exemple@mail.com" value="<?= htmlspecialchars($email) ?>" required>
                </div>

                <div class="form-group">
                    <label for="signin-password">Mot de passe</label>
                    <input type="password" id="signin-password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">Se connecter</button>
            </form>

            <div class="form-footer">
                Pas encore inscrit ? <a href="./inscription.view.php">Créer un compte</a>
            </div>
        </div>
    </div>

</body>
</html>