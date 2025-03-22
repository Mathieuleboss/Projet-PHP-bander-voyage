<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pdo = getDbConnection();
$featuredSneakers = getSneakers(['is_featured' => 1], 4);
$newArrivals = getSneakers(['is_new_arrival' => 1], 8);
$brands = getBrands();

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

$page_title = "Bander-Sneakers - Votre destination pour les sneakers premium";
$page_description = "Découvrez, achetez et partagez les sneakers que vous aimez avec Bander-Sneakers.";
include 'includes/header.php';
?>

<style>
    .chat-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }
    .chat-button:hover {
        transform: scale(1.1);
    }
    .chat-button img {
        width: 30px;
    }
    .chat-modal {
        display: none;
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 350px;
        max-height: 500px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 1000;
        flex-direction: column;
    }
    .chat-header {
        background: var(--primary-color, #007bff);
        color: white;
        padding: 10px 15px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-header span {
        font-weight: bold;
    }
    .chat-body {
        padding: 15px;
        overflow-y: auto;
        flex: 1;
    }
    .message {
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 5px;
        max-width: 80%;
    }
    .message.assistant {
        background: #f1f1f1;
        margin-left: auto;
    }
    .message.user {
        background: var(--primary-color, #007bff);
        color: white;
        margin-right: auto;
    }
    .message .time {
        font-size: 0.8rem;
        color: #999;
        display: block;
        margin-top: 5px;
    }
    .chat-footer {
        padding: 10px;
        display: flex;
        border-top: 1px solid #eee;
    }
    .chat-footer input {
        flex: 1;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px 0 0 4px;
        outline: none;
    }
    .chat-footer button {
        padding: 8px 15px;
        background: var(--primary-color, #007bff);
        color: white;
        border: none;
        border-radius: 0 4px 4px 0;
        cursor: pointer;
    }
    .chat-footer button:hover {
        background:rgb(0, 0, 0);
    }
    .fullscreen-btn, .close-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 1rem;
    }
    .chat-modal.fullscreen {
        width: 100%;
        height: 100%;
        bottom: 0;
        right: 0;
        max-height: none;
    }
</style>

<?php if ($success_message || $error_message): ?>
<div class="alert-container">
    <div class="container">
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success_message ?>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<section class="hero">
    <div class="hero-overlay"></div>
    <img src="https://images.squarespace-cdn.com/content/v1/62583c9f81a49b50ae4a416b/1650375372965-30AM8E87PBO25V5J5Q77/Nike_Selcted.png" alt="Bander-Sneakers Collection" class="hero-image">
    <div class="hero-content">
        <h2 class="hero-subtitle">Nouvelle collection</h2>
        <h1 class="hero-title">Préparez-vous pour la saison</h1>
        <p class="hero-description">Découvrez notre nouvelle collection de sneakers pour la saison et trouvez votre paire parfaite.</p>
        <a href="sneakers.php" class="btn btn-primary">Découvrir</a>
        <a href="promotions.php" class="btn btn-outline">Voir les promotions</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Produits Vedettes</h2>
            <p class="section-subtitle">Notre sélection de sneakers incontournables du moment.</p>
        </div>
        <div class="product-grid">
            <?php foreach ($featuredSneakers as $sneaker): ?>
                <div class="product-card">
                    <div class="product-image">
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
        <div class="text-center mt-4">
            <a href="sneakers.php?is_featured=1" class="btn btn-primary">Voir tous les produits vedettes</a>
        </div>
    </div>
</section>

<section class="banner">
    <div class="container">
        <div class="banner-grid">
            <div class="banner-item">
                <img src="assets/images/hommes.png" alt="Collection Homme">
                <div class="banner-content">
                    <h2>Collection Homme</h2>
                    <p>Trouvez votre style avec notre collection homme.</p>
                    <a href="hommes.php" class="btn btn-outline">Découvrir</a>
                </div>
            </div>
            <div class="banner-item">
                <img src="assets/images/femmes.png" alt="Collection Femme">
                <div class="banner-content">
                    <h2>Collection Femme</h2>
                    <p>Élégantes et confortables pour toutes les occasions.</p>
                    <a href="femmes.php" class="btn btn-outline">Découvrir</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Nouveautés</h2>
            <p class="section-subtitle">Les dernières sneakers tout juste arrivées dans notre boutique.</p>
        </div>
        <div class="product-grid">
            <?php foreach ($newArrivals as $sneaker): ?>
                <div class="product-card">
                    <div class="product-image">
                        <div class="product-badge new">Nouveau</div>
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
        <div class="text-center custom-margin">
            <a href="sneakers.php?is_new_arrival=1" class="btn btn-primary">Voir toutes les nouveautés</a>
        </div>
    </div>
</section>

<section class="section brands-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Nos Marques</h2>
            <p class="section-subtitle">Nous proposons les meilleures marques de sneakers du marché.</p>
        </div>
        <div class="brands-grid">
            <?php foreach ($brands as $brand): ?>
                <div class="brand-item">
                    <a href="sneakers.php?brand_id=<?= $brand['brand_id'] ?>">
                        <img src="assets/images/brands/<?= $brand['brand_logo'] ?>" alt="<?= $brand['brand_name'] ?>">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Livraison Gratuite</h3>
                <p>Livraison gratuite pour toutes les commandes de plus de 100€.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <h3>Retours Faciles</h3>
                <p>Retours gratuits sous 30 jours pour tous les articles.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Paiement Sécurisé</h3>
                <p>Vos transactions sont 100% sécurisées avec nous.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Support 24/7</h3>
                <p>Notre équipe de support est disponible 24/7 pour vous aider.</p>
            </div>
        </div>
    </div>
</section>

<button class="chat-button" onclick="toggleChat()">
    <img src="assets/images/chata.png" alt="Chat">
</button>

<div class="chat-modal" id="chatModal">
    <div class="chat-header">
        <span>Bander Assistant</span>
        <button class="fullscreen-btn" onclick="toggleFullscreen()">⛶</button>
        <button class="close-btn" onclick="closeChat()">✖</button>
    </div>
    <div class="chat-body" id="chatBody">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="message assistant">
                Bonjour ! Connectez-vous pour discuter avec un conseiller.
                <span class="time"><?= date('H:i') ?></span>
            </div>
        <?php else: ?>
            <div class="message assistant">
                Bonjour ! Comment puis-je vous aider aujourd’hui ?
                <span class="time"><?= date('H:i') ?></span>
            </div>
        <?php endif; ?>
    </div>
    <div class="chat-footer">
        <input type="text" id="chatInput" placeholder="Tapez votre message..." onkeydown="if(event.key==='Enter') sendMessage()" <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?>>
        <button onclick="sendMessage()" <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?>>
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
function toggleChat() {
    console.log('Toggle chat clicked');
    const chatModal = document.getElementById('chatModal');
    chatModal.style.display = chatModal.style.display === 'flex' ? 'none' : 'flex';
    if (chatModal.style.display === 'flex') {
        loadMessages();
        scrollToBottom();
    }
}

function closeChat() {
    console.log('Close chat clicked');
    document.getElementById('chatModal').style.display = 'none';
}

function toggleFullscreen() {
    console.log('Toggle fullscreen clicked');
    const chatModal = document.getElementById('chatModal');
    chatModal.classList.toggle('fullscreen');
}

function sendMessage() {
    console.log('Send message triggered');
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message) {
        console.log('Message vide, envoi annulé');
        return;
    }

    console.log('Sending message:', message);
    fetch('chat-api.php?action=send_message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `message=${encodeURIComponent(message)}`
    })
    .then(response => {
        console.log('Response received:', response);
        if (!response.ok) throw new Error('Erreur réseau: ' + response.status);
        return response.json();
    })
    .then(data => {
        console.log('Data parsed:', data);
        if (data.success) {
            console.log('Message envoyé avec succès');
            loadMessages();
            input.value = '';
        } else {
            console.error('Erreur serveur:', data.error);
        }
    })
    .catch(error => console.error('Erreur AJAX:', error));
}

function loadMessages() {
    console.log('Loading messages');
    fetch('chat-api.php?action=get_messages')
    .then(response => {
        console.log('Response received:', response);
        if (!response.ok) throw new Error('Erreur réseau: ' + response.status);
        return response.json();
    })
    .then(data => {
        console.log('Messages received:', data);
        if (data.success) {
            const chatBody = document.getElementById('chatBody');
            chatBody.innerHTML = '';
            data.messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${msg.is_admin ? 'assistant' : 'user'}`;
                messageDiv.innerHTML = `${msg.message_text}<span class="time">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>`;
                chatBody.appendChild(messageDiv);
            });
            scrollToBottom();
        } else {
            console.error('Erreur serveur:', data.error);
        }
    })
    .catch(error => console.error('Erreur AJAX:', error));
}

function scrollToBottom() {
    console.log('Scrolling to bottom');
    const chatBody = document.getElementById('chatBody');
    chatBody.scrollTop = chatBody.scrollHeight;
}

setInterval(() => {
    if (document.getElementById('chatModal').style.display === 'flex') {
        console.log('Interval: Checking for new messages');
        loadMessages();
    }
}, 5000);

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    if (<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
        loadMessages();
    }
});
</script>

<?php include 'includes/footer.php'; ?>