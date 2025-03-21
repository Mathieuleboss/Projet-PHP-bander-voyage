<?php
/**
 * Ajouter ou retirer un produit de la liste de souhaits
 *
 * Ce fichier gère l'ajout et la suppression de produits de la liste de favoris d'un utilisateur.
 * Il fonctionne comme un toggle: si le produit est déjà dans les favoris, il le supprime.
 * S'il n'y est pas, il l'ajoute.
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    $_SESSION['error_message'] = 'Vous devez être connecté pour gérer vos favoris.';

    // Stocker l'URL courante pour rediriger après la connexion
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    header('Location: login.php');
    exit();
}

// Vérifier que l'ID du produit est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = 'Produit invalide.';
    header('Location: index.php');
    exit();
}

$sneakerId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

try {
    $db = getDbConnection();

    // Vérifier si le produit existe
    $stmt = $db->prepare('SELECT sneaker_id FROM sneakers WHERE sneaker_id = ?');
    $stmt->execute([$sneakerId]);
    if (!$stmt->fetch()) {
        $_SESSION['error_message'] = 'Ce produit n\'existe pas.';
        header('Location: index.php');
        exit();
    }

    // Vérifier si le produit est déjà dans la liste de souhaits
    $stmt = $db->prepare('SELECT wishlist_id FROM wishlist WHERE user_id = ? AND sneaker_id = ?');
    $stmt->execute([$userId, $sneakerId]);
    $wishlistItem = $stmt->fetch();

    if ($wishlistItem) {
        // Le produit est déjà dans les favoris, on le supprime
        $stmt = $db->prepare('DELETE FROM wishlist WHERE wishlist_id = ?');
        $stmt->execute([$wishlistItem['wishlist_id']]);

        $_SESSION['success_message'] = 'Le produit a été retiré de vos favoris.';
    } else {
        // Le produit n'est pas dans les favoris, on l'ajoute
        $stmt = $db->prepare('INSERT INTO wishlist (user_id, sneaker_id) VALUES (?, ?)');
        $stmt->execute([$userId, $sneakerId]);

        $_SESSION['success_message'] = 'Le produit a été ajouté à vos favoris.';
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Une erreur est survenue lors de la mise à jour de vos favoris.';
    // Enregistrer l'erreur dans un fichier log
    error_log('Erreur PDO: ' . $e->getMessage());
}

// Rediriger vers la page précédente ou la page d'accueil
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: index.php');
}
exit();
