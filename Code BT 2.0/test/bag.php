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
    <title>Que mettre dans sa valise ? | BANDER-TRAVEL</title>
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
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <a href="index.php">
            <h1 class="text-2xl font-bold text-orange-500">BANDER-TRAVEL</h1>
        </a>
        <nav>
    <a href="index.php" class="text-gray-700 hover:text-orange-500 mx-2">Accueil</a>
    <a href="search.php" class="text-gray-700 hover:text-orange-500 mx-2">Rechercher</a>
    <?php if ($user_pseudo === "Connexion"): ?>
        <a href="login.php" class="text-gray-700 hover:text-orange-500 mx-2"><?php echo $user_pseudo; ?></a>
    <?php else: ?>
        <span class="user-pseudo mx-2"><?php echo $user_pseudo; ?></span>
        <a href="logout.php" class="text-gray-700 hover:text-orange-500 mx-2">Déconnexion</a>
    <?php endif; ?>
</nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-orange-500 text-white text-center py-20">
        <h2 class="text-4xl font-bold mb-4">Que mettre dans sa valise ?</h2>
        <p class="text-lg mb-6">Les indispensables à ne pas oublier avant de partir.</p>
    </section>

    <!-- Content Section -->
    <section class="py-10 px-6">
        <div class="container mx-auto">
            <div class="mb-10 text-center">
                <h3 class="text-3xl font-semibold text-orange-500 mb-4">Les essentiels pour votre voyage</h3>
                <p class="text-lg text-gray-700">Voici une liste des éléments indispensables à emporter pour chaque voyage.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Indispensable 1 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <img src="images/passport.jpg" alt="Passeport" class="rounded-lg mb-4 w-full h-48 object-cover">
                    <h4 class="text-xl font-semibold text-orange-500 mb-2">Passeport et Documents</h4>
                    <p class="text-gray-700">Assurez-vous d'avoir votre passeport et tous les documents nécessaires pour votre voyage.</p>
                </div>

                <!-- Indispensable 2 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <img src="images/vetements.jpg" alt="Vêtements" class="rounded-lg mb-4 w-full h-48 object-cover">
                    <h4 class="text-xl font-semibold text-orange-500 mb-2">Vêtements adaptés</h4>
                    <p class="text-gray-700">N'oubliez pas d'emporter des vêtements adaptés au climat de votre destination.</p>
                </div>

                <!-- Indispensable 3 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <img src="images/toilet.jpg" alt="Produits de toilette" class="rounded-lg mb-4 w-full h-48 object-cover">
                    <h4 class="text-xl font-semibold text-orange-500 mb-2">Produits de Toilette</h4>
                    <p class="text-gray-700">Pensez à prendre vos produits de toilette dans des contenants de voyage.</p>
                </div>

                <!-- Indispensable 4 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <img src="images/chargeur.jpg" alt="Chargeur" class="rounded-lg mb-4 w-full h-48 object-cover">
                    <h4 class="text-xl font-semibold text-orange-500 mb-2">Chargeur et Batterie externe</h4>
                    <p class="text-gray-700">Un chargeur et une batterie externe sont essentiels pour rester connecté pendant votre voyage.</p>
                </div>

                <!-- Indispensable 5 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <img src="images/medoc.jpg" alt="Médicaments" class="rounded-lg mb-4 w-full h-48 object-cover">
                    <h4 class="text-xl font-semibold text-orange-500 mb-2">Médicaments de base</h4>
                    <p class="text-gray-700">Emportez quelques médicaments de base pour faire face à des situations imprévues.</p>
                </div>

                <!-- Indispensable 6 -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <img src="images/bag.jpg" alt="Sac à dos" class="rounded-lg mb-4 w-full h-48 object-cover">
                    <h4 class="text-xl font-semibold text-orange-500 mb-2">Sac à dos ou Sac de voyage</h4>
                    <p class="text-gray-700">Un bon sac de voyage est essentiel pour organiser vos affaires efficacement.</p>
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
