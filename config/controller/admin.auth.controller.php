<!-- HA -->
 <?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] === 'admin') {
            $_SESSION['user_id']   = $user['id_user'];
            $_SESSION['user_name'] = $user['name_user'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: admin.php');
            exit;
        } else {
            $error = "Accès réservé aux administrateurs.";
        }
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}

require_once 'views/admin.login.view.php';