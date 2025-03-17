<?php
session_start();
include 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $hashed_password);
$stmt->fetch();
$stmt->close();

// Gestion du changement de mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['old_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    if (password_verify($_POST['old_password'], $hashed_password)) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password, $user_id);
            if ($stmt->execute()) {
                $message = "Mot de passe mis à jour avec succès !";
            } else {
                $message = "Erreur lors de la mise à jour du mot de passe.";
            }
            $stmt->close();
        } else {
            $message = "Les nouveaux mots de passe ne correspondent pas.";
        }
    } else {
        $message = "L'ancien mot de passe est incorrect.";
    }
}
$conn->close();

$user_pseudo = $username ?? "Connexion";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BANDER-TRAVEL | Mon Compte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
                    <span class="user-pseudo"><?php echo htmlspecialchars($user_pseudo); ?></span>
                    <a href="logout.php" class="flex items-center border border-orange-500 text-orange-500 hover:text-white hover:bg-orange-500 hover:border-orange-600 rounded-lg px-4 py-2 transition-all duration-300 ease-in-out">
                        <img src="images/user2.png" alt="Déconnexion" class="w-5 h-5 mr-2">
                        Déconnexion
                    </a>
                </div>
            <?php endif; ?>
        </nav>
    </header>

    <div class="main-content bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-orange-500 text-center">Mon Compte</h2>

        <div class="mt-4">
            <p class="text-lg"><strong>Pseudo :</strong> <?php echo htmlspecialchars($username); ?></p>
            <p class="text-lg"><strong>Email :</strong> <?php echo htmlspecialchars($email); ?></p>
        </div>

        <!-- Formulaire de modification du mot de passe -->
        <form method="POST" action="" class="mt-6">
            <label class="block text-gray-700">Ancien mot de passe</label>
            <div class="relative">
                <input type="password" name="old_password" id="old-password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 mt-1" required>
                <button type="button" id="toggle-old-password" class="absolute right-3 top-3 text-gray-500">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>

            <label class="block text-gray-700 mt-4">Nouveau mot de passe</label>
            <div class="relative">
                <input type="password" name="new_password" id="new-password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 mt-1" required>
                <button type="button" id="toggle-new-password" class="absolute right-3 top-3 text-gray-500">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>

            <label class="block text-gray-700 mt-4">Confirmer le mot de passe</label>
            <div class="relative">
                <input type="password" name="confirm_password" id="confirm-password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 mt-1" required>
                <button type="button" id="toggle-confirm-password" class="absolute right-3 top-3 text-gray-500">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>

            <button type="submit" class="w-full bg-orange-500 text-white p-3 rounded-lg hover:bg-orange-600 mt-4">Changer le mot de passe</button>
        </form>

        <?php if (isset($message)) echo "<p class='text-red-500 text-center mt-2'>$message</p>"; ?>
    </div>

    <script>
        document.getElementById('toggle-old-password').addEventListener('click', function () {
            const passwordField = document.getElementById('old-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });

        document.getElementById('toggle-new-password').addEventListener('click', function () {
            const passwordField = document.getElementById('new-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', function () {
            const passwordField = document.getElementById('confirm-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });
    </script>

</body>
</html>
