<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDbConnection();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $item_id = isset($_POST['item_id']) && is_numeric($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $reason = cleanInput($_POST['reason'] ?? '');

    if (!in_array($type, ['secondhand', 'review'])) {
        $errors[] = "Type de signalement invalide.";
    }
    if ($item_id <= 0) {
        $errors[] = "ID de l'élément invalide.";
    }
    if (empty($reason)) {
        $errors[] = "Veuillez indiquer une raison pour le signalement.";
    }

    // Vérifier si l'élément existe et récupérer l'utilisateur signalé
    $reported_user_id = 0;
    if (empty($errors)) {
        if ($type === 'secondhand') {
            $stmt = $db->prepare("SELECT id, user_id FROM secondhand_products WHERE id = :id AND statut = 'actif'");
        } else {
            $stmt = $db->prepare("SELECT review_id, user_id FROM reviews WHERE review_id = :id");
        }
        $stmt->execute([':id' => $item_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) {
            $errors[] = "L'élément signalé n'existe pas ou n'est plus disponible.";
        } else {
            $reported_user_id = $item['user_id'];
        }
    }

    // Insérer le signalement avec reported_user_id
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO reports (user_id, reported_user_id, type, item_id, reason, created_at) 
                                  VALUES (:user_id, :reported_user_id, :type, :item_id, :reason, NOW())");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':reported_user_id' => $reported_user_id,
                ':type' => $type,
                ':item_id' => $item_id,
                ':reason' => $reason
            ]);
            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'envoi du signalement : " . $e->getMessage();
        }
    }
}

// Récupérer les données de l'élément signalé si disponibles
$type = $_GET['type'] ?? '';
$item_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$item_title = '';

if ($type && $item_id) {
    if ($type === 'secondhand') {
        $stmt = $db->prepare("SELECT title FROM secondhand_products WHERE id = :id AND statut = 'actif'");
        $stmt->execute([':id' => $item_id]);
        $item = $stmt->fetch();
        $item_title = $item ? htmlspecialchars($item['title']) : 'Article inconnu';
    } else if ($type === 'review') {
        $stmt = $db->prepare("SELECT review_text FROM reviews WHERE review_id = :id");
        $stmt->execute([':id' => $item_id]);
        $item = $stmt->fetch();
        $item_title = $item ? htmlspecialchars(substr($item['review_text'], 0, 50)) . '...' : 'Avis inconnu';
    }
}

$page_title = "Signaler un contenu - Bander-Sneakers";
include 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-form-container">
                <h1 class="auth-title">Signaler un contenu</h1>

                <?php if ($success): ?>
                    <div class="alert alert-success">Signalement envoyé avec succès. Merci de votre contribution !</div>
                <?php elseif (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="report.php" method="POST" class="auth-form">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                    <input type="hidden" name="item_id" value="<?= $item_id ?>">

                    <div class="form-group">
                        <label>Élément signalé :</label>
                        <p><?= $item_title ?: 'Non spécifié' ?></p>
                    </div>

                    <div class="form-group">
                        <label for="reason">Raison du signalement <span class="required">*</span></label>
                        <textarea id="reason" name="reason" rows="5" required placeholder="Expliquez pourquoi vous signalez cet élément..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Envoyer le signalement</button>
                        <a href="<?= $type === 'secondhand' ? '2ndhand-detail.php?id=' . $item_id : 'index.php' ?>" class="btn btn-outline">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>