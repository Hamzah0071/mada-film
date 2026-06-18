<?php
require_once 'config/database.php';
require_once 'models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'signup') {
                $this->register();
            } elseif ($action === 'signin') {
                $this->login();
            }
        }
        
        // Charger la vue
        include 'views/auth_view.php';
    }

    private function register() {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $error = "Les mots de passe ne correspondent pas.";
            return;
        }

        $this->user->nom = $_POST['nom'];
        $this->user->prenom = $_POST['prenom'];
        $this->user->date_naissance = $_POST['date_naissance'];
        $this->user->sexe = $_POST['sexe'];
        $this->user->telephone = $_POST['telephone'];
        $this->user->role = $_POST['role'];
        $this->user->adresse = $_POST['adresse'];
        $this->user->email = $_POST['email'];
        $this->user->password = $_POST['password'];

        if ($this->user->create()) {
            $success = "Compte créé avec succès ! Connectez-vous.";
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    }

    private function login() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $userData = $this->user->login($email, $password);
        if ($userData) {
            session_start();
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['prenom'] . " " . $userData['nom'];
            header("Location: dashboard.php"); // Rediriger vers une page d'accueil
            exit();
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}
?>
