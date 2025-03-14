<?php
session_start();
include 'config.php';

$error_message = "";
$registration_message = "";
$form_type = 'login'; // Valeur par défaut : formulaire de connexion

// Vérifier si l'utilisateur est connecté
$user_pseudo = isset($_SESSION['username']) ? $_SESSION['username'] : "Connexion";

// Traitement des formulaires de connexion et d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        // Traitement de la connexion
        $email_or_username = $_POST['email_or_username'];
        $password = $_POST['password'];

        // Vérifie si c'est un email ou un pseudo et fait la requête en conséquence
        if (filter_var($email_or_username, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT id, username, password FROM users WHERE email='$email_or_username'";
        } else {
            $sql = "SELECT id, username, password FROM users WHERE username='$email_or_username'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Connexion réussie, stocker les informations de l'utilisateur dans la session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Mot de passe incorrect.";
            }
        } else {
            $error_message = "Aucun utilisateur trouvé avec cet email ou pseudo.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'register') {
        // Traitement de l'inscription
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Vérification si les mots de passe correspondent
        if ($password !== $confirm_password) {
            $registration_message = "Les mots de passe ne correspondent pas.";
            $form_type = 'register'; // Rester sur le formulaire d'inscription
        } else {
            // Vérifier si le pseudo existe déjà
            $check_username = "SELECT id FROM users WHERE username = '$username'";
            $username_result = $conn->query($check_username);

            // Vérifier si l'email existe déjà
            $check_email = "SELECT id FROM users WHERE email = '$email'";
            $email_result = $conn->query($check_email);

            if ($username_result->num_rows > 0) {
                $registration_message = "Le pseudo est déjà pris. Choisissez-en un autre.";
                $form_type = 'register'; // Rester sur le formulaire d'inscription
            } elseif ($email_result->num_rows > 0) {
                $registration_message = "L'email est déjà enregistré. Utilisez-en un autre.";
                $form_type = 'register'; // Rester sur le formulaire d'inscription
            } else {
                // Si aucun doublon, on enregistre l'utilisateur
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

                if ($conn->query($sql) === TRUE) {
                    $registration_message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                    $form_type = 'login'; // Redirige vers le formulaire de connexion après inscription réussie
                } else {
                    $registration_message = "Erreur : " . $sql . "<br>" . $conn->error;
                    $form_type = 'register'; // Rester sur le formulaire d'inscription en cas d'erreur
                }
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BANDER-TRAVEL | Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .hidden {
            display: none;
        }

        body {
            background: linear-gradient(120deg, #f8b400, #e6683c, #dc2a67);
            background-size: 600% 600%;
            animation: gradientAnimation 15s ease infinite;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .error {
            color: red;
            font-size: 0.875rem;
        }

        .user-pseudo {
            color: orange;
            font-weight: bold;
        }

        .navbar {
            height: 64px; /* Hauteur de la navbar */
        }

        .main-content {
            margin-top: 64px; /* Marge supérieure pour compenser la hauteur de la navbar */
        }
    </style>
</head>
<body class="text-gray-900 flex flex-col items-center justify-center h-screen">

    <!-- Navbar -->
    <header class="navbar bg-white shadow-md p-4 flex justify-between items-center w-full fixed top-0 left-0 right-0">
        <a href="index.php">
            <h1 class="text-2xl font-bold text-orange-500">BANDER-TRAVEL</h1>
        </a>
        <nav class="flex items-center">
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-orange-500">Accueil</a>
                <a href="search.php" class="text-gray-700 hover:text-orange-500">Rechercher</a>
                <a href="avis.php" class="text-gray-700 hover:text-orange-500">Avis</a>
            </div>
            <?php if ($user_pseudo === "Connexion"): ?>
                <a href="login.php" class="flex items-center border border-orange-500 text-orange-500 hover:text-white hover:bg-orange-500 hover:border-orange-600 rounded-lg px-4 py-2 ml-4 transition-all duration-300 ease-in-out">
                    <img src="images/user2.png" alt="Connexion" class="w-5 h-5 mr-2">
                    Connexion
                </a>
            <?php else: ?>
                <div class="flex items-center space-x-4 ml-4">
                    <span class="user-pseudo"><?php echo $user_pseudo; ?></span>
                    <a href="logout.php" class="flex items-center border border-orange-500 text-orange-500 hover:text-white hover:bg-orange-500 hover:border-orange-600 rounded-lg px-4 py-2 transition-all duration-300 ease-in-out">
                        <img src="images/user2.png" alt="Déconnexion" class="w-5 h-5 mr-2">
                        Déconnexion
                    </a>
                </div>
            <?php endif; ?>
        </nav>
    </header>

    <div class="main-content bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <!-- Formulaire de Connexion -->
        <div id="login-form" class="<?php echo $form_type == 'login' ? '' : 'hidden'; ?>">
            <h2 class="text-2xl font-bold text-orange-500 text-center mb-6">Connexion</h2>
            <?php if ($error_message != ""): ?>
                <div class="error mb-4"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form id="login-form-element" method="POST" action="login.php">
                <input type="hidden" name="action" value="login">
                <div class="mb-4">
                    <label for="login-email-or-username" class="block text-gray-700">Email ou Pseudo</label>
                    <input type="text" id="login-email-or-username" name="email_or_username" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div class="mb-4 relative">
                    <label for="login-password" class="block text-gray-700">Mot de passe</label>
                    <input type="password" id="login-password" name="password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <button type="button" id="toggle-login-password" class="absolute right-3 top-10 text-gray-500">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white p-3 rounded-lg hover:bg-orange-600">Se connecter</button>
            </form>
            <p class="text-center mt-4 text-gray-700">Pas encore de compte ? <a href="javascript:void(0);" onclick="toggleForm('register')" class="text-orange-500">S'inscrire</a></p>
        </div>

        <!-- Formulaire d'Inscription -->
        <div id="register-form" class="<?php echo $form_type == 'register' ? '' : 'hidden'; ?>">
            <h2 class="text-2xl font-bold text-orange-500 text-center mb-6">Inscription</h2>
            <?php if ($registration_message != ""): ?>
                <div class="error mb-4"><?php echo $registration_message; ?></div>
            <?php endif; ?>
            <form id="register-form-element" method="POST" action="login.php">
                <input type="hidden" name="action" value="register">
                <div class="mb-4">
                    <label for="register-username" class="block text-gray-700">Pseudo</label>
                    <input type="text" id="register-username" name="username" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div class="mb-4">
                    <label for="register-email" class="block text-gray-700">Email</label>
                    <input type="email" id="register-email" name="email" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div class="mb-4 relative">
                    <label for="register-password" class="block text-gray-700">Mot de passe</label>
                    <input type="password" id="register-password" name="password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <button type="button" id="toggle-register-password" class="absolute right-3 top-10 text-gray-500">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div class="mb-4 relative">
                    <label for="register-confirm-password" class="block text-gray-700">Confirmer le mot de passe</label>
                    <input type="password" id="register-confirm-password" name="confirm_password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <button type="button" id="toggle-register-confirm-password" class="absolute right-3 top-10 text-gray-500">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white p-3 rounded-lg hover:bg-orange-600">S'inscrire</button>
            </form>
            <p class="text-center mt-4 text-gray-700">Déjà un compte ? <a href="javascript:void(0);" onclick="toggleForm('login')" class="text-orange-500">Se connecter</a></p>
        </div>
    </div>

    <script>
        function toggleForm(formType) {
            if (formType === 'login') {
                document.getElementById('login-form').classList.remove('hidden');
                document.getElementById('register-form').classList.add('hidden');
            } else if (formType === 'register') {
                document.getElementById('register-form').classList.remove('hidden');
                document.getElementById('login-form').classList.add('hidden');
            }
        }

        document.getElementById('toggle-login-password').addEventListener('click', function () {
            const passwordField = document.getElementById('login-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });

        document.getElementById('toggle-register-password').addEventListener('click', function () {
            const passwordField = document.getElementById('register-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });

        document.getElementById('toggle-register-confirm-password').addEventListener('click', function () {
            const passwordField = document.getElementById('register-confirm-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });
    </script>

</body>
</html>
