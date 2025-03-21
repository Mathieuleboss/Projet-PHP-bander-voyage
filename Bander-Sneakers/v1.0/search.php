<?php
// Page de recherche
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Vérifier que le terme de recherche est fourni
if (!isset($_GET['q']) || empty($_GET['q'])) {
    header('Location: index.php');
    exit();
}

$searchTerm = cleanInput($_GET['q']);

// Initialiser les filtres avec le terme de recherche
$filters = [
    'search' => $searchTerm
];

// Pagination
$items_per_page = 12;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Récupérer le nombre total de résultats pour la pagination
$db = getDbConnection();

$sql = "SELECT COUNT(*) as total FROM sneakers s
        WHERE (s.sneaker_name LIKE :search OR s.description LIKE :search)
        OR s.sneaker_id IN (
            SELECT s.sneaker_id FROM sneakers s
            LEFT JOIN brands b ON s.brand_id = b.brand_id
            WHERE b.brand_name LIKE :search
        )";
$params = [':search' => '%' . $searchTerm . '%'];

$stmt = $db->prepare($sql);
$stmt->bindParam(':search', $params[':search']);
$stmt->execute();

$result = $stmt->fetch();
$total_items = $result['total'];
$total_pages = ceil($total_items / $items_per_page);

// Récupérer les sneakers correspondant à la recherche
$sneakers = getSneakers($filters, $items_per_page, $offset);

// Titre et description de la page
$page_title = "Recherche: " . htmlspecialchars($searchTerm) . " | Bander-Sneakers";
$page_description = "Résultats de recherche pour \"" . htmlspecialchars($searchTerm) . "\". Trouvez les sneakers que vous cherchez sur Bander-Sneakers.";

// Inclure l'en-tête
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <ul class="breadcrumb-list">
            <li><a href="index.php">Accueil</a></li>
            <li class="active">Recherche: <?= htmlspecialchars($searchTerm) ?></li>
        </ul>
    </div>
</div>

<!-- Search Results Section -->
<section class="search-section">
    <div class="container">
        <div class="search-header">
            <h1>Résultats de recherche pour "<?= htmlspecialchars($searchTerm) ?>"</h1>
            <p><?= $total_items ?> résultat(s) trouvé(s)</p>
        </div>

        <!-- Search Form -->
        <div class="search-form-wrapper">
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="q" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Rechercher des sneakers...">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <?php if (empty($sneakers)): ?>
            <div class="no-results">
                <p>Aucun résultat trouvé pour votre recherche.</p>
                <p>Essayez avec d'autres termes ou consultez nos suggestions ci-dessous.</p>

                <div class="search-suggestions">
                    <h3>Suggestions populaires</h3>
                    <ul class="suggestions-list">
                        <li><a href="search.php?q=Nike">Nike</a></li>
                        <li><a href="search.php?q=Adidas">Adidas</a></li>
                        <li><a href="search.php?q=Air+Jordan">Air Jordan</a></li>
                        <li><a href="search.php?q=running">Running</a></li>
                        <li><a href="search.php?q=basketball">Basketball</a></li>
                    </ul>
                </div>

                <div class="popular-categories">
                    <h3>Catégories populaires</h3>
                    <div class="category-buttons">
                        <?php
                        $categories = getCategories();
                        foreach ($categories as $category) {
                            echo '<a href="sneakers.php?category_id=' . $category['category_id'] . '" class="category-btn">' . $category['category_name'] . '</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($sneakers as $sneaker): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($sneaker['is_new_arrival']): ?>
                                <div class="product-badge new">Nouveau</div>
                            <?php endif; ?>

                            <?php if ($sneaker['discount_price']): ?>
                                <div class="product-badge sale">-<?= calculateDiscount($sneaker['price'], $sneaker['discount_price']) ?>%</div>
                            <?php endif; ?>

                            <img src="assets/images/sneakers/<?= $sneaker['primary_image'] ?>" alt="<?= $sneaker['sneaker_name'] ?>">

                            <div class="product-actions">
                                <a href="wishlist-add.php?id=<?= $sneaker['sneaker_id'] ?>" class="action-btn wishlist-btn" title="Ajouter aux favoris">
                                    <i class="fas fa-heart"></i>
                                </a>
                                <a href="sneaker.php?id=<?= $sneaker['sneaker_id'] ?>" class="action-btn view-btn" title="Voir le produit">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product-info">
                            <div class="product-brand"><?= $sneaker['brand_name'] ?></div>
                            <h3 class="product-title">
                                <a href="sneaker.php?id=<?= $sneaker['sneaker_id'] ?>"><?= $sneaker['sneaker_name'] ?></a>
                            </h3>
                            <div class="product-price">
                                <?php if ($sneaker['discount_price']): ?>
                                    <span class="current-price"><?= formatPrice($sneaker['discount_price']) ?></span>
                                    <span class="original-price"><?= formatPrice($sneaker['price']) ?></span>
                                <?php else: ?>
                                    <span class="current-price"><?= formatPrice($sneaker['price']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <ul>
                        <?php if ($current_page > 1): ?>
                            <li><a href="search.php?q=<?= urlencode($searchTerm) ?>&page=<?= $current_page - 1 ?>"><i class="fas fa-chevron-left"></i></a></li>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        if ($start_page > 1) {
                            echo '<li><a href="search.php?q=' . urlencode($searchTerm) . '&page=1">1</a></li>';
                            if ($start_page > 2) {
                                echo '<li class="ellipsis">...</li>';
                            }
                        }

                        for ($i = $start_page; $i <= $end_page; $i++) {
                            $active = $i == $current_page ? 'active' : '';
                            echo '<li class="' . $active . '"><a href="search.php?q=' . urlencode($searchTerm) . '&page=' . $i . '">' . $i . '</a></li>';
                        }

                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<li class="ellipsis">...</li>';
                            }
                            echo '<li><a href="search.php?q=' . urlencode($searchTerm) . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                        }
                        ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li><a href="search.php?q=<?= urlencode($searchTerm) ?>&page=<?= $current_page + 1 ?>"><i class="fas fa-chevron-right"></i></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
