<?php
// Page de compte utilisateur
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    $_SESSION['error_message'] = 'Vous devez être connecté pour accéder à cette page.';
    header('Location: login.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$db = getDbConnection();
$userId = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error_message'] = 'Erreur : utilisateur non trouvé.';
    header('Location: index.php');
    exit();
}

// Récupérer les commandes de l'utilisateur
$stmt = $db->prepare("
    SELECT o.*, COUNT(oi.order_item_id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

// Récupérer les adresses de l'utilisateur (à implémenter plus tard)
$addresses = [];

// Récupérer les produits favoris de l'utilisateur
function getUserWishlist($userId) {
    $db = getDbConnection();
    $stmt = $db->prepare("
        SELECT w.*, s.sneaker_name, s.price, s.discount_price, s.stock_quantity,
               b.brand_name, c.category_name,
               (SELECT image_url FROM sneaker_images
                WHERE sneaker_id = s.sneaker_id AND is_primary = 1 LIMIT 1) AS primary_image
        FROM wishlist w
        JOIN sneakers s ON w.sneaker_id = s.sneaker_id
        LEFT JOIN brands b ON s.brand_id = b.brand_id
        LEFT JOIN categories c ON s.category_id = c.category_id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId]);

    return $stmt->fetchAll();
}

$wishlistItems = getUserWishlist($userId);

// Traitement du formulaire de modification du profil
$success_message = '';
$error_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier quelle action a été demandée
    if (isset($_POST['update_profile'])) {
        // Récupérer et valider les données
        $firstName = isset($_POST['first_name']) ? cleanInput($_POST['first_name']) : '';
        $lastName = isset($_POST['last_name']) ? cleanInput($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? cleanInput($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? cleanInput($_POST['phone']) : '';
        $address = isset($_POST['address']) ? cleanInput($_POST['address']) : '';
        $city = isset($_POST['city']) ? cleanInput($_POST['city']) : '';
        $postalCode = isset($_POST['postal_code']) ? cleanInput($_POST['postal_code']) : '';
        $country = isset($_POST['country']) ? cleanInput($_POST['country']) : '';

        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        if ($email !== $user['email']) {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ? AND user_id != ?");
            $stmt->execute([$email, $userId]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                $error_message = "Cette adresse email est déjà utilisée par un autre compte.";
            }
        }

        // Si pas d'erreur, mettre à jour le profil
        if (empty($error_message)) {
            try {
                $stmt = $db->prepare("
                    UPDATE users SET
                    first_name = ?,
                    last_name = ?,
                    email = ?,
                    phone = ?,
                    address = ?,
                    city = ?,
                    postal_code = ?,
                    country = ?
                    WHERE user_id = ?
                ");

                $stmt->execute([
                    $firstName, $lastName, $email, $phone,
                    $address, $city, $postalCode, $country,
                    $userId
                ]);

                $success_message = "Votre profil a été mis à jour avec succès.";

                // Mettre à jour les données utilisateur pour l'affichage
                $user['first_name'] = $firstName;
                $user['last_name'] = $lastName;
                $user['email'] = $email;
                $user['phone'] = $phone;
                $user['address'] = $address;
                $user['city'] = $city;
                $user['postal_code'] = $postalCode;
                $user['country'] = $country;

            } catch (PDOException $e) {
                $error_message = "Une erreur est survenue lors de la mise à jour de votre profil.";
                error_log("Erreur de mise à jour du profil: " . $e->getMessage());
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Récupérer et valider les données
        $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        // Vérifier que les champs sont remplis
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error_message = "Tous les champs sont obligatoires.";
        }
        // Vérifier que le mot de passe actuel est correct
        elseif (!password_verify($currentPassword, $user['password'])) {
            $error_message = "Le mot de passe actuel est incorrect.";
        }
        // Vérifier que les nouveaux mots de passe correspondent
        elseif ($newPassword !== $confirmPassword) {
            $error_message = "Les nouveaux mots de passe ne correspondent pas.";
        }
        // Vérifier que le nouveau mot de passe est assez long
        elseif (strlen($newPassword) < 6) {
            $error_message = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
        }

        // Si pas d'erreur, mettre à jour le mot de passe
        if (empty($error_message)) {
            try {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $stmt = $db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt->execute([$hashedPassword, $userId]);

                $success_message = "Votre mot de passe a été mis à jour avec succès.";

            } catch (PDOException $e) {
                $error_message = "Une erreur est survenue lors de la mise à jour de votre mot de passe.";
                error_log("Erreur de mise à jour du mot de passe: " . $e->getMessage());
            }
        }
    }
}

// Titre et description de la page
$page_title = "Mon Compte - Bander-Sneakers";
$page_description = "Gérez votre compte, vos commandes et vos informations personnelles sur Bander-Sneakers.";

// Inclure l'en-tête
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <ul class="breadcrumb-list">
            <li><a href="index.php">Accueil</a></li>
            <li class="active">Mon Compte</li>
        </ul>
    </div>
</div>

<!-- Account Section -->
<section class="account-section">
    <div class="container">
        <h1 class="section-title">Mon compte</h1>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <div class="account-container">
            <!-- Account Sidebar -->
            <div class="account-sidebar">
                <div class="account-user">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <h3><?= $user['first_name'] ? htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) : htmlspecialchars($user['username']) ?></h3>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>

                <ul class="account-nav">
                    <li class="active"><a href="#dashboard" data-tab="dashboard">Tableau de bord</a></li>
                    <li><a href="#orders" data-tab="orders">Mes commandes</a></li>
                    <li><a href="#wishlist" data-tab="wishlist">Mes favoris</a></li>
                    <li><a href="#profile" data-tab="profile">Informations personnelles</a></li>
                    <li><a href="#password" data-tab="password">Changer de mot de passe</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/index.php">Administration</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </div>

            <!-- Account Content -->
            <div class="account-content">
                <!-- Dashboard Tab -->
                <div id="dashboard" class="account-tab active">
                    <h2>Tableau de bord</h2>
                    <p>Bienvenue dans votre espace personnel, <?= $user['first_name'] ? htmlspecialchars($user['first_name']) : htmlspecialchars($user['username']) ?>.</p>

                    <div class="dashboard-stats">
                        <div class="dashboard-stat">
                            <i class="fas fa-shopping-bag"></i>
                            <div class="stat-content">
                                <span class="stat-value"><?= count($orders) ?></span>
                                <span class="stat-label">Commandes</span>
                            </div>
                        </div>

                        <div class="dashboard-stat">
                            <i class="fas fa-heart"></i>
                            <div class="stat-content">
                                <span class="stat-value"><?= count($wishlistItems) ?></span>
                                <span class="stat-label">Favoris</span>
                            </div>
                        </div>
                    </div>

                    <?php if (count($orders) > 0): ?>
                        <div class="dashboard-section">
                            <h3>Dernières commandes</h3>
                            <div class="dashboard-orders">
                                <?php
                                $recentOrders = array_slice($orders, 0, 3);
                                foreach ($recentOrders as $order):
                                ?>
                                    <div class="dashboard-order">
                                        <div class="order-info">
                                            <div class="order-number">Commande #<?= $order['order_id'] ?></div>
                                            <div class="order-date"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                        </div>
                                        <div class="order-status">
                                            <span class="status-<?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span>
                                        </div>
                                        <div class="order-total">
                                            <?= formatPrice($order['total_amount']) ?>
                                        </div>
                                        <div class="order-items-count">
                                            <?= $order['item_count'] ?> article<?= $order['item_count'] > 1 ? 's' : '' ?>
                                        </div>
                                        <a href="#" class="btn btn-sm">Détails</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($orders) > 3): ?>
                                <div class="text-center mt-3">
                                    <a href="#orders" class="btn btn-outline view-all-orders">Voir toutes mes commandes</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (count($wishlistItems) > 0): ?>
                        <div class="dashboard-section">
                            <h3>Mes favoris</h3>
                            <div class="dashboard-wishlist">
                                <?php foreach (array_slice($wishlistItems, 0, 3) as $item): ?>
                                    <div class="dashboard-wishlist-item">
                                        <div class="wishlist-item-image">
                                            <?php if ($item['primary_image']): ?>
                                                <img src="assets/images/sneakers/<?= $item['primary_image'] ?>" alt="<?= $item['sneaker_name'] ?>">
                                            <?php else: ?>
                                                <div class="no-image">Aucune image</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="wishlist-item-info">
                                            <h4><?= $item['sneaker_name'] ?></h4>
                                            <div class="wishlist-item-price">
                                                <?php if ($item['discount_price']): ?>
                                                    <span class="current-price"><?= formatPrice($item['discount_price']) ?></span>
                                                    <span class="original-price"><?= formatPrice($item['price']) ?></span>
                                                <?php else: ?>
                                                    <span class="current-price"><?= formatPrice($item['price']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="wishlist-item-actions">
                                            <a href="sneaker.php?id=<?= $item['sneaker_id'] ?>" class="btn btn-sm">Voir</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($wishlistItems) > 3): ?>
                                <div class="text-center mt-3">
                                    <a href="#wishlist" class="btn btn-outline view-all-wishlist">Voir tous mes favoris</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Orders Tab -->
                <div id="orders" class="account-tab">
                    <h2>Mes commandes</h2>

                    <?php if (count($orders) > 0): ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div class="order-header-left">
                                            <h3>Commande #<?= $order['order_id'] ?></h3>
                                            <div class="order-date">
                                                <i class="far fa-calendar-alt"></i>
                                                <?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?>
                                            </div>
                                        </div>
                                        <div class="order-header-right">
                                            <div class="order-status">
                                                <span class="status-<?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span>
                                            </div>
                                            <div class="order-total">
                                                Total: <?= formatPrice($order['total_amount']) ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-content">
                                        <div class="order-shipping">
                                            <h4>Adresse de livraison</h4>
                                            <p>
                                                <?= $order['shipping_address'] ?><br>
                                                <?= $order['shipping_postal_code'] ?> <?= $order['shipping_city'] ?><br>
                                                <?= $order['shipping_country'] ?>
                                            </p>
                                        </div>

                                        <div class="order-details">
                                            <h4>Détails</h4>
                                            <ul>
                                                <li><strong>Méthode de paiement:</strong> <?= ucfirst($order['payment_method']) ?></li>
                                                <li><strong>Méthode de livraison:</strong> <?= ucfirst($order['shipping_method']) ?></li>
                                                <li><strong>Nombre d'articles:</strong> <?= $order['item_count'] ?></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="order-actions">
                                        <a href="order-details.php?id=<?= $order['order_id'] ?>" class="btn btn-outline">Voir les détails</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-orders">
                            <i class="fas fa-shopping-bag"></i>
                            <h3>Vous n'avez pas encore passé de commande</h3>
                            <p>Découvrez notre catalogue et passez votre première commande dès maintenant.</p>
                            <a href="sneakers.php" class="btn btn-primary">Explorer les produits</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Wishlist Tab -->
                <div id="wishlist" class="account-tab">
                    <h2>Mes favoris</h2>

                    <?php if (count($wishlistItems) > 0): ?>
                        <div class="account-wishlist-grid">
                            <?php foreach ($wishlistItems as $item): ?>
                                <div class="wishlist-card">
                                    <div class="wishlist-image">
                                        <a href="sneaker.php?id=<?= $item['sneaker_id'] ?>">
                                            <?php if ($item['primary_image']): ?>
                                                <img src="assets/images/sneakers/<?= $item['primary_image'] ?>" alt="<?= $item['sneaker_name'] ?>">
                                            <?php else: ?>
                                                <div class="no-image">Aucune image</div>
                                            <?php endif; ?>
                                        </a>

                                        <div class="wishlist-actions">
                                            <a href="wishlist-add.php?id=<?= $item['sneaker_id'] ?>" class="wishlist-remove" title="Retirer des favoris">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="wishlist-info">
                                        <div class="wishlist-brand"><?= $item['brand_name'] ?></div>
                                        <h3 class="wishlist-title">
                                            <a href="sneaker.php?id=<?= $item['sneaker_id'] ?>"><?= $item['sneaker_name'] ?></a>
                                        </h3>
                                        <div class="wishlist-price">
                                            <?php if ($item['discount_price']): ?>
                                                <span class="current-price"><?= formatPrice($item['discount_price']) ?></span>
                                                <span class="original-price"><?= formatPrice($item['price']) ?></span>
                                            <?php else: ?>
                                                <span class="current-price"><?= formatPrice($item['price']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="wishlist-stock">
                                            <?php if ($item['stock_quantity'] > 0): ?>
                                                <span class="in-stock">En stock</span>
                                            <?php else: ?>
                                                <span class="out-of-stock">Rupture de stock</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="wishlist-buttons">
                                            <a href="sneaker.php?id=<?= $item['sneaker_id'] ?>" class="btn btn-primary">Voir le produit</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-wishlist">
                            <i class="fas fa-heart"></i>
                            <h3>Votre liste de favoris est vide</h3>
                            <p>Vous n'avez pas encore ajouté de produits à vos favoris.</p>
                            <a href="sneakers.php" class="btn btn-primary">Explorer les produits</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Profile Tab -->
                <div id="profile" class="account-tab">
                    <h2>Informations personnelles</h2>

                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="profile-intro">
                            <h3>Bonjour, <?= $user['first_name'] ? htmlspecialchars($user['first_name']) : htmlspecialchars($user['username']) ?></h3>
                            <p>Mettez à jour vos informations personnelles et votre adresse</p>
                        </div>
                    </div>

                    <form action="compte.php" method="post" class="profile-form enhanced">
                        <input type="hidden" name="update_profile" value="1">

                        <div class="form-section">
                            <h3><i class="fas fa-user-edit"></i> Informations de base</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">Prénom</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" placeholder="Votre prénom">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Nom</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" placeholder="Votre nom">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="Votre adresse email">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phone">Téléphone</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Votre numéro de téléphone">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-map-marker-alt"></i> Adresse de livraison</h3>
                            <div class="form-group full-width">
                                <label for="address">Adresse</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-home input-icon"></i>
                                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Votre adresse complète">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">Ville</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-city input-icon"></i>
                                        <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" placeholder="Votre ville">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="postal_code">Code postal</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-mailbox input-icon"></i>
                                        <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" placeholder="Votre code postal">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="country">Pays</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-globe input-icon"></i>
                                    <select id="country" name="country">
                                        <option value="">Sélectionner un pays</option>
                                        <option value="France" <?= ($user['country'] ?? '') == 'France' ? 'selected' : '' ?>>France</option>
                                        <option value="Belgique" <?= ($user['country'] ?? '') == 'Belgique' ? 'selected' : '' ?>>Belgique</option>
                                        <option value="Suisse" <?= ($user['country'] ?? '') == 'Suisse' ? 'selected' : '' ?>>Suisse</option>
                                        <option value="Luxembourg" <?= ($user['country'] ?? '') == 'Luxembourg' ? 'selected' : '' ?>>Luxembourg</option>
                                        <option value="Canada" <?= ($user['country'] ?? '') == 'Canada' ? 'selected' : '' ?>>Canada</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions enhanced">
                            <button type="reset" class="btn btn-outline">Annuler les modifications</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>

                <!-- Password Tab -->
                <div id="password" class="account-tab">
                    <h2>Changer de mot de passe</h2>

                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="profile-intro">
                            <h3>Sécurité du compte</h3>
                            <p>Modifiez votre mot de passe pour sécuriser votre compte</p>
                        </div>
                    </div>

                    <form action="compte.php" method="post" class="password-form enhanced">
                        <input type="hidden" name="change_password" value="1">

                        <div class="form-section animated">
                            <h3><i class="fas fa-key"></i> Modification du mot de passe</h3>

                            <div class="form-group">
                                <label for="current_password">Mot de passe actuel <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" id="current_password" name="current_password" required placeholder="Entrez votre mot de passe actuel">
                                    <span class="toggle-password" data-target="current_password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="password-divider">
                                <span class="divider-line"></span>
                                <span class="divider-text">Nouveau mot de passe</span>
                                <span class="divider-line"></span>
                            </div>

                            <div class="form-group">
                                <label for="new_password">Nouveau mot de passe <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock-open input-icon"></i>
                                    <input type="password" id="new_password" name="new_password" required placeholder="Entrez votre nouveau mot de passe">
                                    <span class="toggle-password" data-target="new_password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <p class="form-help">Le mot de passe doit contenir au moins 6 caractères.</p>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirmer le nouveau mot de passe <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-check-circle input-icon"></i>
                                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirmez votre nouveau mot de passe">
                                    <span class="toggle-password" data-target="confirm_password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div class="password-match-indicator">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Les mots de passe ne correspondent pas</span>
                                </div>
                            </div>
                        </div>

                        <div class="password-tips enhanced">
                            <div class="tips-header">
                                <i class="fas fa-shield-alt"></i>
                                <h4>Conseils pour un mot de passe fort</h4>
                            </div>
                            <div class="tips-content">
                                <ul class="tips-list">
                                    <li class="tip-item">
                                        <span class="tip-icon"><i class="fas fa-ruler"></i></span>
                                        <span class="tip-text">Utilisez au moins 8 caractères</span>
                                    </li>
                                    <li class="tip-item">
                                        <span class="tip-icon"><i class="fas fa-font"></i></span>
                                        <span class="tip-text">Combinez lettres majuscules et minuscules</span>
                                    </li>
                                    <li class="tip-item">
                                        <span class="tip-icon"><i class="fas fa-hashtag"></i></span>
                                        <span class="tip-text">Incluez des chiffres et des caractères spéciaux (!@#$%)</span>
                                    </li>
                                    <li class="tip-item">
                                        <span class="tip-icon"><i class="fas fa-user-slash"></i></span>
                                        <span class="tip-text">Évitez d'utiliser des informations personnelles</span>
                                    </li>
                                    <li class="tip-item">
                                        <span class="tip-icon"><i class="fas fa-sync-alt"></i></span>
                                        <span class="tip-text">N'utilisez pas le même mot de passe que pour d'autres sites</span>
                                    </li>
                                </ul>
                                <div class="password-strength-meter">
                                    <div class="strength-label">Force du mot de passe</div>
                                    <div class="strength-bars">
                                        <span class="strength-bar"></span>
                                        <span class="strength-bar"></span>
                                        <span class="strength-bar"></span>
                                        <span class="strength-bar"></span>
                                    </div>
                                    <div class="strength-text">Pas encore évalué</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions enhanced">
                            <button type="reset" class="btn btn-outline">Annuler</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Changer mon mot de passe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tabs functionality
        const navLinks = document.querySelectorAll('.account-nav a');
        const tabContents = document.querySelectorAll('.account-tab');

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Get the tab ID from data-tab attribute
                const tabId = this.getAttribute('data-tab');

                // Remove active class from all nav links and tabs
                navLinks.forEach(navLink => {
                    navLink.parentElement.classList.remove('active');
                });

                tabContents.forEach(tab => {
                    tab.classList.remove('active');
                });

                // Add active class to current link and tab
                this.parentElement.classList.add('active');
                document.getElementById(tabId).classList.add('active');

                // Update URL hash
                window.location.hash = tabId;
            });
        });

        // Handle initial tab based on URL hash
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            const tabLink = document.querySelector(`.account-nav a[data-tab="${hash}"]`);

            if (tabLink) {
                tabLink.click();
            }
        }

        // Handle "View all orders" and "View all wishlist" buttons
        const viewAllOrdersBtn = document.querySelector('.view-all-orders');
        const viewAllWishlistBtn = document.querySelector('.view-all-wishlist');

        if (viewAllOrdersBtn) {
            viewAllOrdersBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector('.account-nav a[data-tab="orders"]').click();
            });
        }

        if (viewAllWishlistBtn) {
            viewAllWishlistBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector('.account-nav a[data-tab="wishlist"]').click();
            });
        }

        // Toggle password visibility
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');

        togglePasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);

                // Toggle password visibility
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordInput.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        });

        // Check if passwords match
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const matchIndicator = document.querySelector('.password-match-indicator');

        if (newPasswordInput && confirmPasswordInput && matchIndicator) {
            function checkPasswordMatch() {
                const newPass = newPasswordInput.value;
                const confirmPass = confirmPasswordInput.value;

                if (confirmPass.length > 0) {
                    matchIndicator.classList.add('visible');

                    if (newPass === confirmPass) {
                        matchIndicator.classList.add('match');
                        matchIndicator.innerHTML = '<i class="fas fa-check-circle"></i><span>Les mots de passe correspondent</span>';
                    } else {
                        matchIndicator.classList.remove('match');
                        matchIndicator.innerHTML = '<i class="fas fa-times-circle"></i><span>Les mots de passe ne correspondent pas</span>';
                    }
                } else {
                    matchIndicator.classList.remove('visible');
                }
            }

            newPasswordInput.addEventListener('input', checkPasswordMatch);
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        }

        // Password strength meter functionality
        const strengthBars = document.querySelectorAll('.strength-bar');
        const strengthText = document.querySelector('.strength-text');

        if (newPasswordInput && strengthBars.length && strengthText) {
            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = calculatePasswordStrength(password);

                // Reset all bars
                strengthBars.forEach(bar => {
                    bar.className = 'strength-bar';
                });

                // Update strength meter
                if (password.length === 0) {
                    strengthText.textContent = 'Pas encore évalué';
                } else if (strength < 2) {
                    strengthBars[0].classList.add('weak');
                    strengthText.textContent = 'Très faible';
                } else if (strength < 4) {
                    strengthBars[0].classList.add('weak');
                    strengthBars[1].classList.add('weak');
                    strengthText.textContent = 'Faible';
                } else if (strength < 6) {
                    strengthBars[0].classList.add('medium');
                    strengthBars[1].classList.add('medium');
                    strengthBars[2].classList.add('medium');
                    strengthText.textContent = 'Moyen';
                } else {
                    strengthBars[0].classList.add('strong');
                    strengthBars[1].classList.add('strong');
                    strengthBars[2].classList.add('strong');
                    strengthBars[3].classList.add('strong');
                    strengthText.textContent = 'Fort';
                }
            });
        }

        function calculatePasswordStrength(password) {
            let strength = 0;

            // Length
            if (password.length >= 8) strength += 2;
            else if (password.length >= 6) strength += 1;

            // Complexity
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            if (/\d/.test(password)) strength += 1;
            if (/[^a-zA-Z0-9]/.test(password)) strength += 1;

            // Variety
            const uniqueChars = new Set(password.split('')).size;
            if (uniqueChars >= 8) strength += 1;

            return strength;
        }
    });
</script>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
