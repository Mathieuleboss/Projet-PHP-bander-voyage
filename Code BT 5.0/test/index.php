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
    <title>BANDER-TRAVEL | Réservez vos voyages</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Ajout de Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .search-bar {
            transition: all 0.3s ease-in-out;
        }
        .search-bar:focus-within {
            transform: scale(1.05);
        }
        input, textarea {
            color: #000;
            background-color: #fff;
            caret-color: #000;
        }
        input::placeholder {
            color: #555;
            opacity: 1;
        }
        button {
            transition: transform 0.2s ease-in-out;
        }
        button:hover {
            transform: scale(1.05);
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
        /* Loader */
        .loader {
            border-top-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            border-left-color: #fff;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .user-pseudo {
            color: orange;
            font-weight: bold;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        /* Nouveaux styles pour l'assistant */
        .chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #f97316;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chat-button:hover {
            transform: scale(1.1);
        }
        .chat-button img {
            width: 30px; /* Ajustez la taille de l'image selon vos besoins */
            height: 30px;
        }
        .chat-modal {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            height: 500px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
            flex-direction: column;
            z-index: 1001;
        }
        .chat-modal.fullscreen {
            width: 100%;
            height: 100%;
            bottom: 0;
            right: 0;
            border-radius: 0;
        }
        .chat-header {
            background-color: #f97316;
            color: white;
            padding: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .chat-header button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        .chat-header button.fullscreen-btn {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .chat-header button.close-btn {
            margin-left: auto;
        }

        .chat-body {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
        }
        .chat-footer {
            padding: 10px;
            background-color: white;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            display: flex;
            align-items: center;
        }
        .chat-footer input {
            width: calc(100% - 50px); /* Laisser de la place pour le bouton */
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .chat-footer button {
            width: 40px;
            height: 40px;
            margin-left: 10px;
            background-color: #f97316;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chat-footer button img {
            width: 20px;
            height: 20px;
        }
        .message {
            max-width: 70%;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            position: relative;
        }
        .message.user {
            background-color: #f97316;
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }
        .message.assistant {
            background-color: #e5e5ea;
            color: black;
            align-self: flex-start;
            margin-right: auto;
        }
        .message .time {
            font-size: 10px;
            color: gray;
            display: block;
            margin-top: 5px;
            text-align: right;
        }
        .message.user .time {
            text-align: right;
        }
        .message.assistant .time {
            text-align: left;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
   <!-- Navbar -->
<header class="bg-white shadow-md p-4 flex justify-between items-center">
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
                <a href="compte.php" class="text-orange-500 hover:text-orange-500 font-semibold transition-all duration-300">
                    <?php echo htmlspecialchars($user_pseudo); ?>
                </a>
                <a href="logout.php" class="flex items-center border border-orange-500 text-orange-500 hover:text-white hover:bg-orange-500 hover:border-orange-600 rounded-lg px-4 py-2 transition-all duration-300 ease-in-out">
                    <img src="images/user2.png" alt="Déconnexion" class="w-5 h-5 mr-2">
                    Déconnexion
                </a>
            </div>
        <?php endif; ?>
    </nav>
</header>

    <!-- Hero Section avec barre de recherche -->
    <section class="bg-orange-500 text-white text-center py-20">
        <h2 class="text-4xl font-bold mb-4">Trouvez votre prochain voyage</h2>
        <p class="text-lg mb-6">Prêt pour l'aventure ? Découvrez votre prochain vol en un instant !</p>
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg search-bar">
            <form class="grid grid-cols-1 md:grid-cols-4 gap-4" action="search.php" method="GET" onsubmit="return validateForm()">
                <div class="relative">
                    <select id="destination" name="destination" class="p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-black w-full" aria-label="Destination">
                        <option value="" disabled selected>Destination</option>
                        <option value="Paris">Paris</option>
                        <option value="New York">New York</option>
                        <option value="Tokyo">Tokyo</option>
                        <option value="Yaoundé">Yaoundé</option>
                    </select>
                    <span class="absolute right-3 top-3 text-gray-500">📍</span>
                </div>
                <div class="relative">
                    <input type="text" id="start-date" name="start_date" placeholder="Départ" class="p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-black w-full" aria-label="Date de début">
                    <span class="absolute right-3 top-3 text-gray-500">📅</span>
                </div>
                <div class="relative">
                    <input type="text" id="end-date" name="end_date" placeholder="Retour" class="p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-black w-full" aria-label="Date de fin">
                    <span class="absolute right-3 top-3 text-gray-500">📅</span>
                </div>
                <button type="submit" class="bg-orange-500 text-white p-3 rounded-lg hover:bg-orange-600 relative" id="search-button">
                    Rechercher
                    <span class="loader hidden absolute right-4 top-1/2 transform -translate-y-1/2 w-4 h-4 border-4 border-t-4 border-white rounded-full animate-spin"></span>
                </button>
            </form>
            <!-- Sélection du type de voyage ajouté en bas et centré -->
            <div class="mt-6 text-center">
                <select id="trip-type" name="trip_type" placeholder="Type de voyage" class="p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-black w-2/3 md:w-1/2 mx-auto">
                    <option value="" disabled selected>Type de voyage</option>
                    <option value="business">Business</option>
                    <option value="economy">Économie</option>
                    <option value="first-class">Première classe</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Section des promotions -->
    <section class="py-10 px-6">
        <h3 class="text-2xl font-semibold text-center mb-6">Offres spéciales</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                <div class="image-container">
                    <img src="images/paris.jpg" alt="Paris" class="rounded-lg mb-4" loading="lazy">
                </div>
                <h4 class="text-xl font-semibold">Paris -50%</h4>
                <p class="text-gray-700">Découvrez la ville lumière à prix réduit.</p>
                <a href="offre.html" class="text-orange-500 hover:text-orange-600">En savoir plus</a>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                <div class="image-container">
                    <img src="images/newyork.jpg" alt="New York" class="rounded-lg mb-4" loading="lazy">
                </div>
                <h4 class="text-xl font-semibold">New York -30%</h4>
                <p class="text-gray-700">Explorez la Grosse Pomme avec nos offres exclusives.</p>
                <a href="offre.html" class="text-orange-500 hover:text-orange-600">En savoir plus</a>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                <div class="image-container">
                    <img src="images/tokyo.jpg" alt="Tokyo" class="rounded-lg mb-4" loading="lazy">
                </div>
                <h4 class="text-xl font-semibold">Tokyo -40%</h4>
                <p class="text-gray-700">Partez à l'aventure au Japon à prix réduit.</p>
                <a href="offre.html" class="text-orange-500 hover:text-orange-600">En savoir plus</a>
            </div>
        </div>
    </section>

    <!-- Section Blog - Conseils de voyage -->
    <section class="py-16 px-6">
        <h3 class="text-2xl font-semibold text-center mb-6">Conseils de Voyage</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                <h5 class="text-xl font-semibold">✈️ Comment voyager moins cher ?</h5>
                <p class="text-gray-700">Découvrez les meilleures astuces pour économiser sur vos billets d'avion.</p>
                <a href="how.php" class="text-orange-500 hover:text-orange-700 font-semibold">Lire plus</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                <h5 class="text-xl font-semibold">🌍 Les meilleures destinations en 2025</h5>
                <p class="text-gray-700">Notre sélection des lieux incontournables cette année.</p>
                <a href="besttravel.php" class="text-orange-500 hover:text-orange-700 font-semibold">Lire plus</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                <h5 class="text-xl font-semibold">🎒 Que mettre dans sa valise ?</h5>
                <p class="text-gray-700">Les indispensables à ne pas oublier avant de partir.</p>
                <a href="bag.php" class="text-orange-500 hover:text-orange-700 font-semibold">Lire plus</a>
            </div>
        </div>
    </section>

    <!-- Section Pourquoi Choisir BANDER-TRAVEL -->
    <section class="py-16 px-6 bg-gray-100">
        <div class="container mx-auto text-center">
            <div class="heading_container mb-6">
                <h2 class="text-3xl font-bold text-orange-500">Pourquoi choisir BANDER-TRAVEL ?</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                    <div class="flex items-center justify-center mb-4">
                        <img src="images/salary.png" alt="Offres Exclusives" class="w-12">
                    </div>
                    <h5 class="text-xl font-semibold text-orange-500 mb-2">Offres Exclusives</h5>
                    <p class="text-gray-700">Profitez des meilleurs prix sur notre sélection de destinations.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                    <div class="flex items-center justify-center mb-4">
                        <img src="images/customer-service.png" alt="Service Client" class="w-12">
                    </div>
                    <h5 class="text-xl font-semibold text-orange-500 mb-2">Service Client 24/7</h5>
                    <p class="text-gray-700">Nos agents sont disponibles à tout moment pour vous aider.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                    <div class="flex items-center justify-center mb-4">
                        <img src="images/target.png" alt="Flexibilité" class="w-12">
                    </div>
                    <h5 class="text-xl font-semibold text-orange-500 mb-2">Flexibilité Maximale</h5>
                    <p class="text-gray-700">Modifiez ou annulez vos réservations facilement et sans frais cachés.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white text-center p-4 mt-10">
        <p>&copy; 2025 BANDER-TRAVEL. Tous droits réservés.</p>
    </footer>

    <!-- Bouton flottant pour l'assistant -->
    <button class="chat-button" onclick="toggleChat()">
        <img src="images/chata.png" alt="Chat">
    </button>

    <!-- Fenêtre de chat modale -->
    <div class="chat-modal" id="chatModal">
        <div class="chat-header">
            <span>Bander Assistant</span>
            <button class="fullscreen-btn" onclick="toggleFullscreen()">⛶</button>
            <button class="close-btn" onclick="closeChat()">✖</button>
        </div>
        <div class="chat-body" id="chatBody">
            <!-- Messages de l'assistant -->
            <div class="message assistant">
                Bonjour, comment puis-je vous aider ?
                <span class="time">10:00 AM</span>
            </div>
        </div>
        <div class="chat-footer">
            <input type="text" id="chatInput" placeholder="Tapez votre message...">
            <button onclick="sendMessage()">
                <img src="images/send.png" alt="Envoyer">
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Fonction pour valider le formulaire
        function validateForm() {
            let destination = document.getElementById('destination').value;
            let startDate = document.getElementById('start-date').value;
            let endDate = document.getElementById('end-date').value;
            let tripType = document.getElementById('trip-type').value;
            if (!destination || !startDate || !endDate || !tripType) {
                alert('Veuillez remplir tous les champs avant de rechercher.');
                return false;
            }
            return true;
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Initialisation de Flatpickr pour les dates
            flatpickr("#start-date", {
                dateFormat: "d/m/Y", // Format de date : jj/mm/aaaa
                minDate: "today" // La date minimum possible est aujourd'hui
            });
            flatpickr("#end-date", {
                dateFormat: "d/m/Y", // Format de date : jj/mm/aaaa
                minDate: "today" // La date minimum possible est aujourd'hui
            });

            let elements = document.querySelectorAll(".fade-in");
            elements.forEach(el => {
                setTimeout(() => {
                    el.classList.add("show");
                }, 200);
            });
        });

        // Fonctions pour gérer le chat
        function toggleChat() {
            const chatModal = document.getElementById('chatModal');
            chatModal.style.display = chatModal.style.display === 'flex' ? 'none' : 'flex';
        }

        function toggleFullscreen() {
            const chatModal = document.getElementById('chatModal');
            chatModal.classList.toggle('fullscreen');
        }

        function closeChat() {
            const chatModal = document.getElementById('chatModal');
            chatModal.style.display = 'none';
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            if (message) {
                // Ajouter le message de l'utilisateur
                const chatBody = document.getElementById('chatBody');
                const userMessageDiv = document.createElement('div');
                userMessageDiv.className = 'message user';
                userMessageDiv.innerHTML = `${message}`;
                const userTimeSpan = document.createElement('span');
                userTimeSpan.className = 'time';
                userTimeSpan.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                userMessageDiv.appendChild(userTimeSpan);
                chatBody.appendChild(userMessageDiv);

                // Simuler une réponse de l'assistant
                setTimeout(() => {
                    const assistantMessageDiv = document.createElement('div');
                    assistantMessageDiv.className = 'message assistant';
                    assistantMessageDiv.innerHTML = `Je vais vous aider avec cela.`;
                    const assistantTimeSpan = document.createElement('span');
                    assistantTimeSpan.className = 'time';
                    assistantTimeSpan.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    assistantMessageDiv.appendChild(assistantTimeSpan);
                    chatBody.appendChild(assistantMessageDiv);
                    chatBody.scrollTop = chatBody.scrollHeight; // Faire défiler vers le bas
                }, 1000);

                input.value = ''; // Effacer l'input
            }
        }
    </script>
</body>
</html>
