<?php
session_start();
include 'config.php'; // Fichier de connexion à la BDD

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : null;

// Définir user_pseudo
$user_pseudo = $is_logged_in ? $_SESSION['username'] : "Connexion";

// Soumission d’un avis ou d’une réponse
if ($_SERVER["REQUEST_METHOD"] == "POST" && $is_logged_in) {
    if (isset($_POST['comment'])) {
        $comment = trim($_POST['comment']);

        if (!empty($comment)) {
            // Pas de htmlspecialchars ici avant insertion
            // Protection contre les injections XSS à l'affichage

            // Insérer l'avis dans la BDD
            $stmt = $conn->prepare("INSERT INTO avis_utilisateurs (user_id, username, comment) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $_SESSION['user_id'], $_SESSION['username'], $comment);
            $stmt->execute();
            $stmt->close();

            header("Location: avis.php");
            exit();
        }
    } elseif (isset($_POST['rating'])) {
        $rating = intval($_POST['rating']);
        $review_id = intval($_POST['review_id']);

        // Vérifier que l'utilisateur est le propriétaire de l'avis
        $stmt = $conn->prepare("SELECT user_id FROM avis_utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $review_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($author_id);
            $stmt->fetch();
            if ($author_id == $_SESSION['user_id']) {
                // Mettre à jour la note de l'avis
                $stmt = $conn->prepare("UPDATE avis_utilisateurs SET rating = ? WHERE id = ?");
                $stmt->bind_param("ii", $rating, $review_id);
                $stmt->execute();
            }
        }
        $stmt->close();

        header("Location: avis.php");
        exit();
    } elseif (isset($_POST['reply']) && isset($_POST['review_id'])) {
        $reply = trim($_POST['reply']);
        $review_id = intval($_POST['review_id']);

        if (!empty($reply)) {
            // Pas de htmlspecialchars ici avant insertion
            // Protection contre les injections XSS à l'affichage

            // Mettre à jour la réponse de l'avis
            $stmt = $conn->prepare("UPDATE avis_utilisateurs SET reply = ? WHERE id = ?");
            $stmt->bind_param("si", $reply, $review_id);
            $stmt->execute();
            $stmt->close();

            header("Location: avis.php");
            exit();
        }
    }
}

// Pagination
$limit = 4; // Nombre d'avis par page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Tri par date ou par note
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_posted';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Requête pour récupérer les avis avec pagination et tri
$stmt = $conn->prepare("SELECT *, COALESCE(rating, 0) as rating, COALESCE(reply, '') as reply FROM avis_utilisateurs ORDER BY $sort $order LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$avis = $stmt->get_result();
$stmt->close();

// Compter le nombre total d'avis
$stmt = $conn->prepare("SELECT COUNT(*) FROM avis_utilisateurs");
$stmt->execute();
$stmt->bind_result($total_reviews);
$stmt->fetch();
$stmt->close();

$total_pages = ceil($total_reviews / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis des Utilisateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }
        .comment-box {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .comment-box:hover {
            background-color: #fff9f0;
            transform: scale(1.02);
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .pagination a {
            padding: 10px 15px;
            border-radius: 5px;
            background-color: #f3f4f6;
            color: #333;
            transition: background-color 0.3s ease;
        }
        .pagination a:hover {
            background-color: #e2e8f0;
        }
        .rating {
            display: flex;
            gap: 2px;
            cursor: pointer;
        }
        .rating i {
            color: #fbbf24;
        }
        .rating i:hover {
            transform: scale(1.2);
        }

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
    </style>
</head>
<body class="bg-gray-100">

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

<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow mt-10 fade-in show">
    <h2 class="text-3xl font-bold text-orange-500 text-center mb-6">Avis des Utilisateurs</h2>
    <p class="text-center text-gray-600 mb-8">Découvrez ce que nos utilisateurs pensent de leurs voyages avec BANDER-TRAVEL.</p>

    <?php if ($is_logged_in): ?>
        <form method="POST" action="avis.php" class="mb-8 fade-in show">
            <textarea name="comment" class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Partagez votre expérience..." required></textarea>
            <button type="submit" class="w-full bg-orange-500 text-white p-4 rounded-lg hover:bg-orange-600 mt-4 flex items-center justify-center">
                Laisser un Avis <i class="fas fa-paper-plane ml-2"></i>
            </button>
        </form>
    <?php else: ?>
        <p class="text-center text-gray-700 fade-in show">Vous devez être connecté pour laisser un avis. <a href="login.php" class="text-orange-500">Se connecter</a></p>
    <?php endif; ?>

    <!-- Filters -->
    <div class="flex justify-end mb-4 space-x-4">
        <?php
            // Définir l'ordre initial de tri
            $sort_order = 'DESC'; // Par défaut, trier du plus récent au plus vieux
            if (isset($_GET['sort']) && $_GET['sort'] === 'date_posted') {
                // Si le tri est déjà par date, alterner l'ordre
                $sort_order = isset($_GET['order']) && $_GET['order'] === 'ASC' ? 'DESC' : 'ASC';
            }
        ?>
        <a href="?sort=date_posted&order=<?php echo $sort_order; ?>" class="text-gray-700 hover:text-orange-500">
    <i class="fas <?php echo $sort_order === 'DESC' ? 'fa-sort-amount-down' : 'fa-sort-amount-up'; ?>"></i> Trier par date
        </a>

        <a href="?sort=rating" class="text-gray-700 hover:text-orange-500">
            <i class="fas fa-star"></i> Trier par popularité
        </a>
    </div>

    <!-- Liste des Avis -->
    <div class="space-y-6">
        <?php while ($row = $avis->fetch_assoc()): ?>
            <div class="p-6 border rounded-lg shadow-sm comment-box fade-in show">
                <div class="flex justify-between items-center">
                    <p class="font-bold text-orange-500"><?php echo htmlspecialchars($row['username']); ?></p>
                    <div class="rating" data-id="<?php echo $row['id']; ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?php echo $i <= $row['rating'] ? '' : '-o'; ?>" data-rating="<?php echo $i; ?>"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <p class="text-gray-700 mt-2"><?php echo htmlspecialchars($row['comment']); ?></p>
                <span class="text-sm text-gray-500 mt-2"><i class="far fa-clock"></i> <?php echo date("d/m/Y H:i", strtotime($row['date_posted'])); ?></span>

                <!-- Affichage des réponses -->
                <?php if (!empty($row['reply'])): ?>
                    <div class="mt-4 p-4 border-l-4 border-orange-500 bg-gray-50">
                        <p class="text-gray-700"><strong>Réponse :</strong> <?php echo htmlspecialchars($row['reply']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($is_logged_in): ?>
                    <!-- Bouton Répondre -->
                    <button class="text-blue-500 text-sm mt-2 hover:underline reply-btn" data-id="<?php echo $row['id']; ?>">Répondre</button>
                    
                    <!-- Formulaire de réponse caché -->
                    <form class="reply-form hidden mt-4" data-id="<?php echo $row['id']; ?>" method="POST" action="avis.php">
                        <textarea name="reply" class="w-full p-2 border rounded-lg" placeholder="Votre réponse..." required></textarea>
                        <input type="hidden" name="review_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="bg-orange-500 text-white p-2 rounded-lg">Répondre</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class=""><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<script>
    // Afficher le formulaire de réponse lorsqu'on clique sur "Répondre"
    const replyButtons = document.querySelectorAll('.reply-btn');
    replyButtons.forEach(button => {
        button.addEventListener('click', () => {
            const reviewId = button.dataset.id;
            const form = document.querySelector(`.reply-form[data-id='${reviewId}']`);
            form.classList.toggle('hidden');
        });
    });
</script>

</body>
</html>
