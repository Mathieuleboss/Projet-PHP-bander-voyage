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
    <title>Comment voyager moins cher ? | BANDER-TRAVEL</title>
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
    <h2 class="text-4xl font-bold mb-4">Comment voyager moins cher ?</h2>
    <p class="text-lg mb-6">Découvrez les meilleures astuces pour économiser sur vos billets d'avion.</p>
</section>

<!-- Content Section -->
<section class="py-10 px-6">
    <div class="container mx-auto">
        <div class="mb-10 text-center">
            <h3 class="text-3xl font-semibold text-orange-500 mb-4">Les meilleures astuces pour économiser sur vos billets d'avion</h3>
            <p class="text-lg text-gray-700">Voyager à prix réduit est possible avec ces astuces simples mais efficaces !</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Astuce 1 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/clock.png" alt="Réserver tôt" class="w-12">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">Réserver tôt</h4>
                <p class="text-gray-700">Réserver vos billets plusieurs mois à l'avance vous permettra de profiter des meilleurs prix.</p>
            </div>

            <!-- Astuce 2 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/provision.png" alt="Vols flexibles" class="w-12">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">Choisir des vols flexibles</h4>
                <p class="text-gray-700">Les vols avec des horaires flexibles peuvent souvent être moins chers, surtout en dehors des périodes de pointe.</p>
            </div>

            <!-- Astuce 3 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/ideas.png" alt="Comparer les prix" class="w-12">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">Comparer les prix</h4>
                <p class="text-gray-700">Utilisez des comparateurs comme BANDER-TRAVEL pour comparer les prix des billets d'avion de différentes compagnies aériennes.</p>
            </div>

            <!-- Astuce 4 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/seasonality-trend.png" alt="Voyager en basse saison" class="w-12">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">Voyager en basse saison</h4>
                <p class="text-gray-700">Voyager pendant la basse saison permet de bénéficier de tarifs plus bas pour les billets d'avion et l'hébergement.</p>
            </div>

            <!-- Astuce 5 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/subscription-model.png" alt="Abonnement aux alertes" class="w-12">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">S'abonner aux alertes de prix</h4>
                <p class="text-gray-700">De nombreux sites de voyage offrent des alertes de prix pour vous avertir lorsque les billets deviennent moins chers.</p>
            </div>

            <!-- Astuce 6 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <img src="images/shipping.png" alt="Vols avec escale" class="w-12">
                <h4 class="text-xl font-semibold text-orange-500 mb-2">Opter pour des vols avec escale</h4>
                <p class="text-gray-700">Les vols directs sont souvent plus chers. Prendre un vol avec escale peut réduire considérablement le prix de votre billet.</p>
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
