<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Initialiser le panier si nécessaire
if (!isset($_SESSION['cart_id'])) {
    // Créer un panier pour l'utilisateur connecté ou pour la session
    $db = getDbConnection();

    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();

    $sql = "INSERT INTO cart (user_id, session_id) VALUES (:user_id, :session_id)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':session_id', $sessionId);
    $stmt->execute();

    $_SESSION['cart_id'] = $db->lastInsertId();
}

// Récupérer le nombre d'articles dans le panier
function getCartItemCount() {
    if (!isset($_SESSION['cart_id'])) {
        return 0;
    }

    $db = getDbConnection();
    $cartId = $_SESSION['cart_id'];

    $sql = "SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':cart_id', $cartId);
    $stmt->execute();

    $result = $stmt->fetch();
    return $result['total'] ? $result['total'] : 0;
}

$cartItemCount = getCartItemCount();

// Récupérer les catégories pour le menu
$categories = getCategories();
$brands = getBrands();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Bander-Sneakers - Votre destination pour les sneakers'; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Bander-Sneakers - Votre destination pour les sneakers de marque. Découvrez notre collection de Nike, Adidas, Jordan et plus encore.'; ?>">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php
    // Vérifier si l'utilisateur a un panier
    if (!isset($_SESSION['cart_id'])) {
        // Créer un panier pour l'utilisateur
        try {
            $db = getDbConnection();
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $sessionId = session_id();

            // Si l'utilisateur n'est pas connecté, on insère NULL pour user_id
            $sql = "INSERT INTO cart (user_id, session_id) VALUES (:user_id, :session_id)";
            $stmt = $db->prepare($sql);

            // Liaison de paramètre conditionnelle pour user_id
            if ($userId) {
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':user_id', null, PDO::PARAM_NULL);
            }

            $stmt->bindParam(':session_id', $sessionId);
            $stmt->execute();

            $_SESSION['cart_id'] = $db->lastInsertId();
        } catch (PDOException $e) {
            // Enregistrer l'erreur dans un fichier log
            error_log('Erreur lors de la création du panier : ' . $e->getMessage());
        }
    }
    ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="container">
                <div class="top-bar-left">
                    <a href="contact.php" class="top-link">Contact</a>
                    <a href="about.php" class="top-link">À propos</a>
                </div>
                <div class="top-bar-right">
                    <?php if (isLoggedIn()): ?>
                        <a href="compte.php" class="top-link">Mon compte</a>
                        <a href="logout.php" class="top-link">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="top-link">Connexion</a>
                        <a href="register.php" class="top-link">Inscription</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <div class="main-header">
            <div class="container">
                <div class="logo">
                    <a href="index.php">
                        <h1>Bander-Sneakers</h1>
                    </a>
                </div>

                <nav class="main-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a href="sneakers.php" class="nav-link">Sneakers</a>
                            <div class="mega-menu">
                                <div class="menu-column">
                                    <h3>Marques</h3>
                                    <ul>
                                        <li><a href="sneakers.php?brand_id=1">Nike</a></li>
                                        <li><a href="sneakers.php?brand_id=2">Adidas</a></li>
                                        <li><a href="sneakers.php?brand_id=3">Jordan</a></li>
                                        <li><a href="sneakers.php?brand_id=4">Puma</a></li>
                                        <li><a href="sneakers.php?brand_id=5">Reebok</a></li>
                                    </ul>
                                </div>
                                <div class="menu-column">
                                    <h3>Catégories</h3>
                                    <ul>
                                        <li><a href="sneakers.php?category_id=1">Running</a></li>
                                        <li><a href="sneakers.php?category_id=2">Basketball</a></li>
                                        <li><a href="sneakers.php?category_id=3">Lifestyle</a></li>
                                        <li><a href="sneakers.php?category_id=4">Skate</a></li>
                                        <li><a href="sneakers.php?category_id=5">Training</a></li>
                                    </ul>
                                </div>
                                <div class="menu-column">
                                    <h3>Collections</h3>
                                    <ul>
                                        <li><a href="sneakers.php?is_new_arrival=1">Nouveautés</a></li>
                                        <li><a href="sneakers.php?is_featured=1">Produits Vedettes</a></li>
                                        <li><a href="promotions.php">Promotions</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="hommes.php" class="nav-link">Hommes</a>
                        </li>
                        <li class="nav-item">
                            <a href="femmes.php" class="nav-link">Femmes</a>
                        </li>
                        <li class="nav-item">
                            <a href="enfants.php" class="nav-link">Enfants</a>
                        </li>
                        <li class="nav-item">
                            <a href="promotions.php" class="nav-link">Promotions</a>
                        </li>
                    </ul>
                </nav>

                <div class="header-actions">
                    <div class="search-box">
                        <form action="search.php" method="GET">
                            <input type="text" name="q" placeholder="Rechercher...">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <a href="wishlist.php" class="wishlist-icon">
                        <i class="fas fa-heart"></i>
                    </a>

                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        // Afficher le nombre d'articles dans le panier
                        if (isset($_SESSION['cart_id'])) {
                            try {
                                $db = getDbConnection();
                                $cartId = $_SESSION['cart_id'];

                                $stmt = $db->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id");
                                $stmt->bindParam(':cart_id', $cartId);
                                $stmt->execute();

                                $result = $stmt->fetch();
                                $cartCount = $result['total'] ? $result['total'] : 0;

                                if ($cartCount > 0) {
                                    echo '<span class="cart-count">' . $cartCount . '</span>';
                                }
                            } catch (PDOException $e) {
                                // Silencieux en cas d'erreur
                            }
                        }
                        ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->

        <!-- Le contenu principal sera inséré ici -->
