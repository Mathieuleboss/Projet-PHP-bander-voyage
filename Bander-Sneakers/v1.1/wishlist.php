<?php
// Page de liste de souhaits
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    $_SESSION['error_message'] = 'Vous devez être connecté pour accéder à vos favoris.';
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer les produits de la liste de souhaits
function getWishlistItems($userId) {
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
    ");
    $stmt->execute([$userId]);

    return $stmt->fetchAll();
}

$wishlistItems = getWishlistItems($userId);

// Titre et description de la page
$page_title = "Mes Favoris - Bander-Sneakers";
$page_description = "Gérez les produits que vous avez ajoutés à votre liste de favoris sur Bander-Sneakers.";

// Récupérer les messages
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

// Inclure l'en-tête
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <ul class="breadcrumb-list">
            <li><a href="index.php">Accueil</a></li>
            <li class="active">Mes Favoris</li>
        </ul>
    </div>
</div>

<!-- Wishlist Section -->
<section class="wishlist-section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">Mes Favoris</h1>
            <p class="section-subtitle">Les produits que vous avez sauvegardés pour plus tard.</p>
        </div>

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

        <?php if (empty($wishlistItems)): ?>
            <div class="empty-wishlist">
                <i class="fas fa-heart-broken"></i>
                <h2>Votre liste de favoris est vide</h2>
                <p>Vous n'avez encore aucun produit dans vos favoris.</p>
                <a href="sneakers.php" class="btn btn-primary">Explorer les produits</a>
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
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
                                <?php if ($item['stock_quantity'] > 0): ?>
                                    <a href="sneaker.php?id=<?= $item['sneaker_id'] ?>" class="btn btn-primary">Voir le produit</a>
                                <?php else: ?>
                                    <a href="#" class="btn btn-secondary notify-btn" data-id="<?= $item['sneaker_id'] ?>">M'avertir</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notify buttons for out of stock products
    const notifyButtons = document.querySelectorAll('.notify-btn');

    notifyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Cette fonctionnalité sera bientôt disponible. Vous serez averti lorsque le produit sera de retour en stock.');
        });
    });
});
</script>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
