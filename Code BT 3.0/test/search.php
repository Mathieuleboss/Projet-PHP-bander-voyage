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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche | BANDER-TRAVEL</title>
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
    </style>
</head>
<body class="bg-gray-100 text-gray-900">

    <!-- Navbar -->
<header class="bg-white shadow-md p-4 flex justify-between items-center">
    <a href="index.php">
        <h1 class="text-2xl font-bold text-orange-500">BANDER-TRAVEL</h1>
    </a>
    <nav>
        <a href="index.php" class="text-gray-700 hover:text-orange-500 mx-2">Accueil</a>
        <a href="search.php" class="text-gray-700 hover:text-orange-500 mx-2">Rechercher</a>
        <a href="avis.php" class="text-gray-700 hover:text-orange-500 mx-2">Avis</a>
        <?php if ($user_pseudo === "Connexion"): ?>
            <a href="login.php" class="text-gray-700 hover:text-orange-500 mx-2"><?php echo $user_pseudo; ?></a>
        <?php else: ?>
            <span class="user-pseudo mx-2"><?php echo $user_pseudo; ?></span>
            <a href="logout.php" class="text-gray-700 hover:text-orange-500 mx-2">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>


    <!-- Section des résultats -->
    <section class="py-10 px-6">
        <h2 class="text-3xl font-semibold text-center mb-6 text-orange-600">Résultats de votre recherche</h2>

        <!-- Afficher les résultats de recherche -->
        <div id="results" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Dynamique via JavaScript -->
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white text-center p-4 mt-10">
        <p>&copy; 2025 BANDER-TRAVEL. Tous droits réservés.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Récupérer les paramètres de l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const destination = urlParams.get('destination');
            const startDate = urlParams.get('start_date');
            const endDate = urlParams.get('end_date');

            // Afficher les résultats de recherche
            const resultsDiv = document.getElementById('results');
            if (destination && startDate && endDate) {
                const resultHtml = `
                    <div class="result-card bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                        <h4 class="text-xl font-semibold text-orange-600">${destination}</h4>
                        <p class="text-gray-700">Dates: ${startDate} à ${endDate}</p>
                        <p class="text-gray-500">Voici les résultats pour votre recherche.</p>
                    </div>
                `;
                resultsDiv.innerHTML = resultHtml;
            } else {
                resultsDiv.innerHTML = '<p class="text-center text-gray-500">Aucun résultat trouvé. Veuillez essayer avec des critères valides.</p>';
            }

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
