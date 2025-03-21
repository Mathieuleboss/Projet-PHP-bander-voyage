<?php
session_start();
include 'config.php';

$error_message = "";
$registration_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        // Traitement de la connexion
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT id, username, password FROM users WHERE email='$email'";
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
            $error_message = "Aucun utilisateur trouvé avec cet email.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'register') {
        // Traitement de l'inscription
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            $registration_message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } else {
            $registration_message = "Erreur : " . $sql . "<br>" . $conn->error;
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
    </style>
</head>
<body class="text-gray-900 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div id="login-form">
            <h2 class="text-2xl font-bold text-orange-500 text-center mb-6">Connexion</h2>
            <?php if ($error_message != ""): ?>
                <div class="error mb-4"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form id="login-form-element" method="POST" action="login.php">
                <input type="hidden" name="action" value="login">
                <div class="mb-4">
                    <label for="login-email" class="block text-gray-700">Email</label>
                    <input type="email" id="login-email" name="email" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <div id="login-email-error" class="error hidden">Veuillez entrer un email valide.</div>
                </div>
                <div class="mb-4 relative">
                    <label for="login-password" class="block text-gray-700">Mot de passe</label>
                    <input type="password" id="login-password" name="password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <button type="button" id="toggle-login-password" class="absolute right-3 top-10 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white p-3 rounded-lg hover:bg-orange-600">Se connecter</button>
            </form>
            <p class="text-center mt-4 text-gray-700">Pas encore de compte ? <a href="#" id="show-register-form" class="text-orange-500">S'inscrire</a></p>
        </div>

        <div id="register-form" class="hidden">
            <h2 class="text-2xl font-bold text-orange-500 text-center mb-6">Inscription</h2>
            <?php if ($registration_message != ""): ?>
                <div class="mb-4"><?php echo $registration_message; ?></div>
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
                    <div id="register-email-error" class="error hidden">Veuillez entrer un email valide.</div>
                </div>
                <div class="mb-4 relative">
                    <label for="register-password" class="block text-gray-700">Mot de passe</label>
                    <input type="password" id="register-password" name="password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <button type="button" id="toggle-register-password" class="absolute right-3 top-10 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <div class="mb-4 relative">
                    <label for="confirm-password" class="block text-gray-700">Confirmer le mot de passe</label>
                    <input type="password" id="confirm-password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <button type="button" id="toggle-confirm-password" class="absolute right-3 top-10 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <div id="password-match-error" class="error hidden">Les mots de passe ne correspondent pas.</div>
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white p-3 rounded-lg hover:bg-orange-600">S'inscrire</button>
            </form>
            <p class="text-center mt-4 text-gray-700">Déjà un compte ? <a href="#" id="show-login-form" class="text-orange-500">Se connecter</a></p>
        </div>
    </div>

    <script>
        document.getElementById('show-register-form').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
        });

        document.getElementById('show-login-form').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('login-form').classList.remove('hidden');
        });

        document.getElementById('login-email').addEventListener('input', function() {
            const email = this.value;
            const errorElement = document.getElementById('login-email-error');
            if (!email.includes('@')) {
                errorElement.classList.remove('hidden');
            } else {
                errorElement.classList.add('hidden');
            }
        });

        document.getElementById('register-email').addEventListener('input', function() {
            const email = this.value;
            const errorElement = document.getElementById('register-email-error');
            if (!email.includes('@')) {
                errorElement.classList.remove('hidden');
            } else {
                errorElement.classList.add('hidden');
            }
        });

        document.getElementById('confirm-password').addEventListener('input', function() {
            const password = document.getElementById('register-password').value;
            const confirmPassword = this.value;
            const errorElement = document.getElementById('password-match-error');
            if (password !== confirmPassword) {
                errorElement.classList.remove('hidden');
            } else {
                errorElement.classList.add('hidden');
            }
        });

        document.getElementById('login-form-element').addEventListener('submit', function(event) {
            const email = document.getElementById('login-email').value;
            if (!email.includes('@')) {
                event.preventDefault();
            }
        });

        document.getElementById('register-form-element').addEventListener('submit', function(event) {
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (!email.includes('@') || password !== confirmPassword) {
                event.preventDefault();
            }
        });

        document.getElementById('toggle-login-password').addEventListener('click', function() {
            const passwordField = document.getElementById('login-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        });

        document.getElementById('toggle-register-password').addEventListener('click', function() {
            const passwordField = document.getElementById('register-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', function() {
            const passwordField = document.getElementById('confirm-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        });
    </script>

</body>
</html>
