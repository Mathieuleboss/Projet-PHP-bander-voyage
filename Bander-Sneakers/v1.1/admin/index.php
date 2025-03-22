<?php
// Page de gestion du chat pour l'administration
session_start();

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Inclure la configuration et les fonctions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Initialiser les variables
$db = getDbConnection();
$success_message = '';
$error_message = '';

// Récupérer les messages de session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Traitement de l'envoi d'un message par l'admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    $message_text = cleanInput($_POST['message']);
    $admin_id = $_SESSION['user_id'];

    if (empty($message_text)) {
        $error_message = "Le message ne peut pas être vide.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO chat_messages (user_id, admin_id, message_text, is_admin) VALUES (:user_id, :admin_id, :message_text, 1)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':admin_id' => $admin_id,
                ':message_text' => $message_text
            ]);
            $success_message = "Message envoyé avec succès.";
        } catch (PDOException $e) {
            $error_message = "Erreur lors de l'envoi du message : " . $e->getMessage();
            error_log("Erreur PDO dans admin-chat : " . $e->getMessage());
        }
    }
}

// Récupérer la liste des utilisateurs avec messages non lus
$users_with_messages = $db->query("
    SELECT DISTINCT u.user_id, u.username, u.email,
        (SELECT COUNT(*) FROM chat_messages cm2 WHERE cm2.user_id = u.user_id AND cm2.is_admin = 0 AND cm2.created_at > COALESCE((SELECT MAX(cm3.created_at) FROM chat_messages cm3 WHERE cm3.user_id = u.user_id AND cm3.is_admin = 1), '1970-01-01')) as unread_count
    FROM chat_messages cm
    JOIN users u ON u.user_id = cm.user_id
    ORDER BY u.username ASC
")->fetchAll();

// Filtres pour l'historique
$selected_user_id = isset($_GET['user_id']) && is_numeric($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$search_query = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Récupérer les messages pour un utilisateur spécifique avec filtres
$chat_messages = [];
if ($selected_user_id) {
    $sql = "
        SELECT cm.*, u.username
        FROM chat_messages cm
        LEFT JOIN users u ON u.user_id = cm.user_id
        WHERE cm.user_id = :user_id
    ";
    $params = [':user_id' => $selected_user_id];

    if (!empty($search_query)) {
        $sql .= " AND cm.message_text LIKE :search";
        $params[':search'] = '%' . $search_query . '%';
    }

    if (!empty($date_filter)) {
        $sql .= " AND DATE(cm.created_at) = :date";
        $params[':date'] = $date_filter;
    }

    $sql .= " ORDER BY cm.created_at ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $chat_messages = $stmt->fetchAll();
}

// Titre de la page
$page_title = "Gestion du chat - Admin Bander-Sneakers";

// Inclure l'en-tête
include 'includes/header.php';
?>

<!-- Styles spécifiques pour le chat admin -->
<style>
    .chat-container {
        display: flex;
        height: 70vh;
        margin-top: 20px;
    }
    .user-list {
        width: 30%;
        border-right: 1px solid #ddd;
        overflow-y: auto;
        padding: 10px;
    }
    .user-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        position: relative;
    }
    .user-item:hover {
        background: #f5f5f5;
    }
    .user-item.active {
        background: #e0e0e0;
        font-weight: bold;
    }
    .unread-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    .chat-area {
        width: 70%;
        display: flex;
        flex-direction: column;
    }
    .chat-filters {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        display: flex;
        gap: 10px;
    }
    .chat-filters input[type="text"] {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        flex: 1;
    }
    .chat-filters input[type="date"] {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f9f9f9;
    }
    .chat-message {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 5px;
        max-width: 80%;
    }
    .chat-message.admin {
        background: #007bff;
        color: white;
        margin-left: auto;
    }
    .chat-message.user {
        background: #e0e0e0;
        margin-right: auto;
    }
    .chat-message .time {
        font-size: 0.8rem;
        color: #999;
        display: block;
        margin-top: 5px;
    }
    .chat-form {
        padding: 10px;
        border-top: 1px solid #ddd;
        display: flex;
    }
    .chat-form input {
        flex: 1;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px 0 0 4px;
    }
    .chat-form button {
        padding: 8px 15px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 0 4px 4px 0;
    }
</style>

<!-- Main Content -->
<div class="admin-content">
    <div class="container-fluid">
        <div class="admin-header">
            <h1>Gestion du chat</h1>
            <p>Répondez aux messages des utilisateurs.</p>
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

        <div class="chat-container">
            <!-- Liste des utilisateurs -->
            <div class="user-list">
                <h3>Utilisateurs</h3>
                <?php if (empty($users_with_messages)): ?>
                    <p>Aucun utilisateur n'a encore envoyé de message.</p>
                <?php else: ?>
                    <?php foreach ($users_with_messages as $user): ?>
                        <div class="user-item <?= $selected_user_id == $user['user_id'] ? 'active' : '' ?>" onclick="window.location.href='admin-chat.php?user_id=<?= $user['user_id'] ?>'">
                            <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)
                            <?php if ($user['unread_count'] > 0): ?>
                                <span class="unread-badge"><?= $user['unread_count'] ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Zone de chat -->
            <div class="chat-area">
                <?php if ($selected_user_id): ?>
                    <div class="chat-filters">
                        <input type="text" id="searchInput" placeholder="Rechercher dans les messages..." value="<?= htmlspecialchars($search_query) ?>">
                        <input type="date" id="dateFilter" value="<?= htmlspecialchars($date_filter) ?>">
                        <button onclick="applyFilters()">Filtrer</button>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <?php foreach ($chat_messages as $msg): ?>
                            <div class="chat-message <?= $msg['is_admin'] ? 'admin' : 'user' ?>">
                                <strong><?= $msg['is_admin'] ? 'Vous' : htmlspecialchars($msg['username']) ?> :</strong>
                                <?= htmlspecialchars($msg['message_text']) ?>
                                <span class="time"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form class="chat-form" method="POST" action="admin-chat.php?user_id=<?= $selected_user_id ?>">
                        <input type="hidden" name="user_id" value="<?= $selected_user_id ?>">
                        <input type="text" name="message" placeholder="Tapez votre réponse..." required>
                        <button type="submit">Envoyer</button>
                    </form>
                <?php else: ?>
                    <div class="chat-messages">
                        <p>Sélectionnez un utilisateur pour voir ses messages.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Faire défiler automatiquement vers le bas des messages au chargement
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});

// Mise à jour en temps réel des messages
setInterval(function() {
    if (window.location.search.includes('user_id=')) {
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
        const date = document.getElementById('dateFilter') ? document.getElementById('dateFilter').value : '';
        
        fetch(`admin-chat.php?user_id=${userId}&search=${encodeURIComponent(search)}&date=${encodeURIComponent(date)}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newMessages = doc.querySelector('.chat-messages').innerHTML;
                const chatMessages = document.getElementById('chatMessages');
                if (chatMessages.innerHTML !== newMessages) {
                    chatMessages.innerHTML = newMessages;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            })
            .catch(error => console.error('Erreur AJAX:', error));
    }
}, 5000); // Rafraîchit toutes les 5 secondes

// Appliquer les filtres
function applyFilters() {
    const userId = new URLSearchParams(window.location.search).get('user_id');
    const search = document.getElementById('searchInput').value;
    const date = document.getElementById('dateFilter').value;
    window.location.href = `admin-chat.php?user_id=${userId}&search=${encodeURIComponent(search)}&date=${encodeURIComponent(date)}`;
}
</script>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>