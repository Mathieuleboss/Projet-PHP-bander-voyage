<?php
session_start();
$host = 'localhost';
$dbname = 'bander_travel';
$username = 'root';
$password = 'Terrel21';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifiez si l'utilisateur est connecté
$user_pseudo = isset($_SESSION['username']) ? $_SESSION['username'] : "Connexion";
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Récupérer l'historique des recherches de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT terme_recherche, date_recherche FROM recherches WHERE user_id = ? ORDER BY date_recherche DESC");
$stmt->execute([$user_id]);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des recherches | BANDER-TRAVEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .result-item {
            transition: all 0.3s ease-in-out;
        }
        .result-item:hover {
            transform: scale(1.05);
        }
        .result-card {
            transition: transform 0.3s ease-in-out;
        }
        .result-card:hover {
            transform: translateY(-10px);
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }

        .user-pseudo {
            color: orange;
            font-weight: bold;
        }

        .no-history {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh; /* Ajusté pour mieux centrer verticalement */
            text-align: center;
            margin-top: 20px; /* Ajouté pour éviter que la navbar ne cache le texte */
        }

        body {
            padding-top: 64px; /* Hauteur de la navbar */
        }

        /* Assurez-vous que le conteneur parent utilise Flexbox */
        #historique {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">

<!-- Navbar -->
<header class="navbar bg-white shadow-md p-4 flex justify-between items-center w-full fixed top-0 left-0 right-0 z-10">
    <a href="index.php">
        <h1 class="text-2xl font-bold text-orange-500">BANDER-TRAVEL</h1>
    </a>
    <nav class="flex items-center">
        <div class="flex items-center space-x-4">
            <a href="index.php" class="text-gray-700 hover:text-orange-500">Accueil</a>
            <a href="search.php" class="text-gray-700 hover:text-orange-500">Rechercher</a>
            <a href="avis.php" class="text-gray-700 hover:text-orange-500">Avis</a>
            <?php if ($user_pseudo !== "Connexion"): ?>
                <a href="historique.php" class="text-gray-700 hover:text-orange-500">Historique</a>
            <?php endif; ?>
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

    <!-- Section de l'historique des recherches -->
    <section class="py-10 px-6">
        <h2 class="text-3xl font-semibold text-center mb-6 text-orange-600">Historique de vos recherches</h2>

        <!-- Afficher l'historique des recherches -->
        <div id="historique">
            <?php if (empty($historique)): ?>
                <div class="no-history w-full">
                    <p class="text-gray-700">Aucune recherche effectuée pour le moment. Commencez à explorer !</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
                    <?php foreach ($historique as $recherche): ?>
                        <div class="result-card bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                            <h4 class="text-xl font-semibold text-orange-600"><?php echo htmlspecialchars($recherche['terme_recherche']); ?></h4>
                            <p class="text-gray-700">Date: <?php echo date('d/m/Y', strtotime($recherche['date_recherche'])); ?></p>
                            <!-- Ajoutez ici d'autres informations de la recherche si nécessaire -->
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white text-center p-4 mt-10">
        <p>&copy; 2025 BANDER-TRAVEL. Tous droits réservés.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let elements = document.querySelectorAll(".fade-in");
            elements.forEach(el => {
                setTimeout(() => {
                    el.classList.add("show");
                }, 200);
            });
        });
    </script>

</body>
</html>
