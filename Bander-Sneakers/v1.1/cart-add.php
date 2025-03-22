<?php
/**
 * Ajouter un produit au panier
 *
 * Ce fichier gère l'ajout de produits au panier d'achat.
 * Il est appelé via AJAX depuis la page de détail du produit.
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Méthode non autorisée');
}

// Initialiser la réponse
$response = [
    'success' => false,
    'message' => 'Une erreur est survenue.',
    'cart_count' => 0
];

// Vérifier que l'ID du produit et la taille sont fournis
if (!isset($_POST['sneaker_id']) || !is_numeric($_POST['sneaker_id'])) {
    $response['message'] = 'Produit invalide.';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['size_id']) || !is_numeric($_POST['size_id'])) {
    $response['message'] = 'Veuillez sélectionner une taille.';
    echo json_encode($response);
    exit;
}

// Récupérer les données du formulaire
$sneakerId = (int)$_POST['sneaker_id'];
$sizeId = (int)$_POST['size_id'];
$quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Valider la quantité
if ($quantity < 1) {
    $quantity = 1;
}

try {
    $db = getDbConnection();

    // Vérifier que le produit existe et qu'il y a du stock dans cette taille
    $stmt = $db->prepare('
        SELECT s.sneaker_id, s.sneaker_name, s.price, s.discount_price, ss.stock_quantity
        FROM sneakers s
        JOIN sneaker_sizes ss ON s.sneaker_id = ss.sneaker_id
        WHERE s.sneaker_id = :sneaker_id AND ss.size_id = :size_id
    ');
    $stmt->bindParam(':sneaker_id', $sneakerId, PDO::PARAM_INT);
    $stmt->bindParam(':size_id', $sizeId, PDO::PARAM_INT);
    $stmt->execute();

    $sneaker = $stmt->fetch();

    if (!$sneaker) {
        $response['message'] = 'Produit ou taille non disponible.';
        echo json_encode($response);
        exit;
    }

    // Vérifier le stock
    if ($sneaker['stock_quantity'] < $quantity) {
        $response['message'] = 'Stock insuffisant. Il reste seulement ' . $sneaker['stock_quantity'] . ' unité(s) disponible(s).';
        echo json_encode($response);
        exit;
    }

    // Récupérer le prix à utiliser (prix normal ou prix réduit)
    $price = $sneaker['discount_price'] ? $sneaker['discount_price'] : $sneaker['price'];

    // Récupérer l'ID du panier de l'utilisateur
    $cartId = $_SESSION['cart_id'];

    // Vérifier si ce produit avec cette taille est déjà dans le panier
    $stmt = $db->prepare('
        SELECT cart_item_id, quantity
        FROM cart_items
        WHERE cart_id = :cart_id AND sneaker_id = :sneaker_id AND size_id = :size_id
    ');
    $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
    $stmt->bindParam(':sneaker_id', $sneakerId, PDO::PARAM_INT);
    $stmt->bindParam(':size_id', $sizeId, PDO::PARAM_INT);
    $stmt->execute();

    $existingItem = $stmt->fetch();

    if ($existingItem) {
        // Mettre à jour la quantité
        $newQuantity = $existingItem['quantity'] + $quantity;

        // Vérifier que la nouvelle quantité ne dépasse pas le stock
        if ($newQuantity > $sneaker['stock_quantity']) {
            $newQuantity = $sneaker['stock_quantity'];
            $response['message'] = 'Quantité ajustée au stock disponible.';
        }

        $stmt = $db->prepare('
            UPDATE cart_items
            SET quantity = :quantity, updated_at = NOW()
            WHERE cart_item_id = :cart_item_id
        ');
        $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
        $stmt->bindParam(':cart_item_id', $existingItem['cart_item_id'], PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Ajouter un nouvel article au panier
        $stmt = $db->prepare('
            INSERT INTO cart_items (cart_id, sneaker_id, size_id, quantity)
            VALUES (:cart_id, :sneaker_id, :size_id, :quantity)
        ');
        $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
        $stmt->bindParam(':sneaker_id', $sneakerId, PDO::PARAM_INT);
        $stmt->bindParam(':size_id', $sizeId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Récupérer le nombre total d'articles dans le panier
    $stmt = $db->prepare('SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id');
    $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
    $stmt->execute();

    $cartTotal = $stmt->fetch();
    $cartCount = $cartTotal['total'] ? $cartTotal['total'] : 0;

    // Préparer la réponse
    $response = [
        'success' => true,
        'message' => $sneaker['sneaker_name'] . ' a été ajouté à votre panier.',
        'cart_count' => $cartCount
    ];

} catch (PDOException $e) {
    // En production, vous ne devriez pas retourner le message d'erreur exact
    $response['message'] = 'Une erreur est survenue lors de l\'ajout au panier.';
    // Enregistrer l'erreur dans un fichier log
    error_log('Erreur PDO: ' . $e->getMessage());
}

// Renvoyer la réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);
