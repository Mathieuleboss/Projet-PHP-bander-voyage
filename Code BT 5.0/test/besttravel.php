<?php
session_start();
$host = 'localhost';
$dbname = 'bander_travel';
$username = 'root';
$password = 'Terrel21';

// Vérifiez si l'utilisateur est connecté
$user_pseudo = isset($_SESSION['username']) ? $_SESSION['username'] : "Connexion";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les meilleures destinations en 2025 | BANDER-TRAVEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<style>
    .user-pseudo {
        color: orange;
        font-weight: bold;
    }
</style>

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

<!-- Hero Section -->
<section class="bg-orange-500 text-white text-center py-20 mt-16">
    <h2 class="text-4xl font-bold mb-4">Les meilleures destinations en 2025</h2>
    <p class="text-lg mb-6">Notre sélection des lieux incontournables cette année.</p>
</section>

<!-- Content Section -->
<section class="py-10 px-6">
    <div class="container mx-auto">
        <div class="mb-10 text-center">
            <h3 class="text-3xl font-semibold text-orange-500 mb-4">Les destinations à ne pas manquer en 2025</h3>
            <p class="text-lg text-gray-700">Voici les endroits qui feront vibrer le monde du voyage cette année.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Destination 1 - Paris -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/paris.jpg" alt="Paris" class="rounded-lg mb-4 w-full h-48 object-cover">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">Paris, France</h4>
                <p class="text-gray-700">La Ville Lumière vous attend avec ses monuments emblématiques et sa culture incomparable.</p>
            </div>

            <!-- Destination 2 - New York -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/newyork.jpg" alt="New York" class="rounded-lg mb-4 w-full h-48 object-cover">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">New York, USA</h4>
                <p class="text-gray-700">Découvrez la ville qui ne dort jamais, avec ses gratte-ciel imposants et sa vie culturelle unique.</p>
            </div>

            <!-- Destination 3 - Tokyo -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/tokyo.jpg" alt="Tokyo" class="rounded-lg mb-4 w-full h-48 object-cover">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">Tokyo, Japon</h4>
                <p class="text-gray-700">Entre tradition et modernité, Tokyo vous offre une expérience inoubliable de culture et de technologie.</p>
            </div>

            <!-- Destination 4 - Yaoundé -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/yaounde.jpg" alt="Yaoundé" class="rounded-lg mb-4 w-full h-48 object-cover">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">Yaoundé, Cameroun</h4>
                <p class="text-gray-700">La capitale camerounaise vous offre une richesse culturelle fascinante et une nature préservée.</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white text-center p-4 mt-10">
    <p>&copy; 2025 BANDER-TRAVEL. Tous droits réservés.</p>
</footer>

</body>
</html>
