<?php
/**
 * Fonctions utilitaires pour le site Bander-Sneakers
 */

require_once 'config.php';

error_log("Début de functions.php - Chargement des fonctions utilitaires");

/**
 * Nettoie une chaîne de caractères
 * @param string $data Données à nettoyer
 * @return string Données nettoyées
 */
function cleanInput($data) {
    error_log("cleanInput appelé avec data : " . $data);
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    error_log("cleanInput - Résultat après nettoyage : " . $data);
    return $data;
}

/**
 * Redirige vers une URL
 * @param string $url URL de redirection
 */
function redirect($url) {
    error_log("Redirection vers : " . $url);
    header("Location: " . $url);
    exit();
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool True si l'utilisateur est connecté, sinon False
 */
function isLoggedIn() {
    $isLoggedIn = isset($_SESSION['user_id']);
    error_log("isLoggedIn - Résultat : " . ($isLoggedIn ? 'true' : 'false'));
    return $isLoggedIn;
}

/**
 * Vérifie si l'utilisateur est un administrateur
 * @return bool True si l'utilisateur est un administrateur, sinon False
 */
function isAdmin() {
    $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    error_log("isAdmin - Résultat : " . ($isAdmin ? 'true' : 'false'));
    return $isAdmin;
}

/**
 * Génère une chaîne aléatoire
 * @param int $length Longueur de la chaîne
 * @return string Chaîne aléatoire
 */
function generateRandomString($length = 10) {
    error_log("generateRandomString appelé avec length : $length");
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    error_log("generateRandomString - Résultat : $randomString");
    return $randomString;
}

/**
 * Récupère toutes les sneakers avec filtres optionnels
 * @param array $filters Filtres à appliquer (brand_id, category_id, gender, is_featured, is_new_arrival, search, price_min, price_max, sort)
 * @param int $limit Nombre maximum de résultats
 * @param int $offset Décalage pour la pagination
 * @return array Tableau de sneakers
 */
function getSneakers($filters = [], $limit = 0, $offset = 0) {
    error_log("Début de getSneakers - Filtres : " . print_r($filters, true) . ", Limit : $limit, Offset : $offset");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getSneakers");
        throw new Exception("Erreur de connexion à la base de données");
    }

    $sql = "SELECT s.*, b.brand_name, c.category_name,
            (SELECT image_url FROM sneaker_images WHERE sneaker_id = s.sneaker_id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM sneakers s
            LEFT JOIN brands b ON s.brand_id = b.brand_id
            LEFT JOIN categories c ON s.category_id = c.category_id
            WHERE 1=1";
    $conditions = [];
    $params = [];
    $paramCount = 0;

    // Gestion des filtres
    if (isset($filters['brand_id'])) {
        $conditions[] = "s.brand_id = ?";
        $params[] = $filters['brand_id'];
        $paramCount++;
        error_log("Filtre brand_id ajouté : " . $filters['brand_id']);
    }

    if (isset($filters['category_id'])) {
        $conditions[] = "s.category_id = ?";
        $params[] = $filters['category_id'];
        $paramCount++;
        error_log("Filtre category_id ajouté : " . $filters['category_id']);
    }

    if (isset($filters['gender'])) {
        if ($filters['gender'] == 'homme') {
            $conditions[] = "(s.gender = 'homme' OR s.gender = 'unisex')";
        } elseif ($filters['gender'] == 'femme') {
            $conditions[] = "(s.gender = 'femme' OR s.gender = 'unisex')";
        } elseif ($filters['gender'] == 'enfant') {
            $conditions[] = "s.gender = 'enfant'";
        }
        error_log("Filtre gender ajouté : " . $filters['gender']);
    }

    if (isset($filters['is_featured'])) {
        $conditions[] = "s.is_featured = ?";
        $params[] = $filters['is_featured'];
        $paramCount++;
        error_log("Filtre is_featured ajouté : " . $filters['is_featured']);
    }

    if (isset($filters['is_new_arrival'])) {
        $conditions[] = "s.is_new_arrival = ?";
        $params[] = $filters['is_new_arrival'];
        $paramCount++;
        error_log("Filtre is_new_arrival ajouté : " . $filters['is_new_arrival']);
    }

    if (isset($filters['search'])) {
        $conditions[] = "(s.sneaker_name LIKE ? OR b.brand_name LIKE ? OR s.description LIKE ?)";
        $searchValue = '%' . $filters['search'] . '%';
        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;
        $paramCount += 3;
        error_log("Filtre search ajouté : " . $searchValue);
    }

    if (isset($filters['price_min'])) {
        $conditions[] = "((s.discount_price IS NOT NULL AND s.discount_price >= ?) OR (s.discount_price IS NULL AND s.price >= ?))";
        $params[] = $filters['price_min'];
        $params[] = $filters['price_min'];
        $paramCount += 2;
        error_log("Filtre price_min ajouté : " . $filters['price_min']);
    }

    if (isset($filters['price_max'])) {
        $conditions[] = "((s.discount_price IS NOT NULL AND s.discount_price <= ?) OR (s.discount_price IS NULL AND s.price <= ?))";
        $params[] = $filters['price_max'];
        $params[] = $filters['price_max'];
        $paramCount += 2;
        error_log("Filtre price_max ajouté : " . $filters['price_max']);
    }

    // Ajout des conditions à la requête
    if (!empty($conditions)) {
        $sql .= " AND " . implode(' AND ', $conditions);
    }

    // Tri
    if (isset($filters['sort'])) {
        switch ($filters['sort']) {
            case 'price_asc':
                $sql .= " ORDER BY s.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY s.price DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY s.sneaker_name ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY s.sneaker_name DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY s.release_date DESC";
                break;
            default:
                $sql .= " ORDER BY s.sneaker_id DESC";
        }
        error_log("Tri appliqué : " . $filters['sort']);
    } else {
        $sql .= " ORDER BY s.sneaker_id DESC";
        error_log("Tri par défaut appliqué : ORDER BY s.sneaker_id DESC");
    }

    // Limite et décalage pour la pagination
    if ($limit > 0) {
        $sql .= " LIMIT ?";
        $params[] = (int)$limit;
        $paramCount++;
        error_log("Limite ajoutée : $limit");

        if ($offset > 0) {
            $sql .= " OFFSET ?";
            $params[] = (int)$offset;
            $paramCount++;
            error_log("Offset ajouté : $offset");
        }
    }

    error_log("Requête SQL finale dans getSneakers : " . $sql);
    error_log("Nombre de placeholders attendus : $paramCount");
    error_log("Paramètres à lier : " . print_r($params, true));

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans getSneakers : " . print_r($db->errorInfo(), true));
            throw new PDOException("Erreur lors de la préparation de la requête");
        }

        // Lier les paramètres un par un
        for ($i = 0; $i < count($params); $i++) {
            $paramIndex = $i + 1;
            $paramValue = $params[$i];
            $paramType = (is_int($paramValue) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $stmt->bindValue($paramIndex, $paramValue, $paramType);
            error_log("Paramètre lié [$paramIndex] : $paramValue (Type : " . ($paramType == PDO::PARAM_INT ? 'INT' : 'STR') . ")");
        }

        error_log("Nombre total de paramètres liés : " . count($params));

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Nombre de sneakers récupérées : " . count($results));
        return $results;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getSneakers : " . $e->getMessage());
        throw new PDOException("Erreur lors de l'exécution de la requête dans getSneakers : " . $e->getMessage());
    }
}

/**
 * Récupère une sneaker par son ID
 * @param int $sneakerId ID de la sneaker
 * @return array|false Données de la sneaker ou false si non trouvée
 */
function getSneakerById($sneakerId) {
    error_log("getSneakerById appelé avec sneakerId : $sneakerId");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getSneakerById");
        throw new Exception("Erreur de connexion à la base de données");
    }

    $sql = "SELECT s.*, b.brand_name, c.category_name
            FROM sneakers s
            LEFT JOIN brands b ON s.brand_id = b.brand_id
            LEFT JOIN categories c ON s.category_id = c.category_id
            WHERE s.sneaker_id = ?";
    error_log("Requête SQL dans getSneakerById : " . $sql);

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans getSneakerById : " . print_r($db->errorInfo(), true));
            throw new PDOException("Erreur lors de la préparation de la requête");
        }

        $stmt->bindValue(1, $sneakerId, PDO::PARAM_INT);
        error_log("Paramètre lié [1] : $sneakerId (Type : INT)");

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Résultat de getSneakerById : " . ($result ? 'Trouvé' : 'Non trouvé'));
        return $result;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getSneakerById : " . $e->getMessage());
        throw new PDOException("Erreur lors de l'exécution de la requête dans getSneakerById : " . $e->getMessage());
    }
}

/**
 * Récupère les images d'une sneaker
 * @param int $sneakerId ID de la sneaker
 * @return array Tableau d'images
 */
function getSneakerImages($sneakerId) {
    error_log("getSneakerImages appelé avec sneakerId : $sneakerId");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getSneakerImages");
        throw new Exception("Erreur de connexion à la base de données");
    }

    $sql = "SELECT * FROM sneaker_images WHERE sneaker_id = ? ORDER BY is_primary DESC";
    error_log("Requête SQL dans getSneakerImages : " . $sql);

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans getSneakerImages : " . print_r($db->errorInfo(), true));
            throw new PDOException("Erreur lors de la préparation de la requête");
        }

        $stmt->bindValue(1, $sneakerId, PDO::PARAM_INT);
        error_log("Paramètre lié [1] : $sneakerId (Type : INT)");

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Nombre d'images récupérées : " . count($results));
        return $results;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getSneakerImages : " . $e->getMessage());
        throw new PDOException("Erreur lors de l'exécution de la requête dans getSneakerImages : " . $e->getMessage());
    }
}

/**
 * Récupère les tailles disponibles pour une sneaker
 * @param int $sneakerId ID de la sneaker
 * @return array Tableau des tailles disponibles
 */
function getSneakerSizes($sneakerId) {
    error_log("getSneakerSizes appelé avec sneakerId : $sneakerId");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getSneakerSizes");
        throw new Exception("Erreur de connexion à la base de données");
    }

    $sql = "SELECT ss.*, s.size_value, s.size_type
            FROM sneaker_sizes ss
            JOIN sizes s ON ss.size_id = s.size_id
            WHERE ss.sneaker_id = ? AND ss.stock_quantity > 0
            ORDER BY s.size_type, s.size_value";
    error_log("Requête SQL dans getSneakerSizes : " . $sql);

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans getSneakerSizes : " . print_r($db->errorInfo(), true));
            throw new PDOException("Erreur lors de la préparation de la requête");
        }

        $stmt->bindValue(1, $sneakerId, PDO::PARAM_INT);
        error_log("Paramètre lié [1] : $sneakerId (Type : INT)");

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Nombre de tailles récupérées : " . count($results));
        return $results;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getSneakerSizes : " . $e->getMessage());
        throw new PDOException("Erreur lors de l'exécution de la requête dans getSneakerSizes : " . $e->getMessage());
    }
}

/**
 * Récupère tous les avis pour une sneaker
 * @param int $sneakerId ID de la sneaker
 * @return array Tableau des avis
 */
function getSneakerReviews($sneakerId) {
    error_log("getSneakerReviews appelé avec sneakerId : $sneakerId");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getSneakerReviews");
        throw new Exception("Erreur de connexion à la base de données");
    }

    $sql = "SELECT r.*, u.username
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.user_id
            WHERE r.sneaker_id = ?
            ORDER BY r.created_at DESC";
    error_log("Requête SQL dans getSneakerReviews : " . $sql);

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans getSneakerReviews : " . print_r($db->errorInfo(), true));
            throw new PDOException("Erreur lors de la préparation de la requête");
        }

        $stmt->bindValue(1, $sneakerId, PDO::PARAM_INT);
        error_log("Paramètre lié [1] : $sneakerId (Type : INT)");

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Nombre d'avis récupérés : " . count($results));
        return $results;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getSneakerReviews : " . $e->getMessage());
        throw new PDOException("Erreur lors de l'exécution de la requête dans getSneakerReviews : " . $e->getMessage());
    }
}

/**
 * Récupère toutes les marques
 * @return array Tableau des marques
 */
function getBrands() {
    error_log("getBrands appelé");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getBrands");
        throw new Exception("Erreur de connexion à la base de données");
    }

    $sql = "SELECT * FROM brands ORDER BY brand_name";
    error_log("Requête SQL dans getBrands : " . $sql);

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans getBrands : " . print_r($db->errorInfo(), true));
            throw new PDOException("Erreur lors de la préparation de la requête");
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Nombre de marques récupérées : " . count($results));
        return $results;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getBrands : " . $e->getMessage());
        throw new PDOException("Erreur lors de l'exécution de la requête dans getBrands : " . $e->getMessage());
    }
}

/**
 * Récupère toutes les catégories
 * @return array Tableau des catégories
 */
function getCategories() {
    error_log("getCategories appelé");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getCategories");
        throw new Exception("Erreur de connexion à la base de données");
    }

    $sql = "SELECT * FROM categories ORDER BY category_name";
    error_log("Requête SQL dans getCategories : " . $sql);

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans getCategories : " . print_r($db->errorInfo(), true));
            throw new PDOException("Erreur lors de la préparation de la requête");
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Nombre de catégories récupérées : " . count($results));
        return $results;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getCategories : " . $e->getMessage());
        throw new PDOException("Erreur lors de l'exécution de la requête dans getCategories : " . $e->getMessage());
    }
}

/**
 * Formate un prix pour l'affichage
 * @param float $price Prix à formater
 * @return string Prix formaté
 */
function formatPrice($price) {
    error_log("formatPrice appelé avec price : $price");
    $formattedPrice = number_format($price, 2, ',', ' ') . ' €';
    error_log("formatPrice - Résultat : $formattedPrice");
    return $formattedPrice;
}

/**
 * Calcule le pourcentage de réduction
 * @param float $originalPrice Prix original
 * @param float $discountPrice Prix réduit
 * @return int Pourcentage de réduction
 */
function calculateDiscount($originalPrice, $discountPrice) {
    error_log("calculateDiscount appelé avec originalPrice : $originalPrice, discountPrice : $discountPrice");

    if ($originalPrice <= 0 || $discountPrice <= 0 || $discountPrice >= $originalPrice) {
        error_log("calculateDiscount - Conditions non remplies, retourne 0");
        return 0;
    }

    $discount = round(100 - ($discountPrice * 100 / $originalPrice));
    error_log("calculateDiscount - Pourcentage de réduction : $discount");
    return $discount;
}

/**
 * Génère un slug à partir d'une chaîne
 * @param string $string Chaîne à transformer en slug
 * @return string Slug généré
 */
function generateSlug($string) {
    error_log("generateSlug appelé avec string : $string");

    // Remplacer les caractères spéciaux
    $string = str_replace(['é', 'è', 'ê', 'ë'], 'e', $string);
    $string = str_replace(['à', 'â', 'ä'], 'a', $string);
    $string = str_replace(['ù', 'û', 'ü'], 'u', $string);
    $string = str_replace(['ô', 'ö'], 'o', $string);
    $string = str_replace(['ï', 'î'], 'i', $string);
    $string = str_replace(['ç'], 'c', $string);

    // Convertir en minuscules et remplacer les espaces par des tirets
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);

    $slug = trim($string, '-');
    error_log("generateSlug - Résultat : $slug");
    return $slug;
}

/**
 * Ajoute des points de fidélité à un utilisateur
 * @param int $userId ID de l'utilisateur
 * @param int $points Nombre de points à ajouter
 * @param string $description Description de l'opération (non utilisé si la colonne n'existe pas)
 * @return bool True si les points ont été ajoutés, false sinon
 */
function addLoyaltyPoints($userId, $points, $description = 'Points gagnés') {
    error_log("addLoyaltyPoints appelé avec userId : $userId, points : $points, description : $description");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans addLoyaltyPoints");
        return false;
    }

    // Requête ajustée sans la colonne 'description'
    $sql = "INSERT INTO loyalty_points (user_id, points, earned_at) VALUES (?, ?, NOW())";
    error_log("Requête SQL dans addLoyaltyPoints : $sql");

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans addLoyaltyPoints : " . print_r($db->errorInfo(), true));
            return false;
        }

        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $points, PDO::PARAM_INT);
        error_log("Paramètres liés : [user_id: $userId, points: $points]");

        if (!$stmt->execute()) {
            error_log("Échec de l'exécution de la requête dans addLoyaltyPoints : " . print_r($stmt->errorInfo(), true));
            return false;
        }

        error_log("Résultat de l'ajout des points : Succès");
        return true;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans addLoyaltyPoints : " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère le total des points de fidélité d'un utilisateur
 * @param int $userId ID de l'utilisateur
 * @return int Total des points
 */
function getLoyaltyPoints($userId) {
    error_log("getLoyaltyPoints appelé avec userId : $userId");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getLoyaltyPoints");
        return 0;
    }

    $sql = "SELECT SUM(points) as total_points FROM loyalty_points WHERE user_id = ?";
    error_log("Requête SQL dans getLoyaltyPoints : $sql");

    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans getLoyaltyPoints : " . print_r($db->errorInfo(), true));
            return 0;
        }

        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        error_log("Paramètre lié : [user_id: $userId]");

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalPoints = $result['total_points'] ?? 0;
        error_log("Total des points pour userId $userId : $totalPoints");
        return (int)$totalPoints;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getLoyaltyPoints : " . $e->getMessage());
        return 0;
    }
}

/**
 * Utilise des points de fidélité pour une réduction
 * @param int $userId ID de l'utilisateur
 * @param int $pointsToUse Nombre de points à utiliser
 * @return bool True si les points ont été utilisés, false sinon
 */
function useLoyaltyPoints($userId, $pointsToUse) {
    error_log("useLoyaltyPoints appelé avec userId : $userId, pointsToUse : $pointsToUse");

    $totalPoints = getLoyaltyPoints($userId);
    if ($totalPoints < $pointsToUse) {
        error_log("Erreur : Pas assez de points pour userId $userId. Points disponibles : $totalPoints, Points demandés : $pointsToUse");
        return false;
    }

    // Ajouter une entrée négative pour déduire les points
    return addLoyaltyPoints($userId, -$pointsToUse, 'Points utilisés pour une réduction');
}

/**
 * Enregistre une commande et accorde des points de fidélité
 * @param int|null $userId ID de l'utilisateur (peut être null pour les invités)
 * @param float $totalAmount Montant total de la commande
 * @param string $shippingAddress Adresse de livraison
 * @param string $shippingCity Ville de livraison
 * @param string $shippingPostalCode Code postal de livraison
 * @param string $shippingCountry Pays de livraison
 * @param string $paymentMethod Méthode de paiement
 * @param string $shippingMethod Méthode de livraison
 * @return int|bool ID de la commande si succès, false sinon
 */
function createOrderAndAwardPoints($userId, $totalAmount, $shippingAddress, $shippingCity, $shippingPostalCode, $shippingCountry, $paymentMethod, $shippingMethod) {
    error_log("createOrderAndAwardPoints appelé avec userId : " . ($userId ?? 'NULL') . ", totalAmount : $totalAmount, shippingAddress : $shippingAddress, shippingCity : $shippingCity, shippingPostalCode : $shippingPostalCode, shippingCountry : $shippingCountry, paymentMethod : $paymentMethod, shippingMethod : $shippingMethod");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans createOrderAndAwardPoints");
        return false;
    }

    try {
        // Insérer la commande
        $sql = "INSERT INTO orders (
            user_id, total_amount, shipping_address, shipping_city, shipping_postal_code, shipping_country,
            payment_method, shipping_method, order_status
        ) VALUES (
            :user_id, :total_amount, :shipping_address, :shipping_city, :shipping_postal_code, :shipping_country,
            :payment_method, :shipping_method, 'pending'
        )";
        error_log("Requête SQL dans createOrderAndAwardPoints : $sql");

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("Erreur lors de la préparation de la requête dans createOrderAndAwardPoints : " . print_r($db->errorInfo(), true));
            return false;
        }

        $stmt->bindValue(':user_id', $userId, $userId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':total_amount', $totalAmount, PDO::PARAM_STR);
        $stmt->bindValue(':shipping_address', $shippingAddress, PDO::PARAM_STR);
        $stmt->bindValue(':shipping_city', $shippingCity, PDO::PARAM_STR);
        $stmt->bindValue(':shipping_postal_code', $shippingPostalCode, PDO::PARAM_STR);
        $stmt->bindValue(':shipping_country', $shippingCountry, PDO::PARAM_STR);
        $stmt->bindValue(':payment_method', $paymentMethod, PDO::PARAM_STR);
        $stmt->bindValue(':shipping_method', $shippingMethod, PDO::PARAM_STR);

        error_log("Paramètres liés : [user_id: " . ($userId ?? 'NULL') . ", total_amount: $totalAmount, shipping_address: $shippingAddress, shipping_city: $shippingCity, shipping_postal_code: $shippingPostalCode, shipping_country: $shippingCountry, payment_method: $paymentMethod, shipping_method: $shippingMethod]");

        $stmt->execute();
        $orderId = $db->lastInsertId();
        error_log("Commande enregistrée avec order_id : $orderId");

        // Si l'utilisateur est connecté, attribuer des points de fidélité
        if ($userId) {
            $points = floor($totalAmount); // 1 € = 1 point
            error_log("Points calculés pour la commande #$orderId : $points");
            $pointsAdded = addLoyaltyPoints($userId, $points, "Points gagnés pour la commande #$orderId");
            if (!$pointsAdded) {
                error_log("Échec de l'ajout des points pour userId $userId");
                // Ne pas échouer la commande si les points ne sont pas ajoutés
            }
        }

        return $orderId;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans createOrderAndAwardPoints : " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère l'historique des points de fidélité d'un utilisateur
 * @param int $userId ID de l'utilisateur
 * @return array Historique des points
 */
function getLoyaltyPointsHistory($userId) {
    error_log("getLoyaltyPointsHistory appelé avec userId : $userId");

    $db = getDbConnection();
    if (!$db) {
        error_log("Erreur : Impossible d'obtenir la connexion à la base de données dans getLoyaltyPointsHistory");
        return [];
    }

    $sql = "SELECT points, description, earned_at FROM loyalty_points WHERE user_id = ? ORDER BY earned_at DESC";
    error_log("Requête SQL dans getLoyaltyPointsHistory : $sql");

    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Historique des points récupéré : " . print_r($history, true));
        return $history;
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getLoyaltyPointsHistory : " . $e->getMessage());
        return [];
    }
}
// Dans includes/functions.php

function truncate($text, $length, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text; // Retourne le texte tel quel s'il est plus court que la limite
    }
    return substr($text, 0, $length) . $suffix; // Tronque et ajoute les points de suspension
}

error_log("Fin de functions.php - Chargement terminé");