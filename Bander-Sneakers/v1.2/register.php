<?php
// Page d'inscription
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error_message = '';
$success_message = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier que tous les champs requis sont remplis
    if (!isset($_POST['username']) || empty($_POST['username']) ||
        !isset($_POST['email']) || empty($_POST['email']) ||
        !isset($_POST['password']) || empty($_POST['password']) ||
        !isset($_POST['confirm_password']) || empty($_POST['confirm_password'])) {

        $error_message = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        // Récupérer et nettoyer les données
        $username = cleanInput($_POST['username']);
        $email = cleanInput($_POST['email']);
        $password = $_POST['password']; // Ne pas nettoyer le mot de passe avant hachage
        $confirm_password = $_POST['confirm_password'];
        $first_name = isset($_POST['first_name']) ? cleanInput($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? cleanInput($_POST['last_name']) : '';

        // Vérifier que les mots de passe correspondent
        if ($password !== $confirm_password) {
            $error_message = 'Les mots de passe ne correspondent pas.';
        }
        // Vérifier la longueur du mot de passe
        elseif (strlen($password) < 8) {
            $error_message = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        // Vérifier que l'email est valide
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Veuillez entrer une adresse email valide.';
        } else {
            try {
                $db = getDbConnection();

                // Vérifier si l'email est déjà utilisé
                $stmt = $db->prepare('SELECT user_id FROM users WHERE email = :email');
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->fetch()) {
                    $error_message = 'Cette adresse email est déjà utilisée.';
                } else {
                    // Vérifier si le nom d'utilisateur est déjà utilisé
                    $stmt = $db->prepare('SELECT user_id FROM users WHERE username = :username');
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();

                    if ($stmt->fetch()) {
                        $error_message = 'Ce nom d\'utilisateur est déjà utilisé.';
                    } else {
                        // Hacher le mot de passe
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Insérer le nouvel utilisateur
                        $stmt = $db->prepare('
                            INSERT INTO users (username, email, password, first_name, last_name)
                            VALUES (:username, :email, :password, :first_name, :last_name)
                        ');
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':password', $hashed_password);
                        $stmt->bindParam(':first_name', $first_name);
                        $stmt->bindParam(':last_name', $last_name);
                        $stmt->execute();

                        // Récupérer l'ID de l'utilisateur créé
                        $userId = $db->lastInsertId();

                        // Connecter automatiquement l'utilisateur
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['username'] = $username;
                        $_SESSION['email'] = $email;
                        $_SESSION['is_admin'] = false;

                        // Rediriger vers la page d'accueil ou la page précédente si disponible
                        $redirect_url = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
                        unset($_SESSION['redirect_after_login']);

                        header('Location: ' . $redirect_url);
                        exit();
                    }
                }
            } catch (PDOException $e) {
                $error_message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
                // Enregistrer l'erreur dans un fichier log
                error_log('Erreur PDO: ' . $e->getMessage());
            }
        }
    }
}

// Récupérer l'URL de redirection si elle existe
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}

// Titre et description de la page
$page_title = "Inscription | Bander-Sneakers";
$page_description = "Créez votre compte Bander-Sneakers pour profiter de nos offres exclusives et suivre vos commandes.";

// Inclure l'en-tête
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <ul class="breadcrumb-list">
            <li><a href="index.php">Accueil</a></li>
            <li class="active">Inscription</li>
        </ul>
    </div>
</div>

<!-- Register Section -->
<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-form-container">
                <h1 class="auth-title">Créer un compte</h1>

                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?= $success_message ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Prénom</label>
                            <input type="text" name="first_name" id="first_name">
                        </div>

                        <div class="form-group">
                            <label for="last_name">Nom</label>
                            <input type="text" name="last_name" id="last_name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">Nom d'utilisateur <span class="required">*</span></label>
                        <input type="text" name="username" id="username" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" name="email" id="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe <span class="required">*</span></label>
                        <input type="password" name="password" id="password" required>
                        <p class="form-hint">Le mot de passe doit contenir au moins 8 caractères.</p>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe <span class="required">*</span></label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            J'accepte les <a href="terms-conditions.php">conditions générales</a> et la <a href="privacy-policy.php">politique de confidentialité</a>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                    </div>
                </form>

                <div class="auth-links">
                    <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous</a></p>
                </div>
            </div>

            <div class="auth-sidebar">
                <div class="auth-info">
                    <h2>Avantages de créer un compte</h2>
                    <ul>
                        <li>Suivre l'état de vos commandes</li>
                        <li>Gérer votre liste de souhaits</li>
                        <li>Sauvegarder vos adresses de livraison</li>
                        <li>Accès plus rapide au processus de commande</li>
                        <li>Recevoir des offres exclusives par email</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
