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

// Récupérer la liste des utilisateurs avec messages non lus (exclure les messages supprimés)
$users_with_messages = $db->query("
    SELECT DISTINCT u.user_id, u.username, u.email,
        (SELECT COUNT(*) FROM chat_messages cm2 WHERE cm2.user_id = u.user_id AND cm2.is_admin = 0 AND cm2.created_at > COALESCE((SELECT MAX(cm3.created_at) FROM chat_messages cm3 WHERE cm3.user_id = u.user_id AND cm3.is_admin = 1), '1970-01-01') AND cm2.is_deleted = 0) as unread_count
    FROM chat_messages cm
    JOIN users u ON u.user_id = cm.user_id
    WHERE cm.is_deleted = 0
    ORDER BY u.username ASC
")->fetchAll();

// Filtres pour l'historique
$selected_user_id = isset($_GET['user_id']) && is_numeric($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$search_query = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

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
    .chat-header {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f1f1f1;
    }
    .chat-header button {
        background: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }
    .chat-header button:hover {
        background: #c82333;
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
        position: relative;
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
    .chat-message.deleted {
        opacity: 0.5;
        background: #ccc !important;
        color: #666 !important;
    }
    .chat-message .time {
        font-size: 0.8rem;
        color: #999;
        display: block;
        margin-top: 5px;
    }
    .chat-message .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.8rem;
    }
    .chat-message .delete-btn:hover {
        background: #c82333;
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
                    <div class="chat-header">
                        <h4>Conversation avec l'utilisateur #<?= $selected_user_id ?></h4>
                        <button onclick="deleteConversation(<?= $selected_user_id ?>)">Supprimer la conversation</button>
                    </div>
                    <div class="chat-filters">
                        <input type="text" id="searchInput" placeholder="Rechercher dans les messages..." value="<?= htmlspecialchars($search_query) ?>">
                        <input type="date" id="dateFilter" value="<?= htmlspecialchars($date_filter) ?>">
                        <button onclick="applyFilters()">Filtrer</button>
                    </div>
                    <div class="chat-messages" id="chatMessages"></div>
                    <form class="chat-form" id="chatForm" onsubmit="sendAdminMessage(event)">
                        <input type="hidden" name="user_id" value="<?= $selected_user_id ?>">
                        <input type="text" name="message" id="messageInput" placeholder="Tapez votre réponse..." required>
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
// Charger les messages via l'API
function loadMessages() {
    const userId = <?= json_encode($selected_user_id) ?>;
    if (!userId) return;

    const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
    const date = document.getElementById('dateFilter') ? document.getElementById('dateFilter').value : '';

    fetch(`../chat-api.php?action=get_messages&user_id=${userId}${search ? `&search=${encodeURIComponent(search)}` : ''}${date ? `&date=${encodeURIComponent(date)}` : ''}`)
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML = '';
            data.messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-message ${msg.is_admin ? 'admin' : 'user'} ${msg.is_deleted ? 'deleted' : ''}`;
                messageDiv.innerHTML = `
                    <strong>${msg.is_admin ? 'Vous' : msg.username} :</strong>
                    ${msg.message_text}
                    <span class="time">${new Date(msg.created_at).toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                    ${!msg.is_deleted ? `<button class="delete-btn" onclick="deleteMessage(${msg.message_id})">X</button>` : ''}
                `;
                chatMessages.appendChild(messageDiv);
            });
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } else {
            console.error('Erreur serveur:', data.error);
        }
    })
    .catch(error => console.error('Erreur AJAX:', error));
}

// Envoyer un message via l'API
function sendAdminMessage(event) {
    event.preventDefault();
    const form = document.getElementById('chatForm');
    const formData = new FormData(form);

    fetch('../chat-api.php?action=send_message', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('messageInput').value = '';
            loadMessages();
        } else {
            console.error('Erreur serveur:', data.error);
        }
    })
    .catch(error => console.error('Erreur AJAX:', error));
}

// Supprimer un message
function deleteMessage(messageId) {
    if (!confirm('Voulez-vous vraiment supprimer ce message ?')) return;

    fetch(`../chat-api.php?action=delete_message&message_id=${messageId}`)
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            loadMessages();
        } else {
            console.error('Erreur serveur:', data.error);
        }
    })
    .catch(error => console.error('Erreur AJAX:', error));
}

// Supprimer toute la conversation
function deleteConversation(userId) {
    if (!confirm('Voulez-vous vraiment supprimer toute la conversation ?')) return;

    fetch(`../chat-api.php?action=delete_conversation&user_id=${userId}`)
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Recharger la page pour mettre à jour la liste des utilisateurs
            window.location.href = 'admin-chat.php';
        } else {
            console.error('Erreur serveur:', data.error);
        }
    })
    .catch(error => console.error('Erreur AJAX:', error));
}

// Appliquer les filtres
function applyFilters() {
    const userId = new URLSearchParams(window.location.search).get('user_id');
    const search = document.getElementById('searchInput').value;
    const date = document.getElementById('dateFilter').value;
    window.location.href = `admin-chat.php?user_id=${userId}&search=${encodeURIComponent(search)}&date=${encodeURIComponent(date)}`;
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    setInterval(loadMessages, 5000); // Rafraîchit toutes les 5 secondes
});
</script>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>